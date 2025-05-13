<?php
session_start();
include('config.php');

// Handle Accept/Decline
if (isset($_GET['action']) && isset($_GET['trade_id'])) {
    $trade_id = intval($_GET['trade_id']);
    $action = $_GET['action'];

    if ($action == 'accept') {
        $new_status = 'approved';
    } elseif ($action == 'decline') {
        $new_status = 'rejected';
    }

    if (isset($new_status)) {
        $update = $conn->prepare("UPDATE trade_requests SET status = ? WHERE id = ?");
        $update->bind_param('si', $new_status, $trade_id);
        if ($update->execute()) {
            echo "<script>alert('Trade has been " . ($action == 'accept' ? 'ACCEPTED' : 'DECLINED') . " successfully.'); window.location.href='trade_requests.php';</script>";
            exit();
        } else {
            die('Error updating trade: ' . $conn->error);
        }
    }
}

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
            background-color: #f9f9f9;
        }
        .book-section {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            justify-content: center;
        }
        .book-section div {
            text-align: center;
        }
        img {
            width: 120px;
            height: auto;
        }
        .actions {
            margin-top: 10px;
            text-align: center;
        }
        .actions a {
            text-decoration: none;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .accept {
            background-color: green;
        }
        .decline {
            background-color: red;
        }
    </style>
</head>
<body>
    <h1>Trade Requests</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="trade-request">
            <h3>Trader From: <?= htmlspecialchars($row['from_first_name']) . ' ' . htmlspecialchars($row['from_last_name']) ?></h3>
            <h3>Trader To: <?= htmlspecialchars($row['to_first_name']) . ' ' . htmlspecialchars($row['to_last_name']) ?></h3>
            <p>Status: <strong><?= htmlspecialchars(ucfirst($row['status'])) ?></strong></p>
            
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

            <?php if ($row['status'] == 'pending'): ?>
                <div class="actions">
                    <a class="accept" href="?action=accept&trade_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to ACCEPT this trade?');">Accept</a>
                    <a class="decline" href="?action=decline&trade_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to DECLINE this trade?');">Decline</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</body>
</html>
