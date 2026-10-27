
        function addNewAdoption() {
            document.querySelectorAll(".input-fields input").forEach(input => input.value = "");
            document.getElementById("imageFileName").value = ""; 
            document.getElementById("edit_id").value = ""; // Reset the edit ID
        }

        function editAdoption(id, name, age, gender, type, location, breed, imageURL) {
            document.querySelectorAll(".input-fields input").forEach(input => input.value = "");
            document.getElementById("imageFileName").value = ""; 
            document.getElementById("edit_id").value = id;
            document.querySelector('input[name="PetName"]').value = name;
            document.querySelector('input[name="age"]').value = age;
            document.querySelector('input[name="gender"]').value = gender;
            document.querySelector('input[name="type"]').value = type;
            document.querySelector('input[name="location"]').value = location;
            document.querySelector('input[name="breed"]').value = breed;
            document.getElementById('imageFileName').value = "Image already uploaded"; // Indicate an image exists
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this pet?')) {
                window.location.href = '?delete_id=' + id;
            }
        }

        function applyForAdoption(id) {
            fetch('set_pet_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pet_id: id })
            })
            .then(response => {
                if (response.ok) {
                    // Redirect to the adoption application page without pet ID in URL
                    window.location.href = 'adoptionapplication.php';
                } else {
                    console.error('Failed to set pet ID in session');
                }
            })
            .catch(error => console.error('Error:', error));
        }