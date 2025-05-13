<?php
include('config.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure the request_id is passed and is valid
if (isset($_GET['request_id']) && is_numeric($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Check if the trade request exists
    $sql = "SELECT * FROM trade_requests WHERE id = ? AND user_id_to = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $request_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // The trade request exists, now approve the trade
            $trade_request = $result->fetch_assoc();
            
            // Update the trade request status to approved
            $update_sql = "UPDATE trade_requests SET status = 'approved' WHERE id = ?";
            if ($update_stmt = $conn->prepare($update_sql)) {
                $update_stmt->bind_param("i", $request_id);
                $update_stmt->execute();
                
                // Now update the book_status to 'Exchanged' in the books table
                $update_book_sql = "UPDATE tblbook SET book_status = 'Exchanged' WHERE id = ?";
                if ($book_stmt = $conn->prepare($update_book_sql)) {
                    $book_stmt->bind_param("i", $trade_request['book_id_to']);
                    $book_stmt->execute();
                    
                    // Redirect back to the trade requests page with a success message
                    header("Location: trade_requests.php?message=Trade approved and book status updated.");
                    exit();
                } else {
                    echo "Error updating book status.";
                }
            } else {
                echo "Error approving trade request.";
            }
        } else {
            echo "Invalid trade request.";
        }
    } else {
        echo "Error fetching trade request.";
    }
} else {
    echo "Invalid request.";
}
?>