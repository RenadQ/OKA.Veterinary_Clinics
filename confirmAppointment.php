<?php
session_start();
include 'db_connect.php';
$user_id = $_SESSION['id'] ?? null;

$doctor_id = $_POST['doctor_id'] ?? null;
$date_time = $_POST['date_time'] ?? null;
$pet_id = $_POST['pet_id'] ?? null;

// التحقق من وجود بيانات أساسية
if (empty($pet_id)) {
    echo 'no_pet';
    exit;
}
if (empty($doctor_id) || empty($date_time)) {
    echo 'missing_data';
    exit;
}


$check_sql = "SELECT * FROM Appointment 
              WHERE doctor_id = ? 
              AND date_time = ? 
              AND status != 'Cancelled'";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("is", $doctor_id, $date_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo 'booked';
    exit;
}
$stmt->close();

// إدخال الحجز الجديد
$insert_sql = "INSERT INTO appointment (user_id, doctor_id, my_pet_id, date_time, status) 
               VALUES (?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("iiis", $user_id, $doctor_id, $pet_id, $date_time);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
$stmt->close();
$conn->close();
?>
