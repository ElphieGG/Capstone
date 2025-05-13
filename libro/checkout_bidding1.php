<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['book_id'])) {
    echo "No book selected.";
    exit();
}

$bookId = intval($_GET['book_id']);
$userId = $_SESSION['user_id'];

// Fetch book + winning price
$query = "SELECT b.title, b.meeting_spot, b.image, MAX(d.bid_amount) AS winning_price
          FROM tblbook b
          JOIN tblbids d ON b.id = d.book_id
          WHERE b.id = ? AND b.winner_user_id = ?
          GROUP BY b.title, b.meeting_spot, b.image";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $bookId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $bookTitle = htmlspecialchars($row['title']);
    $meetingSpot = htmlspecialchars($row['meeting_spot']);
    $winningPrice = number_format($row['winning_price'], 2);
    $bookImage = base64_encode($row['image']);
} else {
    echo "You are not authorized to checkout this book.";
    exit();
}
?>

<h1>Checkout - Bidding</h1>

<div style="border: 1px solid #ccc; padding: 20px; width: 350px; margin: auto; border-radius: 10px; text-align: center;">
    <img src="data:image/jpeg;base64,<?= $bookImage ?>" alt="Book Image" style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px;">
    <h3><?= $bookTitle ?></h3>
    <p><strong>Meeting Spot:</strong> <?= $meetingSpot ?></p>
    <p><strong>Winning Bid Price:</strong> <?= $winningPrice ?> PHP</p>

    <form method="POST" action="confirm_checkout_bidding.php">
        <input type="hidden" name="book_id" value="<?= $bookId ?>">
        <input type="hidden" name="price" value="<?= $winningPrice ?>">

        <label for="payment_method"><strong>Select Payment Method:</strong></label><br><br>
        <select name="payment_method" id="payment_method" required>
            <option value="">--Select Payment--</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Online Payment">Online Payment</option>
        </select><br><br>

        <button type="submit" style="padding:10px 20px; background-color:green; color:white; border:none; border-radius:5px;">Confirm Purchase</button>
    </form>
</div>
