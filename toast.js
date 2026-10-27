// toast.js
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  toast.className = 'toast ' + type;
  toast.textContent = message;
  toast.style.display = 'block';
  setTimeout(() => {
    toast.style.display = 'none';
  }, 3000);
}
