<?php
session_start();
include('config.php'); // adjust this if your connection file has a different name

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch logged-in user info
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, image FROM tbluser WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$currentUserId = $_SESSION['user_id'];

// Fetch notifications for the current user
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

// Query to get notifications including bid info
$query = "
SELECT 
    n.id AS notification_id,
    n.message,
    n.book_id,
    b.title,
    b.image,
    b.book_status,
    (
        SELECT MAX(bid_amount) 
        FROM bid 
        WHERE bid.book_id = b.id
    ) AS current_highest_bid,
    od.status AS order_status
FROM notifications n
LEFT JOIN tblbook b ON n.book_id = b.id
LEFT JOIN order_details od ON od.product_id = b.id
WHERE n.user_id = ?
ORDER BY n.id DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Notifications</title>
    <link rel="stylesheet" href="navbar.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .notification {
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .notification.unread {
            background-color: #e8f4ff;
            font-weight: bold;
        }
        .notification small {
            display: block;
            margin-top: 5px;
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
<div class="content-container">
        <!-- User Profile Box -->
        <div class="user-box">
  

         <div style="text-align: center;">
    <?php if (!empty($user['image'])): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($user['image']) ?>" alt="Profile Picture" style="width: 120px; height: 120px; border-radius: 50%;">
    <?php else: ?>
        <img src="default_profile_picture.png" alt="Profile Picture" style="width: 120px; height: 120px; border-radius: 50%;">
    <?php endif; ?>

    <h2><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
</div>
<!-- 
    <?php 
    if (!empty($user_data['first_name']) && !empty($user_data['last_name'])) {
        echo htmlspecialchars($user_data['first_name'] . " " . $user_data['last_name']);
    } else {
        echo "User Name Not Available";
    }
    ?> -->
</h2>
<ul class="user-menu">
                <li><a href="user.php">My Books</a></li>
                <li><a href="usersale.php">My Sales</a></li>
                <li><a href="userpurchase.php">My Purchases</a></li>
                <li><a href="my_trade_history.php">My Trades</a></li>
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>

<!-- My Sales Section -->
<div class="products-box">
    <div class="products-header">


<h1>📩 My Notifications</h1>
</div>
<?php if ($result->num_rows > 0): ?>
    
    <ul>
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="notification-card">
        <?php
            $imageSrc = 'data:image/jpeg;base64,' . base64_encode($row['image']);
        ?>
        <img src="<?= $imageSrc ?>" class="book" alt="<?= htmlspecialchars($row['title']) ?>">
        
        <p><strong>Book:</strong> <?= htmlspecialchars($row['title']) ?></p>
        <p><strong>Message:</strong> <?= htmlspecialchars($row['message']) ?></p>

        <?php if (!empty($row['current_highest_bid'])): ?>
            <p><strong>Winning Bid Price:</strong> <?= htmlspecialchars(number_format($row['current_highest_bid'], 2)) ?> PHP</p>
        <?php endif; ?>

        <?php
        // Condition to show "Proceed to Checkout"
        $allowedToCheckout = true;
        if (!empty($row['order_status']) && in_array($row['order_status'], ['sold', 'approved'])) {
            $allowedToCheckout = false;
        }
        ?>

        <?php if ($allowedToCheckout): ?>
            <form action="confirm_checkout_bidding.php" method="post">
                <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
                <button type="submit" name="checkout">Proceed to Checkout</button>
            </form>
        <?php else: ?>
            <p style="color: red;"><strong>This book is already sold or checkout approved.</strong></p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

</ul>
<?php else: ?>
    <p>No notifications yet.</p>
<?php endif; ?>
<?php $stmt->close(); ?>
<?php $conn->close(); ?>
</body>
</html>
<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

/* Navbar */
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

/* Content Layout */
.content-container {
    display: flex;
    padding: 20px;
    gap: 20px;
}

/* User Profile Box */
.user-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 350px;
    height: 480px; /* Increased height */
    text-align: center;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

.profile {
    border-radius: 50%;
}

.user-menu {
    list-style: none;
    padding: 0;
}

.user-menu li {
    padding: 10px 0;
}

.user-menu a {
    text-decoration: none;
    color: black;
    font-weight: bold;
}

/* Products Box */
.products-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    flex-grow: 1;
    height: 480px; /* Increased height */
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}
.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-action {
   /* background: #a52a2a;*/
    background: #660000;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn-action a {
    color: white;
    text-decoration: none;
}

/* Book Grid */
.book-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.book-item {
    background: white;
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 200px;
}

.book-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 6px;
}

.book-item p {
    font-size: 14px;
    margin-top: 5px;
    font-weight: bold;
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
     
/* Table Styling */
.sales-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Table Header */
.sales-table thead {
    background-color: #007bff;
    color: white;
}

.sales-table th {
    padding: 12px;
    text-align: left;
}

/* Table Body */
.sales-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

/* Alternating row colors */
.sales-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* No sales message */
.no-sales {
    text-align: center;
    padding: 15px;
    font-size: 16px;
    color: #666;
    font-weight: bold;
}

/* Rating and review alignment */
.rating {
    text-align: center;
    font-weight: bold;
}

/* Not Rated / No Review styling */
.no-review {
    color: #888;
    font-style: italic;
}

/* Book Image Styling */
.book-image {
    width: 60px;
    height: auto;
    border-radius: 5px;
}

/* Image Cell Styling */
.image-cell {
    text-align: center;
}
</style>