<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current time
$current_time = new DateTime();
$formatted_time = $current_time->format('Y-m-d H:i:s');

// Fetch books where bidding time has ended
$stmt = $conn->prepare("SELECT tblbook.id, tblbook.bidding_end_time, tbluser.email AS poster_email, tbluser.phone AS poster_phone, 
                               (SELECT MAX(bid_amount) FROM bids WHERE book_id = tblbook.id) AS highest_bid,
                               (SELECT user_id FROM bids WHERE book_id = tblbook.id ORDER BY bid_amount DESC LIMIT 1) AS highest_bidder_id
                        FROM tblbook 
                        JOIN tbluser ON tblbook.user_id = tbluser.user_id
                        WHERE tblbook.bidding_end_time <= NOW() AND tblbook.bidding_end_time > ?");
$stmt->bind_param("s", $formatted_time);
$stmt->execute();
$result = $stmt->get_result();

// Check if the bidding has ended
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $book_id = $row['id'];
        $highest_bid = $row['highest_bid'];
        $highest_bidder_id = $row['highest_bidder_id'];
        $poster_email = $row['poster_email'];
        $poster_phone = $row['poster_phone'];

        // Get the highest bidder's email and phone
        $stmt_bidder = $conn->prepare("SELECT email, phone FROM tbluser WHERE user_id = ?");
        $stmt_bidder->bind_param("i", $highest_bidder_id);
        $stmt_bidder->execute();
        $bidder_result = $stmt_bidder->get_result();
        $bidder_info = $bidder_result->fetch_assoc();
        $bidder_email = $bidder_info['email'];
        $bidder_phone = $bidder_info['phone'];

        // Send email to the highest bidder
        $subject_bidder = "You won the bid!";
        $message_bidder = "Congratulations! You have won the bid for the book. Here are the contact details of the seller:
                           \nEmail: $poster_email
                           \nPhone: $poster_phone";
        $headers_bidder = "From: no-reply@yourwebsite.com";
        mail($bidder_email, $subject_bidder, $message_bidder, $headers_bidder);

        // Send email to the poster
        $subject_poster = "You have a winner!";
        $message_poster = "Congratulations! Your book has been sold. Here are the contact details of the highest bidder:
                           \nEmail: $bidder_email
                           \nPhone: $bidder_phone";
        $headers_poster = "From: no-reply@yourwebsite.com";
        mail($poster_email, $subject_poster, $message_poster, $headers_poster);

        // Optionally, update the book status to "Sold"
        $stmt_update = $conn->prepare("UPDATE tblbook SET book_status = 'Sold' WHERE id = ?");
        $stmt_update->bind_param("i", $book_id);
        $stmt_update->execute();
    }
} else {
   // echo "No bidding has ended yet.";
}

// Close connection only once
if ($conn instanceof mysqli && !$conn->ping()) {
    $conn->close();
}
