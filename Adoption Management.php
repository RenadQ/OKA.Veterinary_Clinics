<?php
session_start();
include 'db_connect.php'; // Include database connection

// Load existing pets from the database
$result = $conn->query("SELECT * FROM pet");
$adoptions = $result->fetch_all(MYSQLI_ASSOC);

// Handle new adoption submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $petName = htmlspecialchars(trim($_POST['PetName']));
    $age = htmlspecialchars(trim($_POST['age']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $type = htmlspecialchars(trim($_POST['type']));
    $location = htmlspecialchars(trim($_POST['location']));
    $breed = htmlspecialchars(trim($_POST['breed']));
    $image = $_FILES['image'];

    // Validate inputs
    if (empty($petName) || empty($age) || empty($gender) || empty($type) || empty($location) || empty($breed) || $image['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Please fill in all fields and upload a valid image.');</script>";
    } else {
        // Define upload directory
        $uploadDir = 'uploads_adoption/';

        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Define the file path
        $filePath = $uploadDir . basename($image['name']);

        // Check if the file is an actual image
        if (getimagesize($image['tmp_name']) !== false) {
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($image['tmp_name'], $filePath)) {
                // Insert new adoption into the database
                $stmt = $conn->prepare("INSERT INTO pet (name, age, gender, type, location, breed, ImageURL) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sisssss", $petName, $age, $gender, $type, $location, $breed, $filePath);

                if ($stmt->execute()) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "<script>alert('Error: " . $stmt->error . "');</script>";
                    error_log("SQL Error: " . $stmt->error);
                }

                $stmt->close();
            } else {
                echo "<script>alert('Error uploading the file.');</script>";
            }
        } else {
            echo "<script>alert('Uploaded file is not an image.');</script>";
        }
    }
}

// Handle edit submission
if (isset($_POST['edit_id'])) {
    $editId = intval($_POST['edit_id']);
    $petName = htmlspecialchars(trim($_POST['PetName']));
    $age = htmlspecialchars(trim($_POST['age']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $type = htmlspecialchars(trim($_POST['type']));
    $location = htmlspecialchars(trim($_POST['location']));
    $breed = htmlspecialchars(trim($_POST['breed']));
    $image = $_FILES['image'];

    // Process image upload if a new image is uploaded
    if ($image['error'] == UPLOAD_ERR_OK) {
        // Define upload directory
        $uploadDir = 'uploads_adoption/';
        $filePath = $uploadDir . basename($image['name']);

        // Check if the file is an actual image
        if (getimagesize($image['tmp_name']) !== false) {
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($image['tmp_name'], $filePath)) {
                // Update the pet details
                $stmt = $conn->prepare("UPDATE pet SET name = ?, age = ?, gender = ?, type = ?, location = ?, breed = ?, ImageURL = ? WHERE pet_id = ?");
                $stmt->bind_param("sisssssi", $petName, $age, $gender, $type, $location, $breed, $filePath, $editId);
            } else {
                echo "<script>alert('Error uploading the new file.');</script>";
            }
        } else {
            echo "<script>alert('Uploaded file is not an image.');</script>";
        }
    } else {
        // If no new image, keep the existing image
        $stmt = $conn->prepare("SELECT ImageURL FROM pet WHERE pet_id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $filePath = $row['ImageURL'];
        $stmt->close();

        // Update the pet details without changing the image
        $stmt = $conn->prepare("UPDATE pet SET name = ?, age = ?, gender = ?, type = ?, location = ?, breed = ?, ImageURL = ? WHERE pet_id = ?");
        $stmt->bind_param("sisssssi", $petName, $age, $gender, $type, $location, $breed, $filePath, $editId);
    }

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
        error_log("SQL Error: " . $stmt->error);
    }

    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM pet WHERE pet_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="Adoption Management.css">
    <link rel="stylesheet" href="Admin_Styles.css">
    <title>Adoption Management</title>
	<script src="Adoption Management.js"></script> <!-- Link to the JavaScript file -->
</head>
<body>
    <?php include 'admin_Header_layout.php'; ?>

    <div class="main-content" style="margin-left: 220px;">
        <div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">
            <h2>Manage Adoption</h2>
            <form id="adoptionForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="edit_id" value="">
                <div class="input-fields">
                    <input type="text" name="PetName" placeholder="Pet Name" required>
                    <input type="number" name="age" placeholder="Age" required>
                    <input type="text" name="gender" placeholder="Gender" required>
                    <input type="text" name="type" placeholder="Type" required>
                    <input type="text" name="location" placeholder="Location" required>
                    <input type="text" name="breed" placeholder="Breed" required>
                    <input type="file" name="image" accept="image/*">
                    <input type="text" id="imageFileName" placeholder="Pet Image Selected" readonly style="cursor: not-allowed;">
                </div>
                <button type="submit">Save Adoption</button>
            </form>
            <button onclick="addNewAdoption()" style="margin-top: 20px;">Add New Pet</button>
            <h2>Adoption List</h2>
            <table id="adoptionTable">
                <thead>
                    <tr>
                        <th>Pet ID</th>
                        <th>Pet Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Breed</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adoptions as $adoption): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($adoption['pet_id']); ?></td>
                            <td><?php echo htmlspecialchars($adoption['name']); ?></td>
                            <td><?php echo htmlspecialchars($adoption['age']); ?></td>
                            <td><?php echo htmlspecialchars($adoption['gender']); ?></td>
                            <td><?php echo htmlspecialchars($adoption['type']); ?></td>
                            <td><?php echo htmlspecialchars($adoption['location']); ?></td>
                            <td><?php echo htmlspecialchars($adoption['breed']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($adoption['ImageURL']); ?>" alt="Pet Image" class="pet-image" style="width: 100px; height: auto;"></td>
                            <td>
                                <button onclick="editAdoption(<?php echo $adoption['pet_id']; ?>, '<?php echo addslashes(htmlspecialchars($adoption['name'])); ?>', <?php echo $adoption['age']; ?>, '<?php echo addslashes(htmlspecialchars($adoption['gender'])); ?>', '<?php echo addslashes(htmlspecialchars($adoption['type'])); ?>', '<?php echo addslashes(htmlspecialchars($adoption['location'])); ?>', '<?php echo addslashes(htmlspecialchars($adoption['breed'])); ?>', '<?php echo addslashes($adoption['ImageURL']); ?>')">Edit</button>
                                <button onclick="confirmDelete(<?php echo $adoption['pet_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>