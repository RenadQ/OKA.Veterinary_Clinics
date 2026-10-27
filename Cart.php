<?php
session_start();
$conn = new mysqli("localhost", "root", "", "oka_petstore");
if ($conn->connect_error) die("DB Error");

$user_id = $_SESSION['id'] ?? 2;
$cart_items = [];
$total = 0;

// ===== Get active offer (only if not paused) =====
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
    $discount = 1 - ($offer['discount_percentage'] / 100);
}

// Fetch cart items
$stmt = $conn->prepare("SELECT sc.cart_id, sc.product_id, sc.quantity, p.name, p.price AS original_price, p.image_url, p.stock 
                        FROM ShoppingCart sc
                        JOIN Product p ON sc.product_id = p.product_id
                        WHERE sc.user_id = ? AND sc.quantity > 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['price'] = ($offer && $offer['discount_percentage'] !== null)
                    ? round($row['original_price'] * $discount, 2)
                    : $row['original_price'];

    $row['subtotal'] = $row['price'] * $row['quantity'];
    $cart_items[] = $row;
    $total += $row['subtotal'];
}

$total_items = array_sum(array_column($cart_items, 'quantity'));

// ===== AJAX actions remain unchanged =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_quantity') {
        $stmt = $conn->prepare("UPDATE ShoppingCart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("iii", $_POST['quantity'], $_POST['cart_id'], $user_id);
        $stmt->execute();
        exit;
    }

    if ($action === 'remove_item') {
        $stmt = $conn->prepare("UPDATE ShoppingCart SET quantity = 0 WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $_POST['cart_id'], $user_id);
        $stmt->execute();
        exit;
    }

    if ($action === 'complete_payment') {
        $method = $_POST['method'];
        $amount = (float)$_POST['amount'];

        $stmt = $conn->prepare("SELECT sc.cart_id, sc.product_id, sc.quantity, p.stock, p.price 
                                FROM ShoppingCart sc JOIN Product p ON sc.product_id = p.product_id 
                                WHERE sc.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                echo "not_enough_stock";
                exit;
            }
        }

        if (count($items) > 0) {
            $main_cart_id = $items[0]['cart_id'];
            $stmt = $conn->prepare("INSERT INTO Payment (amount, status, method, cart_id) VALUES (?, 'Completed', ?, ?)");
            $stmt->bind_param("dsi", $amount, $method, $main_cart_id);
            $stmt->execute();

            foreach ($items as $item) {
                $cart_id = $item['cart_id'];
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $total_amount = $price * $quantity;
                $order_date = date("Y-m-d H:i:s");
                $pickup_time = date("Y-m-d H:i:s", strtotime("+2 days"));

                $stmt = $conn->prepare("INSERT INTO `Order` (user_id, admin_id, order_date, pickup_date_time, total_amount, status, cart_id, product_id, quantity, price_at_purchase)
                                        VALUES (?, NULL, ?, ?, ?, 'Pending', ?, ?, ?, ?)");
                $stmt->bind_param("issdiiii", $user_id, $order_date, $pickup_time, $total_amount, $cart_id, $product_id, $quantity, $price);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE Product SET stock = stock - ? WHERE product_id = ?");
                $stmt->bind_param("ii", $quantity, $product_id);
                $stmt->execute();
            }

            $stmt = $conn->prepare("UPDATE ShoppingCart SET quantity = 0 WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            echo "success";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link rel="stylesheet" href="cart_style_user.css">
  <link rel="stylesheet" href="header_styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<div style="height: 80px;"></div>

<div class="container" id="cart-container">
  <div class="cart-section">
    <h2>Cart (<?= $total_items ?> Items)</h2>
    <?php if (empty($cart_items)): ?>
      <p class="empty-message">Your cart is empty. <a href="shop.php" class="shop-link">Go to Shop</a></p>
    <?php else: ?>
      <?php foreach ($cart_items as $item): ?>
        <div class="cart-item">
          <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product">
          <div class="info">
            <h4><?= htmlspecialchars($item['name']) ?></h4>
            <p>
              <?php if ($offer && $offer['discount_percentage'] !== null): ?>
                <span style="color:red; text-decoration:line-through;"><?= number_format($item['original_price'], 2) ?></span>
              <?php endif; ?>
              <?= number_format($item['price'], 2) ?>
              <img src="SAR.JPG" class="currency">
            </p>
            <div>
              <button class="qty-btn" onclick="updateQty(<?= $item['cart_id'] ?>, <?= $item['quantity'] - 1 ?>)">−</button>
              <input class="qty-box" type="number" min="1" value="<?= $item['quantity'] ?>" onchange="updateQty(<?= $item['cart_id'] ?>, this.value)">
              <button class="qty-btn" onclick="updateQty(<?= $item['cart_id'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
            </div>
          </div>
          <button class="remove-btn" onclick="removeItem(<?= $item['cart_id'] ?>)">Remove</button>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="summary-section">
    <h3>Order Summary</h3>
    <div class="total">
      <span>Total:</span>
      <span><?= number_format($total, 2) ?> <img src="SAR.JPG" class="currency"></span>
    </div>
    <?php if (!empty($cart_items)): ?>
      <button class="checkout-btn" onclick="document.getElementById('modal').style.display='flex'">Proceed to Checkout</button>
      <a href="appointmentManagement.php" class="orders-link">📦 Review Previous Orders</a>
    <?php endif; ?>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal">
  <div class="modal-content">
    <h3>Select Payment Method</h3>
    <select id="payMethod">
      <option value="card">Card</option>
      <option value="at_store">In Store</option>
      <option value="taby">Taby</option>
      <option value="apple_pay">Apple Pay</option>
    </select>
    <br><br>
    <button onclick="confirmPayment()">Confirm Payment</button>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast">✅ Order is complete. We will send you email for pick-up date</div>

<script>
function updateQty(cart_id, quantity) {
  if (quantity < 1) quantity = 1;
  fetch("", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: "action=update_quantity&cart_id=" + cart_id + "&quantity=" + quantity
  }).then(() => location.reload());
}

function removeItem(cart_id) {
  fetch("", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: "action=remove_item&cart_id=" + cart_id
  }).then(() => location.reload());
}

function confirmPayment() {
  const method = document.getElementById("payMethod").value;
  fetch("", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: "action=complete_payment&method=" + method + "&amount=<?= $total ?>"
  })
  .then(res => res.text())
  .then(resp => {
    if (resp.trim() === "success") {
      document.getElementById("modal").style.display = "none";
      showToast();
      setTimeout(() => location.reload(), 2500);
    } else if (resp.trim() === "not_enough_stock") {
      alert("❌ One or more products do not have enough stock.");
    }
  });
}

function showToast() {
  const toast = document.getElementById("toast");
  toast.style.display = "block";
  setTimeout(() => { toast.style.display = "none"; }, 2500);
}

window.onclick = function(e) {
  if (e.target === document.getElementById("modal")) {
    document.getElementById("modal").style.display = "none";
  }
}
</script>
<?php include 'footer.php'; ?>
</body>
</html>
