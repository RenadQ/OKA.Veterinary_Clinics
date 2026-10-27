<?php
include 'db.connect.php'; // Database connection

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$role = $data['role'];
$adminId = $data['adminId'];

$sql = "INSERT INTO Admin (name, email, phone, role, adminId) VALUES ('$name', '$email', '$phone', '$role', '$adminId')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>