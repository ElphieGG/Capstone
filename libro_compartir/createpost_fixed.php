
<?php
session_start();
include('config.php');

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['categories'];
    $bookCondition = $_POST['book-condition'];
    $bookStatus = $_POST['book-status'];
    $userId = $_SESSION['user_id'];

    // Handle image upload
    $imageData = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Set bidding fields based on book status
    if ($bookStatus == 'For Bidding') {
        $biddingStartPrice = !empty($_POST['bidding_start_price']) ? $_POST['bidding_start_price'] : null;
        $biddingDuration = !empty($_POST['bidding_duration']) ? intval($_POST['bidding_duration']) : 0;

        if ($biddingDuration > 0) {
            $biddingEndTime = date('Y-m-d H:i:s', strtotime("+{$biddingDuration} hours"));
        } else {
            $biddingEndTime = null;
        }
    } else {
        $biddingStartPrice = null;
        $biddingEndTime = null;
    }

    // Prepare the SQL Insert
    $stmt = $conn->prepare("INSERT INTO tblbook (title, description, category, book_condition, book_status, image, user_id, bidding_start_price, bidding_end_time)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssiss", 
        $title, 
        $description, 
        $category, 
        $bookCondition, 
        $bookStatus, 
        $imageData, 
        $userId, 
        $biddingStartPrice, 
        $biddingEndTime
    );

    if ($stmt->execute()) {
        echo "<script>alert('Book posted successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error posting book: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}
?>
