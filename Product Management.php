<?php
include 'db_connect.php';
session_start();
$adminName = $_SESSION['name'] ?? 'admin';


// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $animal_type = $_POST['animal_type'];
    $product_type = $_POST['product_type'];
    $description = trim($_POST['description']);
    $is_out_of_stock = $stock <= 0 ? 1 : 0;

    // Handle image
    $image_url = '';
    if (!empty($_FILES['image']['name'])) {
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target = 'uploads/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_url = $target;
        }
    }

    if ($product_id) {
        // Update
        $stmt = $image_url ?
            $conn->prepare("UPDATE product SET name=?, price=?, stock=?, animal_type=?, product_type=?, description=?, is_out_of_stock=?, image_url=? WHERE product_id=?") :
            $conn->prepare("UPDATE product SET name=?, price=?, stock=?, animal_type=?, product_type=?, description=?, is_out_of_stock=? WHERE product_id=?");

        if ($image_url) {
            $stmt->bind_param("sdisssssi", $name, $price, $stock, $animal_type, $product_type, $description, $is_out_of_stock, $image_url, $product_id);
        } else {
            $stmt->bind_param("sdissssi", $name, $price, $stock, $animal_type, $product_type, $description, $is_out_of_stock, $product_id);
        }
        $stmt->execute();
        header("Location: Product Management.php?message=Product updated successfully!&type=success");
        exit;
    } else {
        // Add
        $stmt = $conn->prepare("INSERT INTO product (admin_id, name, price, stock, animal_type, product_type, description, is_out_of_stock, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdisssis", $admin_id, $name, $price, $stock, $animal_type, $product_type, $description, $is_out_of_stock, $image_url);
        $stmt->execute();
        header("Location: Product Management.php?message=Product added successfully!&type=success");
        exit;
    }
}

// Handle delete with error handling
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn->query("DELETE FROM product WHERE product_id = $delete_id");
        header("Location: Product Management.php?message=Product deleted successfully!&type=success");
    } catch (mysqli_sql_exception $e) {
        $errorMessage = "Cannot delete this product because it is linked to other records.";
        header("Location: Product Management.php?message=" . urlencode($errorMessage) . "&type=error");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Product Management</title>
  <link rel="stylesheet" href="Admin_Styles.css">
  <link rel="stylesheet" href="Product_M_styles.css">
  <style>
    /* .out-stock { color: red; font-weight: bold; }
    .in-stock { color: green; font-weight: bold; } */

    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      
    }

    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      min-width: 300px;
    }

    .modal-content button {
      padding: 8px 16px;
      margin-top: 10px;
      border: none;
      border-radius: 6px;
      color: white;
      background-color: #28a745;
      cursor: pointer;
    }

    .modal-content .close {
      float: right;
      font-size: 24px;
      cursor: pointer;
    }
    
  </style>
</head>
<body>

<?php include 'admin_Header_layout.php'; ?>

<div class="container">
  <h2>Product Management</h2>

  <form method="GET" style="display: flex; justify-content: space-between; gap: 10px; margin-bottom: 20px;">
    <select name="filter" onchange="this.form.submit()">
      <option value="all" <?= ($_GET['filter'] ?? '') == 'all' ? 'selected' : '' ?>>All</option>
      <option value="in" <?= ($_GET['filter'] ?? '') == 'in' ? 'selected' : '' ?>>In Stock</option>
      <option value="out" <?= ($_GET['filter'] ?? '') == 'out' ? 'selected' : '' ?>>Out of Stock</option>
    </select>
    <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding: 8px; width: 250px;">
    <button class="custom-btn" type="submit">Search</button>
    <button class="custom-btn" type="button" onclick="openModal()">Add Product</button>
  </form>

  <table>
    <thead>
      <tr><th>#</th><th>Name</th><th>Price</th><th>Stock</th><th>Animal</th><th>Type</th><th>Status</th><th>Image</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php
      $filter = $_GET['filter'] ?? 'all';
      $search = $_GET['search'] ?? '';
      $where = [];

      if ($filter == 'in') $where[] = "is_out_of_stock = 0";
      elseif ($filter == 'out') $where[] = "is_out_of_stock = 1";

      if ($search) $where[] = "name LIKE '%" . $conn->real_escape_string($search) . "%'";

      $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

      $result = $conn->query("SELECT * FROM product $whereSQL ORDER BY product_id DESC LIMIT 100");
      $i = 1;
      while ($row = $result->fetch_assoc()):
    ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['price'] ?></td>
        <td><?= $row['stock'] ?></td>
        <td><?= $row['animal_type'] ?></td>
        <td><?= $row['product_type'] ?></td>
        <td><span class="<?= $row['is_out_of_stock'] ? 'out-stock' : 'in-stock' ?>"><?= $row['is_out_of_stock'] ? 'Out of Stock' : 'In Stock' ?></span></td>
        <td><img src="<?= $row['image_url'] ?>" width="50"></td>
        <td>
          <button class="edit-btn" onclick='editProduct(<?= json_encode($row) ?>)'><i class="fas fa-edit"></i> Edit</button>
          <a href="?delete=<?= $row['product_id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash-alt"></i> Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal" id="productModal">
  <div class="modal-content">
    <span class="close" onclick="closeProductModal()">&times;</span>
    <h3 id="modal-title">Add Product</h3>
    <form method="POST" enctype="multipart/form-data" id="product-form">
      <input type="hidden" name="product_id" id="product_id">
      <input type="text" name="name" id="name" placeholder="Name" required>
      <input type="number" step="0.01" name="price" id="price" placeholder="Price" required>
      <input type="number" name="stock" id="stock" placeholder="Stock" required>
      <select name="animal_type" id="animal_type" required>
        <option value="">Select Animal</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Bird">Bird</option>
        <option value="Small animal">Small animal</option>
      </select>
      <select name="product_type" id="product_type" required>
        <option value="">Select Type</option>
        <option value="Food">Food</option>
        <option value="Toy">Toy</option>
        <option value="Supplies">Supplies</option>
      </select>
      <textarea name="description" id="description" placeholder="Description" required></textarea>
      <input type="file" name="image" id="image">
      <button class="custom-btn" type="submit">Save</button>
    </form>
  </div>
</div>

<!-- Message Modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeMessageModal()">&times;</span>
    <p id="modal-message"></p>
    <button onclick="closeMessageModal()">OK</button>
  </div>
</div>

<script>
function openModal() {
  document.getElementById("modal-title").innerText = "Add Product";
  document.getElementById("product-form").reset();
  document.getElementById("product_id").value = '';
  document.getElementById("productModal").style.display = "flex";
}

function closeProductModal() {
  document.getElementById("productModal").style.display = "none";
}

function editProduct(product) {
  document.getElementById("modal-title").innerText = "Edit Product";
  document.getElementById("product_id").value = product.product_id;
  document.getElementById("name").value = product.name;
  document.getElementById("price").value = product.price;
  document.getElementById("stock").value = product.stock;
  document.getElementById("animal_type").value = product.animal_type;
  document.getElementById("product_type").value = product.product_type;
  document.getElementById("description").value = product.description;
  document.getElementById("productModal").style.display = "flex";
}

function showModal(message, isError = false) {
  const modal = document.getElementById("myModal");
  const modalMessage = document.getElementById("modal-message");

  modalMessage.textContent = message;
  modal.querySelector("button").style.backgroundColor = isError ? "#dc3545" : "#28a745";
  modal.style.display = "flex";
}

function closeMessageModal() {
  document.getElementById("myModal").style.display = "none";
}

window.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const message = urlParams.get('message');
  const type = urlParams.get('type');
  if (message) {
    const isError = (type !== 'success');
    showModal(decodeURIComponent(message), isError);
    window.history.replaceState(null, '', window.location.pathname);
  }
});
</script>

</body>
</html>
