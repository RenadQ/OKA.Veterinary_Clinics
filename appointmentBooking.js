let selectedDate = "";
let selectedTime = "";
let selectedPetId = "";
let selectedPetName = "";

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const currentMonthEl = document.getElementById('current-month');
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    const timeSlotsEl = document.getElementById('time-slots');

    let today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];

    function generateDates(month, year) {
        calendarEl.innerHTML = '';

        let start = new Date();
        start.setDate(start.getDate() + 1);
        let end = new Date(start);
        end.setDate(start.getDate() + 6);

        let current = new Date(start);
        while (current <= end) {
            if (current.getMonth() === month && current.getFullYear() === year) {
                const btn = document.createElement('button');
                btn.className = 'date';
                btn.textContent = current.getDate();
                btn.addEventListener('click', function () {
                    handleDateClick(btn, current.getDate());
                });
                calendarEl.appendChild(btn);
            }
            current.setDate(current.getDate() + 1);
        }

        currentMonthEl.textContent = `${monthNames[month]} - ${year}`;

        if (start.getMonth() != end.getMonth()) {
            prevBtn.style.display = 'block';
            nextBtn.style.display = 'block';
        } else {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }

        timeSlotsEl.style.display = 'flex';

        setTimeout(() => {
            document.querySelectorAll('.date').forEach(function(btn){
                btn.addEventListener('click', function(){
                    selectedDate = btn.textContent;
                    document.querySelectorAll('.date').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                });
            });

            document.querySelectorAll('.time-slot').forEach(function(btn){
                btn.addEventListener('click', function(){
                    selectedTime = btn.textContent;
                    document.querySelectorAll('.time-slot').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                });
            });
        }, 100);
    }

    generateDates(currentMonth, currentYear);

    nextBtn.addEventListener('click', function () {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        generateDates(currentMonth, currentYear);
    });

    prevBtn.addEventListener('click', function () {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        generateDates(currentMonth, currentYear);
    });
});

function handleDateClick(btn, day){
    selectedDate = day;
    document.querySelectorAll('.date').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
}

function confirmAppointment(){
    if(selectedDate == "" || selectedTime == ""){
        alert("Please select Date and Time first.");
        return;
    }

    const fullDateTime = formatDate(selectedDate) + ' ' + convertTime(selectedTime);

    fetch('confirmAppointment.php', {
       method: 'POST',
       headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
       },
       body: 'doctor_id=' + doctor_id + '&date_time=' + fullDateTime + '&pet_id=' + selectedPetId
    })
    .then(response => response.text())
    .then(result => {
        if(result.trim() == 'success'){
            alert('Your appointment has been booked successfully!');
            window.location.href = 'appointmentManagement.php';
        }else if(result.trim() == 'booked'){
            alert('Sorry, this time slot is already booked. Please choose another one.');
        }else if(result.trim() == 'no_pet'){
            alert('Please select a pet first.');
        }else{
            alert('Something went wrong.');
        }
    })
    .catch(error => {
        console.log(error);
        alert('Error while booking appointment.');
    });
}

function formatDate(day){
    const month = (currentMonth + 1).toString().padStart(2, '0');
    const date = day.toString().padStart(2, '0');
    return currentYear + '-' + month + '-' + date;
}

function convertTime(time12h) {
    const [time, modifier] = time12h.split(' ');
    let [hours, minutes] = time.split(':');

    if (modifier === 'PM' && hours !== '12') {
        hours = parseInt(hours, 10) + 12;
    }
    if (modifier === 'AM' && hours === '12') {
        hours = '00';
    }

    return `${hours}:${minutes}:00`;
}

function openEditPetPopup(id, name, age){
    document.getElementById('editPetId').value = id;
    document.getElementById('editPetName').value = name;
    document.getElementById('editPetAge').value = age;
    document.getElementById('editPetPopup').style.display = 'flex';
}

function closeEditPetPopup(){
    document.getElementById('editPetPopup').style.display = 'none';
}

function saveEditedPet(){
    const pet_id = document.getElementById('editPetId').value;
    const name = document.getElementById('editPetName').value;
    const age = document.getElementById('editPetAge').value;

    fetch('edit_pet.php?pet_id=' + pet_id + '&name=' + name + '&age=' + age)
    .then(response => response.text())
    .then(result => {
        if(result.trim() == 'success'){
            alert('Pet Updated Successfully');
            fetch('get_pet_info.php?pet_id=' + pet_id)
            .then(response => response.json())
            .then(data => {
                document.querySelector('#pet-' + pet_id + ' span').innerText = data.name;

                if(selectedPetId == pet_id){
                    document.getElementById('selectedPetName').innerText = 'Selected Pet: ' + data.name;
                }
                closeEditPetPopup();
            });
        }else{
            alert(result);
        }
    })
    .catch(error => {
        console.log(error);
        alert('Error updating pet.');
    });
}

function deletePet(event, pet_id){
    event.stopPropagation();

    if(confirm('Are you sure you want to delete this pet?')){
        fetch('delete_pet.php?pet_id=' + pet_id)
        .then(response => response.text())
        .then(result => {
            if(result.trim() == 'success'){
                document.getElementById('pet-' + pet_id).remove();
                if(selectedPetId == pet_id){
                    let pets = document.querySelectorAll('#choosePetPopup div[id^="pet-"]');
                    if(pets.length > 0){
                        let firstPet = pets[0];
                        let newPetId = firstPet.id.split('-')[1];
                        let newPetName = firstPet.querySelector('span').innerText;
                        selectPet(newPetId, newPetName);
                    }else{
                        selectedPetId = "";
                        document.getElementById('selectedPetName').innerText = '';
                    }
                }
            } else {
                alert(result);
            }
        })
        .catch(error => {
            console.log(error);
            alert('Error deleting pet.');
        });
    }
}

function updateBreeds(){
    var petType = document.getElementById('petType').value;
    var petBreed = document.getElementById('petBreed');
    petBreed.innerHTML = "";
    var breeds = [];

    if(petType === "Cat"){
        breeds = ["Persian", "Siamese", "British Shorthair", "Maine Coon", "Others"];
    } else if(petType === "Dog"){
        breeds = ["Husky", "Golden Retriever", "German Shepherd", "Pomeranian", "Others"];
    } else if(petType === "Bird"){
        breeds = ["Parrot", "Canary", "Budgie", "Cockatiel", "Others"];
    } else if(petType === "Rabbit"){
        breeds = ["Holland Lop", "Netherland Dwarf", "Mini Rex", "Lionhead", "Others"];
    } else {
        breeds = ["Unknown"];
    }

    breeds.forEach(function(breed){
        var option = document.createElement('option');
        option.value = breed;
        option.text = breed;
        petBreed.appendChild(option);
    });
}

function selectPet(pet_id, pet_name){
    selectedPetId = pet_id;
    document.getElementById('selectedPetName').innerText = 'Selected Pet: ' + pet_name;
    closeChoosePetPopup();
}

function openChoosePetPopup(){
    document.getElementById('choosePetPopup').style.display = 'flex';
    document.querySelectorAll('#choosePetPopup div[id^="pet-"]').forEach(function(div){
        div.onclick = function(){
            const pet_id = this.id.split('-')[1];
            const pet_name = this.querySelector('span').innerText;
            selectPet(pet_id, pet_name);
        };
    });
}

function closeChoosePetPopup(){
    document.getElementById('choosePetPopup').style.display = 'none';
}
