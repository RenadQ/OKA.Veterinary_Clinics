function showSection(id) {
    ['service-table-container', 'booking-table-container', 'review-table-container'].forEach(s => {
      document.getElementById(s).style.display = (s === id) ? 'block' : 'none';
    });
  }
  
  document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section');
    if (section) showSection(section + '-table-container');
  });