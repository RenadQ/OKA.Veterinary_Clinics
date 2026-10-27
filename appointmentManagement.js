const appointments = appointmentsData;
const reviewedAppointments = reviewedData;
const orders = ordersData;

let selectedAppointmentId = "";
let selectedOrderId = "";

document.addEventListener('DOMContentLoaded', function () {
  handleTypeChange();
  setupStars();
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

function saveReview() {
  const text = document.getElementById('review-text').value.trim();
  const rating = document.getElementById('rating-value').value;

  if (rating == 0) {
    alert("Please select rating.");
    return;
  }

  if (text === "") {
    alert("Please write your review.");
    return;
  }

  fetch('saveReview.php?id=' + selectedAppointmentId + '&text=' + encodeURIComponent(text) + '&rating=' + rating)
    .then(response => response.text())
    .then(result => {
      if (result.trim() == 'success') {
        alert('Review Added Successfully');
        document.getElementById('popup-review').style.display = 'none';

        const reviewBtn = document.getElementById('review-btn-' + selectedAppointmentId);
        if (reviewBtn) {
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
