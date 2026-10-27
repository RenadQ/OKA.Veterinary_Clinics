<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data['email']);
    $new_password = password_hash(trim($data['password']), PASSWORD_BCRYPT);

    // Update the password in the user table
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        echo "success"; // Indicate success
    } else {
        echo "Error updating password.";
    }
}
?>
<!-- ===============================
   Author:
   
   Renad Alkahtani
   
   ===============================  -->