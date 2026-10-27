<?php
session_start();
include 'db_connect.php';  // Connect to the database

echo "Database connection failed";

if (!$conn) {
    echo "Database connection failed";
    exit;
}

if(isset($_GET['id']))
{
     $sql = "DELETE FROM Admin WHERE admin_id=".$_GET['id'];

     
if ($conn->query($sql) === TRUE) {
            header("Location: Adminmanagement.php"); // Redirect after update


} else {
	echo "Error: ".$sql."<br>".$conn->error;
}

    $conn->close();

    header("Location: Adminmanagement.php"); // Redirect after update
    exit();
}
?>