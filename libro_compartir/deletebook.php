<?php
// Include your database connection file
include('config.php');

// Check if the category ID is set and not empty
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Delete related bids first
$deleteBids = "DELETE FROM bids WHERE book_id = $id";
mysqli_query($conn, $deleteBids);

    // Prepare a delete statement
    $sql = "DELETE FROM tblbook WHERE id = $id";

    // Execute the delete statement
    if(mysqli_query($conn, $sql)) {
        // If the deletion is successful, redirect back to the user page
        header("Location:user.php");
        exit();
    } else {
        // If there's an error with the query, display an error message
        echo "Error deleting product: " . mysqli_error($conn);
    }
} else {
    // If category ID is not set or empty, redirect back to the category management page
    header("Location:user.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>