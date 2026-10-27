<?php
include 'db_connect.php';

session_start();
$user_id = $_SESSION['id'] ?? null;

if(isset($_GET['id']) && isset($_GET['text']) && isset($_GET['rating'])){
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $text = mysqli_real_escape_string($conn, $_GET['text']);
    $rating = mysqli_real_escape_string($conn, $_GET['rating']);

    
    if($rating > 0 && $rating <= 5){

        // نتأكد هل فيه تقييم قديم أصلاً؟
        $check = "SELECT * FROM Review WHERE appointment_id='$id' AND user_id='$user_id'";
        $result = $conn->query($check);
        if($result->num_rows > 0){
            echo "Review Already Exists.";
            exit;
        }

        // لو مافيه تقييم قديم
        $sql = "INSERT INTO Review (appointment_id, user_id, comment, rating) VALUES ('$id', '$user_id', '$text', '$rating')";

        if($conn->query($sql)){
            echo "success"; 
            exit;
        }else{
            echo "Error: " . $conn->error;
        }

    }else{
        echo "Invalid Rating Value.";
    }

}else{
    echo "Missing Data.";
}
?>
