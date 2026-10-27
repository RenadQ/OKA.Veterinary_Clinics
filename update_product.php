<?php
include 'db_connect.php';

$product_id = $_POST['product_id'];
$name = $_POST['name'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$animal_type = $_POST['animal_type'];
$product_type = $_POST['product_type'];
$description = $_POST['description'];
$image_url = '';

$is_out_of_stock = $stock == 0 ? 1 : 0;

$stmt = $conn->prepare("UPDATE product SET name=?, price=?, stock=?, description=?, animal_type=?, product_type=?, is_out_of_stock=?, image_url=? WHERE product_id=?");
$stmt->bind_param("sdisssisi", $name, $price, $stock, $description, $animal_type, $product_type, $is_out_of_stock, $image_url, $product_id);

echo $stmt->execute() ? "success" : "error";
?>
