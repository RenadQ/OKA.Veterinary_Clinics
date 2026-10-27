<?php
include 'db_connect.php';

$pet_id = $_GET['pet_id'];

$sql = "SELECT * FROM MyPet WHERE my_pet_id='$pet_id'";

$result = $conn->query($sql);

if($result->num_rows > 0){
    $data = $result->fetch_assoc();

    echo json_encode([
        'my_pet_id' => $pet_id, 
        'name' => $data['name'], 
        'age' => $data['age']
    ]);
}else{
    echo json_encode(['error' => 'Pet Not Found']);
}
?>
