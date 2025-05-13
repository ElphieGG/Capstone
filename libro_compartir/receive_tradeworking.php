<?php
session_start();
include('config.php');

if (!isset($_GET['trade_id'])) {
    http_response_code(400);
    echo "Missing trade ID.";
    exit();
}

$trade_id = intval($_GET['trade_id']);

// First, get book IDs
$query = "SELECT offered_book_id, requested_book_id FROM tblbooktrades WHERE trade_id = ? AND status = 'accepted'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $trade_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(404);
    echo "Trade not found or not accepted.";
    exit();
}

$row = $result->fetch_assoc();
$offered_book_id = $row['offered_book_id'];
$requested_book_id = $row['requested_book_id'];

// Update trade status to 'received'
$updateTrade = $conn->prepare("UPDATE tblbooktrades SET status = 'received' WHERE trade_id = ?");
$updateTrade->bind_param("i", $trade_id);
$updateTrade->execute();

// Update both books to 'Exchanged'
$updateBooks = $conn->prepare("UPDATE tblbook SET book_status = 'Exchanged' WHERE id IN (?, ?)");
$updateBooks->bind_param("ii", $offered_book_id, $requested_book_id);
$updateBooks->execute();

echo "Success";
?>
