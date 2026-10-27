<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';  

//  التحقق من الكوكيز قبل أي استخدام للجلسة
if (!isset($_SESSION['id']) && isset($_COOKIE['remember_id']) && isset($_COOKIE['remember_token'])) {
    $user_id = $_COOKIE['remember_id'];
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT user_id, name, profile_img, remember_token FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($uid, $name, $profile_img, $hashedToken);
        $stmt->fetch();

        if (password_verify($token, $hashedToken)) {
            $_SESSION['id'] = $uid;
            $_SESSION['name'] = $name;
            $_SESSION['profile_pic'] = $profile_img ?: 'profile.png';
        }
    }

    $stmt->close();
}

//  بعد التأكد من الجلسة أو الكوكي،   
$isLoggedIn = isset($_SESSION['id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['name']) : '';
$profileImage = $isLoggedIn && !empty($_SESSION['profile_pic'])
    ? 'profile_pics/' . htmlspecialchars($_SESSION['profile_pic'])
    : 'profile.png';
?>


<header>
    <div class="logo"><a href="homepage.php">OKA Petstore</a></div>
    <nav>
        <ul>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="adopt.php">Adopt</a></li>
            <li><a href="shop.php">Shop</a></li>
           

            <?php if ($isLoggedIn): ?>
                <li><a href="services.php">Services</a></li>
                <li><a href="choosePet.php">Book</a></li>
                <li><a href="appointmentManagement.php">Manage Appointments</a></li>
            <?php endif; ?>

            <li><a href="https://linktr.ee/oka.vet" target="_blank">Contact Us</a></li>
            <li><a href="blogs.php">Blog</a></li>
        </ul>
    </nav>

    <div class="header-buttons">
        <a href="Cart.php">
            <img src="bag-icon.png" alt="Bag Icon" class="bag-icon">
        </a>

        <?php if ($isLoggedIn): ?>
            <div class="profile-container" onclick="toggleProfileDropdown(event)">
                <img src="<?= $profileImage ?>" alt="Profile" class="profile-icon">
                <span class="username"><?= $username ?></span>
                <div class="dropdown-menu" id="profile-dropdown-menu" style="display: none;">
                    <a href="sitting.php">My Profile</a>
                    <a href="logout.php">Sign Out</a>
                </div>
            </div>
        <?php else: ?>
            <a href="signin.php" class="sign-in">Sign in</a>
        <?php endif; ?>
    </div>
</header>

<div class="header-space"></div>

<script>
function toggleProfileDropdown(event) {
    const menu = document.getElementById("profile-dropdown-menu");
    if (menu) {
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    }
}

window.addEventListener('click', function(e) {
    const container = document.querySelector(".profile-container");
    const dropdown = document.getElementById("profile-dropdown-menu");
    if (dropdown && container && !container.contains(e.target)) {
        dropdown.style.display = "none";
    }
});
</script>
<!-- ===============================
   Author:
    
   Renad Alkahtani
   file header to include in all pages 
   ===============================  -->
