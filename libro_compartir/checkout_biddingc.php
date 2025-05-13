<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        b.id,
        b.title,
        b.image,
        (SELECT MAX(bid_amount) FROM tblbids WHERE book_id = b.id) AS winning_bid
    FROM tblbook b
    INNER JOIN tblbids bid ON b.id = bid.book_id
    WHERE bid.user_id = ? AND b.book_status = 'For Bidding'
    GROUP BY b.id
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Bidding</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .book-card {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #fafafa;
            border-radius: 12px;
            transition: 0.3s;
        }
        .book-card:hover {
            background: #f0f0f0;
        }
        .book-image {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
            border: 1px solid #ddd;
        }
        .book-info {
            flex-grow: 1;
        }
        .book-title {
            font-size: 18px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .book-price {
            color: #888;
            font-size: 16px;
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .checkout-btn:hover {
            background: #45a049;
        }
        .no-items {
            text-align: center;
            font-size: 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout Your Winning Bid</h1>

        <?php if ($result->num_rows > 0): ?>
            <form action="confirm_checkout_bidding.php" method="post">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        <?php
                            $imageData = base64_encode($row['image']);
                            $src = 'data:image/jpeg;base64,'.$imageData;
                        ?>
                        <img class="book-image" src="<?= $src ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                        <div class="book-info">
                            <div class="book-title"><?= htmlspecialchars($row['title']) ?></div>
                            <div class="book-price">Winning Bid: <?= htmlspecialchars(number_format($row['winning_bid'], 2)) ?> PHP</div>
                            <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="price" value="<?= $row['winning_bid'] ?>">
                        </div>
                    </div>
                <?php endwhile; ?>

                <button type="submit" class="checkout-btn">Proceed to Confirm Checkout</button>
            </form>
        <?php else: ?>
            <div class="no-items">No winning bids to checkout at the moment.</div>
        <?php endif; ?>

    </div>
</body>
</html>
