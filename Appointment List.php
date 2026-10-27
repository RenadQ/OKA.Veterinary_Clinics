<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Appointment List</title>
  <link rel="stylesheet" href="Admin_Styles.css">
 <link rel="stylesheet" href="appointmentList.css">

</head>

<body>
<?php include 'admin_Header_layout.php'; ?>

  <?php
  include 'db_connect.php';
  // Change Appointment Status
if(isset($_GET['action']) && $_GET['action'] == 'mark_attended' && isset($_GET['id'])){
    $appointment_id = $_GET['id'];
    $conn->query("UPDATE Appointment SET status='Completed' WHERE appointment_id=$appointment_id");
    echo "<script>window.location.href='Appointment List.php';</script>";
    exit;
}

// Get Review
$reviewData = null;
if(isset($_GET['action']) && $_GET['action'] == 'view_review' && isset($_GET['appointment_id'])){
    $appointment_id = intval($_GET['appointment_id']);
    $sql = "SELECT r.rating, r.comment, r.created_at, u.name AS user_name
            FROM Review r
            JOIN Appointment a ON r.appointment_id = a.appointment_id
            JOIN User u ON r.user_id = u.user_id
            WHERE r.appointment_id = $appointment_id";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $reviewData = $result->fetch_assoc();
    }
}

  $branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : null;

  $branch_name = "All Branches";
if ($branch_id) {
  $branch_result = $conn->query("SELECT branch_name FROM Branch WHERE branch_id = $branch_id");
  if ($branch_result->num_rows > 0) {
      $branch_name = $branch_result->fetch_assoc()['branch_name'];
  }
}

  
  $filter = isset($_GET['filter']) && $_GET['filter'] != '' ? $_GET['filter'] : 'Pending';

$appointment_count_sql = "SELECT COUNT(*) as count FROM Appointment 
INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id 
WHERE 1 ".($branch_id ? "AND Doctor.branch_id = $branch_id" : "")."" . ($filter ? " AND Appointment.status = '$filter'" : "");


$appointment_count = $conn->query($appointment_count_sql)->fetch_assoc()['count'];


  $review_count = 0;
if($filter == 'Completed'){
   $review_count_sql = "SELECT COUNT(*) as count FROM Review 
      INNER JOIN Appointment ON Review.appointment_id = Appointment.appointment_id
      INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id
      WHERE 1 ".($branch_id ? "AND Doctor.branch_id = $branch_id" : "")."";
   $review_count = $conn->query($review_count_sql)->fetch_assoc()['count'];
}

  
if($filter == 'Missed'){
  $appointments_sql = "
    SELECT Appointment.*, Doctor.name AS doctor_name, MyPet.name AS pet_name, User.name AS user_name
    FROM Appointment
    INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id
    INNER JOIN MyPet ON Appointment.my_pet_id = MyPet.my_pet_id
    INNER JOIN User ON Appointment.user_id = User.user_id
    WHERE 1 ".($branch_id ? "AND Doctor.branch_id = $branch_id" : "")."

    AND Appointment.status = 'Missed'
    ORDER BY Appointment.date_time DESC";
}else if($filter == 'Completed'){
  $appointments_sql = "
    SELECT Appointment.*, Doctor.name AS doctor_name, MyPet.name AS pet_name, User.name AS user_name, Review.review_id
    FROM Appointment
    INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id
    INNER JOIN MyPet ON Appointment.my_pet_id = MyPet.my_pet_id
    INNER JOIN User ON Appointment.user_id = User.user_id
    LEFT JOIN Review ON Appointment.appointment_id = Review.appointment_id
    WHERE 1 ".($branch_id ? "AND Doctor.branch_id = $branch_id" : "")."
    AND Appointment.status = 'Completed'
    ORDER BY Appointment.date_time DESC";
}else{
  $appointments_sql = "
    SELECT Appointment.*, Doctor.name AS doctor_name, MyPet.name AS pet_name, User.name AS user_name
    FROM Appointment
    INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id
    INNER JOIN MyPet ON Appointment.my_pet_id = MyPet.my_pet_id
    INNER JOIN User ON Appointment.user_id = User.user_id
    WHERE 1 ".($branch_id ? "AND Doctor.branch_id = $branch_id" : "")."
    AND Appointment.status = '$filter'
    ORDER BY Appointment.date_time DESC";
}





$appointments_result = $conn->query($appointments_sql);

?>



  <div class="container" style="margin-top:90px; margin-left:auto; margin-right:auto; width:65%;">








    <h1>Appointment List</h1>


    <div class="dashboard">
      <div class="card">
        <h3>Total Appointments</h3>
        <p><?php echo $appointment_count; ?></p>
      </div>
      <?php if($filter == 'Completed'){ ?>
<div class="card">
    <h3>Total Reviews</h3>
    <p><?php echo $review_count; ?></p>
</div>
<?php } ?>

    </div>
	<form method="GET" style="margin-bottom: 20px;">
  <label for="filter">Filter by Status:</label>
  <select name="filter" id="filter" onchange="this.form.submit()">
    <option value="Pending" <?php if ($filter == 'Pending') echo 'selected'; ?>>Pending</option>
    <option value="Completed" <?php if ($filter == 'Completed') echo 'selected'; ?>>Completed</option>
    <option value="Cancelled" <?php if ($filter == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
	<option value="Missed" <?php if ($filter == 'Missed') echo 'selected'; ?>>Missed</option>
</select>

</form>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Doctor Name</th>
      <th>Pet Name</th>
      <th>Date & Time</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; while ($row = $appointments_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $i++; ?></td>
      <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
      <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
      <td><?php echo htmlspecialchars($row['date_time']); ?></td>
      <td>
        <?php
          $status = htmlspecialchars($row['status']);
          echo $status === 'Cancelled' ? "<span style='color:red;'>Cancelled</span>" : $status;
        ?>
      </td>
      <td>
        <?php
          $appointment_id = $row['appointment_id'];
          $check_review = $conn->query("SELECT * FROM Review WHERE appointment_id = $appointment_id");

          if ($row['status'] == 'Pending') {
              echo "<a href='Appointment List.php?action=mark_attended&id=$appointment_id' class='orange-btn'>Mark as Attended</a>";
              echo "<br><br>";
              echo "<a href='viewPetInfo.php?pet_id=".$row['my_pet_id']."' class='orange-btn'>View Pet Info</a>";
          } elseif ($row['status'] == 'Missed') {
              $appointment_time = strtotime($row['date_time']);
              $now = time();
              $diff_minutes = round(($now - $appointment_time) / 60);

              if ($diff_minutes <= 60) {
                  echo "<a href='mark_attendance.php?id=$appointment_id&status=Completed' class='orange-btn'>Mark as Attended</a><br><br>";
              } else {
                  echo "<span style='color:gray; font-weight:bold;'>Too Late to Mark</span><br><br>";
              }

              echo "<a href='viewPetInfo.php?pet_id=".$row['my_pet_id']."' class='orange-btn'>View Pet Info</a>";
          } elseif ($row['status'] == 'Completed' && $row['review_id'] != NULL) {
			  echo "<button onclick=\"showReview('$appointment_id')\" class='orange-btn'>Show Review</button>";



}

        ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
  
</table>
<div id="reviewPopup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.5); z-index:1000;">
  <div id="reviewContent"></div>
  <button onclick="closeReview()" class="orange-btn" style="margin-top:10px;">Close</button>
</div>



  </div>
<script src="appointmentList.js"></script>


</body>

</html>
