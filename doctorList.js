function updateDoctors() {
  const branchId = document.getElementById('branchSelect').value;
  const container = document.getElementById('doctorContainer');
  container.innerHTML = "";

  if (branchId === "") {
    container.innerHTML = "<p>Please select a branch</p>";
    return;
  }

  fetch('getDoctors.php?branch_id=' + branchId + '&type=' + encodeURIComponent(typeFromPHP))
    .then(response => response.json())
    .then(data => {
      if (data.length === 0) {
        container.innerHTML = "<p>No doctors in this branch.</p>";
      } else {
        data.forEach(doctor => {
          const card = document.createElement('div');
          card.className = 'doctor-card';
          card.innerHTML = `
            <img src="${doctor.img}" alt="${doctor.name}">
            <h3>${doctor.name}</h3>
            <p>Veterinary Doctor - ${doctor.specialization}</p>
            <p>${doctor.working_hours}</p>
            <button class='book-btn' onclick="window.location.href='appointmentBooking.php?doctor_id=${doctor.id}'">Book Now</button>
          `;
          container.appendChild(card);
        });
      }
    });
}

// متغير نجيبه من PHP في السكربت داخل الصفحة:
const typeFromPHP = document.currentScript.getAttribute('data-type');

// تحميل الأطباء عند فتح الصفحة
document.addEventListener('DOMContentLoaded', () => {
  updateDoctors();
});
