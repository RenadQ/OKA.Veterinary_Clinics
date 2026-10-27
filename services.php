
<?php
session_start();
$userId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
include 'db_connect.php';
include 'google_calendar.php';

$services_result = $conn->query("
    SELECT s.name, s.description, s.price, s.image_url, GROUP_CONCAT(s.branch_id) as branches
    FROM Service s
    GROUP BY s.name
");

$branches_result = $conn->query("SELECT * FROM branch");
$all_branches = [];
while ($row = $branches_result->fetch_assoc()) {
    $all_branches[$row['branch_id']] = $row['branch_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Services - OKA Petstore</title>
  <link rel="stylesheet" href="services.css">
  <link rel="stylesheet" href="header_styles.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="footer.css">
  <script src="services.js" defer></script> <!-- إضافة رابط لملف الجافا سكريبت -->
</head>
<body>

<?php include 'header.php'; ?>

<section class="services-image">
  <div class="image-overlay">
    <h2>Welcome to Our Services Page!</h2>
    <p>At OKA Pet Clinic, we are committed to providing the best care for your beloved pets...</p>
  </div>
  <img src="images/servvi.jpg" alt="Services Image" style="width: 100%; height: auto;">
</section>

<section class="services-section">
  <div class="container">
    <div class="sidebar">
      <h3 class="sidebar-title">Services</h3>
      <input type="text" class="search" placeholder="Search services..." oninput="searchServices()" />
      <div class="filter-buttons">
        <button class="filter-button" onclick="filterServices('All')">All</button>
        <button class="filter-button" onclick="filterServices('Dental')">Dental</button>
        <button class="filter-button" onclick="filterServices('Grooming')">Grooming</button>
        <button class="filter-button" onclick="filterServices('Health')">Health</button>
        <button class="filter-button" onclick="filterServices('Medical')">Medical</button>
        <button class="filter-button" onclick="filterServices('Lab')">Lab</button>
      </div>
    </div>

    <div class="services-container">
      <?php while ($service = $services_result->fetch_assoc()):
        $image = !empty($service['image_url']) ? $service['image_url'] : str_replace(' ', '', $service['name']) . '.jpg';
        $branch_ids = explode(',', $service['branches']);
      ?>
      <div class="service-card">
        <div class="service-info">
          <img src="images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
          <h3><?php echo htmlspecialchars($service['name']); ?></h3>
          <p><?php echo htmlspecialchars($service['description']); ?></p>
          <div class="price-container">
            <img src="images/SAR.JPEG" alt="SAR Icon">
            <p class="price"><?php echo htmlspecialchars($service['price']); ?> SAR</p>
          </div>
        </div>

        <!--  Book Now -->
        <form method="POST" action="confirm_payment.php">
          <input type="hidden" name="service_name" value="<?php echo htmlspecialchars($service['name']); ?>">
          <input type="hidden" name="amount" value="<?php echo htmlspecialchars($service['price']); ?>">

          <label for="branch_id">Choose Branch:</label>
          <select name="branch_id" required>
            <?php foreach ($branch_ids as $bid): ?>
              <option value="<?php echo $bid; ?>"><?php echo $all_branches[$bid]; ?></option>
            <?php endforeach; ?>
          </select><br><br>

          <label for="booking_date">Date:</label>
          <input type="date" name="booking_date" required><br><br>

          <label for="booking_time">Time:</label>
          <select name="booking_time" required>
            <option value="08:00">8:00 AM</option>
            <option value="09:00">9:00 AM</option>
            <option value="10:00">10:00 AM</option>
            <option value="11:00">11:00 AM</option>
            <option value="12:00">12:00 PM</option>
            <option value="13:00">1:00 PM</option>
            <option value="14:00">2:00 PM</option>
            <option value="15:00">3:00 PM</option>
          </select><br><br>

          <button type="submit" class="orange-btn">Book Now</button>
        </form>

        <!--  زر Google Calendar بعد الحجز -->
        <button onclick="handleCalendar(this)" 
                data-service="<?php echo htmlspecialchars($service['name']); ?>" 
                class="google-btn">
          Add to Google Calendar
        </button>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>