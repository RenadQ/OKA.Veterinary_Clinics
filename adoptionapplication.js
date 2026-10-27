const urlParams = new URLSearchParams(window.location.search);
const animalName = urlParams.get('animal');
document.getElementById('animal-name').innerText = animalName;