let currentIndex = 0;

function moveCarousel(step) {
    const carousel = document.getElementById('carousel');
    const items = document.querySelectorAll('.team-card');
    const visibleItems = 3; 
    const maxIndex = items.length - visibleItems;

    currentIndex += step;
if (currentIndex < 0) {
        currentIndex = maxIndex; // الذهاب إلى آخر مجموعة من الصور
    } else if (currentIndex > maxIndex) {
        currentIndex = 0; // العودة إلى أول مجموعة من الصور
    }
    if (currentIndex < 0) {
        currentIndex = 0;
    } else if (currentIndex > maxIndex) {
        currentIndex = maxIndex;
    }

    const offset = -(currentIndex * (100 / visibleItems)) + "%";
    carousel.style.transform = `translateX(${offset})`;
}

    function changeMap(branchNumber) {
        const mapIframe = document.getElementById('map-iframe');
        const branchMaps = [
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1829367.8287259713!2d52.55562504375!3d26.423991600000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49fb6bf953d063%3A0xdc83356a92e0c977!2z2LnZitin2K_YqSDYp9mI2YPYpyDYp9mE2KjZiti32LHZitip!5e0!3m2!1sar!2ssa!4v1740572476872!5m2!1sar!2ssa",
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1829367.8287259713!2d52.55562504375!3d26.423991600000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49e84cb752ded1%3A0x12020c80b1a79798!2z2LnZitin2K_YqSDYp9mI2YPYpyDYp9mE2KjZiti32LHZitip!5e0!3m2!1sar!2ssa!4v1740572546747!5m2!1sar!2ssa",
            "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1829367.8287259713!2d52.55562504375!3d26.423991600000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e35a08c4752b371%3A0x34c3a6dda276258b!2z2LnZitin2K_YqSDYp9mI2YPYpyDYp9mE2KjZiti32LHZitipINio2KfZhNis2KjZitmE!5e0!3m2!1sar!2ssa!4v1740572561867!5m2!1sar!2ssa"
        ];
        mapIframe.src = branchMaps[branchNumber];
    }
 function toggleBranchInfo(branchNumber) {
        const infoDivs = document.querySelectorAll('.branch-info');
        // Toggle the display of the selected branch info
        infoDivs.forEach((div, index) => {
            div.style.display = (index === branchNumber && div.style.display === 'none') ? 'block' : 'none';
        });
    }

function goToHomepage() {
        window.location.href = 'index.html';
    }