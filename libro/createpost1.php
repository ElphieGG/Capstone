<?php
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <link rel="stylesheet" href="createpoststyle.css"> <!-- Link your CSS -->
    
    <script>
    function toggleFields() {
        var bookStatus = document.getElementById('book_status').value;
        
        if (bookStatus === 'For Sale') {
            document.getElementById('priceField').style.display = 'block';
            document.getElementById('biddingFields').style.display = 'none';
        } else if (bookStatus === 'For Bidding') {
            document.getElementById('priceField').style.display = 'none';
            document.getElementById('biddingFields').style.display = 'block';
        } else { // For Exchange or anything else
            document.getElementById('priceField').style.display = 'none';
            document.getElementById('biddingFields').style.display = 'none';
        }
    }
    </script>
</head>
<body>

<h2>Create New Post</h2>

<form action="savepost.php" method="POST" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" required></textarea><br><br>

    <label>Category:</label><br>
    <input type="text" name="category" required><br><br>

    <label>Condition:</label><br>
    <input type="text" name="book_condition" required><br><br>

    <label>Book Status:</label><br>
    <select name="book_status" id="book_status" onchange="toggleFields()" required>
        <option value="">--Select--</option>
        <option value="For Sale">For Sale</option>
        <option value="For Exchange">For Exchange</option>
        <option value="For Bidding">For Bidding</option>
    </select><br><br>

    <div id="priceField" style="display:none;">
        <label>Price:</label><br>
        <input type="number" step="0.01" name="price"><br><br>
    </div>

    <div id="biddingFields" style="display:none;">
        <label>Bidding Start Price:</label><br>
        <input type="number" step="0.01" name="bidding_start_price"><br><br>

        <label>Bidding Duration (in days):</label><br>
        <input type="number" name="bidding_duration"><br><br>
    </div>

    <label>Meeting Spot:</label><br>
    <input type="text" name="meeting_spot" required><br><br>

    <label>Upload Image:</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <input type="submit" value="Post">
</form>

</body>
</html>
