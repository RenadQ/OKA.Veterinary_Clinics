<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    // استلام البيانات
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // تشفير كلمة المرور
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // تحديث البيانات
    $query = "UPDATE user SET name = ?, email = ?, phone = ?, password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $email, $phone, $hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Account updated successfully!');
            window.location.href = 'Adminprofile.php';
        </script>";
    } else {
        echo "<script>
            alert('Failed to update account.');
            window.history.back();
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
