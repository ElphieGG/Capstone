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

$book_id = intval($_GET['book_id']);
$user_id = $_SESSION['user_id'];

// Fetch book details
$query = "SELECT b.title, b.image, b.winner_user_id, b.bidding_start_price, 
                 (SELECT MAX(bid_amount) FROM tblbids WHERE book_id = b.id) AS winning_bid
          FROM tblbook b
          WHERE b.id = ? AND b.winner_user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "You are not the winner of this book or book not found.";
    exit();
}

$book = $result->fetch_assoc();

// Prepare image for display
$imageData = base64_encode($book['image']);
$imageSrc = "data:image/jpeg;base64," . $imageData;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Bidding</title>
    <link rel="stylesheet" href="navbar.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
<div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
</div>
    <ul class="nav-links">
        <li><a href="userfyp.php"><i class='bx bx-home'></i> Home</a></li>
        <li><a href="chat.php"><i class='bx bx-chat'></i> Chat</a></li>
        <li><a href="user.php"><i class='bx bx-user'></i> Profile</a></li>
         <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li>
        <li><a href="cart.php"><i class='bx bx-cart'></i> Cart</a></li>
        <li><a href="login.php"><i class='bx bx-log-out'></i> Sign Out</a></li>
    </ul>
</nav>
<div class="products-box">
<div class="products-header">

<h2>Confirm Your Winning Book</h2>
</div>
<div>
    <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($book['title']) ?>" width="150" height="200">
    <h3><?= htmlspecialchars($book['title']) ?></h3>
    <p>Winning Bid: <?= number_format($book['winning_bid'], 2) ?> PHP</p>
</div>

<form action="confirm_checkout_bidding.php" method="POST">
    <input type="hidden" name="book_id" value="<?= $book_id ?>">
    <input type="hidden" name="title" value="<?= htmlspecialchars($book['title']) ?>">
    <input type="hidden" name="price" value="<?= $book['winning_bid'] ?>">
    
    <label for="payment_method">Select Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="Cash on Delivery">Cash on Delivery</option>
        <option value="Online Payment">Online Payment</option>
    </select><br><br>
    
    <button type="submit" name="confirm_checkout">Confirm Checkout</button>
</form>

</body>
</html>
