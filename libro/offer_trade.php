<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$requested_user_id = $_GET['requested_user_id'];
$requested_book_id = $_GET['requested_book_id'];

// Fetch user's own books
$query = "SELECT id, title FROM tblbook WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$books = $stmt->get_result();

// Also fetch books already offered
$offered_books_query = "SELECT offered_book_id FROM tblbooktrades WHERE offered_by_user_id = ? AND status = 'pending'";
$offered_stmt = $conn->prepare($offered_books_query);
$offered_stmt->bind_param("i", $user_id);
$offered_stmt->execute();
$offered_result = $offered_stmt->get_result();

$already_offered = [];
while ($row = $offered_result->fetch_assoc()) {
    $already_offered[] = $row['offered_book_id'];
}

// Filter available books
$available_books = [];
while ($row = $books->fetch_assoc()) {
    if (!in_array($row['id'], $already_offered)) {
        $available_books[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Offer a Trade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fb;
            padding: 30px;
        }
        .trade-offer-card {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .trade-offer-card h2 {
            margin-bottom: 20px;
            color: #333;
        }
        select, button {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .no-books-message {
            color: #f44336;
            font-weight: bold;
            margin-top: 20px;
        }
        a.back-link {
            display: inline-block;
            margin-top: 15px;
            color: #555;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="trade-offer-card">
    <h2>Offer a Trade</h2>

    <?php if (count($available_books) > 0): ?>
    <form action="submit_trade_offer.php" method="post">
        <label for="offered_book_id">Select one of your books to offer:</label>
        <select name="offered_book_id" id="offered_book_id" required>
            <option value="">-- Select Your Book --</option>
            <?php
            $query = "SELECT id, title FROM tblbook WHERE user_id = ? AND book_status = 'For Exchange'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
            <?php endwhile; ?>

            <!-- <?php foreach ($available_books as $book): ?>-->
            <!--   <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>-->
             <!--  <?php endforeach; ?> -->
        </select>

        <input type="hidden" name="requested_user_id" value="<?= $requested_user_id ?>">
        <input type="hidden" name="requested_book_id" value="<?= $requested_book_id ?>">

        <button type="submit">Send Trade Offer</button>
    </form>
    <?php else: ?>
    <p class="no-books-message">You have no available books to offer at the moment.</p>
    <?php endif; ?>

    <a href="javascript:history.back()" class="back-link">← Go Back</a>
</div>

</body>
</html>
