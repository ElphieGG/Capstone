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
<div class="container mt-5">
  <h2 class="text-center mb-4" style="color: #800000;">Your Notifications</h2>
  
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-bell"></i> <?= htmlspecialchars($row['message']) ?></h5>
          <p class="card-text">
            <small class="text-muted">Book: <?= htmlspecialchars($row['title']) ?> | 
            <?= htmlspecialchars(date('M d, Y', strtotime($row['created_at']))) ?></small>
          </p>
          <a href="checkout_bidding.php?book_id=<?= $row['book_id'] ?>" class="btn btn-primary btn-sm">Proceed to Checkout</a>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="alert alert-info text-center">
      <i class="fas fa-bell-slash"></i> No notifications yet!
    </div>
  <?php endif; ?>
</div>
