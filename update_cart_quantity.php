<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';  // Include your DB connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Get the data from the POST request
if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    // Validate the quantity (ensure it's a positive integer)
    if (filter_var($quantity, FILTER_VALIDATE_INT) === false || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
        exit;
    }

    // Prepare the SQL query to update the quantity in the ShoppingCart
    $stmt = $conn->prepare("UPDATE ShoppingCart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
    } else {
        // Return detailed error message for debugging
        echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity. ' . $stmt->error]);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>
