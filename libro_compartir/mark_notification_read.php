<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['notification_id'])) {
    $notificationId = $_POST['notification_id'];

    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
}

header("Location: user.php"); // Redirect back to the dashboard
exit();
?>
