<!--
===============================
  Author:
  Renad Alkahtani
  Team member
===============================
-->
<?php
session_start();
include 'db_connect.php';

// ===== Update Offer Logic =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['offer_id'])) {
    $offer_id = $_POST['offer_id'];
    $name = $_POST['offer_name'];
    $discount = floatval($_POST['discount_percentage']);
    $limit = intval($_POST['usage_limit']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $applies_to = $_POST['applies_to'];

    $stmt = $conn->prepare("UPDATE offer SET offer_name=?, discount_percentage=?, start_date=?, end_date=?, usage_limit=?, applies_to=? WHERE offer_id=?");
    $stmt->bind_param("sdssisi", $name, $discount, $start, $end, $limit, $applies_to, $offer_id);
    $stmt->execute();

    header("Location: Offers Management.php?updated=1");
    exit();
}

// ===== Delete Offer Logic =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM offer WHERE offer_id=$id");
    header("Location: Offers Management.php?deleted=1");
    exit();
}

// ===== Fetch Offers =====
$sql = "SELECT * FROM Offer ORDER BY start_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Offers Management</title>
  <link rel="stylesheet" href="Offers_mangment_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<!-- ===== Header ans sidebar ===== -->
<?php include 'admin_Header_layout.php'; ?>


<!-- ===== Main Container ===== -->
<div class="container">
  <div class="filters">
    <select id="sortSelect" onchange="sortTable()">
      <option value="">Sort by</option>
      <option value="amount">Coupon Amount</option>
      <option value="expiry">Expiry Date</option>
    </select>
    <input type="text" id="searchInput" placeholder="Search offers..." onkeyup="filterTable()">
  </div>
  <button class="add-offer-btn" onclick="openModal()">Add Offer</button>

  <!-- ===== Offers Table ===== -->
  <div class="table-container">
    <table id="offersTable">
      <tr>
        <th>Offer Name</th>
        <th>Discount</th>
        <th>Usage</th>
        <th>Start</th>
        <th>End</th>
        <th>Applies To</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['offer_name']) ?></td>
          <td><?= htmlspecialchars($row['discount_percentage']) ?>%</td>
          <td><?= htmlspecialchars($row['usage_count']) ?>/<?= htmlspecialchars($row['usage_limit']) ?></td>
          <td><?= date('Y-m-d', strtotime($row['start_date'])) ?></td>
          <td><?= date('Y-m-d', strtotime($row['end_date'])) ?></td>
          <td><?= htmlspecialchars($row['applies_to']) ?></td>
          <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
          <td class="action-buttons">
            <button class="edit-btn" onclick='editOffer(<?= json_encode($row) ?>)'>Edit</button>
            <a href="pause_offer.php?offer_id=<?= $row['offer_id'] ?>">
              <button class="delete-btn" style="background-color:#6c757d;">Pause</button>
            </a>
            <a href="?delete=<?= $row['offer_id'] ?>" onclick="return confirm('Are you sure?')">
              <button class="delete-btn">Delete</button>
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8">No offers found.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<!-- ===== Modal for Add/Edit ===== -->
<div id="offerModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2 id="modalTitle">Add Offer</h2>
    <form id="offerForm" action="add_offer.php" method="POST" onsubmit="return validateForm()">
      <input type="hidden" name="offer_id" id="offer_id">
      <div class="form-group">
        <label for="offer_name">Offer Name:</label>
        <input type="text" name="offer_name" id="offer_name" required>
      </div>
      <div class="form-group">
        <label for="discount">Discount %:</label>
        <input type="number" name="discount_percentage" id="discount" step="0.01" required>
      </div>
      <div class="form-group">
        <label for="usage">Usage Limit:</label>
        <input type="number" name="usage_limit" id="usage_limit" required>
      </div>
      <div class="form-group">
        <label for="start">Start Date:</label>
        <input type="date" name="start_date" id="start_date" required>
      </div>
      <div class="form-group">
        <label for="end">End Date:</label>
        <input type="date" name="end_date" id="end_date" required>
      </div>
      <div class="form-group">
        <label>Applies To:</label>
        <select name="applies_to" id="applies_to" required>
          <option value="all">All</option>
          <option value="product">Products</option>
          <option value="service">Services</option>
        </select>
      </div>
      <button class="submit-btn" type="submit">Save Offer</button>
    </form>
  </div>
</div>

<!-- ===== Toast Message ===== -->
<div id="toast" class="toast"></div>

<!-- ===== JavaScript ===== -->
<script>
function openModal() {
  document.getElementById('offerForm').reset();
  document.getElementById('modalTitle').innerText = 'Add Offer';
  document.getElementById('offerForm').action = 'add_offer.php';
  document.getElementById('offerModal').style.display = 'block';
}
function closeModal() {
  document.getElementById('offerModal').style.display = 'none';
}
function editOffer(offer) {
  document.getElementById('modalTitle').innerText = 'Edit Offer';
  document.getElementById('offerForm').action = 'Offers Management.php';
  document.getElementById('offer_id').value = offer.offer_id;
  document.getElementById('offer_name').value = offer.offer_name;
  document.getElementById('discount').value = offer.discount_percentage;
  document.getElementById('usage_limit').value = offer.usage_limit;
  document.getElementById('start_date').value = offer.start_date.split(' ')[0];
  document.getElementById('end_date').value = offer.end_date.split(' ')[0];
  document.getElementById('applies_to').value = offer.applies_to;
  document.getElementById('offerModal').style.display = 'block';
}
function validateForm() {
  const discount = parseFloat(document.getElementById('discount').value);
  const usage = parseInt(document.getElementById('usage_limit').value);
  const startDate = new Date(document.getElementById('start_date').value);
  const endDate = new Date(document.getElementById('end_date').value);
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  if (isNaN(discount) || discount <= 0 || discount >= 100) {
    alert("❗ Discount must be between 1 and 99.");
    return false;
  }
  if (isNaN(usage) || usage <= 0) {
    alert("❗ Usage limit must be more than 0.");
    return false;
  }
  if (startDate < today || endDate < today) {
    alert("❗ Dates must not be in the past.");
    return false;
  }
  if (startDate > endDate) {
    alert("❗ Start date must not be after end date.");
    return false;
  }
  return true;
}
function filterTable() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const rows = document.querySelectorAll("#offersTable tr:not(:first-child)");
  rows.forEach(row => {
    const offerName = row.cells[0].textContent.toLowerCase();
    row.style.display = offerName.includes(input) ? "" : "none";
  });
}
function sortTable() {
  const table = document.getElementById("offersTable");
  const rows = Array.from(table.rows).slice(1);
  const sortBy = document.getElementById("sortSelect").value;
  const colIndex = sortBy === "amount" ? 1 : sortBy === "expiry" ? 4 : null;
  if (colIndex === null) return;
  rows.sort((a, b) => {
    const valA = a.cells[colIndex].textContent.replace('%', '').trim();
    const valB = b.cells[colIndex].textContent.replace('%', '').trim();
    return sortBy === "expiry"
      ? new Date(valA) - new Date(valB)
      : parseFloat(valA) - parseFloat(valB);
  });
  rows.forEach(row => table.appendChild(row));
}
function showToast(message, type = "success") {
  const toast = document.getElementById("toast");
  toast.textContent = message;
  toast.className = `toast show ${type}`;
  setTimeout(() => toast.classList.remove("show"), 3000);
}
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  if (params.get('success') === '1') showToast('✅ Offer added successfully.');
  if (params.get('updated') === '1') showToast('✅ Offer updated successfully.');
  if (params.get('deleted') === '1') showToast('🗑️ Offer deleted.');
  if (params.get('reverted') === '1') showToast('⏸️ Offer paused and reverted.');
});
</script>
</body>
</html>
