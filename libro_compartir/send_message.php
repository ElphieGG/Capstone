<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    die("Invalid request!");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO tblmessages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Message sent!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Message is empty!"]);
}
?>
