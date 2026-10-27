<?php
include 'db_connect.php';
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['userId'];

$sql = "DELETE FROM User WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$result = $stmt->execute();

$response = array("success" => $result);
echo json_encode($response);
$conn->close();
?>