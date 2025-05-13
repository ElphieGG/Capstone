<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Prepare SweetAlert success flag
$showSweetAlert = false;

if (isset($_POST['book_id']) && isset($_POST['bid_amount'])) {
    $bookId = intval($_POST['book_id']);
    $bidAmount = floatval($_POST['bid_amount']);
    $userId = $_SESSION['user_id'];

    // Get book details
    $stmt = $conn->prepare("SELECT bidding_start_price, user_id FROM tblbook WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();

    // Check if owner
    if ($book['user_id'] == $userId) {
        echo "<script>alert('You cannot bid on your own book.'); window.location.href='books_for_bidding.php';</script>";
        exit();
    }

    // Get highest current bid
    $bidStmt = $conn->prepare("SELECT MAX(bid_amount) AS highest_bid FROM tblbids WHERE book_id = ?");
    $bidStmt->bind_param("i", $bookId);
    $bidStmt->execute();
    $currentBid = $bidStmt->get_result()->fetch_assoc();

    $minBid = $currentBid['highest_bid'] ? $currentBid['highest_bid'] : $book['bidding_start_price'];

    if ($bidAmount > $minBid) {
        // Insert bid
        $insert = $conn->prepare("INSERT INTO tblbids (book_id, user_id, bid_amount) VALUES (?, ?, ?)");
        $insert->bind_param("iid", $bookId, $userId, $bidAmount);
        if ($insert->execute()) {
            $showSweetAlert = true; // Flag to trigger SweetAlert later
        } else {
            echo "<script>alert('Error submitting bid.'); window.location.href='books_for_bidding.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Your bid must be higher than the current highest bid.'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bid Result</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($showSweetAlert): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Bid Submitted Successfully!',
    text: 'Your bid has been recorded!',
    confirmButtonColor: '#3085d6',
    confirmButtonText: 'OK'
}).then((result) => {
    if (result.isConfirmed) {
        window.location.href='books_for_bidding.php';
    }
});
</script>
<?php endif; ?>
</body>
</html>
