<!-- 
===============================
  Author:
  Renad Alkahtani
  Team member
===============================
-->
<?php
include 'db_connect.php';

date_default_timezone_set('Asia/Riyadh');
$now = date('Y-m-d H:i:s');

// استعلام لجلب العروض المنتهية
$sql = "SELECT * FROM Offer WHERE end_date < ? AND discount_percentage IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $now);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $applies_to = $row['applies_to'];

    if ($applies_to === 'service' || $applies_to === 'all') {
        $conn->query("UPDATE Service SET price = original_price WHERE original_price IS NOT NULL");
    }

    if ($applies_to === 'product' || $applies_to === 'all') {
        $conn->query("UPDATE Product SET price = original_price WHERE original_price IS NOT NULL");
    }

    $conn->query("UPDATE Offer SET discount_percentage = NULL, is_active = 0 WHERE offer_id = " . intval($row['offer_id']));
}

header("Location: offersManagement.php?reverted=1");
exit();
?>
