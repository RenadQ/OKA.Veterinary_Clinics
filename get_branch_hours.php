<?php
include 'db_connect.php';

$branchName = $_GET['branch'] ?? '';

$stmt = $conn->prepare("SELECT branch_id, working_hours FROM branch WHERE branch_name = ?");
$stmt->bind_param("s", $branchName);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
