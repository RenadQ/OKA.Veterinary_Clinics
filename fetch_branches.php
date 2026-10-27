<?php
header('Content-Type: application/json');

$branch = $_GET['branch'];
$status = $_GET['status'];

// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($status === 'available') {
    $sql = "SELECT name FROM products WHERE branch = '$branch' AND quantity > 0";
} else {
    $sql = "SELECT name FROM products WHERE branch = '$branch' AND quantity = 0";
}

$result = $conn->query($sql);
$items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row['name'];
    }
}

$conn->close();
echo json_encode($items);
?>