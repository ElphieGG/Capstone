<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['book_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $book_condition = mysqli_real_escape_string($conn, $_POST['book_condition']);
    $book_status = mysqli_real_escape_string($conn, $_POST['book_status']);
    $bidding_start_price = mysqli_real_escape_string($conn, $_POST['bidding_start_price']);
    $buy_out_price = !empty($_POST['buy_out_price']) ? mysqli_real_escape_string($conn, $_POST['buy_out_price']) : NULL;
    $meeting_spot = mysqli_real_escape_string($conn, $_POST['meeting_spot']);
    $bidding_end_time = !empty($_POST['bidding_end_time']) ? mysqli_real_escape_string($conn, $_POST['bidding_end_time']) : NULL;

    $updateQuery = "UPDATE tblbook SET 
                    title = '$title', 
                    description = '$description',
                    category = '$category',
                    book_condition = '$book_condition',
                    book_status = '$book_status',
                    bidding_start_price = '$bidding_start_price',
                    buy_out_price = " . ($buy_out_price !== NULL ? "'$buy_out_price'" : "NULL") . ",
                    meeting_spot = '$meeting_spot',
                    bidding_end_time = " . ($bidding_end_time !== NULL ? "'$bidding_end_time'" : "NULL") . "
                    WHERE id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "success";
    } else {
        echo "Error updating book: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
