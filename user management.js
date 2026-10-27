  let currentRow;

    function openEditModal(button) {
        currentRow = button.parentElement.parentElement;
        document.getElementById("editEmail").value = currentRow.cells[0].innerText;
        document.getElementById("editName").value = currentRow.cells[1].innerText;
        document.getElementById("editPhone").value = currentRow.cells[2].innerText;
        document.getElementById("editModal").style.display = "block";
    }

    function closeEditModal() {
        document.getElementById("editModal").style.display = "none";
    }

    function saveChanges() {
        const userId = currentRow.dataset.userId;
        const email = document.getElementById("editEmail").value;
        const name = document.getElementById("editName").value;
        const phone = document.getElementById("editPhone").value;

        // إرسال بيانات التحديث إلى خادم PHP
        fetch('update_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ userId, email, name, phone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentRow.cells[0].innerText = email;
                currentRow.cells[1].innerText = name;
                currentRow.cells[2].innerText = phone;
                addHistory(`Updated user details for ${name}.`);
                closeEditModal();
            } else {
                alert('Error updating user.');
            }
        });
    }

    function deleteUser(button) {
        let row = button.parentElement.parentElement;
        const userId = row.dataset.userId;

        // إرسال طلب الحذف إلى خادم PHP
        fetch('delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.remove();
                addHistory(`Deleted user ${row.cells[1].innerText}.`);
            } else {
                alert('Error deleting user.');
            }
        });
    }

    function addHistory(action) {
        let historyList = document.getElementById("historyList");
        let entry = document.createElement("li");
        entry.innerText = action;
        historyList.appendChild(entry);
    }