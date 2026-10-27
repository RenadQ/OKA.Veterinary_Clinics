<?php
session_start();
include 'db_connect.php';


$admin_id = $_SESSION['admin_id'] ?? null;

$update_message = "";

// إذا تم إرسال النموذج (POST) نقوم بالتحديث
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = $_POST['phone'];
   

    $query = "UPDATE admin SET phone = ?, password = ? WHERE admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $phone, $password, $admin_id);

    if ($stmt->execute()) {
        $update_message = "Profile updated successfully.";
    } else {
        $update_message = "Failed to update profile.";
    }

    $stmt->close();
}

// جلب البيانات المحدثة من قاعدة البيانات
$query = "SELECT name, email, phone FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="admin_profile_css.css">
    
</head>
<body>
    <div id="content">
        <h2>Admin Profile</h2>

        <?php if (!empty($update_message)): ?>
            <div class="message"><?= htmlspecialchars($update_message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Name (not editable):</label>
                <input type="text" value="<?= htmlspecialchars($name) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Email (not editable):</label>
                <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
            </div>
            
            <button type="submit">Update Info</button>
        </form>

        <div class="back-home">
            <button onclick="window.location.href='Admin_welcomepage.php'">Back to Home</button>
        </div>
    </div>
</body>
</html>
