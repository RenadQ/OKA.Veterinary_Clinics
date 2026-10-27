<!DOCTYPE html>
<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user_id'];

$date_today = date('Y-m-d H:i:s');


?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="header_styles.css">
    <link rel="stylesheet" href="apoiment_mangment_css.css">
    
    
</head>
<body>
    
    <?php include 'header.php'; ?>
    <main style="padding: 20px; background-color: rgba(255, 255, 255, 0.9); border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); margin: 60px auto; width: 95%;">
        <h1>My Bookings</h1>
		<div class="filters">
    <select id="typeSelect" onchange="handleTypeChange()" class="branch-select">
    <option value="">Choose Type</option>
    <option value="doctor">Appointments Bookings</option>
    <option value="service">Service Bookings</option>
    <option value="order">Orders Bookings</option>
</select>


    <select id="filterSelect" onchange="handleFilterChange()" class="branch-select" style="display:none;">
    <option value="all">All</option>
    <option value="upcoming">Upcoming</option>
    <option value="previous">Previous</option>
    <option value="missed">Missed</option>
    <option value="cancelled">Cancelled</option>
</select>


</div>
<h2 id="booking-title" style="color: #003366; text-align: center; margin-bottom: 20px; display:none;"></h2>


<div id="appointments-container"></div>

        

       <?php
	   $allAppointmentsQuery = "SELECT Appointment.*, Doctor.name AS doctor_name, Doctor.specialization, Doctor.image, MyPet.name AS pet_name 
FROM Appointment 
INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id 
INNER JOIN MyPet ON Appointment.my_pet_id = MyPet.my_pet_id 
WHERE Appointment.user_id = '".mysqli_real_escape_string($conn, $user_id)."'
ORDER BY Appointment.date_time ASC";


// استعلام يجيب المواعيد اللي عليها ريفيو لليوزر هذا
$reviewedQuery = "SELECT appointment_id FROM Review WHERE user_id = '$user_id'";
$reviewedResult = $conn->query($reviewedQuery);

$reviewedAppointments = [];
while($row = $reviewedResult->fetch_assoc()){
    $reviewedAppointments[] = $row['appointment_id'];
}





$allAppointmentsResult = $conn->query($allAppointmentsQuery);
if($allAppointmentsResult->num_rows == 0){
   echo "<p style='color:red;text-align:center;'>No Appointments Found in DB for User ID = $user_id</p>";
}


$appointmentsData = [];
while($row = $allAppointmentsResult->fetch_assoc()){
    $appointmentsData[] = $row;
}
?>






    </main>
<script>
    const appointments = <?php echo json_encode($appointmentsData); ?>;
	const reviewedAppointments = <?php echo json_encode($reviewedAppointments); ?>;
	function handleTypeChange() {
    const type = document.getElementById('typeSelect').value;
    const filterSelect = document.getElementById('filterSelect');
    const container = document.getElementById('appointments-container');
    const bookingTitle = document.getElementById('booking-title');

    if (type === "") {
        // اذا مافيه نوع مختار
        container.innerHTML = '<p style="text-align:center;">Please select booking type</p>';
        bookingTitle.style.display = "none";
        filterSelect.style.display = "none";
    } else {
        // اذا اختار نوع
        filterSelect.style.display = "inline-block";
        bookingTitle.style.display = "block";

        if (type === "doctor") {
            bookingTitle.innerHTML = "Appointments Bookings";
        } else if (type === "service") {
            bookingTitle.innerHTML = "Service Bookings";
        } else if (type === "order") {
            bookingTitle.innerHTML = "Orders Bookings";
        }

        filterSelect.value = "all";
        handleFilterChange();
    }
}






    function handleFilterChange() {
    const type = document.getElementById('typeSelect').value;
    const filter = document.getElementById('filterSelect').value;
    const container = document.getElementById('appointments-container');

    container.innerHTML = "";

    if (type === "doctor") {
        const now = new Date();

        const upcoming = appointments.filter(row => row.status == 'Pending' && new Date(row.date_time) >= now);
        const previous = appointments.filter(row => row.status == 'Completed');
        const cancelled = appointments.filter(row => row.status == 'Cancelled');
        const missed = appointments.filter(row => row.status == 'Pending' && new Date(row.date_time) < now);

        if (filter === "all") {

            if(upcoming.length){
                container.innerHTML += `<h3 style="color:#003366; text-align:center;">Upcoming Bookings</h3>`;
                upcoming.forEach(row => container.innerHTML += appointmentCard(row));
            }

            if(missed.length){
                container.innerHTML += `<h3 style="color:#003366; text-align:center;">Missed Bookings</h3>`;
                missed.forEach(row => container.innerHTML += appointmentCard(row));
            }

            if(previous.length){
                container.innerHTML += `<h3 style="color:#003366; text-align:center;">Previous Bookings</h3>`;
                previous.forEach(row => container.innerHTML += appointmentCard(row));
            }

            if(cancelled.length){
                container.innerHTML += `<h3 style="color:#003366; text-align:center;">Cancelled Bookings</h3>`;
                cancelled.forEach(row => container.innerHTML += appointmentCard(row));
            }

            if(!upcoming.length && !missed.length && !previous.length && !cancelled.length){
                container.innerHTML = `<p style="text-align:center;">No Appointments Found</p>`;
            }

        } else {
            let filtered = [];
            if(filter === "upcoming") filtered = upcoming;
            if(filter === "previous") filtered = previous;
            if(filter === "missed") filtered = missed;
            if(filter === "cancelled") filtered = cancelled;

            if(filtered.length){
                filtered.forEach(row => container.innerHTML += appointmentCard(row));
            } else {
                container.innerHTML = `<p style="text-align:center;">No Appointments Found</p>`;
            }
        }

    } else if (type === "service" || type === "order") {
        container.innerHTML = `<p style="text-align:center; color:gray;">Coming Soon...</p>`;
    }
}



function appointmentCard(row) {
    const appDate = new Date(row.date_time);
    const now = new Date();

    let cardColor = 'white';  // اللون الافتراضي
    let statusText = '';      // النص اللي فوق الكرت يوضح الحالة
    let actionButtons = '';   // مكان الأزرار

    // تحديد اللون والنص حسب الحالة
    if (row.status == 'Cancelled') {
        cardColor = '#ffe5e5'; // وردي خفيف للمكنسل
        statusText = `<p style="color:red; font-weight:bold;">Cancelled</p>`;
    } else if (row.status == 'Pending' && appDate < now) {
        cardColor = '#f0f0f0'; // رمادي للمفات
        statusText = `<p style="color:gray; font-weight:bold;">Not Attended</p>`;
    }

    // زر Cancel لو الحالة Pending ولسه قدام
    if (row.status == 'Pending' && appDate >= now) {
        actionButtons += `
            <button class="cancel-btn" onclick="confirmCancel(${row.appointment_id}, '${row.doctor_name}', '${row.pet_name}', '${row.date_time}')">
                Cancel
            </button>
        `;
    }

    // زر Add Review لو الحالة Completed ومافيه ريفيو
    if (row.status == 'Completed' && !reviewedAppointments.includes(row.appointment_id)) {
        actionButtons += `
            <button id="review-btn-${row.appointment_id}" class="review-btn" onclick="openReviewPopup(${row.appointment_id}, \`${row.doctor_name}\`)">
                Add Review
            </button>
        `;
    }

    return `
<div class="appointment-card" style="background:${cardColor}; display: flex; align-items: flex-start; position: relative;">
    <img src="${row.image}" alt="${row.doctor_name}" style="flex-shrink: 0;">

    <div style="flex:1; padding:0 20px; display: flex; flex-direction: column; justify-content: space-between;">

        <div>
            ${statusText}
            <h3>${row.doctor_name} (Veterinary Doctor - ${row.specialization})</h3>
            <p><strong>Pet:</strong> ${row.pet_name}</p>
            <p><strong>Date:</strong> ${row.date_time.split(' ')[0]}</p>
            <p><strong>Time:</strong> ${appDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
        </div>

        <div style="margin-top:15px; align-self: flex-end; display: flex; gap: 10px; flex-wrap: wrap;">
            ${actionButtons}
        </div>

    </div>
</div>
    `;
}




    document.addEventListener('DOMContentLoaded', function() {
   console.log("JS Loaded Perfectly");

   const container = document.getElementById('appointments-container');
   const filterSelect = document.getElementById('filterSelect');
   const bookingTitle = document.getElementById('booking-title');
   const typeSelect = document.getElementById('typeSelect');

   typeSelect.value = ""; 
   filterSelect.style.display = "none";
   bookingTitle.style.display = "none";

   container.innerHTML = '<p style="text-align:center;">Please select booking type</p>';
});






let cancelId = "";

function confirmCancel(id, doctor, pet, datetime){
    const popup = document.getElementById('popup-cancel');
    const popupContent = popup.querySelector('div');

    document.getElementById('popup-msg').innerHTML = `
        Are you sure you want to cancel?<br><strong>Doctor:</strong> ${doctor}<br><strong>Pet:</strong> ${pet}<br><strong>Date & Time:</strong> ${datetime}
    `;

    cancelId = id;

    // يطلع البوب اب
    popup.style.display = "flex";

    //  حركة دخول 
    setTimeout(() => {
        popupContent.style.transform = 'scale(1)';
        popupContent.style.transition = 'transform 0.3s ease';
    }, 50);
}

function closePopup(){
    const popup = document.getElementById('popup-cancel');
    const popupContent = popup.querySelector('div');

    // حركة خروج 
    popupContent.style.transform = 'scale(0.9)';

    setTimeout(() => {
        popup.style.display = "none";
    }, 300);
}

function proceedCancel(){
    fetch('cancelAppointment.php?id=' + cancelId)
    .then(response => response.text())
    .then(result => {
        if(result.trim() == 'success'){
            closePopup();               // يقفل البوب اب
            location.reload();          // يعيد تحميل الصفحة كامل ومرتب
        }else{
            alert(result);
        }
    })
    .catch(error => {
        console.log(error);
        alert('Error cancelling appointment.');
    });
}



let selectedAppointmentId = "";

// فتح البوب اب واضافة بياناته
function openReviewPopup(id, doctor) {
    selectedAppointmentId = id;
    document.getElementById('review-text').value = "";
    document.getElementById('rating-value').value = 0; // مهم
    updateStars(0);  // يخلي النجوم فاضية
    document.getElementById('popup-review').style.display = "flex";
}


// اغلاق البوب اب
function closeReviewPopup() {
    document.getElementById('popup-review').style.display = "none";
}

// تحديث شكل النجوم
function updateStars(rating) {
    const stars = document.querySelectorAll('#rating-stars span');
    stars.forEach((star, index) => {
        star.style.color = (index < rating) ? 'gold' : 'lightgray';
    });
}

function saveReview(){
    const text = document.getElementById('review-text').value.trim();
    const rating = document.getElementById('rating-value').value;

    if(rating == 0){
        alert("Please select rating.");
        return;
    }

    if(text === ""){
        alert("Please write your review.");
        return;
    }

    // إرسال البيانات مع id للمراجعة
    fetch('saveReview.php?id=' + selectedAppointmentId + '&text=' + encodeURIComponent(text) + '&rating=' + rating)
    .then(response => response.text())
.then(result => {
    if(result.trim() == 'success'){
        alert('Review Added Successfully'); 

        const reviewBtn = document.getElementById('review-btn-' + selectedAppointmentId);
        if(reviewBtn){
            reviewBtn.style.display = 'none';
        }

        closeReviewPopup();

    } else {
        alert(result);
    }
})
.catch(error => {
    console.log(error);
    alert('Error while saving review.');
});

}



// تجهيز النجوم للضغط من اول ماتشتغل الصفحة
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('#rating-stars span');
    stars.forEach((star, index) => {
        star.addEventListener('click', function () {
            const rating = index + 1;
            document.getElementById('rating-value').value = rating;
            updateStars(rating);
        });
    });
});


</script>


<div id="popup-cancel" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
    <div style="background:white; padding:20px; border-radius:10px; width:300px; text-align:center;">
        <p id="popup-msg"></p>
        <div style="margin-top:15px;">
            <button onclick="proceedCancel()" style="background:#dc3545; color:white; border:none; padding:8px 14px; border-radius:5px; margin-right:10px;">Confirm</button>
            <button onclick="closePopup()" style="background:gray; color:white; border:none; padding:8px 14px; border-radius:5px;">Cancel</button>
        </div>
    </div>
</div>

<div id="popup-review" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
  <div style="background:white; padding:20px; border-radius:10px; width:320px; text-align:center;">
    <h3>Add Your Review</h3>

    <textarea id="review-text" maxlength="200" placeholder="Write your review..." style="width:100%; height:80px; margin:10px 0; border:1px solid gray; border-radius:5px; padding:5px;"></textarea>

    <div id="rating-stars" style="font-size:24px; color:gold; margin-bottom:10px; cursor:pointer;">
  <span data-rate="1">★</span>
  <span data-rate="2">★</span>
  <span data-rate="3">★</span>
  <span data-rate="4">★</span>
  <span data-rate="5">★</span>
</div>


    <input type="hidden" id="rating-value" value="0">



    <p style="font-size:12px; color:gray;">Max 200 Characters</p>

    <div style="margin-top:15px;">
      <button onclick="saveReview()" style="background:#003366; color:white; border:none; padding:8px 14px; border-radius:5px; margin-right:10px;">Save</button>
      <button onclick="closeReviewPopup()" style="background:gray; color:white; border:none; padding:8px 14px; border-radius:5px;">Cancel</button>
    </div>
  </div>
</div>




    <?php include 'footer.php'; ?>

</body>
</html>
