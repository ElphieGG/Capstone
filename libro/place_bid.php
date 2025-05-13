<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You need to log in to place a bid.']);
    exit;
}

if (isset($_POST['bid_amount']) && isset($_POST['book_id']) && is_numeric($_POST['bid_amount'])) {
    $bid_amount = $_POST['bid_amount'];
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the current highest bid and starting bid
    $stmt = $conn->prepare("SELECT bidding_start_price, (SELECT MAX(bid_amount) FROM bids WHERE book_id = ?) AS current_bid FROM tblbook WHERE id = ?");
    $stmt->bind_param("ii", $book_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $starting_bid = $result['bidding_start_price'];
    $current_bid = $result['current_bid'] ?? 0;
    $minBid = max($starting_bid, $current_bid) + 1;

    if ($bid_amount < $minBid) {
        echo json_encode(['status' => 'error', 'message' => "Your bid must be at least $minBid PHP."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO bids (book_id, user_id, bid_amount) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $book_id, $user_id, $bid_amount);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Your bid has been placed successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid bid amount.']);
}
?>
