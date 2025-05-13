<?php
session_start();
include 'config.php';

$book_id = $_POST['book_id'];
$user_id = $_POST['user_id'];
$review_text = $_POST['review_text'];
$rating = $_POST['rating'];

/*if (!isset($_POST['book_id']) || empty($_POST['book_id'])) {
    die("Error: Book ID is missing or empty.");
} else {
    echo "Book ID received: " . $_POST['book_id'];
}*/


$checkBook = $conn->query("SELECT id FROM tblbook WHERE id = $book_id");

if ($checkBook->num_rows == 0) {
    die("Error: The book does not exist in the database.");
}

$sql = "INSERT INTO tblreviews (book_id, user_id, review_text, rating) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $book_id, $user_id, $review_text, $rating);

if ($stmt->execute()) {
    echo "Review submitted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>