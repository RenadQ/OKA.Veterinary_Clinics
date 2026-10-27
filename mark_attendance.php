<?php
session_start();
include 'db_connect.php';



$appointment_id = $_GET['id'];
$new_status = $_GET['status'];

$conn->query("UPDATE Appointment SET status='$new_status' WHERE appointment_id=$appointment_id");

header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?>
