<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['id'] ?? null;
$date_today = date('Y-m-d H:i:s');

// جلب الطلبات الخاصة بالمستخدم
$ordersQuery = "SELECT * FROM `order` WHERE user_id = '$user_id' ORDER BY order_date DESC";
$ordersResult = $conn->query($ordersQuery);
$ordersData = [];
if ($ordersResult && $ordersResult->num_rows > 0) {
    while ($orderRow = $ordersResult->fetch_assoc()) {
        $ordersData[] = $orderRow;
    }
}

// تحديث المواعيد المتأخرة إلى Missed
$updateMissed = "UPDATE Appointment 
                 SET status = 'Missed' 
                 WHERE user_id = '$user_id' 
                 AND status = 'Pending' 
                 AND date_time < NOW()";
$conn->query($updateMissed);

// جلب المواعيد للمستخدم
$appointmentsData = [];
$appointmentsQuery = "SELECT Appointment.*, Doctor.name AS doctor_name, Doctor.specialization, Doctor.image, MyPet.name AS pet_name 
FROM Appointment 
INNER JOIN Doctor ON Appointment.doctor_id = Doctor.doctor_id 
INNER JOIN MyPet ON Appointment.my_pet_id = MyPet.my_pet_id 
WHERE Appointment.user_id = '$user_id'
ORDER BY Appointment.date_time ASC";
$appointmentsResult = $conn->query($appointmentsQuery);
if ($appointmentsResult && $appointmentsResult->num_rows > 0) {
    while ($row = $appointmentsResult->fetch_assoc()) {
        $appointmentsData[] = $row;
    }
}

// جلب المواعيد التي عليها تقييم
$reviewedAppointments = [];
$reviewedQuery = "SELECT appointment_id FROM Review WHERE user_id = '$user_id'";
$reviewedResult = $conn->query($reviewedQuery);
if ($reviewedResult && $reviewedResult->num_rows > 0) {
    while ($row = $reviewedResult->fetch_assoc()) {
        $reviewedAppointments[] = $row['appointment_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Bookings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="header_styles.css" />
  <link rel="stylesheet" href="appoiment_css.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
 
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <h1>My Bookings</h1>
  <div class="filters">
    <select id="typeSelect" onchange="handleTypeChange()" class="branch-select">
      <option value="">Choose Type</option>
      <option value="doctor">Appointments Bookings</option>
      <option value="order">Previous Orders</option>
    </select>

    <select id="filterSelect" onchange="handleFilterChange()" class="branch-select" style="display:none;">
      <option value="all">All</option>
      <option value="upcoming">Upcoming</option>
      <option value="previous">Previous</option>
      <option value="missed">Missed</option>
      <option value="cancelled">Cancelled</option>
    </select>
  </div>

  <h2 id="booking-title" style="color: #003366; text-align: center; display: none;"></h2>
  <div id="appointments-container"></div>
</main>

<div id="popup-review" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0;
background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
  <div style="background:white; padding:20px; border-radius:10px; width:320px; text-align:center;">
    <h3>Add Your Review</h3>

    <textarea id="review-text" maxlength="200" placeholder="Write your review..."
      style="width:100%; height:80px; margin:10px 0; border:1px solid gray; border-radius:5px; padding:5px;"></textarea>

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
      <button onclick="saveReview()" style="background:#003366; color:white; padding:8px 14px; border:none; border-radius:5px;">Save</button>
      <button onclick="closeReviewPopup()" style="background:gray; color:white; padding:8px 14px; border:none; border-radius:5px;">Cancel</button>
    </div>
  </div>
</div>
<div id="popup-order-details" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0;
background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:9999;">
  <div style="background:white; padding:20px; border-radius:10px; width:400px; max-height:80vh; overflow-y:auto;">
    <h3 style="text-align:center; color:#003366;">Order Details</h3>
    <div id="order-details-content" style="margin-top: 15px;"></div>
    <div style="text-align:center; margin-top:20px;">
      <button onclick="closeOrderPopup()" style="background:gray; color:white; padding:10px 15px; border:none; border-radius:6px;">Close</button>
    </div>
  </div>
</div>
<script>
const appointments = <?= json_encode($appointmentsData); ?>;
const reviewedAppointments = <?= json_encode($reviewedAppointments); ?>;
const orders = <?= json_encode($ordersData); ?>;
  

let selectedAppointmentId = "";
let selectedOrderId = "";

document.addEventListener('DOMContentLoaded', function () {
  handleTypeChange();
  setupStars(); // لتجهيز النجوم من البداية
});

function handleTypeChange() {
  const type = document.getElementById('typeSelect').value;
  const filterSelect = document.getElementById('filterSelect');
  const title = document.getElementById('booking-title');
  const container = document.getElementById('appointments-container');

  container.innerHTML = "";
  title.style.display = "block";

  if (type === "doctor") {
    title.innerText = "Appointments Bookings";
    filterSelect.style.display = "inline-block";
    handleFilterChange();
  } else if (type === "order") {
    title.innerText = "Orders Bookings";
    filterSelect.style.display = "none";
    renderOrders();
  } else {
    title.style.display = "none";
    filterSelect.style.display = "none";
    container.innerHTML = "<p style='text-align:center;'>Please select booking type</p>";
  }
}

function handleFilterChange() {
  const filter = document.getElementById('filterSelect').value;
  const container = document.getElementById('appointments-container');
  container.innerHTML = "";

  const now = new Date();
  const upcoming = appointments.filter(a => a.status === 'Pending' && new Date(a.date_time) >= now);
  const previous = appointments.filter(a => a.status === 'Completed');
  const missed = appointments.filter(a => a.status === 'Missed');
  const cancelled = appointments.filter(a => a.status === 'Cancelled');

  let filtered = [];
  if (filter === "all") filtered = [...upcoming, ...missed, ...previous, ...cancelled];
  if (filter === "upcoming") filtered = upcoming;
  if (filter === "previous") filtered = previous;
  if (filter === "missed") filtered = missed;
  if (filter === "cancelled") filtered = cancelled;

  if (filtered.length === 0) {
    container.innerHTML = "<p style='text-align:center;'>No Appointments Found</p>";
    return;
  }

  filtered.forEach(row => container.innerHTML += appointmentCard(row));
}

function appointmentCard(row) {
  const appDate = new Date(row.date_time);
  let statusNote = "", buttons = "";

  if (row.status === 'Cancelled') {
    statusNote = `<p style="color:red;font-weight:bold;">Cancelled</p>`;
  } else if (row.status === 'Pending' && appDate < new Date()) {
    statusNote = `<p style="color:gray;font-weight:bold;">Not Attended</p>`;
  }

  if (row.status === 'Pending' && appDate >= new Date()) {
    buttons += `<button class="cancel-btn" onclick="confirmCancel(${row.appointment_id})">Cancel</button>`;
  }

  if (row.status === 'Completed' && !reviewedAppointments.includes(row.appointment_id)) {
    buttons += `<button class="review-btn" id="review-btn-${row.appointment_id}" onclick="openReviewPopup(${row.appointment_id})">Add Review</button>`;
  }

  return `
    <div class="appointment-card">
      <h3>${row.doctor_name} (${row.specialization})</h3>
      ${statusNote}
      <p><strong>Pet:</strong> ${row.pet_name}</p>
      <p><strong>Date:</strong> ${row.date_time.split(' ')[0]}</p>
      <p><strong>Time:</strong> ${appDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
      <div style="margin-top:10px;">${buttons}</div>
    </div>
  `;
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

    fetch('saveReview.php?id=' + selectedAppointmentId + '&text=' + encodeURIComponent(text) + '&rating=' + rating)
    .then(response => response.text())
    .then(result => {
        if(result.trim() == 'success'){
            alert('Review Added Successfully');
            document.getElementById('popup-review').style.display = 'none';

            const reviewBtn = document.getElementById('review-btn-' + selectedAppointmentId);
            if(reviewBtn){
                reviewBtn.style.display = 'none';
            }

        } else {
            alert(result);
        }
    })
    .catch(error => {
        alert('Error saving review.');
        console.log(error);
    });
}

function confirmCancel(id) {
  if (confirm("Are you sure you want to cancel this appointment?")) {
    fetch('cancelAppointment.php?id=' + id)
      .then(res => res.text())
      .then(result => {
        if (result.trim() === 'success') {
          alert('Appointment cancelled.');
          location.reload(); // إعادة تحميل الصفحة لتحديث الحالة
        } else {
          alert(result);
        }
      })
      .catch(err => {
        alert('Error cancelling appointment.');
        console.log(err);
      });
  }
}

function openOrderPopup(orderId) {
  selectedOrderId = orderId;
  fetch('getOrderDetails.php?order_id=' + orderId)
    .then(res => res.text())
    .then(html => {
      document.getElementById('order-details-content').innerHTML = html;
      document.getElementById('popup-order-details').style.display = "flex";
    }).catch(err => {
      alert('Error loading order details');
      console.log(err);
    });
}

function closeOrderPopup() {
  document.getElementById('popup-order-details').style.display = "none";
}
function renderOrders() {
  const container = document.getElementById('appointments-container');
  container.innerHTML = "";

  if (orders.length === 0) {
    container.innerHTML = `<p style="text-align:center;">No Orders Found</p>`;
    return;
  }

  orders.forEach(order => {
    container.innerHTML += `
      <div class="appointment-card" style="flex-direction:column;">
        <h3 style="color:#003366;">Order #${order.order_id}</h3>
        <p><strong>Order Date:</strong> ${order.order_date}</p>
        <p><strong>Pickup Date:</strong> ${order.pickup_date_time}</p>
        <p><strong>Status:</strong> ${order.status}</p>
        <p><strong>Total:</strong> ${order.total_amount} SAR</p>
        <button class="review-btn" onclick="openOrderPopup(${order.order_id})">View Details</button>
      </div>
    `;
  });
}
function openReviewPopup(id) {
  selectedAppointmentId = id;
  document.getElementById('review-text').value = "";
  document.getElementById('rating-value').value = 0;
  updateStars(0);
  document.getElementById('popup-review').style.display = "flex";
}
function closeReviewPopup() {
  document.getElementById('popup-review').style.display = "none";
}
function updateStars(rating) {
  const stars = document.querySelectorAll('#rating-stars span');
  stars.forEach((star, index) => {
    star.style.color = (index < rating) ? 'gold' : 'lightgray';
  });
}
function setupStars() {
  const stars = document.querySelectorAll('#rating-stars span');
  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      const rating = index + 1;
      document.getElementById('rating-value').value = rating;
      updateStars(rating);
    });
  });
}

</script>
<?php include 'footer.php'; ?>

</body>
</html>


