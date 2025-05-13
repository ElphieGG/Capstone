<?php
include 'config.php';
session_start();

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$limit = 8;
$offset = ($page - 1) * $limit;

// Count total books
$countResult = $conn->query("SELECT COUNT(*) as total FROM tblbook");
$totalBooks = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $limit);

// Fetch paginated books
$sql = "
    SELECT tblbook.id, tblbook.title, tblbook.image, tbluser.first_name, tbluser.last_name, tblbook.book_status
    FROM tblbook 
    JOIN tbluser ON tblbook.user_id = tbluser.user_id
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
    <title>Libro Compartir | UserForyouPage</title>
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
    background-color: rgb(126, 0, 0);
    width: 100%;
    padding: 15px 20px; 
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.navbar .logo {
    width: 200px;
    cursor: pointer;
}

.navbar .nav-links {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

.navbar .nav-links li {
    margin: 0 15px;
}

.navbar .nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
}

.navbar .nav-links a:hover {
    text-decoration: underline;
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
    background-color: rgb(126, 0, 0);
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

        .book-container {
        position: relative;
        display: inline-block;
    }

    .tooltip-text {
        visibility: hidden;
        background-color:  #660000;
        color: white;
        text-align: center;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 15px;
        position: absolute;
        bottom: 110%;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        z-index: 10;
    }

    .book-container:hover .tooltip-text {
        visibility: visible;
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

    </style>
</head>
<body>
<style>
.sidebar {
    width: 220px;
    background-color: #f4eaea;
    padding: 20px;
    position: fixed;
    height: 100%;
    top: 0;
    left: 0;
}

.sidebar a {
    display: block;
    margin: 10px 0;
    padding: 12px;
    background-color: #8b0000;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 6px;
    text-align: center;
}

.sidebar a:hover {
    background-color: #a30000;
}

.content {
    margin-left: 250px;
    padding: 20px;
}
</style>

<div class="sidebar">
    <a href="#">View Books for Sale</a>
    <a href="#">View Books for Bidding</a>
    <a href="#">View Books for Exchange</a>
    <a href="#">Trade Requests</a>
</div>
<div class="content">

    <nav class="navbar">
        <img src="images/logo2.png" class="logo" alt="Logo">
        <ul class="nav-links">
        <li><a href="userfyp.php">Home</a></li>
        <li><a href="chat.php">Chat</a></li>
        <li><a href="user.php">Profile</a></li>
        <li><a href="notifications.php">Notification</a></li>
        <li><a href="cart.php">Cart</a></li>
            <li><a href="login.php">Sign Out</a></li>
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

    <div class="side-button-container" style="position: absolute; right: 20px; top: 200px;">
    
    <button class="search-button"><a href="books_for_sale.php" style="color: white; text-decoration: none;">View Books for Sale</a></button><br>
    <button class="search-button"><a href="books_for_bidding.php" style="color: white; text-decoration: none;">View Books for Bidding</a></button><br>
    <button class="search-button"><a href="booklist_for_exchange.php" style="color: white; text-decoration: none;">View Books for Exchange</a></button><br>
    <button class="search-button"><a href="trade_requests.php" style="color: white; text-decoration: none;">Trade Requests</a></button>    
</div>
    
    <main>   
    <div class="search-bar-container">
        <h1>All Books</h1>        
    </div> 
    <div class="books-wrapper" id="book-list" >
    <?php include 'fetch_books.php'; ?>
    <?php
    foreach ($books as $book) {
        $imageData = base64_encode($book['image']);
        echo '<a href="viewbook.php?id=' . $book['id'] . '" class="book-container">';
        echo '<span class="tooltip-text">Status: ' . htmlspecialchars($book['book_status']) . '</span>';
        echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="book" alt="book">';
        echo '<p class="book-title">' . htmlspecialchars($book['title']) . '</p>';
        echo '<p class="book-author">Posted by: ' . htmlspecialchars($book['first_name'] . ' ' . $book['last_name']) . '</p>';
        echo '</a>';
    }
    ?>
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
                url: "fetch_books.php",
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

<div style="margin-top: 20px; text-align: center;">
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= ($i == $page) ? 'font-weight: bold; text-decoration: underline;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
</div>
</div>
</body>
</html>

