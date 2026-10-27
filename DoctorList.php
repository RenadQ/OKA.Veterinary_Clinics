<!DOCTYPE html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$pet_id = isset($_GET['pet_id']) ? $_GET['pet_id'] : '';

// نحفظ pet_id في session
$_SESSION['selected_pet_id'] = $pet_id;
?>


<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctors List</title>
<link rel="stylesheet" href="header_styles.css">
<link rel="stylesheet" href="doctorList.css">

</head>

<body>

<?php include 'header.php'; ?>

<main class="content-wrapper">
<h1>Select a Branch</h1>

<select class="branch-select" id="branchSelect" onchange="updateDoctors()">
<option value="">Select Branch</option>
<?php
$branchQuery = "SELECT * FROM Branch";
$branchResult = $conn->query($branchQuery);
while($row = $branchResult->fetch_assoc()) {
echo '<option value="'.$row['branch_id'].'">'.$row['branch_name'].'</option>';
}
?>
</select>
<div class="doctor-container" id="doctorContainer"></div>
</main>


<?php include 'footer.php'; ?>

<script src="doctorList.js" data-type="<?php echo $type; ?>"></script>



</body>
</html>
