<?php
include 'config.php';
session_start();
// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$limit = 8;
$offset = ($page - 1) * $limit;

// Count total books for sale
$countResult = $conn->query("SELECT COUNT(*) as total FROM tblbook WHERE book_status = 'For Sale'");
$totalBooks = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $limit);

// Fetch books and user information
$sql = "
    SELECT tblbook.id, tblbook.title, tblbook.image, tblbook.bidding_start_price, tblbook.buy_out_price, 
           tblbook.bidding_end_time, tbluser.first_name, tbluser.last_name, tblbook.book_status,
           (SELECT MAX(bid_amount) FROM bids WHERE bids.book_id = tblbook.id) AS current_highest_bid
    FROM tblbook 
    JOIN tbluser ON tblbook.user_id = tbluser.user_id
    WHERE tblbook.book_status = 'For Bidding'
    LIMIT $limit OFFSET $offset

";
$result = $conn->query($sql);

if ($result === false) {
    die("SQL Error: " . $conn->error);
}

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books for Bidding</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="books_for_bidding.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
.navbar {
    background: linear-gradient(to right, #f52222, #e60000);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 40px;
    font-family: 'Segoe UI', sans-serif;
}

.logo {
    height: 30px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s, padding 0.3s;
    padding: 6px 10px;
    border-radius: 5px;
}

.nav-links li a:hover {
    background: rgba(255, 255, 255, 0.2);
}

.nav-links li a i {
    font-size: 18px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  font-size: 22px;
  font-weight: bold;
  font-style: italic;
}

.brand-logo i {
  font-size: 28px;
}

.menubar {
    text-align: center;
    margin: 20px auto;
}

.navmenu-links {
    list-style: none;
    padding: 0;
    margin: 30px auto;
    text-align: center;
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.navmenu-links li a {
    font-weight: bold;
    font-size: 16px;
    color: red;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 6px;
    transition: background-color 0.3s;
}
.search-button {
    background: linear-gradient(to right, #f52222, #e60000);
color: white;
border: none;
padding: 10px 15px;
border-radius: 4px;
cursor: pointer;
font-size: 16px;
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
        <!-- <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li> -->
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
 <li><a href="trade_requests.php" style="color: red; text-decoration: underline;">Trade Requests</a></li> 
    
    </ul>
    
    <main> 

<main>
    <h1>Books for Bidding</h1>
    <div class="books-wrapper">
        <?php foreach ($books as $book): 
            $minBid = max($book['bidding_start_price'], $book['current_highest_bid'] ?? 0);
            $isBiddingClosed = strtotime($book['bidding_end_time']) < time(); // Check if bidding is closed
        ?>
            <div class="book-container">
                <!-- Book Image 
                <img src="<?= htmlspecialchars($book['image']) ?>" class="book" alt="<?= htmlspecialchars($book['title']) ?>">-->
                <?php
$imageSrc = 'data:image/jpeg;base64,' . base64_encode($book['image']);
?>
<img src="<?= $imageSrc ?>" class="book" alt="<?= htmlspecialchars($book['title']) ?>">

                <p class="book-title"><?= htmlspecialchars($book['title']) ?></p>
                <p class="book-author">Posted by: <?= htmlspecialchars($book['first_name']) . ' ' . htmlspecialchars($book['last_name']) ?></p>
                <p class="book-title">Bidding Start Price: <?= htmlspecialchars($book['bidding_start_price']) ?> PHP</p>
                <p class="book-title">Current Highest Bid: <?= $book['current_highest_bid'] ? $book['current_highest_bid'] : "No bids yet" ?> PHP</p>
                <!-- <p class="book-title">Buy Out Price: <?= htmlspecialchars($book['buy_out_price']) ?> PHP</p> -->

                <!-- Countdown Timer -->
                <p class="book-title">Time Remaining:  <p class="countdown" id="countdown-<?= $book['id'] ?>" data-endtime="<?= $book['bidding_end_time'] ?>">Loading...</p>

                <!-- Buy Now Button (Disabled if bidding closed) -->
                <!-- <form action="cart.php" method="post">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <input type="hidden" name="price" value="<?= $book['buy_out_price'] ?>">
                    <button type="submit" class="buy-now-btn" <?= $isBiddingClosed ? 'disabled style="background-color:gray; cursor:not-allowed;"' : '' ?>>Buy Now</button>
                </form> -->

                <!-- Bid Button (Disabled if bidding closed) -->
                <button class="bid-btn" onclick="showBidForm(<?= $book['id'] ?>)" <?= $isBiddingClosed ? 'disabled style="background-color:gray; cursor:not-allowed;"' : '' ?>>Bid</button>

                <!-- Hidden Bid Form -->
                <form class="bid-form" id="bid-form-<?= $book['id'] ?>" style="display: none;">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <input type="number" name="bid_amount" min="<?= $minBid + 1 ?>" placeholder="Enter bid (Min: <?= $minBid + 1 ?>) " required  style="width: 120px; text-align: center; padding: 5px; font-size: 14px;">
                    <button type="submit" class="bid-btn">Submit Bid</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    
</main>

<script>
    function showBidForm(bookId) {
        let bidForm = document.getElementById("bid-form-" + bookId);
        bidForm.style.display = bidForm.style.display === "none" ? "block" : "none";
    }

    $(document).ready(function () {
        $(".bid-form").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var bidAmount = form.find("input[name='bid_amount']").val();
            var bookId = form.find("input[name='book_id']").val();

            $.ajax({
                url: "place_bid.php",
                type: "POST",
                data: { bid_amount: bidAmount, book_id: bookId },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Bid Placed Successfully!",
                            text: response.message,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                    });
                },
            });
        });

        // Countdown Timer Function
        function updateCountdown() {
            $(".countdown").each(function () {
                let countdownElem = $(this);
                let endTime = new Date(countdownElem.data("endtime")).getTime();
                let now = new Date().getTime();
                let timeLeft = endTime - now;

                let bookId = countdownElem.attr("id").split("-")[1]; 
                let bidButton = $("button[onclick='showBidForm(" + bookId + ")']");
                let buyNowButton = $("form[action='cart.php'] input[name='book_id'][value='" + bookId + "']").closest("form").find(".buy-now-btn");

                if (timeLeft <= 0) {
                    countdownElem.text("Bidding closed");
                    bidButton.prop("disabled", true).css({ "background-color": "gray", "cursor": "not-allowed" });
                    buyNowButton.prop("disabled", true).css({ "background-color": "gray", "cursor": "not-allowed" });
                } else {
                    let days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                    countdownElem.text(`${days}d ${hours}h ${minutes}m ${seconds}s`);
                }
            });
        }

        setInterval(updateCountdown, 1000);
        updateCountdown(); // Run immediately to avoid 1-sec delay
    });
</script>

<div style="text-align: center; margin: 30px 0;">
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" style="margin: 0 10px;">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= ($i == $page) ? 'font-weight: bold; text-decoration: underline;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" style="margin: 0 10px;">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>

</body>
</html>
