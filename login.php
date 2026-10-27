<?php 
session_start();
include 'db_connect.php';

header("Content-Type: application/json");

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST["remember"]);
if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

// (User)
$stmt = $conn->prepare("SELECT user_id, name, password, profile_img FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $name, $hashedPassword, $profile_img);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        $_SESSION['id'] = $user_id;
        $_SESSION['name'] = $name;
        $_SESSION['profile_pic'] = $profile_img ?: 'profile.png';

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE user SET remember_token = ? WHERE user_id = ?");
            $update->bind_param("si", $hashedToken, $user_id);
            $update->execute();

            setcookie("remember_id", $user_id, time() + (86400 * 30), "/");
            setcookie("remember_token", $token, time() + (86400 * 30), "/");
        }

        echo json_encode(['status' => 'success', 'userType' => 'user']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
        exit;
    }
}
$stmt->close();

// (Admin) - كلمة مرور عادية
$stmt = $conn->prepare("SELECT admin_id, name, password, role FROM admin WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($admin_id, $name, $plainPassword, $role);
    $stmt->fetch();

    if ($password === $plainPassword) {
        $_SESSION['id'] = $admin_id;
        $_SESSION['name'] = $name;
        $_SESSION['admin_id'] = $admin_id;
        $userType = ($role === 'super') ? 'super_admin' : 'admin';
        echo json_encode(['status' => 'success', 'userType' => $userType]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
        exit;
    }
}
$stmt->close();

// إذا لم يُوجد في أي جدول
echo json_encode(['status' => 'error', 'message' => 'Email not registered.']);
?>

<!-- ===============================
   Authors:
   Team member  
   Renad Alkahtani
   log in with Remember Me  
   ===============================  -->
