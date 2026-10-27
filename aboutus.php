<!DOCTYPE html>
<html lang="en">
<head>
<!--Team member-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>About Us - OKA Petstore</title>
	<link rel="stylesheet" href="header_styles.css">
    <link rel="stylesheet" href="aboutus.css">
 

</head>
<body>
<?php
include 'header.php'; // إضافة الهيدر الجديد هنا
?>
	
<div class="content">
    <h1>About Our Store</h1>
    <div class="about-container">
        <p>OKA Veterinary Clinics are dedicated to providing high-quality medical care for pets, with a strong focus on excellence and animal well-being. Our team consists of experienced veterinarians and dedicated staff who offer comprehensive services, including routine check-ups, vaccinations, surgeries, and emergency care. We believe that every pet deserves the best medical attention, which is why we utilize the latest technologies and medical advancements to ensure optimal treatment.</p>
        <p>At OKA Veterinary Clinics, we strive to create a comfortable and friendly environment for both pets and their owners, offering personalized care tailored to each animal’s unique needs. We understand the special bond between pet owners and their beloved companions, and we work diligently to support that relationship by providing reliable and high-quality veterinary services. Your pet’s health and happiness are our top priorities.</p>
    </div>
    <h2 class="team-title">Our Team</h2>
    <div class="carousel">
        <button class="carousel-btn left" onclick="moveCarousel(-1)">&lt;</button>
        <div class="carousel-inner" id="carousel">

     <div class="team-card">
        <img src="1.jpg">
        <span class="tooltip">Team Member 1 Description</span>
    </div>
    <div class="team-card">
        <img src="2.jpg">
        <span class="tooltip">Team Member 2 Description</span>
    </div>
    <div class="team-card">
        <img src="3.jpg">
        <span class="tooltip">Team Member 3 Description</span>
    </div>
    <div class="team-card">
        <img src="4.jpg">
        <span class="tooltip">Team Member 4 Description</span>
    </div>
    <div class="team-card">
        <img src="5.jpg">
        <span class="tooltip">Team Member 5 Description</span>
    </div>
    <div class="team-card">
        <img src="6.jpg">
        <span class="tooltip">Team Member 6 Description</span>
    </div>
    <div class="team-card">
        <img src="7.jpg">
        <span class="tooltip">Team Member 7 Description</span>
    </div>
    <div class="team-card">
        <img src="8.jpg">
        <span class="tooltip">Team Member 8 Description</span>
    </div>
    <div class="team-card">
        <img src="9.jpg">
        <span class="tooltip">Team Member 9 Description</span>
    </div>
    <div class="team-card">
        <img src="10.jpg">
        <span class="tooltip">Team Member 10 Description</span>
    </div>
    <div class="team-card">
        <img src="11.jpg">
        <span class="tooltip">Team Member 11 Description</span>
    </div>
	
        </div>
		<button class="carousel-btn right" onclick="moveCarousel(1)">&gt;</button>
</div>
        
    </div>
	

    <!-- New Section for Branches Map -->
    <h2 class="team-title">Our Branches</h2>
    <div class="branches-container">
        <div id="map">
            <iframe id="map-iframe" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14944280.62722007!2d55.72943211574409!3d23.87166907340386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x15e7b33fe7952a41%3A0x5960504bc21ab69b!2z2KfZhNiz2LnZiNiv2YrYqQ!5e0!3m2!1sar!2ssa!4v1740571872552!5m2!1sar!2ssa" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div id="sidebar">
            <h2>Branches</h2>
      <div class="branch" onclick="toggleBranchInfo(0); changeMap(0)">
    <h3>Oka - Dammam</h3>
    <div class="branch-info" style="display: none;">
        <p>Working Time:</p>
        <p>Sunday - Saturday: 9 AM - 4 PM</p>
        <p>Friday: 4 PM - 11 PM</p>
    </div>
</div>
<div class="branch" onclick="toggleBranchInfo(1); changeMap(1)">
    <h3>Oka - Khobar</h3>
    <div class="branch-info" style="display: none;">
        <p>Working Time:</p>
        <p>Sunday - Saturday: 9 AM - 4 PM</p>
        <p>Friday: 4 PM - 11 PM</p>
    </div>
</div>
<div class="branch" onclick="toggleBranchInfo(2); changeMap(2)">
    <h3>Oka - Jubail</h3>
    <div class="branch-info" style="display: none;">
        <p>Working Time:</p>
        <p>Sunday - Saturday: 9 AM - 4 PM</p>
        <p>Friday: 4 PM - 11 PM</p>
    </div>
</div>
        </div>
    </div>
</div>
<<script src="map.js"></script>
<?php
include 'footer.php'; 
?>
</body>
</html>