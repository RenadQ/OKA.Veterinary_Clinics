<?php
session_start();
include 'db_connect.php';


$user_id = $_SESSION['id'] ?? null;
$sql = "SELECT * FROM MyPet WHERE user_id='$user_id' ORDER BY my_pet_id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No pets found']);
}
?>
