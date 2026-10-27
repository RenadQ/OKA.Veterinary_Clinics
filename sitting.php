<?php
session_start();
include_once 'db_connect.php';

$userId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
$errors = [];
$success = '';
$username = $useremail = $userphone = '';
$profileImg = 'default.png';

// حذف الحساب
if (isset($_POST['delete_account'])) {
  $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
  $stmt->bind_param("i", $userId);
  if ($stmt->execute()) {
    session_unset();
    session_destroy();
    header("Location: goodbye.php"); // صفحة وداعية بعد حذف الحساب
    exit;
  } else {
    $errors[] = "Failed to delete account.";
  }
  $stmt->close();
}

// عند الإرسال
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_account'])) {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $img = $_FILES['profile_img'] ?? null;

  if (!$name || !$email || !$phone) $errors[] = "All fields are required.";
  if (!preg_match('/^\d{9}$/', $phone)) $errors[] = "Phone must be exactly 9 digits.";
  if (!empty($password) && strlen($password) < 9) $errors[] = "Password must be at least 9 characters.";

  $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ? AND user_id != ?");
  $stmt->bind_param("si", $email, $userId);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) $errors[] = "Email is already in use.";
  $stmt->close();

  $filename = null;
  if ($img && $img['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
    $filename = "user_" . $userId . "_" . time() . "." . $ext;
    $folder = "profile_pics/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $path = $folder . $filename;
    if (!move_uploaded_file($img['tmp_name'], $path)) {
      $errors[] = "Failed to upload profile image.";
    }
  }

  if (empty($errors)) {
    $query = "UPDATE user SET name = ?, email = ?, phone = ?";
    $params = [$name, $email, $phone];
    $types = "sss";

    if (!empty($password)) {
      $query .= ", password = ?";
      $params[] = password_hash($password, PASSWORD_DEFAULT);
      $types .= "s";
    }

    if ($filename) {
      $query .= ", profile_img = ?";
      $params[] = $filename;
      $types .= "s";
    }

    $query .= " WHERE user_id = ?";
    $params[] = $userId;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
      $success = "✅ Profile updated successfully.";
    } else {
      $errors[] = "Error updating profile.";
    }
    $stmt->close();
  }
}

$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();
  $username = $row['name'];
  $useremail = $row['email'];
  $userphone = $row['phone'];
  $profileImg = $row['profile_img'] ?: 'default.png';
}
$stmt->close();
$isLoggedIn = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .main-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 6px; font-weight: bold; }
    input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
      width: 100%; padding: 10px; box-sizing: border-box; border-radius: 6px; border: 1px solid #ccc;
    }
    .submit-btn {
      width: 100%; background: #007bff; color: white; border: none; padding: 12px;
      border-radius: 6px; font-size: 16px; cursor: pointer; margin-top: 15px;
    }
    .submit-btn:hover { background: #0056b3; }
    .delete-btn {
      width: 100%; background: #dc3545; color: white; border: none; padding: 12px;
      border-radius: 6px; font-size: 16px; cursor: pointer; margin-top: 10px;
    }
    .delete-btn:hover { background: #c82333; }
    .back-home { margin-top: 30px; text-align: center; }
    .back-home button {
      background: #28a745; color: white; border: none;
      padding: 12px 25px; border-radius: 6px; font-size: 16px; cursor: pointer;
    }
    .back-home button:hover { background: #1e7e34; }
    .message { margin-bottom: 20px; padding: 10px; border-radius: 5px; }
    .message.error { background: #ffe0e0; color: #b30000; }
    .message.success { background: #e0ffe0; color: #006600; }
    .profile-preview { text-align: center; margin-bottom: 20px; }
    .profile-preview img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; }
  </style>
</head>
<body>
  <div class="main-container">
    <h2>Edit Profile</h2>

    <?php if (!empty($errors)): ?>
      <div class="message error">
        <?php foreach ($errors as $error) echo "<p>❌ $error</p>"; ?>
      </div>
    <?php elseif ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
    <div class="profile-preview">
      <img src="profile_pics/<?= htmlspecialchars($profileImg) ?>" alt="Profile Image">
    </div>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="user-name">Name</label>
        <input type="text" id="user-name" name="name" value="<?= htmlspecialchars($username) ?>" required>
      </div>

      <div class="form-group">
        <label for="user-email">Email</label>
        <input type="email" id="user-email" name="email" value="<?= htmlspecialchars($useremail) ?>" required>
      </div>

      <div class="form-group">
        <label for="user-phone">Phone Number</label>
        <input type="text" id="user-phone" name="phone" value="<?= htmlspecialchars($userphone) ?>" required>
      </div>

      <div class="form-group">
        <label for="user-password">Password</label>
        <input type="password" id="user-password" name="password" placeholder="Enter new password">
      </div>

      <div class="form-group">
        <label for="user-image">Profile Image</label>
        <input type="file" name="profile_img" id="user-image" accept="image/*">
      </div>

      <button type="submit" class="submit-btn">Update Account</button>
    </form>

    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
      <input type="hidden" name="delete_account" value="1">
      <button type="submit" class="delete-btn">Delete Account</button>
    </form>
    <?php endif; ?>

    <div class="back-home">
      <button onclick="window.location.href='homepage.php'">Back to Home</button>
    </div>

    <div class="additional-info">
      <div class="info-section">
        <h3>Policy and Terms</h3>
        <p>Read our <a href="privecyandterms.php">policy and terms</a> to understand your rights.</p>
      </div>
      <div class="info-section">
        <h3>Follow Us on Instagram</h3>
        <p><a href="https://www.instagram.com/okavetenary" target="_blank">Instagram - @okavetenary</a></p>
      </div>
      <div class="info-section">
        <h3>Contact Us</h3>
        <p>Message us on <a href="https://wa.me/1234567890" target="_blank">WhatsApp</a>.</p>
      </div>
    </div>
  </div>
</body>
</html>
