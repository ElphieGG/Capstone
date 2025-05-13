<?php
session_start();
include('config.php');

date_default_timezone_set('Asia/Riyadh');
$serverNowDateTime = date('Y-m-d H:i:s');
$query = "SELECT b.*, u.first_name, u.last_name FROM tblbook b JOIN tbluser u ON b.user_id = u.user_id WHERE b.book_status = 'For Bidding' AND (b.bidding_closed = 0 OR b.bidding_closed IS NULL) ORDER BY b.bidding_end_time ASC";
$result = $conn->query($query);
$userId = $_SESSION['user_id'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserForyouPage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <!-- <link rel="stylesheet" href="assets/css/styles.css">  Optional custom styles 
   -->
   <link rel ="stylesheet" href = "navbar.css">
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.books-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}
.book-container {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    width: 180px;
    text-align: center;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.book-container img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}
.book-title {
    font-weight: bold;
    margin: 5px 0;
    font-size: 14px;
}
.book-author {
    font-size: 12px;
    color: gray;
}
</style>

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

    <div class="search-bar-container">
   <!---- <h1>Your Feed, <?php echo $_SESSION['username']; ?></h1> ---->
    <h1>Your Feed, <?php echo ucwords(strtolower($_SESSION['username'])); ?></h1>
         <!----  <input type="text" class="search-bar" placeholder="Search...">---->
         <div style="position: relative; width: 300px;">
    <input type="text" id="search" class="search-bar"placeholder="Search for a book..." autocomplete="off" style="width: 100%;">
    <div id="suggestions"style="width: 300px"; ></div>
   

</div>
        <button class="search-button">Search</button>
    </div>

    <ul class="navmenu-links">
 <li> <a href="books_for_sale.php" style="color: red; text-decoration: underline;">View Books for Sale</a></li>
 <li><a href="books_for_bidding.php" style="color: red; text-decoration: underline;">View Books for Bidding</a></li>
 <li><a href="booklist_for_exchange.php" style="color: red; text-decoration: underline;">View Books for Exchange</a></li>
 <li><a href="view_trades.php" style="color: red; text-decoration: underline;">View Trade Offers</a></li> 
    
    </ul>
    
    <main>   
    <div class="search-bar-container">
<h1>Books for Bidding</h1>
</div> 

<div style="margin-bottom: 20px;">
    <form action="announce_winners.php" method="POST" onsubmit="return confirm('Are you sure you want to announce winners?');">
        <!-- <button type="submit" style="padding:10px 20px; background-color:green; color:white; border:none; border-radius:5px;">Announce Winners</button> -->
        <button onclick="announceWinner('<?= htmlspecialchars($winner['first_name'] . ' ' . $winner['last_name']) ?>')">
    Announce Winner
</button>
    </form>
</div>

<div class="books-wrapper">
<script>
var serverNow = new Date("<?= $serverNowDateTime ?>").getTime();
var clientNowAtLoad = new Date().getTime();
var offset = clientNowAtLoad - serverNow;
</script>
<?php while ($row = $result->fetch_assoc()):
    $bookId = $row['id'];
    $biddingEnd = $row['bidding_end_time'];
    $isOwner = ($userId == $row['user_id']);
    $isBiddingClosed = strtotime($row['bidding_end_time']) <= strtotime($serverNowDateTime);
    $bidQuery = $conn->prepare("SELECT MAX(bid_amount) AS highest_bid FROM tblbids WHERE book_id = ?");
    $bidQuery->bind_param("i", $bookId);
    $bidQuery->execute();
    $bidResult = $bidQuery->get_result()->fetch_assoc();
    $highestBid = $bidResult['highest_bid'];
    $minBid = $highestBid ? $highestBid : $row['bidding_start_price'];

    $imageSrc = 'data:image/jpeg;base64,' . base64_encode($row['image']);
?>
    <div class="book-container">
        <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($row['title']) ?>">
        <div class="book-title"><?= htmlspecialchars($row['title']) ?></div>
        <div class="book-author">By <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
        <p>Bidding Start Price: <?= number_format($row['bidding_start_price'], 2) ?> PHP</p>
        <p id="countdown-<?= $bookId ?>"></p>

        <?php if (!$isOwner): ?>
            <button <?= $isBiddingClosed ? 'disabled' : '' ?>
                style="background-color: <?= $isBiddingClosed ? '#ccc' : '#007bff' ?>; color: white; padding:5px 10px; border:none; border-radius:5px; margin-top:5px;"
                onclick="openBidForm(<?= $bookId ?>)">
                <?= $isBiddingClosed ? 'Bidding Closed' : 'Place a Bid' ?>
            </button>
        <?php else: ?>
            <p style="color:red; font-size:12px; margin-top:5px;">(Your Book)</p>
        <?php endif; ?>

        <div id="bid-form-<?= $bookId ?>" style="display:none; margin-top:10px;">
            <form action="submit_bid.php" method="POST">
                <input type="hidden" name="book_id" value="<?= $bookId ?>">
                <input type="number" name="bid_amount" step="0.01" placeholder="Min: <?= number_format($minBid + 1,2) ?>" min="<?= $minBid + 1 ?>" required style="margin-bottom:5px;">
                <br>
                <button type="submit" style="background-color: #007bff; color: white; padding:5px 10px; border:none; border-radius:5px;">Submit</button>
            </form>
        </div>
    </div>

    <script>
    function openBidForm(bookId) {
        document.getElementById('bid-form-' + bookId).style.display = 'block';
    }
    var endTime = new Date("<?= $biddingEnd ?>").getTime();
    function countdown<?= $bookId ?>() {
        var now = new Date().getTime();
        var correctedNow = now - offset;
        var distance = endTime - correctedNow;

        if (distance <= 0) {
            document.getElementById("countdown-<?= $bookId ?>").innerHTML = "Bidding closed";
            return;
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown-<?= $bookId ?>").innerHTML =
            days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
    }
    setInterval(countdown<?= $bookId ?>, 1000);
    </script>
<?php endwhile; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function announceWinner(winnerName) {
    Swal.fire({
        icon: 'success',
        title: 'We have a winner!',
        text: 'Congratulations ' + winnerName + '!',
    });
}
</script>