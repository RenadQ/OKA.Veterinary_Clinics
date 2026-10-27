<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



$user_id = $_SESSION['id'] ?? null;


// إذا ما اختار pet يرجعه يختار pet
if(!isset($_SESSION['selected_pet_id'])){
    header('Location: choosePet.php');
    exit;
}

include 'db_connect.php';


$doctor_id = $_GET['doctor_id'];
$selected_pet_id = $_SESSION['selected_pet_id'];


$stmt = $conn->prepare("SELECT * FROM Doctor WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $doctor = $result->fetch_assoc();
}else{
    echo "Doctor not found!";
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment </title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="header_styles.css">
    <link rel="stylesheet" href="appointmentBooking.css">

    
</head>
<body>
    <?php include 'header.php'; ?>

    <main style="padding: 20px; background-color: rgba(255, 255, 255, 0.9); border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); margin: 60px auto; width: 95%;">

        <h1 class="title">Book Appointment</h1>
        <div class="container">
        
		<section class="doctor-card">
   <img src="<?php echo $doctor['image']; ?>" alt="<?php echo $doctor['name']; ?>" class="doctor-img">

   <div class="doctor-details">
       <h2><?php echo $doctor['name']; ?></h2>
       <p class="specialty">Veterinary Doctor - <?php echo $doctor['specialization']; ?></p>
       <p class="doctor-bio"><?php echo $doctor['bio']; ?></p>
       <p class="working-hours">Working Hours: <?php echo $doctor['working_hours']; ?></p>
   </div>
</section>




            <section class="booking-card">
                <h3>Select a Date</h3>
                <div class="calendar-container">
                    <button id="prev-month">◀</button>
                    <h3 id="current-month">March 2025</h3>
                    <button id="next-month">▶</button>
                    <div class="calendar" id="calendar"></div>
					<p style="font-size:14px; color:gray; margin-top:10px; text-align:center;">
                     * Booking is available for the next 7 days only *
                     </p>

                </div>

                <h3>Select a Time Slot</h3>
				
               
		<div class="time-slots" id="time-slots">
        <?php
		$selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : date('Y-m-d');


$booked_times = [];

$booked_sql = "SELECT DATE_FORMAT(date_time, '%h:%i %p') AS time 
               FROM Appointment 
               WHERE doctor_id='$doctor_id' 
               AND DATE(date_time) = '$selected_date'
               AND status != 'Cancelled'";

$booked_result = $conn->query($booked_sql);

if($booked_result->num_rows > 0){
    while($row = $booked_result->fetch_assoc()){
        $booked_times[] = $row['time'];
    }
}


        function generateTimeSlots($working_hours, $booked_times) {
    $parts = explode(' - ', $working_hours);
    $start = strtotime($parts[0]);
    $end = strtotime($parts[1]);

    while($start < $end) {
        $slot = date('h:i A', $start);
        
        if(!in_array($slot, $booked_times)){ 
            echo "<button class='time-slot'>$slot</button>";
        }

        $start = strtotime('+30 minutes', $start);
    }
}

generateTimeSlots($doctor['working_hours'], $booked_times);

         ?>
</div>


                </div>

            </section>
			
			
			

            <section class="info-card" style="margin: auto;">
    
    <div class="add-pet-buttons" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
        
    </div>
	



    <button class="confirm-btn" onclick="confirmAppointment()">Confirm Appointment</button>
</section>





<!-- Edit Pet Popup -->
    <!-- Edit Pet Popup -->
<div id="editPetPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;">
    <div style="background:#fff; padding:30px; border-radius:15px; width:350px; text-align:center; position:relative; box-shadow:0px 4px 10px rgba(0,0,0,0.2);">
        <h3 style="color: #003366; margin-bottom: 20px;">Edit Pet</h3>

        <form id="editPetForm">
            <input type="hidden" id="editPetId">

            <label>Pet Name:</label>
            <input type="text" id="editPetName" required style="width:100%; padding:10px; margin-bottom:10px;">

            <label>Pet Age:</label>
            <input type="number" id="editPetAge" required style="width:100%; padding:10px; margin-bottom:10px;">

            <div style="margin-top:15px;">
                <button type="button" onclick="saveEditedPet()" style="background:#003366; color:white; border:none; padding:8px 14px; border-radius:5px; margin-right:5px;">Save</button>
                <button type="button" onclick="closeEditPetPopup()" style="background:gray; color:white; border:none; padding:8px 14px; border-radius:5px;">Cancel</button>
            </div>
        </form>
    </div>
</div>

		
		
		
		
    </main>
    

<?php
if(isset($_GET['new_pet'])){
    echo "<script>
        fetch('get_last_pet.php')
.then(response => response.json())
.then(data => {
     selectedPetId = data.my_pet_id; 
     document.getElementById('selectedPetName').innerText = 'Selected Pet: ' + data.name;
});

		
    </script>";
}
?>
<script src="appointmentBooking.js"></script>


<?php include 'footer.php'; ?>
</body>
</html>