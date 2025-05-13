<?php
include('config.php');
session_start();  // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in. <a href='login.php'>Login here</a>");
}

// Ensure database connection is working
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch books from other users with 'For Exchange' status
//$user_id = $_SESSION['user_id'];
//$sql = "SELECT * FROM tblbook WHERE user_id != ? AND book_status = 'For Exchange'";

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$limit = 8;
$offset = ($page - 1) * $limit;

// Count total books for sale
$countResult = $conn->query("SELECT COUNT(*) as total FROM tblbook WHERE book_status = 'For Sale'");
$totalBooks = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $limit);

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$sql = "SELECT tblbook.*, tbluser.first_name, tbluser.last_name,tblbook.book_status
        FROM tblbook
        JOIN tbluser ON tblbook.user_id = tbluser.user_id 
        WHERE tblbook.user_id != ? AND tblbook.book_status = 'For Exchange'
         LIMIT $limit OFFSET $offset
        ";
         
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

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

   <style>


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
        <h1>Available Books for Exchange</h1>        
    </div> 

<!-- Optional: Display User Info 
<div class="container mt-3">
  <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
</div>-->

<!-- Display Books -->
<div class="container mt-4">
  <!--   <h2>Available Books for Exchange</h2> -->
    
    <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card mb-4 ">
                        <?php if (!empty($book['image'])): ?>
                            <!--   <img src="data:image/jpeg;base64,<?= base64_encode($book['image']) ?>" class="card-img-top" style="width:100px;height: 150px; object-fit: cover; ">-->
                            <img src="data:image/jpeg;base64,<?= base64_encode($book['image']) ?>" class="card-img-top" style="width:100px;height: 150px; object-fit: cover; display: block; margin: 0 auto;">

                        <?php else: ?>
                             <!--    <img src="assets/default-book.jpg" class="card-img-top" style="height: 200px; object-fit: cover;">-->
                                  <img src="data:image/jpeg;base64,<?= base64_encode($book['image']) ?>" class="card-img-top" style="width:100px;height: 150px; object-fit: cover; display: block; margin: 0 auto;">
                        <?php endif; ?>
                        <div class="card-body">
                        <h5 class="card-title" style="font-size: 14px; font-weight: bold;  text-align: center;"">
        <?= htmlspecialchars($book['title']) ?>
    </h5>             
    <p style="font-size: 12px; color: #555; text-align: center;">
        Posted by: <?= htmlspecialchars($book['first_name'] . ' ' . $book['last_name']) ?>
    </p>
                            <!-- <a href="offer_trade.php?book_id=<?= $book['id'] ?>" class="btn btn-primary" style="display: block; margin: 0 auto; width: fit-content;">Request Exchange</a>
                        </button> -->

                         <!-- Offer Trade Button -->
        <?php if ($_SESSION['user_id'] != $book['user_id']) { ?>
            <form action="offer_trade.php" method="get" style="margin-top:10px;">
                <input type="hidden" name="requested_user_id" value="<?= $book['user_id']; ?>">
                <input type="hidden" name="requested_book_id" value="<?= $book['id']; ?>">
                <div style="display: flex; justify-content: center; margin-top: 10px;">
    <button class="offer-trade" style="padding: 10px 20px;">Offer Trade</button>
</div>
            </form>
        <?php } ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No books available for trade at the moment.</p>
    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("#search").keyup(function() {
        let query = $(this).val();

        if (query.length > 1) {
            $.ajax({
                url: "fetch_books_for_exchange.php",
                method: "POST",
                data: { query: query },
                dataType: "json",
                success: function(data) {
                    let suggestions = $("#suggestions");
                    suggestions.empty();

                    if (data.length > 0) {
                        data.forEach(book => {
                            suggestions.append(
                                `<div class='suggestion-item' data-id='${book.id}'>${book.title}</div>`
                            );
                        });
                    } else {
                        suggestions.append("<div class='suggestion-item'>No results found</div>");
                    }

                    $(".suggestion-item").click(function() {
                        $("#search").val($(this).text());
                        $("#suggestions").empty();
                        window.location.href = "viewbook.php?id=" + $(this).data("id");
                    });
                }
            });
        } else {
            $("#suggestions").empty();
        }
    });

    $(document).click(function(event) {
        if (!$(event.target).closest("#search, #suggestions").length) {
            $("#suggestions").empty();
        }
    });
});
</script>


<div style="display: flex; justify-content: center; margin: 30px 0;">
<?php if ($totalPages > 1): ?>
    <div class="pagination" style="display: flex; gap: 10px;">
        <?php if ($page > 1): ?>
            <!--- <a href="?page=<?= $page - 1 ?>" style="padding: 8px 12px; background: #eee; text-decoration: none;">&laquo; Previous</a> --->
            <a href="?page=<?= $page - 1 ?>" style="margin: 0 10px;">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <!---   <a href="?page=<?= $i ?>" style="padding: 8px 12px; background: <?= ($i == $page) ? '#ccc' : '#fff' ?>; text-decoration: none; border: 1px solid #ddd;"> --->
            <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= ($i == $page) ? 'font-weight: bold; text-decoration: underline;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
           <!---  <a href="?page=<?= $page + 1 ?>" style="padding: 8px 12px; background: #eee; text-decoration: none;">Next &raquo;</a> --->
           <a href="?page=<?= $page + 1 ?>" style="margin: 0 10px;">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
</body>
</html>

