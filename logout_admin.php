<!-- ===============================
//    Author:
//    Renad Alkahtani
handling logout  for admins
===============================  -->
<?php
session_start();
session_destroy();

header("Location: signin.php");
exit();
?>