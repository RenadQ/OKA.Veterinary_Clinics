<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['id'] ?? null;

$query = "SELECT MyPet.*, User.name AS owner_name 
          FROM MyPet 
          JOIN User ON MyPet.user_id = User.user_id
          WHERE MyPet.user_id = '$user_id'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Choose Your Pet</title>
  <link rel="stylesheet" href="header_styles.css">
  <link rel="stylesheet" href="choosePet.css">
</head>

<body>
<?php include 'header.php'; ?>

<div class="main-wrapper">
  <main class="content-wrapper">
    <h1>
      Select Your Pet
      <button class="btn add-btn" onclick="openAddPetPopup()">+ Add New Pet</button>
    </h1>

    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom:20px;">
      <label style="font-weight:bold; font-size:16px; color:#003366;">Search:</label>
      <input type="text" id="searchInput" placeholder="Search by Pet Name" class="branch-select" onkeyup="searchPets()">
    </div>

    <?php include 'pet_add_popup.php'; ?>
    <?php include 'pet_edit_popup.php'; ?>

    <div class="pet-container">
    <?php
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $typeWithS = "";
            if($row['type'] == "Dog") $typeWithS = "Dogs";
            else if($row['type'] == "Cat") $typeWithS = "Cats";
            else if($row['type'] == "Bird") $typeWithS = "Birds";
            else if($row['type'] == "Rabbit") $typeWithS = "Rabbits";
            else $typeWithS = $row['type'];

            echo "<div class='pet-card'>
            <p><strong>Pet Name:</strong> {$row['name']}</p>
            <p><strong>Type:</strong> {$row['type']}</p>
            <p><strong>Breed:</strong> {$row['breed']}</p>
            <p><strong>Age:</strong> {$row['age']} Years</p>
            <p><strong>Gender:</strong> {$row['gender']}</p>

            <div style='display:flex; gap:5px; justify-content:center; flex-wrap:wrap; margin-top:10px;'>
                <button class='btn choose-btn' onclick='window.location.href=\"DoctorList.php?pet_id={$row['my_pet_id']}&type={$typeWithS}\"'>Choose</button>
                <button class='btn edit-btn' onclick=\"openEditPopup({$row['my_pet_id']}, '" . addslashes($row['name']) . "', {$row['age']})\">Edit</button>
                <button class='btn delete-btn' onclick='confirmDelete({$row['my_pet_id']})'>Delete</button>
            </div>
            </div>";
        }
    } else {
        echo "<p style='color:gray; text-align:center;'>No Pets Found. Please Add New Pet.</p>";
    }
    ?>
    </div>
  </main>
</div>

<?php include 'footer.php'; ?>

<script src="choosePet.js"></script>
</body>
</html>
