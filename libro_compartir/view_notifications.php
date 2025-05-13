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

<h1>Your Notifications</h1>
<ul>
<?php while ($row = $result->fetch_assoc()): ?>
    <li>
        <?= htmlspecialchars($row['message']) ?> - 
        <a href="checkout_bidding.php?book_id=<?= $row['book_id'] ?>">Proceed to Checkout</a>
    </li>
<?php endwhile; ?>
</ul>