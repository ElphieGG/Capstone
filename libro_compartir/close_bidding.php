<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['book_id'])) {
    $bookId = $_POST['book_id'];
    $currentUserId = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT user_id FROM tblbook WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book && $book['user_id'] == $currentUserId) {
        $stmtUpdate = $conn->prepare("UPDATE tblbook SET bidding_end_time = NOW() WHERE id = ?");
        $stmtUpdate->bind_param("i", $bookId);
        $stmtUpdate->execute();

        $stmtWinner = $conn->prepare("SELECT user_id, bid_amount FROM bids WHERE book_id = ? ORDER BY bid_amount DESC LIMIT 1");
        $stmtWinner->bind_param("i", $bookId);
        $stmtWinner->execute();
        $resultWinner = $stmtWinner->get_result();
        $highestBid = $resultWinner->fetch_assoc();

        if ($highestBid) {
            $winnerUserId = $highestBid['user_id'];
            $message = "Congratulations! You won the bidding for the book you placed a bid on!";

            $stmtCheck = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND message = ?");
            $stmtCheck->bind_param("is", $winnerUserId, $message);
            $stmtCheck->execute();
            $checkResult = $stmtCheck->get_result();

            if ($checkResult->num_rows == 0) {
                $stmtNotif = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $stmtNotif->bind_param("is", $winnerUserId, $message);
                $stmtNotif->execute();
            }
        }

        header('Location: books_for_bidding.php?success=1');
        exit();
    } else {
        header('Location: books_for_bidding.php?error=1');
        exit();
    }
} else {
    header('Location: books_for_bidding.php');
    exit();
}
?>