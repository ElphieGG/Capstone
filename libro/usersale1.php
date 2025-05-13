<?php
session_start();
include 'config.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}




// Get user data based on session username
$username = $_SESSION['username'];
//$stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM tbluser WHERE username = ?");
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, image FROM tbluser WHERE username = ?");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['user_id'];
    $image_data = $user_data['image'];
} else {
    die("No user data found!");
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);
    $status = $_POST['action'] === 'approve' ? 'approved' : 'declined';

    if ($conn) {
        $stmt = $conn->prepare("UPDATE order_details SET status = ? WHERE order_id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $orderId);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Prepare failed: " . $conn->error;
        }
    } else {
        echo "Database connection failed.";
    }
}

//$sales_stmt = $conn->prepare("SELECT total_price FROM orders WHERE user_id = ?");
$sales_stmt = $conn->prepare("
    SELECT 
        o.orders_id,
        o.user_id AS buyer_id,
        od.product_id,
        b.title,
        b.image,
          u.first_name,
        u.last_name,
        od.quantity,
        od.price,
        o.order_date,
        od.status,
        r.book_rating,
        r.book_review,
        r.seller_rating,
        r.seller_review
    FROM orders o
    JOIN order_details od ON o.orders_id = od.order_id
    JOIN tblbook b ON od.product_id = b.id
    LEFT JOIN tblreviews r ON od.product_id = r.id AND o.user_id = r.user_id
    JOIN tbluser u ON o.user_id = u.user_id
    WHERE b.user_id = ?
    ORDER BY o.order_date DESC
");
$sales_stmt->bind_param("i", $seller_id);
$sales_stmt->execute();
$sales = $sales_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (!$sales_stmt) {
    die("Prepare failed: " . $conn->error);
}
$sales_stmt->bind_param("i", $user_id);
$sales_stmt->execute();
$sales_result = $sales_stmt->get_result();

$sales = [];
if ($sales_result->num_rows > 0) {
    while ($row = $sales_result->fetch_assoc()) {
        $sales[] = $row;
    }
}

$sales_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Sales</title>
    <link rel="stylesheet" href="userstyle.css">
</head>
<body>
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

    <div class="content-container">
        <!-- User Profile Box -->
        <div class="user-box">
        <!--   <img src="images/profile.jpg" class="profile" alt="Profile" style="width: 80px;"> -->

        <?php if (!empty($image_data)): ?>
    <img src="data:image/jpeg;base64,<?php echo base64_encode($image_data); ?>" class="profile" alt="Profile" style="width: 120px;"> 
<?php else: ?>
    <img src="images/default-profile.jpg" class="profile" alt="Default Profile">
<?php endif; ?>

         <!--     <h2><?php echo $user_data['first_name'] . " " . $user_data['last_name']; ?></h2>-->
          
         <h2>
    <?php 
    if (!empty($user_data['first_name']) && !empty($user_data['last_name'])) {
        echo htmlspecialchars($user_data['first_name'] . " " . $user_data['last_name']);
    } else {
        echo "User Name Not Available";
    }
    ?>
</h2>
            <ul class="user-menu">
            <li><a href="user.php">My Books</a></li>
                <li><a href="usersale.php">My Sales</a></li>
                <li><a href="userpurch.php">My Purchases</a></li>            
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>

<!-- My Sales Section -->
<div class="products-box">
    <div class="products-header">
        <h2>My Sales</h2>
    </div>

    <div class="book-container">
        <table class="sales-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Buyer Name</th>
        <th>Price</th>
        <th>Order Date</th>
        <th>Order Status</th>
        <th>Action</th>
                    <th>Book Rating</th>
                    <th>Book Review</th>
                    <th>Seller Rating</th>
                    <th>Seller Review</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($sales)): ?>
    <tr>
        <td colspan="6" class="no-sales">You have not made any sales yet.</td>
    </tr>
<?php else: ?>
    <?php foreach ($sales as $sale): ?>
        <tr>
            <td class="image-cell">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($sale['image']); ?>" class="book-image" alt="Sale">
            </td>
            <td><?php echo htmlspecialchars($sale['title']); ?></td>
            
            <td><?= htmlspecialchars($sale['first_name'] . ' ' . $sale['last_name']) ?></td>
        <td><?= $sale['price'] ?></td>
        <td><?= $sale['order_date'] ?></td>
        <td><?= htmlspecialchars($sale['status']) ?></td>

        <td>
        <a class='btn btn-success' href='edit_orderstatus.php?id=$row[detail_id]'>Edit</a>
         </td>  
          <td><?php echo !empty($sale['book_rating']) ? $sale['book_rating'] . "/5" : "<span class='no-review'>Not Rated</span>"; ?></td>
            <td><?php echo !empty($sale['book_review']) ? htmlspecialchars($sale['book_review']) : "<span class='no-review'>No Review</span>"; ?></td>
            <td><?php echo !empty($sale['seller_rating']) ? $sale['seller_rating'] . "/5" : "<span class='no-review'>Not Rated</span>"; ?></td>
            <td><?php echo !empty($sale['seller_review']) ? htmlspecialchars($sale['seller_review']) : "<span class='no-review'>No Review</span>"; ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


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
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    width: 100px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 15px;
}

.nav-links li {
    display: inline;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
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