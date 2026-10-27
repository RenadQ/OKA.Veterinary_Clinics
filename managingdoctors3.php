
<?php
// Team member
include 'db_connect.php'; // Include the database connection

$adminName = $_SESSION['name'] ?? 'admin';
$adminId = $_SESSION['admin_id'] ?? null;
// Handle updating working hours
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_hours') {
    $branchId = $_POST['branch_id'];
    $newHours = $_POST['working_hours'];

    $stmt = $conn->prepare("UPDATE branch SET working_hours = ? WHERE branch_id = ?");
    $stmt->bind_param("si", $newHours, $branchId);
    $stmt->execute();
    $stmt->close();
}

// Handle adding a new doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_doctor') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $hours = $_POST['hours'];
    $branch = $_POST['branch'];
    $adminName = $_SESSION['name'] ?? 'admin';

    $stmt = $conn->prepare("INSERT INTO doctor (name, specialization, branch_id, admin_id, working_hours) VALUES (?, ?, (SELECT branch_id FROM Branch WHERE branch_name = ?), ?, ?)");
    $stmt->bind_param("sssis", $name, $specialty, $branch, $adminId, $hours);
    $stmt->execute();
}

// Handle adding a new doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_branch') {
    $name = $_POST['name'];
    $url = $_POST['url'];
    $hours = $_POST['hours'];
    $doctors = $_POST['doctors'];
    $adminName = $_SESSION['name'] ?? 'admin';

    $stmt = $conn->prepare("INSERT INTO branch (branch_name, location, working_hours, admin_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $url, $hours, $adminId);
    $stmt->execute();
    $stmt->close();

    $doctors = explode(',', $doctors); // Split the doctors string into an array

    foreach ($doctors as $doctor) {
        $doctor = trim($doctor);
        $specialty = 'General doctor'; // Default specialty

        $stmt = $conn->prepare("INSERT INTO doctor (name, specialization, branch_id, admin_id, working_hours) VALUES (?, ?, (SELECT branch_id FROM Branch WHERE branch_name = ?), ?, ?)");
        $stmt->bind_param("sssis", $doctor, $specialty, $name, $adminId, $hours);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle deleting a doctor
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_doctor') {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM doctor WHERE doctor_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Handle deleting a branch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_branch') {
    $name = $_POST['name'];

    $stmt = $conn->prepare("DELETE FROM branch WHERE branch_name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    die;
}

// Fetch branches
$branches = $conn->query("SELECT branch_name FROM branch");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Branch Management</title>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <link rel="stylesheet" href="mangmindoctors3_css.css">

     
    <script>
        let currentBranch = "";

        // Function to switch between tabs
        function showTab(tabName, branchName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });

            const tabElements = document.querySelectorAll('.tab');
            tabElements.forEach(tab => {
                tab.classList.remove('active');
            });

            currentBranch = branchName;
            document.getElementById(tabName).classList.add('active');
            const activeTab = Array.from(tabElements).find(tab => tab.textContent === "Doctors List");
            if (activeTab) activeTab.classList.add('active');

            if (tabName === 'doctorsTab') {
                displayDoctors(branchName);
            }
        }

        function deleteDoctor(doctorId) {
            if (confirm('Are you sure to delete this doctor?')) {
                fetch(`?action=delete_doctor&id=${doctorId}`, { method: 'GET' })
                    .then(() => {
                        displayDoctors(currentBranch);
                    });
            }
        }

        // Function to display the list of doctors for the selected branch
        function displayDoctors(branchName) {
            fetch(`get_doctors.php?branch=${branchName}`)
                .then(response => response.json())
                .then(data => {
                    const doctorList = document.getElementById('doctor-list');
                    doctorList.innerHTML = "";
                    data.forEach(doctor => {
                        doctorList.innerHTML += `
                            <div class="doctor">
                                ${doctor.name} - ${doctor.specialization} (${doctor.working_hours}) 
                                <button class="delete-button" onclick="deleteDoctor(${doctor.doctor_id})">Delete</button>
                            </div>
                        `;
                    });
                });
        }

        // Functions for adding and closing modals for branches and doctors
        function openAddBranchModal() {
            document.getElementById('addBranchModal').style.display = 'block';
        }

        function closeAddBranchModal() {
            document.getElementById('addBranchModal').style.display = 'none';
        }

        function addBranch() {
            const newBranchName = document.getElementById('new-branch-name').value;
            const newBranchLocation = document.getElementById('new-branch-location').value;
            const newBranchDoctors = document.getElementById('new-branch-doctors').value.split(',').map(d => d.trim()).filter(d => d !== '');
            const newBranchHours = document.getElementById('new-branch-hours').value;

            if (newBranchName && newBranchLocation && newBranchDoctors.length > 0) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'add_branch',
                        name: newBranchName,
                        url: newBranchLocation,
                        hours: newBranchHours,
                        doctors: newBranchDoctors
                    })
                }).then(() => {
                    showTab('doctorsTab', newBranchName);
                    closeAddBranchModal();
                });

                const sidebar = document.getElementById('sidebar');
                const branchDiv = document.createElement('div');
                branchDiv.className = 'branch';
                branchDiv.id = newBranchName;
                branchDiv.onclick = () => showTab('doctorsTab', newBranchName);
                branchDiv.innerHTML = `<h3>${newBranchName}</h3>
                    <button class="delete-button" onclick="deleteBranch('${newBranchName}'); event.stopPropagation();">Delete</button>`;
                
                sidebar.querySelector('.branch-container').appendChild(branchDiv);
                closeAddBranchModal();
                document.getElementById('new-branch-name').value = '';
                document.getElementById('new-branch-location').value = '';
                document.getElementById('new-branch-doctors').value = '';
                document.getElementById('new-branch-hours').value = '';

                // Automatically switch to the new branch's tab
                showTab('doctorsTab', newBranchName);
            } else {
                alert('Please fill all fields.');
            }
        }

        // Function to delete a branch
        function deleteBranch(branchName) {
            if (confirm('Are you sure to remove this branch?')) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { name: branchName, action: 'delete_branch' },
                    error: function(error) {
                        alert('Something is wrong, make sure to delete all the doctors first.');
                    },
                    success: function(data) {
                        alert("Record removed successfully");  
                        location.reload();
                    }
                });
            }
        }

        // Functions for adding doctors
        function openAddDoctorModal() {
            document.getElementById('addDoctorModal').style.display = 'block';
        }

        function closeAddDoctorModal() {
            document.getElementById('addDoctorModal').style.display = 'none';
        }

        function addDoctor() {
            const doctorName = document.getElementById('doctor-name').value;
            const doctorSpecialty = document.getElementById('doctor-specialty').value;
            const doctorHours = document.getElementById('doctor-hours').value;

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'add_doctor',
                    name: doctorName,
                    specialty: doctorSpecialty,
                    hours: doctorHours,
                    branch: currentBranch
                })
            }).then(() => {
                displayDoctors(currentBranch);
                closeAddDoctorModal();
            });
        }
    </script>
</head>
<body>
    <div id="sidebar">
        <h2>Branches</h2>
        <div class="branch-container">
            <?php while ($row = $branches->fetch_assoc()): ?>
                    <div class="branch" onclick="showTab('doctorsTab', '<?php echo $row['branch_name']; ?>');" style="cursor: pointer;">
                        <h3><?php echo $row['branch_name']; ?></h3>
                        <button class="delete-button" onclick="deleteBranch('<?php echo $row['branch_name']; ?>'); event.stopPropagation();">Delete</button>
                    </div>
            <?php endwhile; ?>
        </div>
        <div class="add-branch">
            <button class="button" onclick="openAddBranchModal()">Add Branch</button>
        </div>
        <div class="back-home">
            <button class="button" onclick="window.location.href='superadminhomepage1.php'">
                Back to Home
            </button>
        </div>
    </div>
    <div id="content">
        <h2>Manage Branch</h2>
        <div class="tabs">
            <button class="tab" onclick="showTab('doctorsTab', currentBranch)">Doctors List</button>
            <button class="tab" onclick="showTab('workingTimeTab', currentBranch)">Working Time</button>
        </div>

        <div id="doctorsTab" class="tab-content">
            <h3>Doctors List</h3>
            <div id="doctor-list" class="doctor-list"></div>
            <div class="button-container">
                <button class="button" onclick="openAddDoctorModal()">Add Doctor</button>
            </div>
        </div>

<!--to be able to update-->
<div id="workingTimeTab" class="tab-content">
    <h3>Working Time</h3>
    <form id="workingHoursForm">
        <input type="hidden" name="action" value="update_hours">
        <input type="hidden" name="branch_id" id="workingHoursBranchId">
        <label for="working_hours">Working Hours</label>
        <input type="text" id="working_hours_input" name="working_hours" style="width: 100%; padding: 10px; margin: 10px 0;">
        <button type="submit" class="button">Save Changes</button>
    </form>
    <p id="workingHoursStatus" style="color: green; display: none;">Working hours updated!</p>
</div>

<script>
    function loadWorkingHours(branchName) {
        fetch('get_branch_hours.php?branch=' + encodeURIComponent(branchName))
            .then(response => response.json())
            .then(data => {
                document.getElementById('working_hours_input').value = data.working_hours;
                document.getElementById('workingHoursBranchId').value = data.branch_id;
            });
    }

    document.getElementById('workingHoursForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('', {
            method: 'POST',
            body: formData
        }).then(() => {
            document.getElementById('workingHoursStatus').style.display = 'block';
        });
    });
</script>


    <!-- Add Branch Modal -->
    <div id="addBranchModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Branch</h2>
                <button class="modal-close" onclick="closeAddBranchModal()">X</button>
            </div>
            <div class="modal-body">
                <div class="doctor-info">
                    <input type="text" id="new-branch-name" placeholder="Branch Name" />
                </div>
                <div class="doctor-info">
                    <input type="text" id="new-branch-location" placeholder="Branch Location (URL)" />
                </div>
                <div class="doctor-info">
                    <input type="text" id="new-branch-doctors" placeholder="Doctors (comma separated)" />
                </div>
                <div class="doctor-info">
                    <input type="text" id="new-branch-hours" placeholder="Working Hours" />
                </div>
            </div>
            <div class="modal-footer">
                <button class="button" onclick="addBranch()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div id="addDoctorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Doctor</h2>
                <button class="modal-close" onclick="closeAddDoctorModal()">X</button>
            </div>
            <div class="modal-body">
                <div class="doctor-info">
                    <input type="text" id="doctor-name" placeholder="Doctor's Name" />
                </div>
                <div class="doctor-info">
                    <input type="text" id="doctor-specialty" placeholder="Specialty" />
                </div>
                <div class="doctor-info">
                    <input type="text" id="doctor-hours" placeholder="Working Hours" />
                </div>
            </div>
            <div class="modal-footer">
                <button class="button" onclick="addDoctor()">Save Changes</button>
            </div>
        </div>
    </div>
</body>
</html>

<h2>Edit Branch Working Hours</h2>
<form method="POST">
<table border="1" cellpadding="5" cellspacing="0">
    <tr><th>Branch Name</th><th>Working Hours</th><th>Action</th></tr>
<?php
$result = $conn->query("SELECT branch_id, branch_name, working_hours FROM Branch");
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <form method='POST'>
            <input type='hidden' name='action' value='update_hours'>
            <input type='hidden' name='branch_id' value='{$row['branch_id']}'>
            <td>{$row['branch_name']}</td>
            <td><input type='text' name='working_hours' value='{$row['working_hours']}'></td>
            <td><input type='submit' value='Update'></td>
        </form>
    </tr>";
}
?>
</table>
</form>
