<?php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['userId'];
$email = $data['email'];
$name = $data['name'];
$phone = $data['phone'];

$sql = "UPDATE User SET email = ?, name = ?, phone = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $email, $name, $phone, $userId);
$result = $stmt->execute();

$response = array("success" => $result);
echo json_encode($response);
$conn->close();
?>