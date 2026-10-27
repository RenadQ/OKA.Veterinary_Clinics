<?php
session_start();
include 'db_connect.php';

$admin_id = $_SESSION['id'] ?? 0;
if (!$admin_id) {
    echo "Unauthorized";
    exit;
}

// ===== Handle Deletion =====
if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    $id = intval($_POST['product_id']);
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ? AND admin_id = ?");
    $stmt->bind_param("ii", $id, $admin_id);
    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Failed to delete product.";
    }
    $stmt->close();
    exit;
}

// ===== Handle Add/Edit =====
$name = trim($_POST['name'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$animal_type = $_POST['animal_type'] ?? '';
$product_type = $_POST['product_type'] ?? '';
$description = trim($_POST['description'] ?? '');
$image_url = '';
$is_out_of_stock = $stock <= 0 ? 1 : 0;

if (!$name || !$price || !$animal_type || !$product_type || !$description) {
    echo "Please fill all fields";
    exit;
}

// Handle Image Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $img_name = uniqid('prod_') . '_' . basename($_FILES['image']['name']);
    $img_path = 'uploads/' . $img_name;
    move_uploaded_file($_FILES['image']['tmp_name'], $img_path);
    $image_url = $img_path;
}

// Check if we're updating
$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id) {
    // Update existing
    if ($image_url) {
        $stmt = $conn->prepare("UPDATE product SET name=?, price=?, stock=?, is_out_of_stock=?, animal_type=?, product_type=?, description=?, image_url=? WHERE product_id=? AND admin_id=?");
        $stmt->bind_param("sdisssssii", $name, $price, $stock, $is_out_of_stock, $animal_type, $product_type, $description, $image_url, $product_id, $admin_id);
    } else {
        $stmt = $conn->prepare("UPDATE product SET name=?, price=?, stock=?, is_out_of_stock=?, animal_type=?, product_type=?, description=? WHERE product_id=? AND admin_id=?");
        $stmt->bind_param("sdissssii", $name, $price, $stock, $is_out_of_stock, $animal_type, $product_type, $description, $product_id, $admin_id);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Update failed.";
    }
    $stmt->close();
} else {
    // Insert new
    $stmt = $conn->prepare("INSERT INTO product (admin_id, name, price, stock, is_out_of_stock, animal_type, product_type, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdisssss", $admin_id, $name, $price, $stock, $is_out_of_stock, $animal_type, $product_type, $description, $image_url);
    if ($stmt->execute()) {
        echo "Product added successfully.";
    } else {
        echo "Insert failed.";
    }
    $stmt->close();
}
?>
