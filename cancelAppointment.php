<?php
session_start();
include 'db_connect.php';

header('Content-Type: text/plain');

$user_id = $_SESSION['id'] ?? null;



if (isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']); // حماية أكثر

    
    $check = "SELECT * FROM Appointment 
              WHERE appointment_id = $appointment_id 
              AND user_id = $user_id 
              LIMIT 1";

    $result = $conn->query($check);

    if ($result->num_rows == 0) {
        echo "Unauthorized Cancel.";
        exit;
    }

    
    $update = "UPDATE Appointment 
               SET status = 'Cancelled' 
               WHERE appointment_id = $appointment_id";

    if ($conn->query($update) === TRUE) {
        echo "success";
    } else {
        echo "Error cancelling appointment: " . $conn->error;
    }

} else {
    echo "Invalid Request.";
}
?>
