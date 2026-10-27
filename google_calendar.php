<?php
// google_calendar.php
?>

<!-- Google Calendar Button Style -->
<style>
.google-btn {
  
    background:rgba(15, 157, 88, 0.61);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
    margin-top: auto; 
}
.google-btn:hover {
    background-color: #0c7c45;
}
</style>

<!-- Google Calendar Button Script -->
<script>
function handleCalendar(btn) {
    const card = btn.closest('.service-card');
    const dateInput = card.querySelector('input[name="booking_date"]');
    const timeInput = card.querySelector('select[name="booking_time"]');
    const serviceName = btn.getAttribute('data-service');
    const branch = card.querySelector('select[name="branch_id"]');

    const date = dateInput?.value;
    const time = timeInput?.value;
    const branchText = branch?.selectedOptions[0]?.text || 'OKA Clinic';

    if (!date || !time) {
        alert("❌ Please select both date and time before adding to calendar.");
        return;
    }

    const today = new Date();
    today.setHours(0,0,0,0); // ignore time in comparison
    const selectedDate = new Date(date);

    if (selectedDate < today) {
        alert("⚠️ The selected date is in the past. Please choose a valid future date.");
        return;
    }

    const start = `${date} ${time}`;
    const startDate = new Date(start);
    const endDate = new Date(startDate.getTime() + 30 * 60000);

    function formatDate(dt) {
        return dt.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
    }

    const url = `https://calendar.google.com/calendar/render?action=TEMPLATE`
        + `&text=${encodeURIComponent(serviceName)}`
        + `&details=${encodeURIComponent("Appointment at OKA Clinic")}`
        + `&location=${encodeURIComponent(branchText)}`
        + `&dates=${formatDate(startDate)}/${formatDate(endDate)}`;

    window.open(url, '_blank');
}
</script>
