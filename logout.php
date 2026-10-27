<?php
session_start();
include 'db_connect.php';

// حذف التوكن من قاعدة البيانات إذا المستخدم مسجّل دخول
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    $stmt = $conn->prepare("UPDATE user SET remember_token = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// تدمير الجلسة
session_destroy();

// حذف الكوكيز
setcookie("remember_id", "", time() - 3600, "/");
setcookie("remember_token", "", time() - 3600, "/");

header("Location: homepage.php");
exit();
?>
