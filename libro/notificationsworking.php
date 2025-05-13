<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

$query = "SELECT n.*, b.title
          FROM tblnotifications n
          JOIN tblbook b ON n.book_id = b.id
          WHERE n.user_id = ?
          ORDER BY n.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
 <link rel="stylesheet" href="navbar.css">
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
<div class="products-box">
<div class="products-header">

<h1>Your Notifications</h1>
<div>
<ul>
<?php while ($row = $result->fetch_assoc()): ?>
    <li>
        <?= htmlspecialchars($row['message']) ?> - 
        <a href="checkout_bidding.php?book_id=<?= $row['book_id'] ?>">Proceed to Checkout</a>
    </li>
<?php endwhile; ?>
</ul>