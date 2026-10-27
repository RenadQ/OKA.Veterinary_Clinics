<?php
session_start();
include 'db_connect.php';
// استعلام SQL
$sql = "SELECT blog_id, title, category, image_url FROM Blog"; // تأكد من اسم الجدول والأعمدة
$result = $conn->query($sql);

// تحقق من الأخطاء في الاستعلام
if (!$result) {
    die("Query failed: " . $conn->error);
}

$blogs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    echo json_encode($blogs);
} else {
    echo json_encode([]); // إذا لم تكن هناك مدونات
}

$conn->close();
?>
