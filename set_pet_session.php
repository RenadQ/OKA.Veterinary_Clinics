<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['pet_id'])) {
    $_SESSION['pet_id'] = $data['pet_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>