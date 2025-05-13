<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    die("Unauthorized access!");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_GET['receiver_id']);

$sql = "SELECT sender_id, message, timestamp FROM tblmessages 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $class = ($row['sender_id'] == $sender_id) ? 'sent' : 'received';
    echo "<div class='$class'><strong>{$row['message']}</strong><br><small>{$row['timestamp']}</small></div>";
}
$stmt->close();
?>
