<?php
include 'db_connect.php';
session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    exit("User not logged in");
}

if (!isset($_POST['action'], $_POST['product_id'])) {
    exit("Missing data");
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity'] ?? 1);

if ($_POST['action'] === 'add') {
    // Check if stock is available
    $stmt = $conn->prepare("SELECT stock FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    if ($stock < $quantity) {
        exit("Out of stock");
    }

    // Add or update cart
    $stmt = $conn->prepare("INSERT INTO shoppingcart (user_id, product_id, quantity, created_at)
                            VALUES (?, ?, ?, NOW())
                            ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
    if ($stmt->execute()) {
        echo "Added";
    } else {
        echo "Error adding to cart";
    }
    $stmt->close();
}
elseif ($_POST['action'] === 'remove') {
    $stmt = $conn->prepare("DELETE FROM shoppingcart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        echo "Removed";
    } else {
        echo "Error removing";
    }
    $stmt->close();
}
