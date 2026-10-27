<?php
include 'db_connect.php';

// Fetch admins from the database
$sql = "SELECT name, email, phone, role, education, last_access, privileges FROM Admin";
$result = $conn->query($sql);

$admins = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}

$conn->close();
echo json_encode($admins);
?>