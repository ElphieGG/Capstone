<?php
include 'config.php'; // <-- Change this to your actual DB connection file

if (isset($_GET['id']) && isset($_GET['status'])) {
    $userId = $_GET['id'];
    $status = $_GET['status'];

    // Validate status input
    if (in_array($status, ['approved', 'rejected'])) {
        $sql = "UPDATE tbluser SET registration_status = '$status' WHERE user_id = $userId";

        if (mysqli_query($conn, $sql)) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid status.";
    }
} else {
    echo "Invalid request.";
}

header("Location: all_users.php"); // Redirect back to user list
exit();
?>
