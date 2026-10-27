<?php
session_start();
include 'db_connect.php'; // تضمين الاتصال بقاعدة البيانات

// جلب البلوقات من قاعدة البيانات
$result = $conn->query("SELECT * FROM blog");
$blogs = $result->fetch_all(MYSQLI_ASSOC);

// التعامل مع إضافة بلوق جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $category = htmlspecialchars(trim($_POST['category']));
    $image = $_FILES['image'];

    // التحقق من المدخلات
    if (empty($title) || empty($content) || empty($category) || $image['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Please fill in all fields and upload a valid image.');</script>";
    } else {
       $imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $targetPath = 'uploads_blogs/' . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $targetPath;
    } else {
        echo "<script>alert('Failed to upload image.');</script>";
    }
}


        // إدخال البلوق الجديد في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO blog (title, content, category, image_url, date) VALUES (?, ?, ?, ?, NOW())");
       $stmt->bind_param("ssss", $title, $content, $category, $imagePath);


        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
            error_log("SQL Error: " . $stmt->error);
        }

        $stmt->close();
    }
}

// التعامل مع تعديل بلوق
if (isset($_POST['edit_id'])) {
    $editId = intval($_POST['edit_id']);
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $category = htmlspecialchars(trim($_POST['category']));
    $image = $_FILES['image'];

    // معالجة تحميل الصورة إذا كانت صورة جديدة
    if ($image['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($image['tmp_name']);
        $base64Image = 'data:' . $image['type'] . ';base64,' . base64_encode($imageData);
    } else {
        // إذا لم يتم تحميل صورة جديدة، استخدم الصورة الموجودة
        $stmt = $conn->prepare("SELECT image_url FROM blog WHERE blog_id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $base64Image = $row['image_url'];
        $stmt->close();
    }

    // تحديث البلوق
    $stmt = $conn->prepare("UPDATE blog SET title = ?, content = ?, category = ?, image_url = ? WHERE blog_id = ?");
    $stmt->bind_param("ssssi", $title, $content, $category, $base64Image, $editId);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
        error_log("SQL Error: " . $stmt->error);
    }

    $stmt->close();
}

// التعامل مع طلب الحذف
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM blog WHERE blog_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// إغلاق الاتصال
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs Management</title>
    <link rel="stylesheet" href="Blogs Management.css">
    <script src="Blogs Mangement.js"></script>
</head>
<body>
<?php include 'admin_Header_layout.php'; ?>

 <div class="main-content" style="margin-left: 220px;">
       <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h2>Blog Management</h2>


<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="edit_id" id="edit_id" value="">
    <input type="hidden" name="edit_id" id="edit_id" value="">
    <input type="text" name="title" placeholder="Blog Title" required>
    <textarea name="content" placeholder="Blog Content" required></textarea>
    <select name="category" required>
        <option value="Dogs">Dogs</option>
        <option value="Cats">Cats</option>
        <option value="Birds">Birds</option>
        <option value="All Animals">All Animals</option>
    </select>
    <input type="file" name="image" accept="image/*">
    <button type="submit">Save Blog</button>
</form>

<h3>Existing Blogs</h3>
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($blogs as $blog): ?>
            <tr>
                <td><?php echo htmlspecialchars($blog['title']); ?></td>
                <td><?php echo htmlspecialchars($blog['date']); ?></td>
                <td>
                    <button onclick="editBlog(<?php echo $blog['blog_id']; ?>, '<?php echo addslashes(htmlspecialchars($blog['title'])); ?>', '<?php echo addslashes(htmlspecialchars($blog['content'])); ?>', '<?php echo addslashes(htmlspecialchars($blog['category'])); ?>', '<?php echo addslashes($blog['image_url']); ?>')">Edit</button>
                    <a href="?delete_id=<?php echo $blog['blog_id']; ?>" class="delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</body>
</html>
