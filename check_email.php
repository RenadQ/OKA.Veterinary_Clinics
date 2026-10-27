<?php 
include 'db_connect.php';

header("Content-Type: text/plain");

$data = json_decode(file_get_contents("php://input"), true);
$email = strtolower(trim($data['email'] ?? ''));

if (!$email) exit("missing_email");

$stmt = $conn->prepare("SELECT user_id FROM user WHERE LOWER(email) = ?");
if (!$stmt) exit("server_error");

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    exit("exists");
} else {
    exit("not_found");
}


// <!-- ===============================
//    Authors:
//    Team member  
//    Renad Alkahtani
//    ===============================  -->