function showReview(appointment_id) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "getReview.php?appointment_id=" + appointment_id, true);
    xhr.onload = function () {
        if (xhr.status == 200) {
            document.getElementById('reviewContent').innerHTML = xhr.responseText;
            document.getElementById('reviewPopup').style.display = 'block';
        }
    };
    xhr.send();
}

function closeReview() {
    document.getElementById('reviewPopup').style.display = 'none';
}
