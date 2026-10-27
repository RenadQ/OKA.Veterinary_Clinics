let adminsData = [];

window.onload = function() {
    fetchAdmins();
};

function fetchAdmins() {
    fetch('adminretrive.php')
        .then(response => response.json())
        .then(data => {
            adminsData = data;
            displayAdmins();
        });
}

function displayAdmins() {
    const adminList = document.getElementById('admin-list');
    adminList.innerHTML = "";
    adminsData.forEach((admin) => {
        adminList.innerHTML += `
            <div class="admin" onclick="showAdminInfo(${admin.id})">
                ${admin.name} (ID: ${admin.id})
                <button class="delete-button" onclick="deleteAdmin(${admin.id}); event.stopPropagation();">Delete</button>
            </div>
        `;
    });
}

function showAdminInfo(id) {
    const admin = adminsData.find(admin => admin.id === id);
    const infoDiv = document.getElementById('admin-info');
    infoDiv.innerHTML = `
        <strong>Name:</strong> <input type="text" value="${admin.name}" onchange="updateAdmin(${admin.id}, 'name', this.value)"/><br>
        <strong>Email:</strong> <input type="email" value="${admin.email}" onchange="updateAdmin(${admin.id}, 'email', this.value)"/><br>
        <strong>Phone:</strong> <input type="text" value="${admin.phone}" onchange="updateAdmin(${admin.id}, 'phone', this.value)"/><br>
        <strong>Role:</strong> <input type="text" value="${admin.role}" onchange="updateAdmin(${admin.id}, 'role', this.value)"/><br>
        <strong>Education:</strong> <input type="text" value="${admin.education}" onchange="updateAdmin(${admin.id}, 'education', this.value)"/><br>
        <strong>Last Access:</strong> <input type="text" value="${admin.last_access}" onchange="updateAdmin(${admin.id}, 'last_access', this.value)"/><br>
        <strong>Admin ID:</strong> <input type="text" value="${admin.adminId}" onchange="updateAdmin(${admin.id}, 'adminId', this.value)"/><br>
    `;
    infoDiv.style.display = 'block';
}

function updateAdmin(id, field, value) {
    const admin = adminsData.find(admin => admin.id === id);
    admin[field] = value;
}

function addAdmin() {
    const adminName = document.getElementById('admin-name').value.trim();
    const adminEmail = document.getElementById('admin-email').value.trim();
    const adminPhone = document.getElementById('admin-phone').value.trim();
    const adminRole = document.getElementById('admin-role').value.trim();
    const adminEducation = document.getElementById('admin-education').value.trim();
    const adminId = document.getElementById('admin-id').value.trim();

    if (adminName && adminEmail && adminPhone && adminRole && adminEducation && adminId) {
        const newAdmin = {
            name: adminName,
            email: adminEmail,
            phone: adminPhone,
            role: adminRole,
            education: adminEducation,
            adminId: adminId
        };

        fetch('add_admin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(newAdmin)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                fetchAdmins();
                clearAdminInputFields();
                closeModal();
            } else {
                alert(data.message);
            }
        });
    } else {
        alert('Please fill all fields.');
    }
}

function clearAdminInputFields() {
    document.getElementById('admin-name').value = '';
    document.getElementById('admin-email').value = '';
    document.getElementById('admin-phone').value = '';
    document.getElementById('admin-role').value = '';
    document.getElementById('admin-education').value = '';
    document.getElementById('admin-id').value = '';
}

function deleteAdmin(id) {
    if (confirm('Are you sure you want to delete this admin?')) {
        fetch('delete_admin.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                fetchAdmins();
                document.getElementById('admin-info').style.display = 'none';
            } else {
                alert(data.message);
            }
        });
    }
}

function openModal() {
    document.getElementById('addAdminModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addAdminModal').style.display = 'none';
}