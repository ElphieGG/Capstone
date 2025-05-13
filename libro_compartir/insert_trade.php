<?php
session_start();
include('config.php');

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to send a trade request.');
}

$user_id = $_SESSION['user_id'];

// Fetch user's own books (to offer)
$own_books_query = $conn->prepare("SELECT id, title FROM tblbook WHERE user_id = ?");
$own_books_query->bind_param("i", $user_id);
$own_books_query->execute();
$own_books_result = $own_books_query->get_result();

// Fetch other users' books (to request)
$other_books_query = $conn->prepare("SELECT id, title FROM tblbook WHERE user_id != ?");
$other_books_query->bind_param("i", $user_id);
$other_books_query->execute();
$other_books_result = $other_books_query->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id_from = intval($_POST['book_id_from']);
    $book_id_to = intval($_POST['book_id_to']);

    if ($book_id_from == $book_id_to) {
        die('Error: You cannot offer and request the same book.');
    }

    // Get owner of the requested book
    $get_owner = $conn->prepare("SELECT user_id FROM tblbook WHERE id = ?");
    $get_owner->bind_param("i", $book_id_to);
    $get_owner->execute();
    $owner_result = $get_owner->get_result();

    if ($owner_row = $owner_result->fetch_assoc()) {
        $user_id_to = $owner_row['user_id'];

        // Insert into trade_requests
        $insert = $conn->prepare("INSERT INTO trade_requests (user_id_from, book_id_from, user_id_to, book_id_to, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $insert->bind_param("iiii", $user_id, $book_id_from, $user_id_to, $book_id_to);

        if ($insert->execute()) {
            echo "<script>alert('Trade request sent successfully!'); window.location.href='trade_requests.php';</script>";
            exit();
        } else {
            die('Error sending trade request: ' . $conn->error);
        }
    } else {
        die('Requested book not found.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Trade Request</title>
</head>
<body>
    <h1>Send a Trade Request</h1>
    <form method="POST" action="">
        <label for="book_id_from">Select Your Book to Offer:</label><br>
        <select name="book_id_from" id="book_id_from" required>
            <option value="">--Select Your Book--</option>
            <?php while ($row = $own_books_result->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="book_id_to">Select Book You Want to Request:</label><br>
        <select name="book_id_to" id="book_id_to" required>
            <option value="">--Select Other User's Book--</option>
            <?php while ($row = $other_books_result->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Send Trade Request</button>
    </form>
</body>
</html>
