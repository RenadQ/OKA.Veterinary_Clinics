<?php
session_start();
include 'db_connect.php';
$adminName = $_SESSION['name'] ?? 'admin';
$adminId = $_SESSION['admin_id'] ?? null;
if (!$conn) {
    echo "Database connection failed";
    exit;
}

$validation_errors = array();
$emailErr = $phoneErr = "";
$addSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = trim($_POST['role']);
    $phone = trim($_POST['phone']);

    $checkEmail = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if (!$email) {
        $emailErr = "Email is required";
        $validation_errors[] = "Email is required";
    } else {
        $email_add = test_input($email);
        if (!filter_var($email_add, FILTER_VALIDATE_EMAIL)) {
            $validation_errors[] = "Invalid email format";
            $emailErr = "Invalid email format";
        }
        if ($result->num_rows > 0) {
            $validation_errors[] = "This email already exists";
            $emailErr = "This email already exists";
        }
    }

    if (strlen($phone) !== 9) {
        $phoneErr = "Phone number must be exactly 9 digits long.";
        $validation_errors[] = "Phone number must be exactly 9 digits long.";
    } elseif (substr($phone, 0, 1) !== '5') {
        $phoneErr = "Phone number must start with the digit '5'.";
        $validation_errors[] = "Phone number must start with the digit '5'.";
    } elseif (!ctype_digit($phone)) {
        $phoneErr = "Phone number must contain only digits.";
        $validation_errors[] = "Phone number must contain only digits.";
    }

    $checkPhone = $conn->prepare("SELECT * FROM admin WHERE phone = ?");
    $checkPhone->bind_param("s", $phone);
    $checkPhone->execute();
    $resultPhone = $checkPhone->get_result();
    if ($resultPhone->num_rows > 0) {
        $phoneErr = "This phone number already exists";
        $validation_errors[] = "This phone number already exists";
    }

    if (empty($validation_errors)) {
        $stmt = $conn->prepare("INSERT INTO admin (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $phone);
        $stmt->execute();
        $stmt->close();

        $addSuccess = true;
        header("Location: Adminmanagement.php");
        exit();
    }
    $checkEmail->close();
}

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_GET['delete_id'])) {
    $admin_id = intval($_GET['delete_id']);
    $delete_stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
    $delete_stmt->bind_param("i", $admin_id);
    $delete_stmt->execute();
    header("Location: Adminmanagement.php");
    exit();
}

$admins = [];
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        unset($row['password']);
        $admins[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Management</title>
    <link rel="stylesheet" href="Adminmangment_css.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        function openModal() {
            document.getElementById('addAdminModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('addAdminModal').style.display = 'none';
        }
        function showAdminDetails(admin_id, name, email, role, phone) {
            const detailsContainer = document.getElementById('admin-details');
            detailsContainer.innerHTML = `
                <h3>Admin Details</h3>
                <form method="POST" action='AdminUpdateScript.php'>
                    <input type="hidden" name="admin_id" value="${admin_id}">
                    <p><strong>Name:</strong> <input type="text" name="name" value="${name}" required></p>
                    <p><strong>Email:</strong> <input type="email" name="email" value="${email}" required></p>
                    <p><strong>Role:</strong> <input type="text" name="role" value="${role}" required></p>
                    <p><strong>Phone:</strong> <input type="text" name="phone" value="${phone}" required></p>
                    <button class="btn btn-success" type="submit" name="edit_admin" value="1">Update Admin</button>
                </form>
                <button class="remove_admin btn btn-danger" id="${admin_id}" onclick="confirmDelete(${admin_id})">Delete Admin</button>
            `;
        }
        function confirmDelete(adminId) {
            if (confirm('Are you sure to remove this record ?')) {
                $.ajax({
                    url: 'AdminDeleteScript.php',
                    type: 'GET',
                    data: { id: adminId },
                    success: function(data) {
                        $("#" + adminId).remove();
                        alert("Record removed successfully");
                        location.reload();
                    },
                    error: function(error) {
                        alert('Something is wrong');
                    }
                });
            }
        }
    </script>
</head>
<body>
    <div id="sidebar">
        <h2>Admin Management</h2>
        <div id="admin-list">
            <?php foreach ($admins as $admin): ?>
                <div class="admin-card" onclick="showAdminDetails(<?php echo $admin['admin_id']; ?>, '<?php echo htmlspecialchars($admin['name']); ?>', '<?php echo htmlspecialchars($admin['email']); ?>', '<?php echo htmlspecialchars($admin['role']); ?>', '<?php echo htmlspecialchars($admin['phone']); ?>')">
                    <div>
                        <strong><?php echo htmlspecialchars($admin['name']); ?></strong> (<?php echo htmlspecialchars($admin['email']); ?>)
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button onclick="document.getElementById('addAdminModal').style.display='block'">Add New Admin</button>
        <br><br>
        <a href="superadminhomepage1.php" class="btn btn-primary" style="
            background-color: #337ab7;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
        ">Back to Home</a>
    </div>

    <div id="content">
        <h2>Admin Details</h2>
        <?php foreach ($validation_errors as $error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <div id="admin-details" class="admin-details">
            <p>Select an admin to see their details.</p>
        </div>
    </div>

    <div id="addAdminModal" class="modal">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h2>Add New Admin</h2>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" placeholder="Admin Name" required />
                    <input type="email" name="email" placeholder="Admin Email" required />
                    <span class="error text-danger">* <?php echo $emailErr;?></span>
                    <input type="password" name="password" placeholder="Password" required />
                    <input type="text" name="role" placeholder="Admin Role" required />
                    <input type="text" name="phone" placeholder="Admin Phone Number" required />
                    <span class="error text-danger">* <?php echo $phoneErr;?></span>
                    <input type="hidden" name="add_admin" value="1">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Save Changes</button>
                    <button class="btn btn-danger" type="button" onclick="closeModal()">Close</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
