<?php 
session_start();
include 'db_connect.php';

header("Content-Type: text/plain");

//  1. استقبال وتحقيق أولي
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$rawPass  = trim($_POST['password'] ?? '');
$ads      = isset($_POST['receive_ads']) ? 1 : 0;

if (!$name || !$email || !$phone || !$rawPass) {
    exit("invalid_data");
}

if (!preg_match('/^\d{9,}$/', $phone) || strlen($rawPass) < 9) {
    exit("invalid_data");
}

$password = password_hash($rawPass, PASSWORD_DEFAULT);
$profile  = "profile.png";

//  2. تأكد من عدم تكرار الإيميل
$stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
if (!$stmt) exit("server_error");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    exit("email_exists");
}
$stmt->close();

//  3. تأكد من عدم تكرار رقم الهاتف
$stmt = $conn->prepare("SELECT user_id FROM user WHERE phone = ?");
if (!$stmt) exit("server_error");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    exit("phone_exists");
}
$stmt->close();

// 4. إدخال المستخدم
$stmt = $conn->prepare("INSERT INTO user (name, phone, email, password, receive_advertising, profile_img) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) exit("server_error");

$stmt->bind_param("ssssss", $name, $phone, $email, $password, $ads, $profile);

if ($stmt->execute()) {
    $_SESSION['id'] = $conn->insert_id;
    $_SESSION['name'] = $name;
    $_SESSION['profile_pic'] = $profile;

    //  الأهم: لا شيء قبله، لا تحذيرات، لا مسافات
    exit("success");
} else {
    exit("server_error");
}
?>
