function searchPets(){
    const input = document.getElementById('searchInput').value.trim().toLowerCase();
    const allPets = document.querySelectorAll('.pet-card');
    let found = false;

    allPets.forEach(card => {
        const petName = card.querySelector('p').textContent.replace('Pet Name:', '').trim().toLowerCase();
        if(petName.startsWith(input)) {
            card.style.display = "block";
            found = true;
        } else {
            card.style.display = "none";
        }
    });

    const msg = document.getElementById('noResultMsg');
    if(!found && !msg){
        const msg = document.createElement('p');
        msg.id = 'noResultMsg';
        msg.style.color = 'gray';
        msg.style.textAlign = 'center';
        msg.textContent = 'No Pets Found';
        document.querySelector('.pet-container').appendChild(msg);
    } else if(found && msg){
        msg.remove();
    }
}

function updateBreeds(){
    var petType = document.getElementById('petType').value;
    var petBreed = document.getElementById('petBreed');
    petBreed.innerHTML = "";

    var breeds = [];

    if(petType === "Cat") breeds = ["Persian", "Siamese", "British Shorthair", "Maine Coon", "Others"];
    else if(petType === "Dog") breeds = ["Husky", "Golden Retriever", "German Shepherd", "Pomeranian", "Others"];
    else if(petType === "Bird") breeds = ["Parrot", "Canary", "Budgie", "Cockatiel", "Others"];
    else if(petType === "Rabbit") breeds = ["Holland Lop", "Netherland Dwarf", "Mini Rex", "Lionhead", "Others"];
    else breeds = ["Unknown"];

    breeds.forEach(function(breed){
        var option = document.createElement('option');
        option.value = breed;
        option.text = breed;
        petBreed.appendChild(option);
    });
}

function openAddPetPopup(){
    document.getElementById('addPetPopup').style.display = 'flex';
}
function closeAddPetPopup(){
    document.getElementById('addPetPopup').style.display = 'none';
}

function submitAddPet() {
    const form = document.getElementById('addPetForm');
    const formData = new FormData(form);

    fetch('add_pet.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if(result.trim() === 'success'){
            alert('Pet added successfully!');
            closeAddPetPopup();
            location.reload();
        }else{
            alert(result);
        }
    })
    .catch(() => {
        alert('Error adding pet');
    });
}

function confirmDelete(pet_id) {
    if(confirm('Are you sure you want to delete this pet?')) {
        fetch('delete_pet.php?pet_id=' + pet_id)
        .then(response => response.text())
        .then(result => {
            if(result.trim() === 'success'){
                alert('Pet deleted successfully!');
                location.reload();
            } else {
                alert(result);
            }
        })
        .catch(() => {
            alert('Error deleting pet');
        });
    }
}

function openEditPopup(pet_id, name, age) {
    document.getElementById('editPetId').value = pet_id;
    document.getElementById('editPetName').value = name;
    document.getElementById('editPetAge').value = age;
    document.getElementById('editPetPopup').style.display = 'flex';
}

function closeEditPetPopup() {
    document.getElementById('editPetPopup').style.display = 'none';
}

function submitEditPet() {
    const form = document.getElementById('editPetForm');
    const formData = new FormData(form);

    fetch('edit_pet.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if(result.trim() === 'success'){
            alert('Pet updated successfully!');
            closeEditPetPopup();
            location.reload();
        }else{
            alert(result);
        }
    })
    .catch(() => {
        alert('Error updating pet');
    });
}
