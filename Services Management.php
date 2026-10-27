<!-- 
===============================
  Author:
  Team member
===============================
-->

<?php
session_start();
$_SESSION['admin_id'] = 6; 
/*
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin22') {
    header("Location: login.php");
    exit();
}
*/

include 'db_connect.php';

$branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 1;
$branch_result = $conn->query("SELECT branch_name FROM Branch WHERE branch_id = $branch_id");
$branch = $branch_result->fetch_assoc();
$branch_name = $branch['branch_name'];

$services_sql = "SELECT * FROM Service WHERE branch_id = $branch_id";
$services_result = $conn->query($services_sql);

$service_count = $conn->query("SELECT COUNT(*) as count FROM Service WHERE branch_id = $branch_id")->fetch_assoc()['count'];
$booking_count = $conn->query("SELECT COUNT(*) as count FROM ServiceBooking WHERE service_id IN (SELECT service_id FROM Service WHERE branch_id = $branch_id)")->fetch_assoc()['count'];
// $review_count = $conn->query("SELECT COUNT(*) as count FROM ServiceReview WHERE booking_id IN (SELECT booking_id FROM ServiceBooking WHERE service_id IN (SELECT service_id FROM Service WHERE branch_id = $branch_id))")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Manage Services</title>
  <link rel="stylesheet" href="servicesManagement.css">
  <link rel="stylesheet" href="Admin_Styles.css">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="servicesManagement.js" defer></script> <!-- إضافة رابط لملف الجافا سكريبت -->
</head>
<body>

<?php include 'admin_Header_layout.php'; ?>

<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h1><?php echo htmlspecialchars($branch_name); ?></h1>
    <form method="get">
      <select name="branch_id" onchange="this.form.submit()">
        <option value="1" <?php if ($branch_id == 1) echo 'selected'; ?>>Jubail</option>
        <option value="2" <?php if ($branch_id == 2) echo 'selected'; ?>>Dammam</option>
        <option value="3" <?php if ($branch_id == 3) echo 'selected'; ?>>Khobar</option>
      </select>
    </form>
  </div>

  <div class="dashboard">
    <div class="card">
      <h3>Orders</h3>
      <p><?php echo $booking_count; ?></p>
    </div>
    <div class="card">
      <h3>Services</h3>
      <p><?php echo $service_count; ?></p>
    </div>
    <!-- <div class="card">
      <h3>Reviews</h3>
      <p><?php //echo $review_count; ?></p>
    </div> -->
  </div>

  <div>
    <button class="orange-btn" onclick="showSection('service-table-container')">Service Management</button>
    <button class="orange-btn" onclick="showSection('booking-table-container')">Booking Management</button>
    <!-- <button class="orange-btn" onclick="showSection('review-table-container')">Review Management</button> -->
  </div>

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="success-message">
    Booking updated successfully!
    <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
  </div>
<?php endif; ?>

  <!-- Service Management Table -->
<div id="service-table-container" style="margin-top: 20px;">
  <h3>Services</h3>
  <div style="overflow-x: auto;"> 
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Service Name</th>
          <th>Price (SR)</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($row = $services_result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['price']); ?></td>
            <td>
              <?php
              $image = !empty($row['image_url']) ? $row['image_url'] : str_replace(' ', '', $row['name']) . '.jpg';
              ?>
              <img src="images/<?php echo htmlspecialchars($image); ?>" class="service-image" />
            </td>
            <td>
              <a href="edit_service.php?id=<?php echo $row['service_id']; ?>" class="edit-btn">Edit</a>
              <a href="delete_service.php?id=<?php echo $row['service_id']; ?>" class="delete-btn">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div> <!-- end of scroll wrapper -->
  <div style="margin-top: 15px; text-align: right;">
    <a href="add_service.php" class="orange-btn">Add New Service</a>
  </div>
</div>

  <!-- Booking Management Section -->
  <div id="booking-table-container" style="display:none; margin-top: 20px;">
    <?php include 'bookingManagement.php'; ?>
  </div>

  <!-- Review Management Section -->
  <div id="review-table-container" style="display:none; margin-top: 20px;">
    <p>Review Management Coming Soon...</p>
  </div>
</div>
</body>

</html>