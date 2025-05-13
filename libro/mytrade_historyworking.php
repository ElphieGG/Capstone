
<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = $conn->prepare("SELECT * FROM tbluser WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();
$image_data = $user_data['image']; 


$query = "
SELECT t.*, ob.title AS offered_book_title, rb.title AS requested_book_title
FROM tblbooktrades t
JOIN tblbook ob ON t.offered_book_id = ob.id
JOIN tblbook rb ON t.requested_book_id = rb.id
WHERE t.offered_by_user_id = ? OR t.requested_user_id = ?
ORDER BY t.offer_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trade History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="userfyp.css">
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
         <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li>
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
                <li><a href="usersale.php">My Sales</a></li>
                <li><a href="userpurchase.php">My Purchases</a></li>
                <li><a href="mytrade_history.php">My Trades</a></li>
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
            
        </div>

 
        
   <!-- My Products Section -->
   <div class="products-box">
            <div class="products-header">
            </div>

   <div class="book-container">
    <h2>My Trade History</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Offered Book</th>
                <th>Requested Book</th>
                <th>Offer Date</th>
                <th>Decision Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['offered_book_title']) ?></td>
                    <td><?= htmlspecialchars($row['requested_book_title']) ?></td>
                    <td><?= htmlspecialchars($row['offer_date']) ?></td>
                    <td><?= htmlspecialchars($row['decision_date']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td id="action-<?= $row['trade_id'] ?>">
                        <?php if ($row['status'] === 'accepted'): ?>
                            <button class="btn btn-success btn-sm" onclick="markReceived(<?= $row['trade_id'] ?>)">Receive</button>
                        <?php elseif ($row['status'] === 'received'): ?>
                            <span class="badge bg-success">Received</span>
                        <?php else: ?>
                            <span class="text-muted">Waiting</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function markReceived(tradeId) {
    if (confirm("Are you sure you have received the book?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "receive_trade.php?trade_id=" + tradeId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('action-' + tradeId).innerHTML = '<span class="badge bg-success">Received</span>';
            } else {
                alert('Failed to update. Please try again.');
            }
        };
        xhr.send();
    }
}
</script>

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

/* Navbar
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
} */

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
    /* background: #660000; */
    background: linear-gradient(to right, #f52222, #e60000);
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

.book-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Space between books */
    justify-content: flex-start; /* Align books horizontally */
    padding: 20px;
}

.book-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 180px; /* Adjust as needed */
    padding: 15px;
    background-color: #f8f9fa; /* Light background */
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.book-item img {
    width: 100px; /* Adjust image size */
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
}

.book-title {
    margin-top: 10px;
    font-size: 16px;
    font-weight: bold;
}

.book-actions {
    margin-top: 10px;
}

.btn {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
}

.btn-edit {
    background-color: #28a745;
    color: white;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}


    .btn-delete:hover {
        background-color: #c82333;
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

</style>

