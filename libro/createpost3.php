<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $imageData = file_get_contents($fileTmpPath);

            $title = $_POST['title'];
            $description = $_POST['description'];
            $category = $_POST['categories'];
            $book_condition = $_POST['book-condition'];
            $book_status = $_POST['book-status'];
            $meeting_spot = $_POST['meeting-spot'];
            $user_id = $_SESSION['user_id']; 

            // Initialize variables
            $bidding_start_price = 0;
            // $buy_out_price = 0;
            $bidding_end_time_str = NULL;

            if ($book_status === "For Bidding") {
                $bidding_start_price = $_POST['bidding-price'];
                $bidding_duration = $_POST['bidding-duration'];
                $buy_out_price = $_POST['buy-out-price'];

                $bidding_end_time = new DateTime();
                $bidding_end_time->add(new DateInterval('PT' . $bidding_duration . 'H'));
                $bidding_end_time_str = $bidding_end_time->format('Y-m-d H:i:s');
            } elseif ($book_status === "For Sale") {
                $bidding_start_price = $_POST['bidding-price']; // Store Price in bidding_start_price
            } elseif ($book_status === "For Exhange") {
                $bidding_start_price = 0;
                $buy_out_price = 0;
                $bidding_end_time_str = NULL;
            }

            $stmt = $conn->prepare("INSERT INTO tblbook (title, description, category, book_condition, book_status, image, user_id, bidding_start_price, buy_out_price, meeting_spot, bidding_end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param("ssssssdisss", $title, $description, $category, $book_condition, $book_status, $imageData, $user_id, $bidding_start_price, $buy_out_price, $meeting_spot, $bidding_end_time_str);
 // Load SweetAlert2
 echo "<html><head>
 <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
 </head><body>";
                if ($stmt->execute()) {
                   // echo "Post created successfully!";
                   echo "<script>
                   Swal.fire({
                       icon: 'success',
                       title: 'Post created successfully!',
                       text: 'Your book has been successfully added.',
                       confirmButtonText: 'OK'
                   }).then(() => {
                        window.location.href = 'user.php';
                   });
                 </script>";
                } else {
                 //   echo "Error: " . $stmt->error;
                    // Books was not added
        echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Books was not added',
            text: 'You must try again.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'user.php';
        });
      </script>";
                }

                $stmt->close();
            } else {
                echo "Statement preparation failed.";
            }
        } else {
            echo "Invalid file extension.";
        }
    } else {
        echo "No image uploaded.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="createpoststyle.css">
</head>
<body>
    <nav class="navbar">
        <img src="images/logo2.png" class="logo" alt="Logo">
        <ul class="nav-links">
        <li><a href="userfyp.php">Home</a></li>
 <li><a href="chat.php">Chat</a></li>
 <li><a href="user.php">Profile</a></li>
 <li><a href="cart.php">Cart</a></li>
            <li><a href="login.php">Sign Out</a></li>
        </ul>
    </nav>

    <div class="search-bar-container">
        <h1>Create New Post</h1>
    </div>

    <div class="form-container">
        <div class="form-left">
            <h2>Add Photo</h2>
            <form action="createpost1.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">
                <img id="photo-preview" src="" alt="Photo Preview" style="display: none; margin-top: 10px; max-width: 100%;">
        </div>

        <div class="form-right">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="categories">Categories:</label>
            <select id="categories" name="categories">
                <option value="Educational">Educational</option>
                <option value="Fiction">Fiction</option>
                <option value="Non-Fiction">Non-Fiction</option>
                <option value="Mystery">Mystery</option>
                <option value="Fantasy">Fantasy</option>
            </select>

            <label for="book-condition">Book Condition:</label>
            <select id="book-condition" name="book-condition">
                <option value="New">New</option>
                <option value="Used">Used</option>
                <option value="Worn">Worn</option>
            </select>

            <label for="book-status">Book Status:</label>
            <select id="book-status" name="book-status" onchange="toggleBiddingFields()">
                <option value="For Sale">For Sale</option>
                <option value="For Exchange">For Exchange</option>
                <option value="For Bidding">For Bidding</option>
            </select>

            <div id="bidding-price-container">
                <label id="price-label" for="bidding-price">Bidding Start Price:</label>
                <input type="number" id="bidding-price" name="bidding-price" step="0.01">
            </div>

            <!-- <div id="buy-out-price-container">
                <label for="buy-out-price">Buy Out Price:</label>
                <input type="number" id="buy-out-price" name="buy-out-price" step="0.01">
            </div> -->

            <div id="bidding-duration-container">
                <label for="bidding-duration">Bidding Duration (hours):</label>
                <input type="number" id="bidding-duration" name="bidding-duration" step="1" min="1">
            </div>

            <label for="meeting-spot">Meeting Spot:</label>
            <!-- <input type="text" id="meeting-spot" name="meeting-spot" required> --->
            <select id="meeting-spot" name="meeting-spot">
                <option value="WMSU Campus">WMSU Campus</option>
                <option value="Outside WMSU Campus">Outside WMSU Campus</option>
            </select>


            <div class="button-container" style="position: absolute; left: 120px; bottom: 40px;">
                <button type="submit">Post</button>
                <button type="reset"><a href="user.php" style="color: white; text-decoration: none;">Cancel</a></button>
            </div>
        </div>
        </form>
    </div>

    <script>
        function toggleBiddingFields() {
            var status = document.getElementById("book-status").value;
            var priceLabel = document.getElementById("price-label");

            document.getElementById("bidding-price-container").style.display = (status !== "For Exchange") ? "block" : "none";
            document.getElementById("buy-out-price-container").style.display = (status === "For Bidding") ? "block" : "none";
            document.getElementById("bidding-duration-container").style.display = (status === "For Bidding") ? "block" : "none";

            priceLabel.innerText = (status === "For Sale") ? "Price:" : "Bidding Start Price:";
        }

        document.addEventListener("DOMContentLoaded", toggleBiddingFields);
    </script>
</body>
</html>

