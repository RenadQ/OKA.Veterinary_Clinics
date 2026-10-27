<!-- 
===============================
  Author:
  Team member
===============================
-->

<?php

include 'db_connect.php';

$branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 1;

// جلب بيانات الحجوزات للفرع المحدد فقط
$sql = "SELECT b.booking_id, s.name AS service_name, u.name AS user_name, b.appointment_date, st.status_id, st.status
        FROM servicebooking b
        JOIN service s ON b.service_id = s.service_id
        JOIN user u ON b.user_id = u.user_id
        JOIN servicestatus st ON b.status_id = st.status_id
        WHERE s.branch_id = $branch_id
        ORDER BY b.appointment_date DESC";

$result = $conn->query($sql);

// جلب الحالات
$status_options = $conn->query("SELECT * FROM servicestatus");
$status_list = [];
while ($status = $status_options->fetch_assoc()) {
    $status_list[$status['status_id']] = $status['status'];
}
?>


<h2>Booking Management</h2>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>User</th>
      <th>Service</th>
      <th>Date</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $i++; ?></td>
      <td><?php echo htmlspecialchars($row['user_name']); ?></td>
      <td><?php echo htmlspecialchars($row['service_name']); ?></td>
      <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
      <td>
        <form method="POST" action="update_booking_status.php" style="display:flex; gap:10px; align-items:center;">
          <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
          <select name="status_id" class="status-select">
            <?php foreach ($status_list as $id => $label): ?>
              <option value="<?php echo $id; ?>" <?php if ($id == $row['status_id']) echo 'selected'; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>
      </td>
      <td>
          <button type="submit" class="orange-btn">Update</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
