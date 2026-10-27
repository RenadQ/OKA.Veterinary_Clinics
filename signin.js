// <!-- ===============================
//    Authors:
//    Team member  
//    Renad Alkahtani
//    ===============================  -->

// ========== Toggle Forms ==========
document.getElementById('signUp')?.addEventListener('click', () => {
  document.querySelector('.container').classList.add("right-panel-active");
});
document.getElementById('signIn')?.addEventListener('click', () => {
  document.querySelector('.container').classList.remove("right-panel-active");
});

// ========== Password Strength ==========
function checkPasswordStrength() {
  const passwordInput = document.getElementById('password');
  const strengthBar = document.getElementById('strength-bar');
  const strengthText = document.getElementById('strength-text');
  if (!passwordInput || !strengthBar || !strengthText) return;

  const password = passwordInput.value;
  let strength = 0;
  if (password.match(/[a-z]/)) strength++;
  if (password.match(/[A-Z]/)) strength++;
  if (password.match(/[0-9]/)) strength++;
  if (password.match(/[$@#&!]/)) strength++;
  if (password.length >= 8) strength++;

  const levels = [
    { text: 'Very Weak', color: 'red', width: '20%' },
    { text: 'Weak', color: 'orange', width: '40%' },
    { text: 'Medium', color: 'yellow', width: '60%' },
    { text: 'Good', color: 'lightgreen', width: '80%' },
    { text: 'Strong', color: 'green', width: '100%' }
  ];

  const level = levels[strength - 1] || levels[0];
  strengthBar.style.width = level.width;
  strengthBar.style.backgroundColor = level.color;
  strengthText.textContent = level.text;
}

// ========== Signup ==========
document.getElementById('signup-form')?.addEventListener('submit', function (e) {
  e.preventDefault();
  const form = this;
  const name = form.querySelector('[name="name"]').value.trim();
  const email = form.querySelector('[name="email"]').value.trim();
  const phone = form.querySelector('[name="phone"]').value.trim();
  const password = form.querySelector('[name="password"]').value.trim();
  const errorContainer = document.getElementById('error-container');
  errorContainer.innerText = '';

  if (!name || !email || !phone || !password) {
    showError('error-container', 'Please fill in all fields.');
    return;
  }

  if (!/^\d{9,}$/.test(phone)) {
    showError('error-container', 'Phone must be at least 9 digits.');
    return;
  }

  if (password.length < 9) {
    showError('error-container', 'Password must be at least 9 characters.');
    return;
  }

  const formData = new FormData(form);

  fetch('signup.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.text())
    .then(response => {
      const clean = response.trim().toLowerCase();
      if (clean === 'success') {
        alert("Account created successfully!");
        window.location.href = "homepage.php";
      } else if (clean === 'email_exists') {
        showError('error-container', 'Email already registered.');
      } else if (clean === 'phone_exists') {
        showError('error-container', 'Phone already in use.');
      } else {
        showError('error-container', 'Signup failed. Try again.');
      }
    })
    .catch(() => showError('error-container', 'Server error. Please try again.'));
});

// ========== Login ==========
document.getElementById('login-form')?.addEventListener('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('login.php', {
    method: 'POST',
    body: new URLSearchParams(formData)
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        if (data.userType === 'super_admin') {
          window.location.href = "superadminhomepage1.php"; 
        } else if (data.userType === 'admin') {
          window.location.href = "Admin_welcomepage.php";
        } else {
          window.location.href = "homepage.php";
        }
      } else {
        showError('error-container-login', data.message);
      }
    })
    .catch(() => showError('error-container-login', 'Login failed. Try again.'));
});

// ========== Forgot Password ==========
let resetEmail = "";
const validCode = "12345";

// عرض نموذج إدخال الإيميل
function showEmailForm() {
  hideAllPopups();
  document.getElementById('forgot-password-form')?.classList.add('show');
}

// التحقق من البريد ثم عرض نموذج الكود
function showCodeForm() {
  const emailInput = document.getElementById('reset-email').value.trim();
  const errorContainer = document.getElementById('email-error');
  errorContainer.innerText = '';

  if (!emailInput) {
    errorContainer.innerText = 'Please enter your email.';
    return;
  }

  fetch('check_email.php', {
    method: 'POST',
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: emailInput })
  })
  .then(res => res.text())
  .then(response => {
    const clean = response.trim();
    if (clean === "exists") {
      resetEmail = emailInput; //  حفظ الإيميل بنجاح
      hideAllPopups();
      document.getElementById('code-verification-form')?.classList.add('show');
    } else if (clean === "not_found") {
      errorContainer.innerText = 'Email not found.';
    } else if (clean === "missing_email") {
      errorContainer.innerText = 'Email is required.';
    } else {
      errorContainer.innerText = 'Unexpected response. Try again.';
    }
  })
  .catch(() => {
    errorContainer.innerText = 'Server error. Try again.';
  });
}

// التحقق من الكود المؤقت (حالياً ثابت)
function verifyCode() {
  const code = document.getElementById('reset-code').value.trim();
  const error = document.getElementById('reset-error');
  error.innerText = '';

  if (code === validCode) {
    hideAllPopups();
    document.getElementById('new-password-form')?.classList.add('show');
  } else {
    error.innerText = 'Invalid code.';
  }
}

// تحديث كلمة المرور بعد التحقق
function updatePassword() {
  const newPass = document.getElementById('new-password').value.trim();
  const confirmPass = document.getElementById('confirm-password').value.trim();
  const errorContainer = document.getElementById('update-error');
  errorContainer.innerText = '';

  if (!newPass || !confirmPass) {
    errorContainer.innerText = 'Please fill both fields.';
    return;
  }

  if (newPass !== confirmPass) {
    errorContainer.innerText = 'Passwords do not match.';
    return;
  }

  fetch('update_password.php', {
    method: 'POST',
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: resetEmail, password: newPass })
  })
    .then(res => res.text())
    .then(response => {
      const clean = response.trim();
      if (clean === 'success') {
        alert("Password updated!");
        hideAllPopups();
      } else {
        errorContainer.innerText = 'Update failed. Try again.';
      }
    })
    .catch(() => {
      errorContainer.innerText = 'Server error. Try again.';
    });
}

// مساعد لإغلاق كل النوافذ المؤقتة
function hideAllPopups() {
  document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('show'));
  document.getElementById('email-error').innerText = '';
  document.getElementById('reset-error').innerText = '';
  document.getElementById('update-error').innerText = '';
}

// ========== Helpers ==========
function showError(id, message) {
  const container = document.getElementById(id);
  container.textContent = message;
  container.style.display = 'block';
}

function hideAllPopups() {
  document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('show'));
  document.getElementById('email-error').innerText = '';
  document.getElementById('reset-error').innerText = '';
  document.getElementById('update-error').innerText = '';
}

window.addEventListener("click", function (event) {
  document.querySelectorAll(".modal.show").forEach(modal => {
    if (event.target === modal) {
      modal.classList.remove("show");
    }
  });
});
