<?php
session_start();
include('config.php');

// Fetch trade requests with book details
$query = "SELECT 
            tr.id,
            tr.user_id_from,
            tr.book_id_from,
            tr.user_id_to,
            tr.book_id_to,
            tr.status,
            b1.title AS offered_title,
            b1.image AS offered_image,
            b2.title AS requested_title,
            b2.image AS requested_image,
            u_from.first_name AS from_first_name,
            u_from.last_name AS from_last_name,
            u_to.first_name AS to_first_name,
            u_to.last_name AS to_last_name
          FROM trade_requests tr
          JOIN tbluser u_from ON tr.user_id_from = u_from.user_id
          JOIN tbluser u_to ON tr.user_id_to = u_to.user_id
          JOIN tblbook b1 ON tr.book_id_from = b1.id
          JOIN tblbook b2 ON tr.book_id_to = b2.id";

$result = $conn->query($query);

if (!$result) {
    die('Error in query: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trade Requests</title>
    <style>
        .trade-request {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            background-color: #f9f9f9;
        }
        .book-section {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        .book-section div {
            text-align: center;
        }
        img {
            width: 120px;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>Trade Requests</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="trade-request">
            <!-- <h3>Trader: <?= htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) ?></h3> -->
            <h3>Trader: <?= htmlspecialchars($row['from_first_name']) . ' ' . htmlspecialchars($row['from_last_name']) ?></h3>

            
            <div class="book-section">
    <div>
        <strong>Offered Book:</strong><br>
        <p><?= htmlspecialchars($row['offered_title']) ?></p>
        <img src="data:image/jpeg;base64,<?= base64_encode($row['offered_image']) ?>" alt="Offered Book">
    </div>
    <div>
        <strong>Requested Book:</strong><br>
        <p><?= htmlspecialchars($row['requested_title']) ?></p>
        <img src="data:image/jpeg;base64,<?= base64_encode($row['requested_image']) ?>" alt="Requested Book">
    </div>
</div>
        </div>
    <?php endwhile; ?>
</body>
</html>
