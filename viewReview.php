<?php
session_start();
include 'db_connect.php';

// تحقق من تسجيل دخول المشرف
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// تحقق من وجود معرف الموعد
if (!isset($_GET['appointment_id'])) {
    echo "معرف الموعد غير محدد.";
    exit();
}

$appointment_id = intval($_GET['appointment_id']);

// استعلام لجلب المراجعة المرتبطة بالموعد
$sql = "SELECT 
            r.rating,
            r.comment,
            r.created_at,
            u.name AS user_name
        FROM Review r
        JOIN Appointment a ON r.appointment_id = a.appointment_id
        JOIN User u ON r.user_id = u.user_id
        WHERE r.appointment_id = $appointment_id";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>عرض المراجعة</title>
    <link rel="stylesheet" href="Admin_Styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .review-container {
            margin: 50px auto;
            width: 60%;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #003366;
        }
        .review-details {
            margin-top: 20px;
        }
        .review-details p {
            margin: 10px 0;
        }
        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            text-decoration: none;
            color: #003366;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="review-container">
    <h2>تفاصيل المراجعة</h2>
    <?php
if ($result->num_rows > 0) {
    $review = $result->fetch_assoc();
?>
    <div class="review-details">
        <p><strong>User Name:</strong> <?php echo htmlspecialchars($review['user_name']); ?></p>
        <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?>/5</p>
        <p><strong>Review:</strong> <?php echo htmlspecialchars($review['comment']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($review['created_at']); ?></p>
    </div>
<?php
} else {
    echo "<p>No review found for this appointment.</p>";
}
?>
<a class="back-link" href="Appointment List.php">Back to Appointments</a>

 
