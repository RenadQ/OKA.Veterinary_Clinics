<?php
include 'db_connect.php'; // Include the database connection

$branch = $_GET['branch'];
$sql = "SELECT doctor_id, name, specialization, working_hours FROM Doctor WHERE branch_id in (SELECT branch_id FROM Branch WHERE branch_name = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $branch);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode($doctors);