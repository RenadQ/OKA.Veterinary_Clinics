<?php
include 'db_connect.php';

if (!isset($_GET['order_id'])) {
    echo "<p style='color:red;'>Order ID is missing.</p>";
    exit;
}

$order_id = intval($_GET['order_id']);

// جلب تفاصيل المنتجات المرتبطة بالطلب
$query = "
SELECT 
    o.product_id,
    o.quantity,
    o.price_at_purchase,
    p.name AS product_name,
    p.image_url
FROM `order` o
JOIN product p ON o.product_id = p.product_id
WHERE o.order_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No products found for this order.</p>";
    exit;
}

echo "<div style='display:flex; flex-direction:column; gap:15px;'>";

while ($row = $result->fetch_assoc()) {
    echo "
    <div style='border:1px solid #ccc; padding:15px; border-radius:8px; display:flex; align-items:center; gap:15px;'>
        <img src='{$row['image_url']}' alt='{$row['product_name']}' style='width:80px; height:80px; object-fit:cover; border-radius:8px;'>
        <div>
            <h4 style='margin:0; color:#003366;'>{$row['product_name']}</h4>
            <p style='margin:5px 0;'>Quantity: <strong>{$row['quantity']}</strong></p>
            <p style='margin:0;'>Price per unit: <strong>{$row['price_at_purchase']} SAR</strong></p>
        </div>
    </div>
    ";
}

echo "</div>";

$stmt->close();
?>
