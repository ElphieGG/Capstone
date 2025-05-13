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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Sales</title>
    <link rel="stylesheet" href="userstyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <li><a href="usersale_new.php">My Sales</a></li>
                <li><a href="userpurchase_new.php">My Purchases</a></li>
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>

<!-- My Sales Section -->
<div class="products-box">
    <div class="products-header">
        <h2>My Sale Transactions</h2>
    </div>

   


<?php
$seller_id = $_SESSION['user_id']; // Adjust based on your session handling

$query = "SELECT 
            od.detail_id,
            b.image AS book_image,
            b.title,
            u.first_name,
            u.last_name,
            o.total_price,
            o.order_date,
            o.payment_method,
            od.status
          FROM order_details od
          JOIN orders o ON od.order_id = o.orders_id
          JOIN tbluser u ON o.user_id = u.user_id
          JOIN tblbook b ON od.product_id = b.id
          WHERE b.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
  .sales-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  .sales-table th, .sales-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }
  .sales-table th {
    background-color: #f2f2f2;
    font-weight: bold;
  }
  .sales-table img {
    width: 50px;
    height: 60px;
    object-fit: cover;
  }
  .action-btn {
    padding: 5px 10px;
    margin: 2px;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 4px;
  }
  .approve-btn {
    background-color: green;
  }
  .decline-btn {
    background-color: red;
  }
</style>
<div class="book-container">
        <table class="sales-table">
            <thead>
  <tr>
    <!-- <th>Detail ID</th> -->
    <th>Image</th>
    <th>Title</th>
    <th>Buyer Name</th>
    <th>Price</th>
    <th>Order Date</th>
    <th>Payment Method</th>
    <th>Order Status</th>
    <th>Action</th>
  </tr>

  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <!-- <td><?php echo $row['detail_id']; ?></td> -->
      <td><img src='data:image/jpeg;base64,<?php echo base64_encode($row['book_image']); ?>' /></td>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
      <td><?php echo number_format($row['total_price'], 2); ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
      <td><?php echo htmlspecialchars($row['status']); ?></td>
      <td>
        <form method="post" action="update_status.php" style="display:inline;">
          <input type="hidden" name="detail_id" value="<?php echo $row['detail_id']; ?>">
          <button class="action-btn approve-btn" type="submit" name="action" value="approved">Approve</button>
        </form>
        <form method="post" action="update_status.php" style="display:inline;">
          <input type="hidden" name="detail_id" value="<?php echo $row['detail_id']; ?>">
          <button class="action-btn decline-btn" type="submit" name="action" value="declined">Decline</button>
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

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