<?php
session_start();
include 'db_connect.php';

// تسجيل الخروج
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: signin.php");
    exit;
}

$admin_id = $_SESSION['admin_id'] ?? null;
$adminName = $_SESSION['name'] ?? 'admin';
$update_message = "";

// تحديث رقم الجوال
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = $_POST['phone'];
    $stmt = $conn->prepare("UPDATE admin SET phone = ? WHERE admin_id = ?");
    $stmt->bind_param("si", $phone, $admin_id);
    $update_message = $stmt->execute() ? "Phone updated successfully." : "Failed to update phone.";
    $stmt->close();
}

// جلب البيانات
$stmt = $conn->prepare("SELECT name, email, phone FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Super Admin Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0; display: flex; justify-content: center; align-items: center;
      font-family: Arial, sans-serif; background: #f2f2f2; height: 100vh;
    }
    #content {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 400px;
    }
    h2 { text-align: center; color: #293055; }
    .form-group { margin-bottom: 15px; }
    label { font-weight: bold; color: #293055; display: block; margin-bottom: 5px; }
    input[type="text"], input[type="email"] {
      width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;
    }
    button {
      padding: 10px; background: #ffa500; color: white;
      border: none; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 10px;
    }
    button:hover { background: #ff8c00; }
    .actions { display: flex; justify-content: space-between; margin-top: 15px; }
    .actions button { width: 48%; background-color: #293055; }
    .actions button:hover { background-color: #1e2431; }
    .message { text-align: center; color: green; margin-bottom: 10px; }
  </style>
</head>
<body>
  <div id="content">
    <h2>Super Admin Account</h2>

    <?php if (!empty($update_message)): ?>
      <div class="message"><?= htmlspecialchars($update_message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Name</label>
        <input type="text" value="<?= htmlspecialchars($name) ?>" readonly>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
      </div>
      <button type="submit">Update Phone</button>
    </form>

    <div class="actions">
      <button onclick="location.href='superadminhomepage1.php'">Back</button>
      <button onclick="location.href='?logout=1'">Logout</button>
    </div>
  </div>
</body>
</html>
