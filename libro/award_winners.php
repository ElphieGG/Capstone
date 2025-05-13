<?php
include 'config.php';

// Get books where bidding has ended and not yet awarded
$sql = "SELECT b.id, b.bidding_end_time, 
               (SELECT user_id FROM bids WHERE book_id = b.id ORDER BY bid_amount DESC LIMIT 1) AS winner_id
        FROM tblbook b
        WHERE b.bidding_end_time <= NOW() AND b.bidding_closed = 0";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    if ($row['winner_id']) {
        // Award the book to the highest bidder
        $updateSql = "UPDATE tblbook SET winner_user_id = ?, bidding_closed = 1 WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ii", $row['winner_id'], $row['id']);
        $stmt->execute();

        // Insert notification for the winner
        $message = "Congratulations! You have won the book. Please proceed to checkout.";
        $notifySql = "INSERT INTO notifications (user_id, book_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($notifySql);
        $stmt->bind_param("iis", $row['winner_id'], $row['id'], $message);
        $stmt->execute();
    }
}

$conn->close();
?>
