<?php
// Include database connection
include('config.php');

// Check if user_id is provided
if(isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    // Use a prepared statement to prevent SQL injection
    $sql = "DELETE FROM tbluser WHERE user_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET['user_id']);
        
        // Execute the statement
        if(mysqli_stmt_execute($stmt)) {
            // Redirect to users page after successful deletion
            header("Location: all_users.php?msg=User deleted successfully");
            exit();
        } else {
            echo "Error deleting user: " . mysqli_error($conn);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
} else {
    // Redirect if no user_id is provided
    header("Location: all_users.php?error=Invalid request");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>
