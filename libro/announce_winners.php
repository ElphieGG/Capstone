<?php
include('config.php');

date_default_timezone_set('Asia/Riyadh');
$currentDateTime = date('Y-m-d H:i:s');

$query = "SELECT id FROM tblbook WHERE bidding_end_time <= ? AND (bidding_closed = 0 OR bidding_closed IS NULL)";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $currentDateTime);
$stmt->execute();
$endedBooks = $stmt->get_result();

while ($book = $endedBooks->fetch_assoc()) {
    $bookId = $book['id'];

    $bidQuery = $conn->prepare("SELECT user_id FROM tblbids WHERE book_id = ? ORDER BY bid_amount DESC LIMIT 1");
    $bidQuery->bind_param("i", $bookId);
    $bidQuery->execute();
    $winner = $bidQuery->get_result()->fetch_assoc();

    if ($winner) {
        $winnerId = $winner['user_id'];

        $update = $conn->prepare("UPDATE tblbook SET winner_user_id = ?, bidding_closed = 1 WHERE id = ?");
        $update->bind_param("ii", $winnerId, $bookId);
        $update->execute();

        $bookTitleQuery = $conn->prepare("SELECT title FROM tblbook WHERE id = ?");
        $bookTitleQuery->bind_param("i", $bookId);
        $bookTitleQuery->execute();
        $bookTitleRow = $bookTitleQuery->get_result()->fetch_assoc();
        $bookTitle = $bookTitleRow['title'];

        $message = "🎉 Congratulations! You won the bid for '$bookTitle'. Please proceed to checkout.";
        $notify = $conn->prepare("INSERT INTO tblnotifications (user_id, book_id, message) VALUES (?, ?, ?)");
        $notify->bind_param("iis", $winnerId, $bookId, $message);
        $notify->execute();
    } else {
        $close = $conn->prepare("UPDATE tblbook SET bidding_closed = 1 WHERE id = ?");
        $close->bind_param("i", $bookId);
        $close->execute();
    }
}

echo "<script>alert('Winner announcement completed!'); window.location.href='books_for_bidding.php';</script>";
?>