<?php
include 'db_connect.php';
session_start();

$pet_id = $_GET['pet_id'];
$user_id = $_SESSION['id'] ?? null;

// تحقق إذا الحيوان عنده موعد
$checkAppointments = "SELECT * FROM Appointment WHERE my_pet_id = '$pet_id'";
$result = $conn->query($checkAppointments);

if ($result->num_rows > 0) {
    echo "Cannot delete pet because it has an appointment.";
    exit;
}

// لو ما عنده موعد احذفه
$sql = "DELETE FROM MyPet WHERE my_pet_id='$pet_id' AND user_id='$user_id'";

if($conn->query($sql)){
    echo "success";
}else{
    echo "Error deleting pet.";
}
?>
