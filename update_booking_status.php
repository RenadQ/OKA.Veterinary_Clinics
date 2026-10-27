<!-- 
===============================
  Author:
  Team member
===============================
-->
<?php

include 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $status_id = intval($_POST['status_id']);

    // تحديث حالة الحجز
    $stmt = $conn->prepare("UPDATE servicebooking SET status_id = ? WHERE booking_id = ?");
    $stmt->bind_param("ii", $status_id, $booking_id);
    $stmt->execute();
    $stmt->close();

    // جلب فرع الحجز لإعادة التوجيه إليه
    $branch_sql = "SELECT s.branch_id
                   FROM servicebooking b
                   JOIN Service s ON b.service_id = s.service_id
                   WHERE b.booking_id = $booking_id";
    $branch_result = $conn->query($branch_sql);
    $branch_id = 1; // fallback افتراضي

    if ($branch_row = $branch_result->fetch_assoc()) {
        $branch_id = $branch_row['branch_id'];
    }

    // إعادة التوجيه مع تحديد الفرع وتبويب الحجوزات وعرض رسالة النجاح
    header("Location: Services Management.php?branch_id=$branch_id&section=booking&success=1");
    exit();
} else {
    echo "Invalid request.";
}
