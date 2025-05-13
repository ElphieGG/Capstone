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
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    line-height: 1.6;
    margin: 0;
}

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


.search-bar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    background-color: #f4f4f4;
    border-bottom: 1px solid rgb(126, 0, 0);
}

.search-bar-container h1 {
    color: rgb(126, 0, 0);
    margin: 0;
    flex: 1;
}

.search-bar {
    width: 400px;
    padding: 10px;
    border: 1px solid rgb(126, 0, 0);
    border-radius: 4px;
    font-size: 16px;
    margin-right: 10px;
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

.search-button:hover {
    opacity: 0.8;
}
            /* General styles */
            body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
            background-color: #f7f8fc;
        }

        /* Header styles */
        header {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
            text-align: center;
            padding: 2rem 0;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        /* Main content styles */
        main {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Section styles */
        section {
            margin-bottom: 2rem;
        }

        section h2, section h3 {
            color: #2575fc;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        section p {
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: justify;
        }

        section ul, section ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        section ul li, section ol li {
            margin-bottom: 0.5rem;
        }

        /* Links */
   a {
            /*color: #2575fc;*/
            color:red;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Footer styles */
        footer {
            text-align: center;
            background: #6a11cb;
            color: #fff;
            padding: 1rem 0;
            margin-top: 2rem;
            border-top: 4px solid #2575fc;
        }

        footer p {
            margin: 0;
        }

        /* Button styles */
        button, a.button {
            display: inline-block;
            background: #2575fc;
            color: #fff;
            padding: 0.8rem 1.5rem;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 1rem;
            text-decoration: none;
        }

        button:hover, a.button:hover {
           /*  background: #1a61c8;*/
           background:rgb(246, 72, 72);
            
        }

        /* Responsive design */
        @media (max-width: 768px) {
            main {
                padding: 1rem;
            }

            header h1 {
                font-size: 2rem;
            }

            section h2, section h3 {
                font-size: 1.5rem;
            }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f3e5e5;
            color: #333;
        }

        /* Header */
        .header {
            background: #800000; /* Maroon */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: bold;
        }
        .nav {
            display: flex;
            gap: 15px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            background: #a52a2a; /* Softer Maroon */
            font-size: 0.9em;
        }
        .nav a:hover {
            background: #660000; /* Darker Maroon */
        }

        /* Search Bar */
        .search-bar input[type="text"] {
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            width: 200px;
        }
        .search-bar button {
            padding: 8px 15px;
            border: none;
            background: #a52a2a;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background: #660000;
        }

        /* Hero Section */
        .hero {
            /*background: #ffffff;    */
            background: rgb(243, 233, 233);
            padding: 10px 10px;
            
        }
      
        .hero h1{
            color: #800000; /* Maroon */
            text-align: center;
        }
        .hero h2 {
            font-size: 2.2em;
            margin: 0;
            color: #800000; /* Maroon */
        }
        .hero h3 {
            font-size: 1.8em;
            color: #800000;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.2em;
            color: #800000;
        }

        /* How It Works Section */
        .how-it-works {
           
          /*  background: #d7bcbc; /* Light Maroon Tint */
            background: rgb(243, 233, 233);
            padding: 30px 20px;
            text-align: center;
        }
        .how-it-works h3 {
            font-size: 1.8em;
            color: #800000;
            margin-bottom: 20px;
        }
        .steps {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .step {
           /* background: #f3e5e5; /* Light Maroon Background */
           background: #ffffff;   
            padding: 20px;
            border-radius: 8px;
            width: 220px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .step h4 {
            font-size: 1.2em;
            color: #800000;
        }
        .step p {
            font-size: 0.9em;
            color: #666;
        }

        /* Call to Action */
        .cta {
            background: #800000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .cta a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            background: #a52a2a;
            font-size: 1.2em;
            margin-top: 10px;
            display: inline-block;
        }
        .cta a:hover {
            background: #660000;
        }

        .center {
  margin-left: 180px;
  margin-right: auto;
}

.books-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .book-container {
            text-align: center;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
            font-size: 14px; 
        }
     

        .book-container:hover {
            transform: scale(1.05);
        }

        .book {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .book-title {
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
        }

        .book-author {
            font-size: 12px;
            color: #555;
        }

        #suggestions {
    position: absolute;
    width: 200px;
    background: #fff;
    border: 1px solid #ccc;
    max-height: 150px;
    overflow-y: auto;
    z-index: 1000;
}

.suggestion-item {
    padding: 10px;
    cursor: pointer;
}

.suggestion-item:hover {
    background: #f0f0f0;
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

    </style>
</head>
<body>

<!-- Navigation Bar 
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Book Trading System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="booklist_for_exchange.php">View Books</a></li>
        <li class="nav-item"><a class="nav-link" href="trade_requests.php">Trade Requests</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>-->
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
                            <a href="request_trade.php?book_id=<?= $book['id'] ?>" class="btn btn-primary" style="display: block; margin: 0 auto; width: fit-content;">Request Exchange</a>
                        </button>
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

