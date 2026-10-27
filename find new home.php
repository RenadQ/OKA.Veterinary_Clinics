<!DOCTYPE html>
<html lang="en">
<head>
<!--Team member-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="header_styles.css">
	<link rel="stylesheet" href="find new home.css">
    <title>Find Your Pet a New Home</title>
	<script src="find new home.js" defer></script> <!-- Link to JavaScript file -->
    
</head>
<body>
        <?php include 'header.php'; ?>
    <div class="header-space"></div>
    <br>
    <div class="container">
        <h2 style="margin-bottom: 20px;"><strong>Find Your Pet a New Home</strong></h2>
        <form id="petForm">
            <label for="name"><strong>Your Name</strong></label>
            <input type="text" id="name" name="name" required>
            <label for="phone"><strong>Phone Number</strong></label>
            <input type="tel" id="phone" name="phone" required>
            <label for="email"><strong>Email</strong></label>
            <input type="email" id="email" name="email" required>
            <label for="petName"><strong>Your Pet's Name</strong></label>
            <input type="text" id="petName" name="petName" required>
            <label for="gender"><strong>Your Pet's Gender</strong></label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="female" required> Female</label>
                <label><input type="radio" name="gender" value="male"> Male</label>
            </div>
            <label for="petType"><strong>Your Pet Type</strong></label>
            <select id="petType" name="petType" required>
                <option value="">Select Pet Type</option>
                <option value="cat">Cat</option>
                <option value="dog">Dog</option>
                <option value="other">Other</option>
            </select>
            <label for="breed"><strong>Your Pet's Breed</strong></label>
            <input type="text" id="breed" name="breed" required>
            <label for="age"><strong>Your Pet's Age</strong></label>
            <input type="text" id="age" name="age" required>
            <label for="location"><strong>Your Pet's Location</strong></label>
            <input type="text" id="location" name="location" required>
            <label for="petImage"><strong>Upload a Picture of Your Pet</strong></label>
            <input type="file" id="petImage" name="petImage" accept="image/*" required style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: center; gap: 10px;">
                <button type="submit" class="action-button">Submit</button>
                <a href="Homepage.html" class="action-button">Back</a>
            </div>
        </form>
        <div class="confirmation" id="confirmationMessage" style="display:none;">
            Thank you! Your application has been submitted successfully.
        </div>
    </div>

        <?php include 'footer.php'; ?>
</body>
</html>