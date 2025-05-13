<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in."); // You can also redirect instead
}

$loggedInUserId = $_SESSION['user_id']; // Now it's safe to use


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
    SELECT tblbook.id, tblbook.title, tblbook.image, tblbook.user_id, 
           tbluser.first_name, tbluser.last_name, tblbook.book_status
    FROM tblbook 
    JOIN tbluser ON tblbook.user_id = tbluser.user_id
    WHERE tblbook.book_status = 'For Sale'
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
 } else {
    echo "No books found.";
 }

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserForyouPage</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
<link rel="stylesheet" href="books_for_sale.css">  
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        <h1>Books for Sale</h1>        
    </div>  

 
    <div class="books-wrapper">
    <?php
$loggedInUserId = $_SESSION['user_id']; // Get the logged-in user ID

foreach ($books as $book) {
    if (filter_var($book['image'], FILTER_VALIDATE_URL) || file_exists($book['image'])) {
        $imageSrc = htmlspecialchars($book['image']);
    } else {
        $imageSrc = 'data:image/jpeg;base64,' . base64_encode($book['image']);
    }

    echo '<div class="book-container">';
    echo '<a href="viewbook_details.php?id=' . $book['id'] . '">';
    echo '<img src="' . $imageSrc . '" class="book" alt="book">';
    echo '<p class="book-title">' . htmlspecialchars($book['title']) . '</p>';
    echo '<p class="book-author">Posted by: ' . htmlspecialchars($book['first_name']) . ' ' . htmlspecialchars($book['last_name']) . '</p>';
    echo '</a>';

    // Check if the book belongs to the logged-in user
    if ($loggedInUserId !== null && $book['user_id'] == $loggedInUserId) {
        echo '<button class="add-to-cart-btn owner-book-btn" onclick="ownerAlert()">Add to Cart</button>';
    } else {
        echo '<form action="cart.php" method="post">';
        echo '<input type="hidden" name="book_id" value="' . $book['id'] . '">';
        echo '<button type="submit" class="add-to-cart-btn">Add to Cart</button>';
        echo '</form>';
    }

    echo '</div>';
}
?>

</div>


</main> 
</div>

</main> 
       </div>
       <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("#search").keyup(function() {
        let query = $(this).val();

        if (query.length > 1) {
            $.ajax({
                url: "fetch_books_for_sale.php",
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
<script>
function ownerAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Oops!',
        text: 'You cannot add your own book to the cart.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
}
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
