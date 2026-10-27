<!-- 
===============================
  Author:
  Team member
===============================
-->

<?php
session_start();
include 'db_connect.php';

if (!isset($_POST['service_name'], $_POST['branch_id'], $_POST['booking_date'], $_POST['booking_time'], $_POST['amount'])) {
    header("Location: services.php");
    exit();
}

$service_name = $_POST['service_name'];
$branch_id = intval($_POST['branch_id']);
$booking_date = $_POST['booking_date'];
$booking_time = $_POST['booking_time'];
$amount = floatval($_POST['amount']);
$user_id = $_SESSION['id'] ?? null;

if (!$user_id) {
    die("User not logged in.");
}

// عند التأكيد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $method = $_POST['payment_method'];

    // جلب service_id
    $stmt = $conn->prepare("SELECT service_id FROM Service WHERE name = ? AND branch_id = ?");
    $stmt->bind_param("si", $service_name, $branch_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $service = $res->fetch_assoc();

    if ($service) {
        $service_id = $service['service_id'];
        $datetime = "$booking_date $booking_time:00";

        // الحجز
        $conn->query("INSERT INTO servicebooking (service_id, user_id, appointment_date, status_id)
                      VALUES ($service_id, $user_id, '$datetime', 1)");
        $booking_id = $conn->insert_id;

        // الدفع
        $conn->query("INSERT INTO payment (amount, status, method, service_booking_id)
                      VALUES ($amount, 'Paid', '$method', $booking_id)");

        //   رسالة نجاح
        header("Location: thankYou.php");
        exit();

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Payment - OKA Petstore</title>
    <link rel="stylesheet" href="services.css">
</head>
<body>
<div class="confirm-container">
    <h2>Confirm Your Booking and Payment</h2>

    <div class="booking-info">
        <p><strong>Service:</strong> <?php echo htmlspecialchars($service_name); ?></p>
        <p><strong>Branch ID:</strong> <?php echo $branch_id; ?></p>
        <p><strong>Date:</strong> <?php echo $booking_date; ?></p>
        <p><strong>Time:</strong> <?php echo $booking_time; ?></p>
        <p><strong>Amount to Pay:</strong> <?php echo $amount; ?> SAR</p>
    </div>

    <div class="section-divider"></div>

    <form method="POST">
        <input type="hidden" name="service_name" value="<?php echo htmlspecialchars($service_name); ?>">
        <input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
        <input type="hidden" name="booking_date" value="<?php echo $booking_date; ?>">
        <input type="hidden" name="booking_time" value="<?php echo $booking_time; ?>">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">

        <div class="payment-method">
            <label>Select Payment Method:</label><br><br>
            <input type="radio" name="payment_method" value="Card" required checked> Pay with Card<br><br>
            <input type="radio" name="payment_method" value="Apple Pay" required> Pay with Apple Pay<br>
        </div>

        <button type="submit" name="confirm_payment" class="orange-btn">Confirm Payment</button><br><br>
        <a href="services.php" class="edit-btn">Cancel</a>

    </form>
</div>
</body>
</html>
