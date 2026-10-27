<?php
session_start();
include 'db_connect.php';



$user_id = $_SESSION['id'] ?? null;

$name = $_POST['name'] ?? '';
$age = $_POST['age'] ?? '';
$type = $_POST['type'] ?? '';
$breed = $_POST['breed'] ?? '';
$gender = $_POST['gender'] ?? '';

if(empty($name) || empty($age) || empty($type) || empty($breed) || empty($gender)){
    echo "Please fill all fields.";
    exit;
}

$sql = "INSERT INTO MyPet (user_id, name, age, type, breed, gender) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isisss", $user_id, $name, $age, $type, $breed, $gender);

if($stmt->execute()){
    echo "success";
} else {
    echo "Error: " . $stmt->error;
}
?>
