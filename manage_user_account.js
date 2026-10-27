let userData = {
    name: "Demo User",
    email: "user@example.com",
    phone: "0500000000",
    password: "password123"
};

// Populate form with user data
document.getElementById('user-name').value = userData.name;
document.getElementById('user-email').value = userData.email;
document.getElementById('user-phone').value = userData.phone;

// Handle form submission to update user data
document.getElementById('manage-account-form').addEventListener('submit', function(event) {
    event.preventDefault();
    userData.name = document.getElementById('user-name').value;
    userData.email = document.getElementById('user-email').value;
    userData.phone = document.getElementById('user-phone').value;
    userData.password = document.getElementById('user-password').value;

    alert(`Account updated:\nName: ${userData.name}\nEmail: ${userData.email}\nPhone: ${userData.phone}`);
});

// Toggle edit info section visibility
document.getElementById('edit-info-btn').addEventListener('click', function() {
    const editInfoSection = document.getElementById('edit-info');
    editInfoSection.classList.toggle('hidden');
});

// Handle profile picture upload
document.getElementById('upload-pic').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profile-pic').src = e.target.result;
    }
    reader.readAsDataURL(file);
});