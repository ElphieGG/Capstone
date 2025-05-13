<?php
include('config.php');

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Get user ID and sanitize

    $sql = "UPDATE tbluser SET user_status = 'inactive' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('User deactivated successfully!'); window.location.href='all_users.php';</script>";
    } else {
        echo "<script>alert('Error deactivating user.'); window.location.href='all_users.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='all_users.php';</script>";
}

$conn->close();
?>
