<?php
include 'db_connect.php';

$pet_id = $_GET['pet_id'];

$query = "SELECT * FROM MyPet WHERE my_pet_id='$pet_id'";
$result = $conn->query($query);

if($result->num_rows == 0){
    echo "<h2 style='color:red;text-align:center;'>Pet Not Found!</h2>";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Information</title>
<link rel="stylesheet" href="viewPetInfo.css">
</head>

<body>

<div class="container">
    <h2>Pet Information</h2>

    <p><strong>Name:</strong> <?= $row['name'] ?></p>
    <p><strong>Age:</strong> <?= $row['age'] ?></p>
    <p><strong>Type:</strong> <?= $row['type'] ?></p>
    <p><strong>Breed:</strong> <?= $row['breed'] ?></p>
    <p><strong>Gender:</strong> <?= $row['gender'] ?></p>

    <a href="Appointment%20List.php" class="btn">Back</a>

</div>

</body>
</html>
