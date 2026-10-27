<?php
session_start();
include_once 'db_connect.php';

$userId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
?>
<!-- Team member -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy and Terms of Service</title>
   
    <link rel="stylesheet" href=" privacy_css.css">

    <style>
       
    </style>
</head>
<body>
    <div class="container">
        <h1>Privacy Policy and Terms of Service</h1>
        <p>At Oka, we are committed to protecting your privacy and ensuring a safe and secure shopping experience. By using our website, you agree to both our Privacy Policy and Terms of Service. Please review the following information carefully.</p>

        <h2 class="main-title">Privacy Policy</h2>
        <p>This Privacy Policy explains how we collect, use, and protect your personal information when you visit our website, create an account, or make purchases.</p>

        <div class="section">
            <h3>1. Information We Collect</h3>
            <p>We may collect the following types of information from you:</p>
            <ol>
                <li><strong>Personal Information</strong>
                    <ul>
                        <li>Contact Information: Name, email address, phone number, billing and shipping address.</li>
                        <li>Account Information: Username, password, and account preferences when you create an account.</li>
                        <li>Payment Information: Credit/debit card details, PayPal information, and other payment-related information necessary to process transactions.</li>
                        <li>Order Details: Products purchased, order history, and shipping information.</li>
                        <li>Communication Records: Messages or inquiries sent to our customer service team.</li>
                    </ul>
                </li>
                <li><strong>Non-Personal Information</strong>
                    <ul>
                        <li>Device Information: Browser type, operating system, device type (mobile, tablet, desktop).</li>
                        <li>Usage Data: IP address, pages visited, time spent on the site, and referring/exit pages.</li>
                        <li>Cookies and Tracking Technologies: Data collected through cookies, pixels, and similar technologies to improve your user experience and analyze website traffic.</li>
                    </ul>
                </li>
            </ol>
        </div>

        <div class="section">
            <h3>2. How We Use Your Information</h3>
            <p>We use your information to:</p>
            <ol>
                <li>Process Orders: Fulfill orders, send order confirmations, and provide shipping updates.</li>
                <li>Provide Customer Support: Respond to your inquiries, requests, and complaints.</li>
                <li>Improve Our Services: Analyze website performance, troubleshoot technical issues, and enhance user experience.</li>
                <li>Personalize Your Experience: Show relevant products, recommendations, and promotional offers.</li>
                <li>Send Marketing Communications: Send newsletters, promotional emails, and special offers (only if you have opted in).</li>
                <li>Ensure Security: Detect and prevent fraudulent activity and protect the integrity of our website.</li>
            </ol>
        </div>

        <div class="section">
            <h3>3. Sharing Your Information</h3>
            <p>We do not sell, rent, or trade your personal information to third parties. However, we may share your information with trusted service providers who help us operate our business.</p>
            <ul>
                <li>Payment Processors: To process payments securely.</li>
                <li>Shipping Partners: To deliver your orders to your address.</li>
                <li>IT and Website Support: To maintain and improve the functionality of our website.</li>
                <li>Marketing Service Providers: To manage email marketing campaigns and promotional offers.</li>
            </ul>
        </div>

        <div class="section">
            <h3>4. Cookies and Tracking Technologies</h3>
            <p>Our website uses cookies and similar tracking technologies to enhance your browsing experience and improve website functionality. You can manage your cookie preferences through your browser settings.</p>
        </div>

        <div class="section">
            <h3>5. Data Security</h3>
            <p>We implement reasonable security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>
        </div>

        <div class="section">
            <h3>6. Your Rights</h3>
            <p>You have the following rights regarding your personal information:</p>
            <ul>
                <li><strong>Access:</strong> Request a copy of the personal information we hold about you.</li>
                <li><strong>Correction:</strong> Update or correct inaccurate or incomplete information.</li>
                <li><strong>Deletion:</strong> Request the deletion of your personal data.</li>
                <li><strong>Opt-Out:</strong> Unsubscribe from marketing emails.</li>
            </ul>
        </div>

        <div class="section">
            <h3>7. Changes to This Privacy Policy</h3>
            <p>We may update this Privacy Policy from time to time to reflect changes in our practices. Any updates will be posted on this page.</p>
        </div>

        <h2 class="main-title">Terms of Service</h2>
        <p>By accessing or using Oka’s website, you agree to comply with these Terms of Service.</p>

        <div class="section">
            <h3>1. Use of the Website</h3>
            <p>You agree to use our website for lawful purposes only. Prohibited activities include:</p>
            <ul>
                <li>Attempting to gain unauthorized access to our website.</li>
                <li>Engaging in fraudulent, abusive, or illegal activity.</li>
                <li>Uploading or distributing harmful software.</li>
                <li>Using automated scripts or bots.</li>
            </ul>
        </div>

        <div class="section">
            <h3>2. Account Registration</h3>
            <p>If you create an account, you are responsible for maintaining the confidentiality of your login credentials.</p>
        </div>

        <div class="section">
            <h3>3. Orders and Payments</h3>
            <p>When you place an order, you agree to provide accurate billing and shipping information. We reserve the right to refuse any order.</p>
        </div>

        <div class="section">
            <h3>4. Product Information and Availability</h3>
            <p>We strive to provide accurate product descriptions, but we do not guarantee that all information is error-free.</p>
        </div>

        <div class="section">
            <h3>5. Shipping and Returns</h3>
            <p>Please review our separate Shipping and Return Policy for details on shipping timelines and return eligibility.</p>
        </div>

        <div class="section">
            <h3>6. Intellectual Property</h3>
            <p>All content on our website is the property of Oka and is protected by copyright laws.</p>
        </div>

        <div class="section">
            <h3>7. Third-Party Links</h3>
            <p>We may include links to third-party websites for your convenience, but we are not responsible for their content.</p>
        </div>

        <div class="section">
            <h3>8. Disclaimer of Warranties</h3>
            <p>Our website and products are provided "as is" without warranties of any kind.</p>
        </div>

        <div class="section">
            <h3>9. Limitation of Liability</h3>
            <p>Oka is not liable for any damages resulting from your use of the website.</p>
        </div>

        <div class="section">
            <h3>10. Indemnification</h3>
            <p>You agree to indemnify Oka from any claims arising from your violation of these Terms of Service.</p>
        </div>

        <div class="section">
            <h3>11. Changes to These Terms</h3>
            <p>We may update these Terms of Service at any time. Any changes will be posted on this page.</p>
        </div>

        <div class="section">
            <h3>12. Governing Law</h3>
            <p>These Terms of Service are governed by the laws of Saudi Arabia.</p>
        </div>

        <div class="checkbox-section">
            <input type="checkbox" id="terms-checkbox" checked>
            <label for="terms-checkbox">I accept the Privacy Policy and Terms of Service</label>
        </div>
        <div class="button-container">
            <button id="continue-button">Continue</button>
            <button onclick="window.location.href='sitting.php'">Back to Settings</button>
            <button id="home-button" onclick="goToHomePage()">Back to Home</button>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Oka. All rights reserved.</p>
    </div>

    <div id="warningModal" class="modal">
        <div class="modal-content">
            <p>You will not be able to use the website without accepting terms.</p>
            <button id="cancel-button">Cancel</button>
            <button id="confirm-button">OK</button>
        </div>
    </div>

    <script>
        const termsCheckbox = document.getElementById('terms-checkbox');
        const warningModal = document.getElementById('warningModal');
        const cancelButton = document.getElementById('cancel-button');
        const confirmButton = document.getElementById('confirm-button');

        termsCheckbox.addEventListener('change', function() {
            if (!this.checked) {
                warningModal.style.display = "block";
                termsCheckbox.checked = true;
            }
        });

        cancelButton.addEventListener('click', function() {
            warningModal.style.display = "none";
        });

        confirmButton.addEventListener('click', function() {
            warningModal.style.display = "none";
            termsCheckbox.checked = false;
        });

        document.getElementById('continue-button').addEventListener('click', function() {
            if (!termsCheckbox.checked) {
                alert("You must accept the terms to continue.");
            } else {
                alert("Thank you for accepting the terms!");
            }
        });

        function goToHomePage() {
            if (!termsCheckbox.checked) {
                alert("You must accept the terms to go back to the homepage.");
            } else {
                window.location.href = 'homepage.php';
            }
        }
    </script>
</body>
</html>