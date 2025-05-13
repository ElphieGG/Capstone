<?php
include("config.php"); // Connect to database

if (isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);

    $update = $conn->prepare("UPDATE tblbook SET book_status = 'Sold' WHERE id = ?");
    $update->bind_param("i", $book_id);

    if ($update->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
