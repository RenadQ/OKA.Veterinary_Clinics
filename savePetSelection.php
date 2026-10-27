<?php
session_start();

$pet_id = $_GET['pet_id'];


$_SESSION['selected_pet_id'] = $pet_id;


header('Location: DoctorList.php');
exit;
?>
