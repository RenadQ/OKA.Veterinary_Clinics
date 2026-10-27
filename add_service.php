<!-- 
===============================
  Author:
  Team member
===============================
-->

<?php
session_start();


include 'db_connect.php';

// معالجة الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $branch_id = intval($_POST['branch_id']);
    $admin_id = $_SESSION['id'];

    // التعامل مع الصورة
    $image_name = null;

    // إذا الادمن لم يختار صورة 
    if (!isset($_POST['use_default']) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $original_name = str_replace(' ', '', basename($_FILES['image']['name']));
        $upload_path = 'images/' . $original_name;
    
        if (move_uploaded_file($tmp_name, $upload_path)) {
            $image_name = $original_name;
        }
    }
    
    // إذا لم تُرفع صورة، استخدم الصورة الافتراضية
    if (!$image_name) {
        $image_name = 'Healthcare.jpg';
    }
    



    // إدخال الخدمة في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO service (name, description, price, image_url, admin_id, branch_id) VALUES (?, 'Standard Service', ?, ?, ?, ?)");
    $stmt->bind_param("sdsii", $name, $price, $image_name, $admin_id, $branch_id);
    
$stmt->execute();
$stmt->close();
// إعادة التوجيه
header("Location: Services Management.php?branch_id=$branch_id");
exit();
}


// جلب الفروع
$branches_result = $conn->query("SELECT * FROM branch");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Service</title>
  <link rel="stylesheet" href="servicesManagement.css">
</head>
<body>
<div class="container">
  <h2>Add New Service</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Service Name:</label>
    <input type="text" name="name" required><br><br>

    <label>Price (SR):</label>
    <input type="number" name="price" step="0.01" required><br><br>

    <label>Branch:</label>
    <select name="branch_id" required>
      <?php while ($branch = $branches_result->fetch_assoc()): ?>
        <option value="<?php echo $branch['branch_id']; ?>"><?php echo htmlspecialchars($branch['branch_name']); ?></option>
      <?php endwhile; ?>
    </select><br><br>

    <label>Image:</label>
<input type="file" name="image" accept="image/*"><br><br>

<input type="checkbox" name="use_default" id="use_default">
<label for="use_default">Use default image</label><br><br>
<!--
(Healthcare.jpg)
      -->

    <button type="submit" class="orange-btn">Add Service</button>
    <a href="Services Management.php" class="orange-btn">Cancel</a>
  </form>
</div>
</body>
</html>