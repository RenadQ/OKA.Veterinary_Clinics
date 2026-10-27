<?php
include 'db_connect.php';
session_start();

$admin_id = $_SESSION['id'] ?? 0;
$page = intval($_GET['page'] ?? 1);
$filter = $_GET['filter'] ?? 'all';

$limit = 50;
$offset = ($page - 1) * $limit;

// === Filter logic ===
$condition = "WHERE admin_id = ?";
$params = [$admin_id];
$types = "i";

if ($filter === "in") {
    $condition .= " AND is_out_of_stock = 0";
} elseif ($filter === "out") {
    $condition .= " AND is_out_of_stock = 1";
}

// === Total count ===
$totalStmt = $conn->prepare("SELECT COUNT(*) FROM product $condition");
$totalStmt->bind_param($types, ...$params);
$totalStmt->execute();
$totalStmt->bind_result($total);
$totalStmt->fetch();
$totalStmt->close();

// === Get paginated data ===
$sql = "SELECT product_id, name, price, stock, animal_type, product_type, description, is_out_of_stock, image_url 
        FROM product $condition ORDER BY product_id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode([
    "total" => $total,
    "products" => $products
]);
?>
