<?php
session_start();
include 'db_connect.php';

// جلب المنتجات حسب الحالة
function getItems($conn, $status) {
    $condition = $status === 'available' ? "stock > 0" : "stock = 0";
    $query = "SELECT name FROM product WHERE $condition";
    $result = $conn->query($query);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row['name'];
    }
    return $items;
}

// جلب الخدمات المحجوزة مع التاريخ والحالة
function getBookedServices($conn) {
    $query = "
        SELECT s.name AS service_name, sb.appointment_date, sb.status_id
        FROM servicebooking sb
        JOIN service s ON sb.service_id = s.service_id
    ";
    $result = $conn->query($query);
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = [
            'name' => $row['service_name'],
            'date' => $row['appointment_date'],
            'status' => $row['status_id']
        ];
    }
    return $services;
}

$availableItems = getItems($conn, 'available');
$outOfStockItems = getItems($conn, 'outOfStock');
$bookedServices = getBookedServices($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super Admin - Branch Management</title>
    <link rel="stylesheet" href=" branch_css.css">
   
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <h2>Branches</h2>

        <div class="branch-list">
            <div class="branch-card">Oka - Dammam</div>
            <div class="branch-card">Oka - Khobar</div>
            <div class="branch-card">Oka - Jubail</div>
        </div>

        <button class="back-button" onclick="window.location.href='superadminhomepage1.php'">Back to Home</button>
    </div>

    <!-- Content -->
    <div id="content">
        <h2>Manage All Branches</h2>
        <div class="tabs">
            <button class="tab active" onclick="showTab('availableItemsTab', this)">Available Items</button>
            <button class="tab" onclick="showTab('outOfStockTab', this)">Out of Stock Items</button>
            <button class="tab" onclick="showTab('bookedServicesTab', this)">Booked Services</button>
        </div>

        <div id="availableItemsTab" class="tab-content active">
            <h3>Available Items</h3>
            <div class="list">
                <ul>
                    <?php foreach ($availableItems as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div id="outOfStockTab" class="tab-content">
            <h3>Out of Stock Items</h3>
            <div class="list">
                <ul>
                    <?php foreach ($outOfStockItems as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div id="bookedServicesTab" class="tab-content">
            <h3>Booked Services</h3>
            <div class="list">
                <ul>
                    <?php foreach ($bookedServices as $service): ?>
                        <li>
                            <?= htmlspecialchars($service['name']) ?> —
                            <?= htmlspecialchars($service['date']) ?> 
                            (Status: <?= htmlspecialchars($service['status']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>



    <script>
        function showTab(tabId, element) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            document.querySelectorAll('.tab').forEach(btn => btn.classList.remove('active'));
            if (element) element.classList.add('active');
        }
    </script>
</body>
</html>
