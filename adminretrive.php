<?php
include 'db.connect.php'; // Database connection

$sql = "SELECT * FROM Admin";
$result = $conn->query($sql);

$admins = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}

echo json_encode($admins);
$conn->close();
?>