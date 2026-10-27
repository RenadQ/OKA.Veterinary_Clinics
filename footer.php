<!-- ===============================
//    Authors:
//    Team member  
//    Renad Alkahtani
//    ===============================  -->
<!-- footer.php -->
<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<div style="height: 80px;"></div>

<footer>
        <p><strong>Oka Petstore - Your Pet's Best Place</strong></p>
        <div class="social-icons">
            <a href="https://www.facebook.com/oka.vetclinics" target="_blank" title="Facebook">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="mailto:info@oka-vet.com" title="Email">
                <i class="fas fa-envelope"></i>
            </a>
            <a href="https://www.instagram.com/oka.vetclinics/" target="_blank" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.tiktok.com/@oka.clinic" target="_blank" title="TikTok">
                <i class="fab fa-tiktok"></i>
            </a>
            <a href="https://www.snapchat.com/add/oka.vetclinics" target="_blank" title="Snapchat">
                <i class="fab fa-snapchat-ghost"></i>
            </a>
            <a href="https://api.whatsapp.com/send?phone=966543606037" target="_blank" title="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
        <div class="footer-links">
            <a href="about Us.php">About</a>
            <a href="https://linktr.ee/oka.vet">Contact Us</a>
            <a href="https://api.whatsapp.com/send?phone=966543606037">Support</a>
            <a href="adopt.php">Adopt Pets</a>
            <a href="Services.php">Pet Care</a>
            <a href="signin.php">Register</a>
        </div>
    </footer>

<style>
footer {
            background-color: #002f5f;
            color: white;
            padding: 30px 20px;
            position: relative;

left: 0;
bottom: 0;
width: 100%;
            text-align: center;
            margin-top: 50px;
        }
        .social-icons {
            margin: 15px 0;
        }
        .social-icons a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }
        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
</style>