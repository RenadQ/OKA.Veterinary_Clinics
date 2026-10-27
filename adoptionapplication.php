<!---Team member-->
<?php
// Start the session
session_start();
include 'db_connect.php'; // Include database connection
// Function to validate user input
function validateInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize variables to hold form data and error messages
$name = $phone = $email = $streetAddress = $city = $zip = $age = $live = $medicalNeeds = $moreQuestions = '';
$qualities = [];
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = validateInput($_POST['name']);
    $phone = validateInput($_POST['phone']);
    $email = validateInput($_POST['email']);
    $streetAddress = validateInput($_POST['streetaddress']);
    $city = validateInput($_POST['city']);
    $zip = validateInput($_POST['zip']);
    $age = validateInput($_POST['age']);
    $live = validateInput($_POST['live']);
    $medicalNeeds = validateInput($_POST['medicalNeeds']);
    $qualities = isset($_POST['qualities']) ? implode(',', $_POST['qualities']) : ''; // Store as a comma-separated string
    $moreQuestions = validateInput($_POST['morequestion']);

    // Simple validation
    if (empty($name) || empty($phone) || empty($email) || empty($streetAddress) || empty($city) || empty($zip)) {
        $errors[] = "All fields are required.";
    }

    // If no errors, process the application
    if (empty($errors)) {
        // Prepare SQL statement
        $sql = "INSERT INTO adoption (Name, Phone, Email, StreetAddress, City, Zip, Age, Live, MedicalNeeds, Qualities, MoreQuestions) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare and bind
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssssssss", $name, $phone, $email, $streetAddress, $city, $zip, $age, $live, $medicalNeeds, $qualities, $moreQuestions);
            
            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['application_submitted'] = true;
                // Redirect to the same page to avoid resubmission on refresh
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errors[] = "Error submitting the application.";
            }
            $stmt->close();
        } else {
            $errors[] = "Error preparing the SQL statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="adoptionapplication.css">
	<link rel="stylesheet" href="header_styles.css">
    <title>Adoption Application</title>
	<script src="adoptionapplication.js"></script> <!-- Link to external JavaScript file -->
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Apply for Adoption - <span id="animal-name"></span></h2>
        
        <?php if (isset($_SESSION['application_submitted'])): ?>
            <div class="confirmation">
                Thank you! Your application has been submitted successfully.
            </div>
            <?php unset($_SESSION['application_submitted']); // Clear the session variable ?>
        <?php endif; ?>

        <form id="petForm" method="POST" action="">
            <div class="L1">Your Contact Information</div>
            <br>
            <label for="name"><strong>Your Name</strong></label>
            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">

            <label for="phone"><strong>Phone Number</strong></label>
            <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($phone); ?>">

            <label for="email"><strong>Email</strong></label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">

            <label for="streetaddress"><strong>Street Address</strong></label>
            <input type="text" id="streetaddress" name="streetaddress" required value="<?php echo htmlspecialchars($streetAddress); ?>">

            <label for="city"><strong>City</strong></label>
            <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($city); ?>">

            <label for="zip"><strong>Zip/Postal Code</strong></label>
            <input type="text" id="zip" name="zip" required value="<?php echo htmlspecialchars($zip); ?>">

            <div class="L1">Questions</div>
            <br>
            <label for="age"><strong>Age Range of Adopter</strong></label>
            <div class="radio-group">
                <label><input type="radio" name="age" value="lessthan20" required <?php if ($age === 'lessthan20') echo 'checked'; ?>> Less than 20 years old</label>
                <label><input type="radio" name="age" value="21-59" <?php if ($age === '21-59') echo 'checked'; ?>> 21-59 years old</label>
                <label><input type="radio" name="age" value="60+" <?php if ($age === '60+') echo 'checked'; ?>> 60+ years old</label>
            </div>

            <label for="live"><strong>Where will Your new Pet live?</strong></label>
            <div class="radio-group">
                <label><input type="radio" name="live" value="insideonly" required <?php if ($live === 'insideonly') echo 'checked'; ?>> Inside Only</label>
                <label><input type="radio" name="live" value="insideoutdoor" <?php if ($live === 'insideoutdoor') echo 'checked'; ?>> Inside with access to the outdoors</label>
                <label><input type="radio" name="live" value="outsideonly" <?php if ($live === 'outsideonly') echo 'checked'; ?>> Outside Only</label>
            </div>

            <label><strong>Are you willing to adopt a pet with pre-existing medical needs? </strong></label>
            <div class="radio-group">
                <label><input type="radio" name="medicalNeeds" value="yes" required <?php if ($medicalNeeds === 'yes') echo 'checked'; ?>> Yes</label>
                <label><input type="radio" name="medicalNeeds" value="no" <?php if ($medicalNeeds === 'no') echo 'checked'; ?>> No</label>
                <label><input type="radio" name="medicalNeeds" value="depends" <?php if ($medicalNeeds === 'depends') echo 'checked'; ?>> Depends on the medical needs</label>
            </div>

            <label><strong>What qualities are you looking for in an adopted pet? </strong></label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="qualities[]" value="calm" <?php if (in_array('calm', $qualities)) echo 'checked'; ?>> Calm</label>
                <label><input type="checkbox" name="qualities[]" value="active" <?php if (in_array('active', $qualities)) echo 'checked'; ?>> Active</label>
                <label><input type="checkbox" name="qualities[]" value="affectionate" <?php if (in_array('affectionate', $qualities)) echo 'checked'; ?>> Affectionate</label>
                <label><input type="checkbox" name="qualities[]" value="younger" <?php if (in_array('younger', $qualities)) echo 'checked'; ?>> Younger</label>
                <label><input type="checkbox" name="qualities[]" value="older" <?php if (in_array('older', $qualities)) echo 'checked'; ?>> Older</label>
                <label><input type="checkbox" name="qualities[]" value="goodWithChildren" <?php if (in_array('goodWithChildren', $qualities)) echo 'checked'; ?>> Good with Children</label>
            </div>

            <label for="morequestions"><strong>Is there anything else you think we should know?</strong></label>
            <input type="text" id="morequestions" name="morequestion" class="large-textbox" required value="<?php echo htmlspecialchars($moreQuestions); ?>">

            <div class="disclaimer">
                <strong>Disclaimer:</strong>
                <p>Thank you very much for your interest in adopting a pet. You will receive a response email shortly providing you more information.</p>
            </div>

            <div style="display: flex; justify-content: center; gap: 10px;">
                <button type="submit" class="action-button">Submit</button>
                <a href="Homepage.php" class="action-button">Back</a>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
