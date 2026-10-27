<?php
include 'db_connect.php';

$userId = $_GET['user_id'];

// استعلام لجلب الطلبات
$orderSql = "SELECT * FROM `Order` WHERE user_id = $userId";
$orderResult = $conn->query($orderSql);
$orders = [];
if ($orderResult->num_rows > 0) {
    while($order = $orderResult->fetch_assoc()) {
        $orders[] = $order;
    }
}

// استعلام لجلب حجوزات الخدمات
$serviceSql = "SELECT * FROM ServiceBooking WHERE user_id = $userId";
$serviceResult = $conn->query($serviceSql);
$services = [];
if ($serviceResult->num_rows > 0) {
    while($service = $serviceResult->fetch_assoc()) {
        $services[] = $service;
    }
}

$response = [
    'orders' => $orders,
    'services' => $services
];

echo json_encode($response);
$conn->close();
?>