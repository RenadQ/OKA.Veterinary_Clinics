<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$adminName = $_SESSION['name'] ?? 'admin';
?>

<!-- Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="Admin_styles.css">

<!-- HEADER -->
<header style="display: flex; justify-content: space-between; align-items: center; background: #f04e30; color: white; height: 70px; padding: 0 20px; position: fixed; width: 100%; top: 0; z-index: 1000;">
  <div class="profile" style="position: relative;">
    <!-- Toggle: Name -->
    <div id="profile-toggle" style="cursor: pointer;">
      <?= htmlspecialchars($adminName) ?> <i class="fas fa-caret-down" style="margin-left: 6px;"></i>
    </div>

    <!-- Dropdown -->
    <div id="profile-dropdown" class="dropdown-content" style="display: none; position: absolute; top: 100%; right: 0; background: white; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); border-radius: 5px; width: 160px; z-index: 100;">
      <a href="Adminprofile.php" style="display: flex; align-items: center; padding: 10px; text-decoration: none; color: black;">
        <i class="fas fa-user" style="margin-right: 10px;"></i> Your Profile
      </a>
      <a href="logout_admin.php" style="display: flex; align-items: center; padding: 10px; text-decoration: none; color: black;">
        <i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Sign Out
      </a>
    </div>
  </div>

  <!-- وسط: OKA Petstore -->
  <div style="position: absolute; left: 50%; transform: translateX(-50%); font-size: 16px; font-weight: bold;">
    OKA Petstore
  </div>
</header>

<!-- SIDEBAR -->
<div class="sidebar">
  <ul>
    <li><a href="Product Management.php">Product Management</a></li>
    <li><a href="Offers Management.php">Offers Management</a></li>
    <li><a href="Services Management.php">Services Management</a></li>
    <li><a href="Orders Management.php">Orders Management</a></li>
    <li><a href="Adoption Management.php">Adoption Management</a></li>
    <li><a href="Appointment List.php">Appointment List</a></li>
    <li><a href="Blogs Management.php">Blogs Management</a></li>
    <li><a href="User Management.php">User Management</a></li>
  </ul>
</div>

<!-- Welcome Back Popup -->
<div id="welcomePopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #f58c79; color: white; padding: 25px 50px; border-radius: 10px; box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1); z-index: 1001; text-align: center; font-family: 'Arial', sans-serif; transition: opacity 0.5s ease-in-out;">
  <h3 style="font-size: 20px; margin-bottom: 10px;">Welcome back, <?= htmlspecialchars($adminName); ?>!</h3>
  <p style="font-size: 14px; margin-bottom: 20px;">Your command center for efficient management and powerful insights. Let's make great things happen!</p>
  <button onclick="closePopup()" style="background-color: #fff; color:rgb(185, 97, 81); border: none; padding: 8px 16px; border-radius: 5px; font-size: 14px; cursor: pointer; transition: background-color 0.3s ease;">Close</button>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById('profile-toggle');
  const dropdown = document.getElementById('profile-dropdown');

  // إظهار أو إخفاء القائمة عند الضغط على الاسم
  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
  });

  // إخفاء القائمة عند الضغط خارجها
  window.addEventListener('click', function () {
    dropdown.style.display = "none";
  });
  
  // عرض نافذة الترحيب عند تحميل الصفحة
  setTimeout(function() {
    document.getElementById('welcomePopup').style.display = 'block';
    document.getElementById('welcomePopup').style.opacity = '1'; // جعل النافذة تظهر تدريجياً
    setTimeout(function() {
      closePopup();
    }, 5000); // إخفاء النافذة بعد 5 ثوانٍ
  }, 1000); // عرض النافذة بعد 1 ثانية
});

// Close popup function
function closePopup() {
  document.getElementById('welcomePopup').style.opacity = '0'; // جعل النافذة تختفي تدريجياً
  setTimeout(function() {
    document.getElementById('welcomePopup').style.display = 'none';
  }, 500); // إخفاء النافذة بعد التأثير التدريجي
}

// Highlight active sidebar link
window.addEventListener('DOMContentLoaded', () => {
  const currentFile = decodeURIComponent(window.location.pathname.split("/").pop().split("?")[0].toLowerCase());

  document.querySelectorAll('.sidebar a').forEach(link => {
    const linkHref = link.getAttribute('href').toLowerCase();
    if (linkHref === currentFile) {
      link.classList.add('active');
    }
  });
});
</script>

</body>
</html>
