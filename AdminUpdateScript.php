<?php
session_start();
include 'db_connect.php';  // Connect to the database

if (!$conn) {
    echo "Database connection failed";
    exit;
}

// Handle admin editing (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_admin'])) {
    $admin_id = intval($_POST['admin_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $phone = trim($_POST['phone']);

    $sql = "update Admin set name='$name',  email = '$email', role = '$role', phone = '$phone' where admin_id = '$admin_id'";

if ($conn->query($sql) === TRUE) {
            header("Location: Adminmanagement.php"); // Redirect after update

    	echo "Records updated: ";

} else {
	echo "Error: ".$sql."<br>".$conn->error;
}

    $conn->close();

    header("Location: Adminmanagement.php"); // Redirect after update
    exit();
}
?>