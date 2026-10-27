<?php 
session_start();
include 'db_connect.php';

header("Content-Type: text/plain");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['email']) || empty($data['password'])) {
        exit("missing_data");
    }

    $email = strtolower(trim($data['email']));
    $new_password = password_hash(trim($data['password']), PASSWORD_BCRYPT);

    // التحقق من وجود المستخدم
    $checkStmt = $conn->prepare("SELECT user_id FROM user WHERE LOWER(email) = ?");
    if (!$checkStmt) {
        exit("prepare_error_check");
    }

    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        $checkStmt->close();
        $conn->close();
        exit("not_found_or_not_user");
    }
    $checkStmt->close();

    // التحديث
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE LOWER(email) = ?");
    if (!$stmt) {
        exit("prepare_error_update");
    }

    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        exit("success");
    } else {
        exit("update_failed");
    }
}
exit("invalid_request");
