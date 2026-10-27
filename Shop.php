<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['id'] ?? null;


// Get current active offer (excluding paused)
$offer = null;
$discount = 1;
$offerQuery = "SELECT * FROM offer 
               WHERE (applies_to = 'all' OR applies_to = 'product') 
               AND NOW() BETWEEN start_date AND end_date 
               AND discount_percentage IS NOT NULL
               LIMIT 1";
$offerResult = $conn->query($offerQuery);
if ($offerResult && $offerResult->num_rows > 0) {
    $offer = $offerResult->fetch_assoc();
    $discount = 1 - $offer['discount_percentage'] / 100;
}

// Get products
$products = [];
$result = $conn->query("SELECT * FROM product");
while ($row = $result->fetch_assoc()) {
    // $row['original_price'] = $row['price'];
    // $row['price'] = round($row['price'] * $discount, 2);
    $row['original_price'] = $row['price'];

if ($offer && $offer['discount_percentage'] !== null) {
    $row['price'] = round($row['price'] * $discount, 2);
}

    $products[] = $row;
}

// Cart items for current user
$cart_items = [];
if ($user_id) {
    $cartResult = $conn->query("SELECT product_id FROM shoppingcart WHERE user_id = $user_id");
    while ($cartRow = $cartResult->fetch_assoc()) {
        $cart_items[] = $cartRow['product_id'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shop</title>
  <link rel="stylesheet" href="Shop_Style.css">
  <link rel="stylesheet" href="header_styles.css">
  <script src="https://kit.fontawesome.com/7d804d1342.js" crossorigin="anonymous"></script>
</head>
<body>
<?php include 'header.php'; ?>
<!--  Toast Message -->
<div id="toast" class="toast"></div>

<!--  Popups -->
<div id="loginPopup" class="popup">
  <div class="popup-content">
    <p>You must be logged in to add to cart.</p>
    <button onclick="window.location.href='signin.php'">Login</button>
  </div>
</div>

<div id="confirmRemovePopup" class="popup">
  <div class="popup-content">
    <p>Remove this item from cart?</p>
    <button onclick="confirmRemove(true)">Yes</button>
    <button onclick="confirmRemove(false)">No</button>
  </div>
</div>

<!--  Detail Modal -->
<div class="product-popup" id="detailModal">
  <div class="product-popup-content">
    <button class="popup-close" onclick="closeDetailModal()">&times;</button>
    <img id="popup-image" class="popup-img" src="" alt="">
    <h2 id="popup-title"></h2>
    <p id="popup-description"></p>
    <div class="popup-price">
      <img src="SAR.JPEG" alt="SAR" style="width: 20px;">
      <span id="popup-price-value"></span>
    </div>
    <div id="popup-stock-status-value"></div>
  </div>
</div>

<!--  Main Shop Container -->
<div class="container">
  <div class="sidebar">
    <input type="text" class="search" id="search-input" placeholder="Search products...">

    <!-- Filters -->
    <div class="animal-type">
      <h3>Animal Type</h3>
      <div class="filter-buttons">
        <button class="filter-button animal-filter active" data-animal="all">All</button>
        <button class="filter-button animal-filter" data-animal="dog">Dog</button>
        <button class="filter-button animal-filter" data-animal="cat">Cat</button>
        <button class="filter-button animal-filter" data-animal="bird">Bird</button>
        <button class="filter-button animal-filter" data-animal="small animal">Small Animal</button>
      </div>
    </div>

    <div class="animal-type">
      <h3>Category</h3>
      <div class="filter-buttons">
        <button class="filter-button category-filter active" data-category="all">All</button>
        <button class="filter-button category-filter" data-category="toys">Toys</button>
        <button class="filter-button category-filter" data-category="food">Food</button>
        <button class="filter-button category-filter" data-category="supplies">Supplies</button>
      </div>
    </div>

    <div class="availability-filter">
      <h3>Availability</h3>
      <div class="filter-buttons">
        <button class="filter-button availability-filter" data-availability="in-stock">In Stock</button>
        <button class="filter-button availability-filter" data-availability="out-of-stock">Out of Stock</button>
      </div>
    </div>

    <div class="price-range">
      <h3>Price Range</h3>
      <input type="number" id="min-price" placeholder="Min Price">
      <input type="number" id="max-price" placeholder="Max Price">
      <button class="filter-button" id="price-range-filter">Apply</button>
      <button class="filter-button" id="reset-filters">Reset</button>
    </div>
  </div>

  <div class="products" id="product-list">
    <?php foreach ($products as $product): ?>
      <?php
        $inCart = in_array($product['product_id'], $cart_items);
        $stock = $product['stock'];
      ?>
      <div class="product"
           data-animal="<?= strtolower($product['animal_type']) ?>"
           data-category="<?= strtolower($product['product_type']) ?>"
           data-availability="<?= $stock > 0 ? 'in-stock' : 'out-of-stock' ?>"
           data-price="<?= $product['price'] ?>">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= $product['name'] ?>">
        <h4 class="product-name"><?= htmlspecialchars($product['name']) ?></h4>
        <p><?= htmlspecialchars($product['description']) ?></p>
       
        <div class="price-container">
  <img src="SAR.JPEG" alt="SAR">
  <?php if ($offer && $offer['discount_percentage'] !== null): ?>
    <span style="text-decoration:line-through; color:red"><?= $product['original_price'] ?></span>
  <?php endif; ?>
  <span><?= $product['price'] ?></span>
</div>

        <div class="quantity">
          <input type="number" min="1" value="1" class="product-quantity">
          <button class="add-to-cart <?= $inCart ? 'active' : '' ?>"
            onclick="handleCartClick(<?= $product['product_id'] ?>, this, '<?= addslashes($product['name']) ?>', <?= $stock ?>)">
            <?= $inCart ? "✔ {$product['name']} in Cart" : "Add to Cart" ?>
          </button>
        </div>
        <button class="view-details" onclick='openDetailModal(<?= json_encode($product) ?>)'>View Details</button>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<div id="toast" class="toast"></div>

<script>
const userLoggedIn = <?= $user_id ? 'true' : 'false' ?>;
let cart = {};

function showOutOfStockPopup() {
  const popup = document.createElement("div");
  popup.style.position = "fixed";
  popup.style.top = "50%";
  popup.style.left = "50%";
  popup.style.transform = "translate(-50%, -50%)";
  popup.style.backgroundColor = "#f44336";
  popup.style.color = "white";
  popup.style.padding = "20px";
  popup.style.borderRadius = "8px";
  popup.style.zIndex = "9999";
  popup.textContent = "❌ This product is out of stock and cannot be added to cart.";

  document.body.appendChild(popup);
  setTimeout(() => popup.remove(), 3000);
}

function handleCartClick(productId, btn, name, stock) {
  if (!userLoggedIn) return document.getElementById("loginPopup").style.display = "flex";
  if (stock <= 0) {
    showOutOfStockPopup();
    return;
  }
  if (btn.classList.contains("active")) {
    document.getElementById("confirmRemovePopup").style.display = "flex";
    cart._pending = { id: productId, btn };
  } else {
    const qty = btn.previousElementSibling.value;
    fetch("cart_handler.php", {
      method: "POST",
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=add&product_id=${productId}&quantity=${qty}`
    }).then(() => {
      btn.classList.add("active");
      btn.innerHTML = `✔ ${name} in Cart`;
      showToast("Added to cart");
    });
  }
}

function confirmRemove(decision) {
  document.getElementById("confirmRemovePopup").style.display = "none";
  if (!decision) return;
  const { id, btn } = cart._pending;
  fetch("cart_handler.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=remove&product_id=${id}`
  }).then(() => {
    btn.classList.remove("active");
    btn.textContent = "Add to Cart";
    showToast("Removed from cart");
  });
}

function openDetailModal(product) {
  document.getElementById("popup-image").src = product.image_url;
  document.getElementById("popup-title").textContent = product.name;
  document.getElementById("popup-description").textContent = product.description;
  document.getElementById("popup-price-value").textContent = product.price;
  const status = document.getElementById("popup-stock-status-value");
  status.className = product.stock > 0 ? 'stock-in' : 'stock-out';
  status.textContent = product.stock > 0 ? 'In Stock' : 'Out of Stock';
  document.getElementById("detailModal").style.display = "flex";
}
function closeDetailModal() {
  document.getElementById("detailModal").style.display = "none";
}

function applyFilters() {
  const search = document.getElementById("search-input").value.toLowerCase();
  const animals = [...document.querySelectorAll(".animal-filter.active")].map(b => b.dataset.animal);
  const cats = [...document.querySelectorAll(".category-filter.active")].map(b => b.dataset.category);
  const avail = [...document.querySelectorAll(".availability-filter.active")].map(b => b.dataset.availability);
  const min = parseFloat(document.getElementById("min-price").value) || 0;
  const max = parseFloat(document.getElementById("max-price").value) || Infinity;

  document.querySelectorAll(".product").forEach(p => {
    const name = p.querySelector(".product-name").textContent.toLowerCase();
    const price = parseFloat(p.dataset.price);
    const match = (
      name.includes(search) &&
      (animals.includes("all") || animals.includes(p.dataset.animal)) &&
      (cats.includes("all") || cats.includes(p.dataset.category)) &&
      (avail.length === 0 || avail.includes(p.dataset.availability)) &&
      price >= min && price <= max
    );
    p.style.display = match ? "block" : "none";
  });
}

document.querySelectorAll('.filter-button').forEach(btn => {
  btn.addEventListener("click", () => {
    btn.classList.toggle("active");
    applyFilters();
  });
});
document.getElementById("search-input").addEventListener("input", applyFilters);
document.getElementById("price-range-filter").addEventListener("click", applyFilters);
document.getElementById("reset-filters").addEventListener("click", () => {
  document.querySelectorAll(".filter-button").forEach(b => b.classList.remove("active"));
  document.querySelector('.animal-filter[data-animal="all"]').classList.add("active");
  document.querySelector('.category-filter[data-category="all"]').classList.add("active");
  document.getElementById("min-price").value = '';
  document.getElementById("max-price").value = '';
  document.getElementById("search-input").value = '';
  applyFilters();
});
document.addEventListener("DOMContentLoaded", applyFilters);
window.addEventListener("click", e => {
  if (e.target.classList.contains("popup")) e.target.style.display = "none";
});
window.addEventListener("keydown", e => {
  if (e.key === "Escape") {
    document.querySelectorAll(".popup").forEach(p => p.style.display = "none");
    closeDetailModal();
  }
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
<!-- ===============================
//    Author:
//   
//    Renad Alkahtani
//    ===============================  -->