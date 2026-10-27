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
$service_query = $conn->query("SELECT * FROM service WHERE service_id = $service_id");
if ($service_query->num_rows === 0) {
    echo "Service not found.";
    exit();
}
$service = $service_query->fetch_assoc();
$branches_result = $conn->query("SELECT * FROM branch");

// عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $branch_id = intval($_POST['branch_id']);

    $image_name = $service['image_url']; // الافتراضي هو الصورة الحالية

    if (isset($_POST['use_default'])) {
        $image_name = 'Healthcare.jpg';
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $original_name = str_replace(' ', '', basename($_FILES['image']['name']));
        $upload_path = 'images/' . $original_name;

        if (move_uploaded_file($tmp_name, $upload_path)) {
            $image_name = $original_name;
        }
    }

    $stmt = $conn->prepare("UPDATE service SET name = ?, price = ?, image_url = ?, branch_id = ? WHERE service_id = ?");
    $stmt->bind_param("sdsii", $name, $price, $image_name, $branch_id, $service_id);
    $stmt->execute();
    $stmt->close();

    header("Location: Services Management.php?branch_id=$branch_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Service</title>
  <link rel="stylesheet" href="servicesManagement.css">
</head>
<body>
<div class="container">
  <h2>Edit Service</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Service Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required><br><br>

    <label>Price (SR):</label>
    <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($service['price']); ?>" required><br><br>

    <label>Branch:</label>
    <select name="branch_id" required>
      <?php while ($branch = $branches_result->fetch_assoc()): ?>
        <option value="<?php echo $branch['branch_id']; ?>" <?php if ($branch['branch_id'] == $service['branch_id']) echo 'selected'; ?>><?php echo htmlspecialchars($branch['branch_name']); ?></option>
      <?php endwhile; ?>
    </select><br><br>

    <label>Current Image:</label><br>
    <img src="images/<?php echo htmlspecialchars($service['image_url']); ?>" width="100"><br><br>

    <label>Upload New Image:</label>
    <input type="file" name="image" accept="image/*"><br><br>

    <input type="checkbox" name="use_default" id="use_default">
    <label for="use_default">Use default image (Healthcare.jpg)</label><br><br>

    <button type="submit" class="orange-btn">Save Changes</button>
    <a href="Services Management.php?branch_id=<?php echo $service['branch_id']; ?>" class="orange-btn">Cancel</a>
  </form>
</div>
</body>
</html>
