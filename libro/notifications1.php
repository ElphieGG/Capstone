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

$purchases_stmt = $conn->prepare("SELECT 
    od.order_id,
    b.id AS book_id,
    b.title,
    b.image,
    od.quantity,
    od.price,
    o.total_price,
    o.order_date,
    (SELECT COUNT(*) FROM tblreviews r WHERE r.user_id = o.user_id AND r.id = b.id) AS rated
FROM orders o
JOIN order_details od ON o.orders_id = od.order_id
JOIN tblbook b ON od.product_id = b.id
WHERE o.user_id = ?;");

if (!$purchases_stmt) {
    die("Prepare failed: " . $conn->error);
}
$purchases_stmt->bind_param("i", $user_id);
$purchases_stmt->execute();
$purchases_result = $purchases_stmt->get_result();

$purchases = [];
if ($purchases_result->num_rows > 0) {
    while ($row = $purchases_result->fetch_assoc()) {
        $purchases[] = $row;
    }
}
$purchases_stmt->close();

$userId = $_SESSION['user_id']; // Get the logged-in user ID

// Fetch unread notifications
$sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases</title>
    <link rel="stylesheet" href="userstyle.css">
    <style>
        .book-item {
            position: relative;
            text-align: center;
            width: 120px;
            background: white;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .book-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
        }
        .btn-action {
            display: none;
            position: absolute;
            top: 50%;
            left: 35%;
            transform: translate(-50%, -50%);
            background: black;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .book-item:hover .btn-action {
            display: block;
        }
        .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        position: relative; /* This ensures the close button is positioned inside */
        background: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        width: 350px;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: #555;
    }
    .close:hover {
        color: #000;
    }
    .rating-input {
        width: 100%;
        padding: 8px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        text-align: center;
    }
    .submit-btn {
        background: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }
    .submit-btn:hover {
        background: #45a049;
    }
    </style>
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
                <!-- <li><a href="notifications.php">My Notifications</a></li> -->
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>
        
 <!-- Include SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- My Books Section -->
<div class="products-box">
   

    <div class="book-container">
      <!-- Notifications Section -->
 <div class="notifications">
            <h2>My Notifications</h2>
            <ul>
                <?php if (!empty($notifications)) : ?>
                    <?php foreach ($notifications as $notification) : ?>
                        <li>
                            <?= htmlspecialchars($notification['message']) ?>
                            <form action="mark_notification_read.php" method="post" style="display: inline;">
                                <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                <button type="submit" class="mark-read-btn">Mark as Read</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No new notifications.</p>
                <?php endif; ?>
            </ul>
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

</style>