 // دالة لقراءة محتوى التدوينة
    function readPost() {
        const content = document.getElementById("postContent").textContent;
        const utterance = new SpeechSynthesisUtterance(content);
        utterance.lang = detectLanguage(content); // تعيين اللغة
        speechSynthesis.speak(utterance);
    }

    // دالة لإيقاف القراءة
    function stopReading() {
        speechSynthesis.cancel();
    }

    // دالة لكشف اللغة
    function detectLanguage(text) {
        const arabicRegex = /[\u0600-\u06FF]/;
        const englishRegex = /[a-zA-Z]/;
        return arabicRegex.test(text) && !englishRegex.test(text)
            ? "ar-SA"
            : "en-US";
    }

    // تعيين اتجاه النص بناءً على اللغة
    const content = document.getElementById("postContent").textContent;
    const language = detectLanguage(content);
    document.body.style.direction = language === "ar-SA" ? "rtl" : "ltr";
    document.body.style.textAlign = language === "ar-SA" ? "right" : "left";