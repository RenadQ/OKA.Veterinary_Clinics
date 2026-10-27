// Cookie Consent Functionality
document.addEventListener("DOMContentLoaded", () => {
    const cookieBox = document.querySelector("#cookieConsent"),
          acceptBtn = document.querySelector("#acceptCookies");

    acceptBtn.onclick = () => {
        document.cookie = "CookieBy=accepted; max-age=" + 60 * 60 * 24 * 30; // 30 days
        closeCookieConsent();
    }

    function closeCookieConsent() {
        cookieBox.classList.add("hide");
    }

    let checkCookie = document.cookie.indexOf("CookieBy");
    if (checkCookie === -1) {
        cookieBox.classList.remove("hide");
    } else {
        cookieBox.classList.add("hide");
    }
});