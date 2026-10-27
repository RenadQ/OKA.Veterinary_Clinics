<?php
session_start();
include 'db_connect.php';

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM shoppingcart WHERE cart_id = '$cart_id' AND user_id = '$user_id'";
    mysqli_query($conn, $sql);
}
?>
