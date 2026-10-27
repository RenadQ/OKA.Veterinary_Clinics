<?php
session_start(); 
include 'db_connect.php';

// Handle status update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = filter_var($_POST['order_id'], FILTER_SANITIZE_NUMBER_INT);
    $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

    if (!in_array($status, ['Pending', 'Confirmed', 'Cancelled', 'Completed', 'Received'])) {
        echo "Invalid status";
        exit;
    }

    if ($status == 'Received') {
        $status = 'Completed';
    }

    $update_query = "UPDATE `Order` SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $status, $order_id);

    if ($stmt->execute()) {
        echo "SUCCESS";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
    exit;
}

$searchTerm = isset($_POST['search']) ? filter_var($_POST['search'], FILTER_SANITIZE_STRING) : '';
$statusFilter = isset($_POST['statusFilter']) ? filter_var($_POST['statusFilter'], FILTER_SANITIZE_STRING) : '';

$query = "SELECT o.order_id, u.name AS client_name, u.phone, o.status AS order_status, o.total_amount, o.order_date, o.pickup_date_time
          FROM `Order` o
          JOIN User u ON o.user_id = u.user_id
          GROUP BY o.order_id";

if ($searchTerm) {
    $query .= " HAVING client_name LIKE ? OR phone LIKE ? OR order_status LIKE ?";
}
if ($statusFilter && $statusFilter != 'All') {
    $query .= $searchTerm ? " AND order_status = ?" : " HAVING order_status = ?";
}
$query .= " ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);

if ($searchTerm && $statusFilter && $statusFilter != 'All') {
    $searchTermLike = "%$searchTerm%";
    $stmt->bind_param('ssss', $searchTermLike, $searchTermLike, $searchTermLike, $statusFilter);
} elseif ($searchTerm) {
    $searchTermLike = "%$searchTerm%";
    $stmt->bind_param('sss', $searchTermLike, $searchTermLike, $searchTermLike);
} elseif ($statusFilter && $statusFilter != 'All') {
    $stmt->bind_param('s', $statusFilter);
}
$stmt->execute();
$result = $stmt->get_result();

$dashboard_query = "SELECT status, COUNT(DISTINCT order_id) AS order_count FROM `Order` GROUP BY status";
$dashboard_result = $conn->query($dashboard_query);
$order_counts = [];
while ($row = $dashboard_result->fetch_assoc()) {
    $order_counts[$row['status']] = $row['order_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Management</title>
    <link rel="stylesheet" href="Product_M_styles.css">
    <link rel="stylesheet" href="order_mangment_css.css">
    
</head>
<body>
<?php include 'admin_Header_layout.php'; ?>
<div class="container">
    <h2>Orders Management</h2>
    <div class="dashboard">
        <div><h4>Pending</h4><span><?php echo $order_counts['Pending'] ?? 0; ?></span></div>
        <div><h4>Confirmed</h4><span><?php echo $order_counts['Confirmed'] ?? 0; ?></span></div>
        <div><h4>Cancelled</h4><span><?php echo $order_counts['Cancelled'] ?? 0; ?></span></div>
        <div><h4>Completed</h4><span><?php echo $order_counts['Completed'] ?? 0; ?></span></div>
    </div>
    <form method="POST">
        <input type="text" name="search" class="search-bar" placeholder="Search Orders..." value="<?php echo htmlspecialchars($searchTerm); ?>">
    </form>
    <form method="POST">
        <select name="statusFilter" class="status-filter" onchange="this.form.submit()">
            <option value="All">All Statuses</option>
            <option value="Pending" <?php if ($statusFilter == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Confirmed" <?php if ($statusFilter == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
            <option value="Cancelled" <?php if ($statusFilter == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
            <option value="Completed" <?php if ($statusFilter == 'Completed') echo 'selected'; ?>>Completed</option>
        </select>
    </form>
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Client</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Total</th>
                <th>Pickup</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($order = $result->fetch_assoc()): ?>
            <tr id="order-<?php echo $order['order_id']; ?>">
                <td>ORD-<?php echo $order['order_id']; ?></td>
                <td><?php echo $order['client_name']; ?></td>
                <td><?php echo $order['phone']; ?></td>
                <td><?php echo $order['order_status']; ?></td>
                <td>$<?php echo $order['total_amount']; ?></td>
                <td><?php echo $order['pickup_date_time'] ?? 'N/A'; ?></td>
                <td>
                    <?php if (!in_array($order['order_status'], ['Completed', 'Cancelled'])): ?>
                    <button class="received-btn" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'Received')">Received</button>
                    <?php endif; ?>
                    <a class="view-detail-btn" href="?order_id=<?php echo $order['order_id']; ?>">View Details</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php if (isset($_GET['order_id'])): ?>
<?php
    $order_id = intval($_GET['order_id']);
    $stmt = $conn->prepare("SELECT o.order_id, u.name AS client_name, u.phone, o.order_date, o.pickup_date_time, o.total_amount, o.status
                            FROM `Order` o
                            JOIN User u ON o.user_id = u.user_id
                            WHERE o.order_id = ? LIMIT 1");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    $stmt2 = $conn->prepare("SELECT p.product_id, p.name, o.quantity, o.price_at_purchase
                             FROM `Order` o
                             JOIN product p ON o.product_id = p.product_id
                             WHERE o.order_id = ?");
    $stmt2->bind_param('i', $order_id);
    $stmt2->execute();
    $products = $stmt2->get_result();
?>
<div id="orderModal" class="modal" style="display:block;">
    <div class="modal-content">
        <span class="close" onclick="window.location='Orders%20Management.php';">&times;</span>
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
        <p><strong>Client:</strong> <?php echo $order['client_name']; ?></p>
        <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
        <p><strong>Pickup:</strong> <?php echo $order['pickup_date_time']; ?></p>
        <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
        <p><strong>Total:</strong> $<?php echo $order['total_amount']; ?></p>

        <h3>Products</h3>
        <table border="1">
            <thead><tr><th>ID</th><th>Name</th><th>Qty</th><th>Price</th></tr></thead>
            <tbody>
                <?php while($p = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['product_id']; ?></td>
                        <td><?php echo $p['name']; ?></td>
                        <td><?php echo $p['quantity']; ?></td>
                        <td>$<?php echo $p['price_at_purchase']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<div id="successPopup" style="text-align: center; display: none;">
    <div class="modal-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Status updated successfully!</h2>
    </div>
</div>
<script>
    function updateStatus(orderId, status) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "Orders%20Management.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        var params = "order_id=" + orderId + "&status=" + status;

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText.trim() === "SUCCESS") {
                    document.getElementById('successPopup').style.display = "flex";
                    document.getElementById('order-' + orderId).querySelector("td:nth-child(4)").innerText = "Completed";
                    setTimeout(function() {
                        document.getElementById('successPopup').style.display = "none";
                    }, 3000);
                } else {
                    alert("Error: " + xhr.responseText);
                }
            }
        };

        xhr.send(params);
    }

    function closePopup() {
        document.getElementById('successPopup').style.display = "none";
    }
</script>
</body>
</html>
