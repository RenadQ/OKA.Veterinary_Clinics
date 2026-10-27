<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_id = $_POST['pet_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? '';
    $user_id = $_SESSION['id'] ?? null;

    if(empty($pet_id) || empty($name) || empty($age)){
        echo "Please fill all fields!";
        exit;
    }

    $sql = "UPDATE MyPet SET name='$name', age='$age' WHERE my_pet_id='$pet_id' AND user_id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
