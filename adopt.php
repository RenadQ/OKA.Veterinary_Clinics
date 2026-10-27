<!---Team member-->
<?php
session_start();
include 'db_connect.php'; // Include database connection

// Load existing adoptions from the database
$result = $conn->query("SELECT * FROM pet");
$adoptions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="header_styles.css">
    <link rel="stylesheet" href="adopt.css">
    <title>Adopt Your Pet - OKA Petstore</title>
	<script src="adopt.js"></script> <!-- Link to JavaScript file -->
</head>
<body>
    
    <?php include 'header.php'; ?>

    <div class="header-space"></div>

    <div class="content-container">
        <div class="image-container">
            <img src="Animals.png" alt="Cute Animals">
        </div>
        
        <div class="text-container">
            <h1>Adopt Your Pet</h1>
            <p>Thinking about welcoming a new companion animal into your family? Many cute pets need a home, join us in OKA Adoption Group and meet your new pet!</p>
            <div>
                <a href="https://ig.me/j/AbbUvOCf0l5o5fQA/" class="join-group-button">Join Group</a>
            </div>
            <a href="find new home.php" class="new-home-button">Want to find your pet a new home?</a>
        </div>
    </div>

    <div class="meet-our-pets">
        <h2>Meet Our Pets</h2>
    </div>

    <div id="adoptContainer" class="pet-list">
    <?php if (empty($adoptions)): ?>
        <p>No pets available for adoption at this time.</p>
    <?php else: ?>
        <?php foreach ($adoptions as $adoption): ?>
            <div class="pet-card">
                <img src="<?php echo htmlspecialchars($adoption['ImageURL']); ?>" alt="<?php echo htmlspecialchars($adoption['name']); ?>">
                <div class="overlay">
                    <div>
                        <h3><?php echo htmlspecialchars($adoption['name']); ?></h3>
                        <p>Age: <?php echo htmlspecialchars($adoption['age']); ?></p>
                        <p>Gender: <?php echo htmlspecialchars($adoption['gender']); ?></p>
                        <p>Type: <?php echo htmlspecialchars($adoption['type']); ?></p>
                        <p>Location: <?php echo htmlspecialchars($adoption['location']); ?></p>
                        <p>Breed: <?php echo htmlspecialchars($adoption['breed']); ?></p>
                    </div>
                </div>
                <button class="apply-button" onclick="location.href='adoptionapplication.php?animal=<?php echo urlencode($adoption['name']); ?>'">Apply for Adoption</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>