<?php
session_start();
include('config.php');

if (!isset($_GET['book_id'])) {
    echo "No book selected.";
    exit();
}

$bookId = intval($_GET['book_id']);

$query = "SELECT b.bid_amount, b.bid_time, u.first_name, u.last_name
          FROM tblbids b
          JOIN tbluser u ON b.user_id = u.user_id
          WHERE b.book_id = ?
          ORDER BY b.bid_amount DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Bid History</h1>
<table border="1" cellpadding="10">
<tr>
    <th>Bidder</th>
    <th>Amount</th>
    <th>Time</th>
</tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
    <td><?= number_format($row['bid_amount'], 2) ?> PHP</td>
    <td><?= date('Y-m-d H:i:s', strtotime($row['bid_time'])) ?></td>
</tr>
<?php endwhile; ?>
</table>