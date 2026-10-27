<?php
include 'db_connect.php';
session_start();
if (
    isset($_POST['offer_name'], $_POST['discount_percentage'], 
          $_POST['usage_limit'], $_POST['start_date'], $_POST['end_date'], $_POST['applies_to'])
) {
    $name = $_POST['offer_name'];
    $discount = floatval($_POST['discount_percentage']);
    $limit = intval($_POST['usage_limit']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $applies_to = $_POST['applies_to'];
    $admin_id = $_SESSION['admin_id'] ?? null;

    $stmt = $conn->prepare("INSERT INTO offer (admin_id, offer_name, discount_percentage, start_date, end_date, usage_limit, usage_count, applies_to, is_active) 
                            VALUES (?, ?, ?, ?, ?, ?, 0, ?, 1)");
    $stmt->bind_param("isdssis", $admin_id, $name, $discount, $start, $end, $limit, $applies_to);

    if ($stmt->execute()) {
        // 1. حفظ السعر الأصلي مرة واحدة (إذا null فقط)
        if ($applies_to === 'service' || $applies_to === 'all') {
            $conn->query("UPDATE Service SET original_price = price WHERE original_price IS NULL");
            $conn->query("UPDATE Service SET price = original_price - (original_price * ($discount / 100))");
        }

        if ($applies_to === 'product' || $applies_to === 'all') {
            $conn->query("UPDATE Product SET original_price = price WHERE original_price IS NULL");
            $conn->query("UPDATE Product SET price = original_price - (original_price * ($discount / 100))");
        }

        header("Location: Offers Management.php?success=1");
        exit();
    } else {
        echo "Error inserting offer: " . $stmt->error;
    }

} else {
    echo "Missing required fields.";
}
?>
<!-- 
===============================
  Author:
  Renad Alkahtani
  Team member
===============================
-->
