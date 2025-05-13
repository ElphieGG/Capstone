<?php
// Your PHP code here (database connection, query, etc.)
session_start();
include 'config.php';
// Ensure the user is logged in
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    echo "You need to log in to bid.";
    exit;
}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch book details and user who posted the book
    $stmt = $conn->prepare(
        "SELECT tblbook.title, tblbook.description, tblbook.category, tblbook.book_condition, tblbook.image, 
                tblbook.book_status, tblbook.bidding_start_price, tblbook.meeting_spot, tblbook.bidding_end_time,
                tbluser.first_name, tbluser.last_name, 
                (SELECT MAX(bid_amount) FROM bids WHERE book_id = tblbook.id) AS current_bid
         FROM tblbook 
         JOIN tbluser ON tblbook.user_id = tbluser.user_id 
         WHERE tblbook.id = ?"
    );
    $stmt->bind_param("i", $book_id);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "Book not found.";
        exit;
    }

    // Get current time and bidding end time
    $current_time = new DateTime();
    $bidding_end_time = new DateTime($book['bidding_end_time']);
    $remaining_time = $bidding_end_time->diff($current_time);
    
    // Check if the bidding has ended
    $is_bidding_closed = ($current_time > $bidding_end_time);

    // Format remaining time (hours:minutes:seconds)
    $remaining_time_str = $remaining_time->format('%H:%I:%S');

    if ($is_bidding_closed) {
        // Call the bidding expiry logic here
        include('check_bidding_end.php'); // This would execute the logic to send the email
    }
    
} else {
    echo "No book selected.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Bid</title>
    <link rel="stylesheet" href="viewpoststyle.css">
</head>
<body>
    <nav class="navbar">
        <img src="images/logo2.png" class="logo" alt="Logo">
        <ul class="nav-links">
            <li><a href="user.php">Profile</a></li>
            <li><a href="userfyp.php">Home</a></li>
            <li><a href="login.php">Sign Out</a></li>
        </ul>
    </nav>

    <div class="post-container">
        <div class="left-div">
            <?php
            // Output the image as base64
            $imageData = base64_encode($book['image']);
            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Book Cover" class="book-cover">';
            ?>
        </div>

        <div class="right-div">
            <h2 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h2>
            <p>Posted by: <strong><?php echo htmlspecialchars($book['first_name'] . ' ' . $book['last_name']); ?></strong></p>
            
            <div class="description-container">
                <h3>Description</h3>
                <p class="description"><?php echo htmlspecialchars($book['description']); ?></p>
            </div>
            <div class="category-container">
                <h3>Category</h3>
                <p class="category"><?php echo htmlspecialchars($book['category']); ?></p>
            </div>
            <div class="condition-container">
                <h3>Condition</h3>
                <p class="condition"><?php echo htmlspecialchars($book['book_condition']); ?></p>
            </div>
            <div class="condition-container">
                <h3>Status</h3>
                <p class="condition"><?php echo htmlspecialchars($book['book_status']); ?></p>
            </div>
            <div class="condition-container">
                <h3>Bidding Start Price</h3>
                <p class="bidding-price"><?php echo htmlspecialchars($book['bidding_start_price']); ?> PHP</p>
            </div>
            <div class="condition-container">
                <h3>Meeting Spot</h3>
                <p class="meeting-spot"><?php echo htmlspecialchars($book['meeting_spot']); ?></p>
            </div>

            <!-- Display remaining time -->
            <div class="bidding-timer">
                <h3>Time Remaining: <span id="timer"><?php echo $remaining_time_str; ?></span></h3>
            </div>
            
            <?php if ($is_bidding_closed): ?>
                <p>Bidding has ended.</p>
            <?php else: ?>
                <div class="bidding-section">
                    <h3>Place Your Bid</h3>
                    <form id="bid-form" method="POST" action="place_bid.php?id=<?php echo $book_id; ?>">
    <label for="bid-amount">Bid Amount (PHP):</label>
    <input type="number" name="bid_amount" id="bid-amount" placeholder="Enter your bid" min="<?php echo $book['bidding_start_price'] + 1; ?>" required>
    <button type="submit" id="submit-bid">Place Bid</button>
</form>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    var currentBid = <?php echo $book['current_bid'] ?? 0; ?>;

    // Handle the form submission with AJAX
    document.getElementById("bid-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission

        var bidAmount = parseFloat(document.getElementById("bid-amount").value);

        // Validate the bid amount is greater than the current highest bid
        if (bidAmount <= currentBid) {
            alert("Your bid must be higher than the current highest bid of " + currentBid + " PHP.");
            return;
        }

        // Create a FormData object to send the data
        var formData = new FormData();
        formData.append("bid_amount", bidAmount);

        // Send the request via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "place_bid.php?id=<?php echo $book_id; ?>", true);

        // Handle the response
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);

                // Check if the response indicates success
                if (response.status === 'success') {
                    // Show the success modal
                    var modal = document.getElementById("success-modal");
                    var closeModal = document.getElementsByClassName("close")[0];

                    // Display the modal with a success message
                    document.getElementById("success-message").innerHTML = response.message;
                    modal.style.display = "block";

                    // Close the modal when the user clicks on the "x" button
                    closeModal.onclick = function() {
                        modal.style.display = "none";
                    }

                    // Close the modal when the user clicks anywhere outside of the modal
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                } else {
                    alert(response.message); // Show the error message
                }
            }
        };

        xhr.send(formData); // Send the FormData to place the bid
    });
</script>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>


</body>
</html>
