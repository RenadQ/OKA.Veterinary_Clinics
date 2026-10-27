<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("لا يوجد تدوينة محددة.");
}

$blog_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM Blog WHERE blog_id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    die("لم يتم العثور على التدوينة.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($blog['title']); ?></title>
  <link rel="stylesheet" href="post.css">
</head>
<body>
  <div class="post-container">
    <div class="buttons">
      <button onclick="readPost()">🔊 Read</button>
      <button onclick="stopReading()">⏹️ Stop</button>
    </div>

    <h1 id="postTitle"><?php echo htmlspecialchars($blog['title']); ?></h1>

    <div class="image-container">
      <img id="postImage" src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="Post Image" />
    </div>

    <p id="postContent"><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>

    <div class="back-button">
      <a href="blogs.php">← Back to Blogs</a>
    </div>
  </div>

 <script src="post.js"></script>
</body>
</html>