<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// --- Authentication Check ---
if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}
$user_id = $_SESSION['id'];

// --- Read Input ---
$method = $_POST['method'] ?? 'card';
$type = $_POST['type'] ?? null; // cart, appointment, service
$cardHolder = trim($_POST['card_holder'] ?? '');
$cardNumber = trim($_POST['card_number'] ?? '');
$expiry = trim($_POST['expiry'] ?? '');
$cvc = trim($_POST['cvc'] ?? '');

// --- Card Validation (only if method is card) ---
if ($method === 'card') {
    if (!preg_match('/^\d{16}$/', $cardNumber) || !preg_match('/^\d{2}\/\d{2}$/', $expiry) || !preg_match('/^\d{3}$/', $cvc)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid card details']);
        exit;
    }

    // Save card to DB (reuse if exists)
    $stmt = $conn->prepare("SELECT card_id FROM PaymentCard WHERE user_id=? AND card_number=?");
    $stmt->bind_param("is", $user_id, $cardNumber);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $card_id = $row['card_id'];
    } else {
        $expInt = intval(str_replace('/', '', $expiry));
        $stmt = $conn->prepare("INSERT INTO PaymentCard (user_id, card_number, cvc, month_year, card_holder_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiss", $user_id, $cardNumber, $cvc, $expInt, $cardHolder);
        $stmt->execute();
        $card_id = $stmt->insert_id;
    }
} else {
    $card_id = null;
}

// ====================== Offer Check =========================
$discount = 1.0;
$offerResult = $conn->query("SELECT discount_percentage FROM Offer WHERE applies_to='all' AND NOW() BETWEEN start_date AND end_date LIMIT 1");
if ($offerRow = $offerResult->fetch_assoc()) {
    $discount = 1 - ($offerRow['discount_percentage'] / 100);
}

// ====================== Type Handling =======================
if ($type === 'cart') {
    // Get cart items
    $cartQ = $conn->query("SELECT sc.cart_id, sc.product_id, sc.quantity, p.price, p.admin_id 
        FROM ShoppingCart sc 
        JOIN Product p ON sc.product_id = p.product_id 
        WHERE sc.user_id = $user_id");

    $total = 0;
    $orders = [];
    while ($row = $cartQ->fetch_assoc()) {
        $price = round($row['price'] * $discount, 2);
        $total += $price * $row['quantity'];
        $orders[] = $row + ['price_at_purchase' => $price];
    }

    foreach ($orders as $ord) {
        $stmt = $conn->prepare("INSERT INTO `Order` (user_id, admin_id, order_date, total_amount, status, cart_id, product_id, quantity, price_at_purchase) VALUES (?, ?, NOW(), ?, 'Confirmed', ?, ?, ?, ?)");
        $stmt->bind_param("iisiiii", $user_id, $ord['admin_id'], $total, $ord['cart_id'], $ord['product_id'], $ord['quantity'], $ord['price_at_purchase']);
        $stmt->execute();
    }

    // Save payment
    $stmt = $conn->prepare("INSERT INTO Payment (amount, status, method, cart_id, card_id) VALUES (?, 'Paid', ?, ?, ?)");
    $stmt->bind_param("dsii", $total, $method, $orders[0]['cart_id'], $card_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Cart payment processed.']);
    exit;

} elseif ($type === 'appointment') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    if (!$appointment_id) {
        echo json_encode(['status' => 'error', 'message' => 'Appointment ID is required']);
        exit;
    }
    $price = floatval($_POST['amount']);
    $discounted = round($price * $discount, 2);

    $stmt = $conn->prepare("INSERT INTO Payment (amount, status, method, appointment_id, card_id) VALUES (?, 'Paid', ?, ?, ?)");
    $stmt->bind_param("dsii", $discounted, $method, $appointment_id, $card_id);
    $stmt->execute();

    $conn->query("UPDATE Appointment SET status='Confirmed' WHERE appointment_id=$appointment_id");
    echo json_encode(['status' => 'success', 'message' => 'Appointment paid']);
    exit;

} elseif ($type === 'service') {
    $service_booking_id = $_POST['service_booking_id'] ?? null;
    if (!$service_booking_id) {
        echo json_encode(['status' => 'error', 'message' => 'Service booking ID is required']);
        exit;
    }
    $price = floatval($_POST['amount']);
    $discounted = round($price * $discount, 2);

    $stmt = $conn->prepare("INSERT INTO Payment (amount, status, method, service_booking_id, card_id) VALUES (?, 'Paid', ?, ?, ?)");
    $stmt->bind_param("dsii", $discounted, $method, $service_booking_id, $card_id);
    $stmt->execute();

    $conn->query("UPDATE ServiceBooking SET status_id=2 WHERE booking_id=$service_booking_id");
    echo json_encode(['status' => 'success', 'message' => 'Service booking paid']);
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid type provided']);
    exit;
}
?>
