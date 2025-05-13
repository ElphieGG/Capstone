<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT 
            t.trade_id,
            ob.title AS offered_book_title,
            ob.image AS offered_book_image,
            rb.title AS requested_book_title,
            rb.image AS requested_book_image,
            u.first_name, 
            u.last_name,
            t.status
          FROM tblbooktrades t
          JOIN tblbook ob ON t.offered_book_id = ob.id
          JOIN tblbook rb ON t.requested_book_id = rb.id
          JOIN tbluser u ON t.offered_by_user_id = u.user_id
          WHERE t.requested_user_id = ? 
          ORDER BY t.offer_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Offers</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel ="stylesheet" href = "navbar.css">

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

        body {
            font-family: Arial, sans-serif;
            background: #f7f9fb;
            padding: 20px;
        }
        .trade-card {
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .trade-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .trade-images {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            margin-bottom: 15px;
        }
        .trade-images img {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .trade-info {
            text-align: center;
            margin-bottom: 10px;
        }
        .trade-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }
        .trade-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        button.accept-btn {
            background-color: #4CAF50;
            color: white;
        }
        button.decline-btn {
            background-color: #f44336;
            color: white;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
            margin-top: 5px;
        }
        .pending {
            background-color: #ff9800;
        }
        .accepted {
            background-color: #4CAF50;
        }
        .declined {
            background-color: #f44336;
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

   <!----    <div class="side-button-container" style="position: absolute; right: 20px; top: 200px;"> ---->
        <ul class="navmenu-links">
 <li> <a href="books_for_sale.php" style="color: red; text-decoration: underline;">View Books for Sale</a></li>
 <li><a href="books_for_bidding.php" style="color: red; text-decoration: underline;">View Books for Bidding</a></li>
 <li><a href="booklist_for_exchange.php" style="color: red; text-decoration: underline;">View Books for Exchange</a></li>
 <li><a href="view_trades.php" style="color: red; text-decoration: underline;">View Trade Offers</a></li> 
    
    </ul>
 
 <!---- </div> ---->
    
    <main>   
    <div class="search-bar-container">
<h1>Trade Offers</h1>
</div> 


<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $offered_img = 'data:image/jpeg;base64,' . base64_encode($row['offered_book_image']);
        $requested_img = 'data:image/jpeg;base64,' . base64_encode($row['requested_book_image']);
?>
    <div class="trade-card">
        <div class="trade-images">
            <div>
                <img src="<?= $offered_img ?>" alt="Offered Book">
                <p><strong><?= htmlspecialchars($row['offered_book_title']) ?></strong></p>
            </div>
            <div><strong>⇆</strong></div>
            <div>
                <img src="<?= $requested_img ?>" alt="Requested Book">
                <p><strong><?= htmlspecialchars($row['requested_book_title']) ?></strong></p>
            </div>
        </div>

        <div class="trade-info">
            <p>Offered by: <strong><?= htmlspecialchars($row['first_name']) ?> <?= htmlspecialchars($row['last_name']) ?></strong></p>
            <span class="status-badge <?= htmlspecialchars($row['status']) ?>">
                <?= ucfirst($row['status']) ?>
            </span>
        </div>

        <?php if ($row['status'] == 'pending'): ?>
        <div class="trade-actions">
            <form action="respond_trade.php" method="post">
                <input type="hidden" name="trade_id" value="<?= $row['trade_id'] ?>">
                <button type="submit" name="response" value="accepted" class="accept-btn">Accept</button>
                <button type="submit" name="response" value="declined" class="decline-btn">Decline</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
<?php
    }
} else {
    echo "<p>No trade offers at the moment.</p>";
}
?>

</body>
</html>
