<?php
include 'db_connect.php';

// استعلام لجلب بيانات المستخدمين
$sql = "SELECT user_id, email, name, phone FROM User";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="user management.css">
	<title>User Management</title>
    
</head>
<body>
<?php include 'admin_Header_layout.php'; ?>

<div class="container">
    <h2>User Management</h2>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr data-user-id='{$row['user_id']}'>
                            <td>{$row['email']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['phone']}</td>
                            <td>
                                <button class='edit-btn' onclick='openEditModal(this)'>Edit</button>
                                <button class='delete-btn' onclick='deleteUser(this)'>Delete</button>
                                <button class='history-btn' onclick='showHistory(this)'>History</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No users found</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
    <div class="history">
        <h3>History</h3>
        <ul id="historyList"></ul>
    </div>
</div>
<div id="historyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeHistoryModal()">&times;</span>
        <h3>User History</h3>
        <h4>Orders</h4>
        <ul id="orderList"></ul>
        <h4>Service Bookings</h4>
        <ul id="serviceList"></ul>
    </div>
</div>

<script src="history.js"></script>

<div id="editModal" class="modal">
    <h3>Edit User</h3>
    <input type="text" id="editEmail" placeholder="Email">
    <input type="text" id="editName" placeholder="Name">
    <input type="text" id="editPhone" placeholder="Phone">
    <div class="buttons">
        <button class="save-btn" onclick="saveChanges()">Save</button>
        <button class="close-btn" onclick="closeEditModal()">Cancel</button>
    </div>
</div>

<script src="user management.js"></script>
</body>
</html>