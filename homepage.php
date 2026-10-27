<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title>OKA Petstore</title>
    <link rel="stylesheet" href="homepage_css.css">
    <link rel="stylesheet" href="header_styles.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="cookiescript.js" defer></script>
    
</head>
<body>
<body>

<?php include 'header.php'; ?>

<section class="hero">
  <div class="hero-text">
    <h1>Oka, Your Pet's First<br>Destination</h1>
  </div>
</section>

<!-- خدمات بتمرير تلقائي -->
<section class="services-section-home">
  <h2>Our Services</h2>
  <div class="services-container">
    <?php
    $result = mysqli_query($conn, "SELECT * FROM service");
    if ($result && mysqli_num_rows($result) > 0):
      $services = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
      }
      // تكرار العناصر مرتين لضمان تمرير سلس
      for ($i = 0; $i < 2; $i++):
        foreach ($services as $service):
    ?>
      <div class="service-home-card">
  <img src="images/<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
  <h3><?php echo htmlspecialchars($service['name']); ?></h3>
  <p><?php echo htmlspecialchars($service['description']); ?></p>
</div>

    <?php
        endforeach;
      endfor;
    else:
      echo "<p style='text-align:center;'>No services found.</p>";
    endif;
    ?>
  </div>
</section>

<!-- قصص الفيديو -->
<section class="our-stories">
  <h2>Our Stories</h2>
  <div class="video-container">
    <div class="video-card"><video controls><source src="ourstories1.mp4" type="video/mp4"></video><h3>الفحص الدوري وميلا</h3></div>
    <div class="video-card"><video controls><source src="ourstories5.mp4" type="video/mp4"></video><h3>الولادة القيصرية ولوسي</h3></div>
    <div class="video-card"><video controls><source src="ourstories3.mp4" type="video/mp4"></video><h3>رحلة تعافي لوكي</h3></div>
    <div class="video-card"><video controls><source src="ourstories4.mp4" type="video/mp4"></video><h3>نانا وعملية التعقيم</h3></div>
    <div class="video-card"><video controls><source src="ourstories2.mp4" type="video/mp4"></video><h3>حملة اطعام</h3></div>
  </div>
</section>

<!-- التبني -->
<section class="adopt-home-section">
  <a href="#" class="join-group">Join Group</a>
</section>


    <!-- Cookie Consent Box -->
    <div class="wrapper" id="cookieConsent">
        <button class="close-btn" onclick="closeCookieConsent()">&times;</button>
        <img src="cookie.png" alt="Pet Icon" width="120">
        <div class="content">
            <header>Cookies Consent</header>
            <p>This website uses cookies to ensure you get the best experience on our website.</p>
            <div class="buttons">
                <button class="item" id="acceptCookies">I understand</button>
                <a href="#" class="item">Learn more</a>
            </div>
        </div>
    </div>


  <!-- footer calling  -->
<?php include 'footer.php'; ?>  
</body>
</html>
<!-- Team member  -->