<!-- 
===============================
  Author:
  Team member
===============================
-->


<?php
session_start();

include 'db_connect.php';

if (!isset($_GET['id'])) {
    echo "No service ID provided.";
    exit();
}

$service_id = intval($_GET['id']);

// جلب بيانات الخدمة أولًا للتأكيد
$service_query = $conn->query("SELECT * FROM service WHERE service_id = $service_id");
if ($service_query->num_rows === 0) {
    echo "Service not found.";
    exit();
}
$service = $service_query->fetch_assoc();
$branch_id = $service['branch_id'];

// إذا تم تأكيد الحذف
if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $conn->query("DELETE FROM service WHERE service_id = $service_id");
    header("Location: Services Management.php?branch_id=$branch_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Service</title>
  <link rel="stylesheet" href="servicesManagement.css">
</head>
<body>
<div class="container">
  <h2>Are you sure you want to delete this service?</h2>
  <p><strong><?php echo htmlspecialchars($service['name']); ?></strong> - SR <?php echo htmlspecialchars($service['price']); ?></p>
  
 
  <form method="POST" class="form-inline">
  <input type="hidden" name="confirm" value="yes">
  <button type="submit" class="delete-btn">Yes, Delete</button>
  <a href="Services Management.php?branch_id=<?php echo $branch_id; ?>" class="edit-btn">Cancel</a>
  </form>

  </form>
</div>
</body>
</html>
