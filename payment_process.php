<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');
$user_id = $_SESSION['id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

$type = $_POST['type'] ?? ''; // cart | appointment | service
$method = $_POST['method'] ?? '';
$amount = $_POST['amount'] ?? 0;

// Card info
$card_number = $_POST['card_number'] ?? '';
$cvc = $_POST['cvc'] ?? '';
$expiry = $_POST['expiry'] ?? '';
$card_holder = $_POST['card_holder'] ?? '';

// INSERT CARD IF METHOD = CARD
$card_id = null;
if ($method === 'card') {
    $stmt = $conn->prepare("INSERT INTO PaymentCard (user_id, card_number, cvc, month_year, card_holder_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $user_id, $card_number, $cvc, $expiry, $card_holder);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Card failed']);
        exit;
    }
    $card_id = $stmt->insert_id;
}

// HANDLE BASED ON TYPE
if ($type === 'cart') {
    // 1. Get all items from cart
    $cart_sql = "SELECT * FROM ShoppingCart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    while ($item = $cart_result->fetch_assoc()) {
        // Insert into Order table
        $insert_order = $conn->prepare("INSERT INTO `Order` (user_id, admin_id, order_date, pickup_date_time, total_amount, status, cart_id, product_id, quantity, price_at_purchase) 
            VALUES (?, NULL, NOW(), NULL, ?, 'Pending', ?, ?, ?, ?)");
        $insert_order->bind_param("idiiiid", $user_id, $amount, $item['cart_id'], $item['product_id'], $item['quantity'], $amount);
        $insert_order->execute();
    }

    // Insert Payment
    $insert_payment = $conn->prepare("INSERT INTO Payment (amount, status, method, cart_id, card_id) VALUES (?, 'Success', ?, ?, ?)");
    $insert_payment->bind_param("dsii", $amount, $method, $item['cart_id'], $card_id);
    $insert_payment->execute();
    echo json_encode(['status' => 'success', 'message' => 'Order placed']);
    exit;

} elseif ($type === 'appointment') {
    $doctor_id = $_POST['doctor_id'] ?? 1;
    $pet_id = $_POST['pet_id'] ?? 1;
    $price = $_POST['price'] ?? 100;

    // Insert into Appointment
    $stmt = $conn->prepare("INSERT INTO Appointment (doctor_id, user_id, pet_id, date_time, status, price, admin_id) VALUES (?, ?, ?, NOW(), 'Pending', ?, NULL)");
    $stmt->bind_param("iiid", $doctor_id, $user_id, $pet_id, $price);
    $stmt->execute();
    $appointment_id = $stmt->insert_id;

    // Insert Payment
    $stmt = $conn->prepare("INSERT INTO Payment (amount, status, method, appointment_id, card_id) VALUES (?, 'Success', ?, ?, ?)");
    $stmt->bind_param("dsii", $price, $method, $appointment_id, $card_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Appointment Booked']);
    exit;

} elseif ($type === 'service') {
    $service_id = $_POST['service_id'] ?? 1;
    $date = $_POST['appointment_date'] ?? date("Y-m-d H:i:s");

    // Insert Booking
    $stmt = $conn->prepare("INSERT INTO ServiceBooking (service_id, user_id, appointment_date, status_id) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iis", $service_id, $user_id, $date);
    $stmt->execute();
    $booking_id = $stmt->insert_id;

    // Insert Payment
    $stmt = $conn->prepare("INSERT INTO Payment (amount, status, method, service_booking_id, card_id) VALUES (?, 'Success', ?, ?, ?)");
    $stmt->bind_param("dsii", $amount, $method, $booking_id, $card_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Service Booked']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid payment type']);
?>
