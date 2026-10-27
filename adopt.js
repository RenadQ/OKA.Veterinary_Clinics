		window.onload = function() {
            const adoptContainer = document.getElementById('adoptContainer');
            const adoptions = <?php echo json_encode($adoptions); ?>;

            adoptContainer.innerHTML = ""; // Clear existing content

            adoptions.forEach(adoption => {
                const petCard = document.createElement('div');
                petCard.classList.add('pet-card');
                petCard.innerHTML = `
                    <img src="${adoption.ImageURL}" alt="${adoption.name}">
                    <div class="overlay">
                        <div>
                            <h3>${adoption.name}</h3>
                            <p>Age: ${adoption.age}</p>
                            <p>Gender: ${adoption.gender}</p>
                            <p>Type: ${adoption.type}</p>
                            <p>Location: ${adoption.location}</p>
                            <p>Breed: ${adoption.breed}</p>
                        </div>
                    </div>
                    <button class="apply-button" onclick="location.href='adoptionapplication.php?animal=' + encodeURIComponent('${adoption.name}')">Apply for Adoption</button>
                `;
                adoptContainer.appendChild(petCard);
            });
        };