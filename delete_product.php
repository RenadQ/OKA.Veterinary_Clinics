<?php
include 'db_connect.php';
session_start();

// Make sure product ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Missing product ID";
    exit;
}

$product_id = intval($_GET['id']);


// Check if the product belongs to the logged-in admin
$check = $conn->prepare("SELECT product_id FROM product WHERE product_id = ? ");
$check->bind_param("ii", $product_id, $admin_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo "Product not found or unauthorized.";
    exit;
}
$check->close();

// Proceed to delete
$stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo "Deleted successfully";
} else {
    echo "Error deleting product";
}

$stmt->close();
?>
