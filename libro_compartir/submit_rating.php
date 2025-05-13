<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "error";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $book_id = intval($_POST['book_id']);
    $book_rating = intval($_POST['book_rating']);
    $book_review = trim($_POST['book_review']);
    $seller_rating = intval($_POST['seller_rating']);
    $seller_review = trim($_POST['seller_review']);

    // Check if review already exists
    $check = $conn->prepare("SELECT rate_id FROM tblreviews WHERE user_id = ? AND id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "exists";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tblreviews (user_id, id, book_rating, book_review, seller_rating, seller_review) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisis", $user_id, $book_id, $book_rating, $book_review, $seller_rating, $seller_review);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $check->close();
    $conn->close();
}
?>
