function searchServices() {
    const input = document.querySelector('.search').value.toLowerCase();
    document.querySelectorAll('.service-card').forEach(card => {
      const title = card.querySelector('h3').textContent.toLowerCase();
      card.style.display = title.includes(input) ? 'block' : 'none';
    });
  }
  
  function filterServices(category) {
    document.querySelectorAll('.service-card').forEach(card => {
      const title = card.querySelector('h3').textContent.toLowerCase();
      card.style.display = (category === 'All' || title.includes(category.toLowerCase())) ? 'block' : 'none';
    });
  }
  
  //  التحقق من التاريخ في Book Now
  document.querySelectorAll('form[action="confirm_payment.php"]').forEach(form => {
    form.addEventListener('submit', function (e) {
      const dateInput = form.querySelector('input[name="booking_date"]');
      const selectedDate = new Date(dateInput.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
  
      if (selectedDate < today) {
        alert("❌ Please choose a valid future date.");
        e.preventDefault();
      }
    });
  });
  
  //  زر Google Calendar: يتحقق من التاريخ والوقت
  function handleCalendar(btn) {
    const card = btn.closest('.service-card');
    const dateInput = card.querySelector('input[name="booking_date"]');
    const timeInput = card.querySelector('select[name="booking_time"]');
    const branch = card.querySelector('select[name="branch_id"]');
    const serviceName = btn.getAttribute('data-service');
  
    const date = dateInput?.value;
    const time = timeInput?.value;
    const branchText = branch?.selectedOptions[0]?.text || 'OKA Clinic';
  
    if (!date || !time) {
      alert("❌ Please select both date and time before adding to calendar.");
      return;
    }
  
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const selectedDate = new Date(date);
    if (selectedDate < today) {
      alert("⚠️ The selected date is in the past. Please choose a valid future date.");
      return;
    }
  
    const start = `${date} ${time}`;
    const startDate = new Date(start);
    const endDate = new Date(startDate.getTime() + 30 * 60000); // 30 mins
  
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