<?php
include 'db_connect.php';
$appointment_id = intval($_GET['appointment_id']);
$sql = "SELECT r.rating, r.comment, u.name AS name
        FROM review r
        JOIN appointment a ON r.appointment_id = a.appointment_id
        JOIN user u ON r.user_id = u.user_id
        WHERE r.appointment_id = $appointment_id";

$result = $conn->query($sql);

if($result->num_rows > 0){
    $review = $result->fetch_assoc();
    echo "<h2 style='color:#003366;'>Review Details</h2>";
    echo "<p><strong>User Name:</strong> " . htmlspecialchars($review['name']) . "</p>";
    echo "<p><strong>Rating:</strong> " . htmlspecialchars($review['rating']) . "/5</p>";
    echo "<p><strong>Comment:</strong> " . htmlspecialchars($review['comment']) . "</p>";
}else{
    echo "No review found.";
}
?>
