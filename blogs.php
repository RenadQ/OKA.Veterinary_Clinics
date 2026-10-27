
<!DOCTYPE html>
<html lang="en">
<head>
<!--Team member-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OKA Petstore - Blog</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="header_styles.css">
    <link rel="stylesheet" href="blogs.css">
	<link rel="stylesheet" href="contactstyle.css">
	<script src="ContactUs.js"></script>
</head>
<body>
<?php include 'header.php'; ?>

<div class="sidebar">
    <h3>Categories</h3>
    <ul>
        <li><a href="#" onclick="filterPosts('all')">All</a></li>
        <li><a href="#" onclick="filterPosts('cats')">Cats</a></li>
        <li><a href="#" onclick="filterPosts('dogs')">Dogs</a></li>
        <li><a href="#" onclick="filterPosts('rabbits')">Rabbits</a></li>
    </ul>
</div>

<div class="blog-container" id="blogContainer"></div>
 
<script src="blogs.js"></script>

<?php include 'footer.php'; ?>
</body>
</html>