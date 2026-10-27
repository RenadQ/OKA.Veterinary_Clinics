<?php
include 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user data from POST request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Update user information in the database
    $sql = "UPDATE Users SET name=?, email=?, phone=?, password=? WHERE user_id=?"; // Adjust the query accordingly
    $stmt = $conn->prepare($sql);
    
    // Assuming user_id is passed in as a hidden field or session
    $user_id = 1; // Replace with actual user ID logic
    $stmt->bind_param("ssssi", $name, $email, $phone, $password, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Account updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update account."]);
    }

    $stmt->close();
    $conn->close();
}
?>