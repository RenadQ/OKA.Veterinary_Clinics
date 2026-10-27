<?php
include 'db_connect.php';

header('Content-Type: application/json');

$branch_id = $_GET['branch_id'];
$type = isset($_GET['type']) ? $_GET['type'] : '';

if(!empty($type)){
   $query = "SELECT * FROM Doctor 
             WHERE branch_id='$branch_id' 
             AND (specialization='$type' OR specialization='All Pets')";
}else{
   $query = "SELECT * FROM Doctor WHERE branch_id='$branch_id'";
}


$result = $conn->query($query);

$doctors = [];

while($row = $result->fetch_assoc()){
    $doctors[] = [
        "id" => $row['doctor_id'],
        "name" => $row['name'],
        "specialization" => $row['specialization'],
        "working_hours" => $row['working_hours'],
        "img" => $row['image']
    ];
}

echo json_encode($doctors);
?>
