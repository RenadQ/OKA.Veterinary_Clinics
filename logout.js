function signOut() {
    sessionStorage.removeItem("loggedIn");
    sessionStorage.removeItem("username");
    updateUI();
    window.location.href = "sighin.php"; // إعادة التوجيه إلى صفحة تسجيل الدخول
}
// <!-- ===============================
//    Authors:
//    Team member  
//    Renad Alkahtani
//    
//    ===============================  -->