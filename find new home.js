		// Handle form submission
        document.getElementById("petForm").addEventListener("submit", function(event) {
            event.preventDefault();

            const name = document.getElementById("name").value;
            const phone = document.getElementById("phone").value;
            const email = document.getElementById("email").value;
            const petName = document.getElementById("petName").value;
            const gender = document.querySelector('input[name="gender"]:checked').value;
            const petType = document.getElementById("petType").value;
            const breed = document.getElementById("breed").value;
            const age = document.getElementById("age").value;
            const location = document.getElementById("location").value;
            const petImage = document.getElementById("petImage").files[0];

            // Check if a file was selected
            if (petImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create a new request object
                    const request = {
                        id: Date.now(),
                        name,
                        phone,
                        email,
                        petName,
                        petGender: gender, // Ensure property matches approval function
                        petType,
                        breed,
                        petAge: age, // Ensure property matches approval function
                        location,
                        petImage: e.target.result // Use the Data URL from the FileReader
                    };

                    // Add the request to the adoption requests list
                    let requestList = JSON.parse(localStorage.getItem("adoptionRequests")) || [];
                    requestList.push(request);
                    localStorage.setItem("adoptionRequests", JSON.stringify(requestList));

                    // Clear the form fields
                    document.getElementById("petForm").reset();

                    // Display confirmation message
                    document.getElementById("confirmationMessage").style.display = "block";
                };
                reader.readAsDataURL(petImage); // Read the file as a Data URL
            } else {
                alert("Please select an image for your pet.");
            }
        });
