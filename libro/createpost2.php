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
            } elseif ($book_status === "For Exchange") {
                $bidding_start_price = 0;
                $buy_out_price = 0;
                $bidding_end_time_str = NULL;
            }

            $stmt = $conn->prepare("INSERT INTO tblbook (title, description, category, book_condition, book_status, image, user_id, bidding_start_price, meeting_spot, bidding_end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param("ssssssdiss", $title, $description, $category, $book_condition, $book_status, $imageData, $user_id, $bidding_start_price, $meeting_spot, $bidding_end_time_str);
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
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
<nav class="navbar">
<div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
</div>
    <ul class="nav-links">
        <li><a href="userfyp.php"><i class='bx bx-home'></i> Home</a></li>
        <li><a href="chat.php"><i class='bx bx-chat'></i> Chat</a></li>
        <li><a href="user.php"><i class='bx bx-user'></i> Profile</a></li>
        <!-- <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li> -->
        <li><a href="cart.php"><i class='bx bx-cart'></i> Cart</a></li>
        <li><a href="login.php"><i class='bx bx-log-out'></i> Sign Out</a></li>
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
            <textarea id="description" name="description" rows="2" required></textarea>

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
            <!-- <select id="book-status" name="book-status" onchange="toggleBiddingFields()"> -->
                    <select name="book_status" id="book_status" onchange="toggleFields()" required>
                    <option value="">--Select Book Status--</option>
                    <option value="For Sale">For Sale</option>
                <option value="For Exchange">For Exchange</option>
                <option value="For Bidding">For Bidding</option>
            </select>

            <div id="priceField" style="display:none;">
        <label>Price:</label><br>
        <input type="number" step="5" name="bidding_start_price">
    </div>

               <div id="biddingFields" style="display:none;">
        <label>Bidding Start Price:</label><br>
        <input type="number" step="5" name="bidding_start_price">
    
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


</body>
</html>

