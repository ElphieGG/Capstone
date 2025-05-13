<?php
// reject_trade.php
include('config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Update trade request status to 'rejected'
    $sql = "UPDATE trade_requests SET status = 'rejected' WHERE id = ? AND user_id_to = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $request_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            header("Location: trade_requests.php");
            exit();
        } else {
            echo "Error rejecting trade request.";
        }
    }
}

?>