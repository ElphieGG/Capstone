<?php
session_start();
include('config.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, phone, email, birthday, address, ship_location, image FROM tbluser WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['user_id'];
    $image_data = $user_data['image'];
} else {
    die("No user data found!");
}
$stmt->close();

// Check if book ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch book details
    $stmt = $conn->prepare("SELECT * FROM tblbook WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $title = $row['title'];
        $description = $row['description'];
        $category = $row['category'];
        $book_condition = $row['book_condition'];
        $book_status = $row['book_status'];
        $bidding_start_price = $row['bidding_start_price'];
        $buy_out_price = $row['buy_out_price'];
        $meeting_spot = $row['meeting_spot'];
        $bidding_end_time = $row['bidding_end_time'];
    } else {
        echo "Book not found!";
        exit();
    }
    $stmt->close();
} else {
    header("Location: user.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $newTitle = $_POST['title'];
    $newDescription = $_POST['description'];
    $newCategory = $_POST['category'];
    $newCondition = $_POST['book_condition'];
    $newStatus = $_POST['book_status'];
    $newStartPrice = $_POST['bidding_start_price'];
    $newBuyOutPrice = !empty($_POST['buy_out_price']) ? $_POST['buy_out_price'] : NULL;
    $newMeetingSpot = $_POST['meeting_spot'];
    $newEndTime = !empty($_POST['bidding_end_time']) ? $_POST['bidding_end_time'] : NULL;

    // Use prepared statement to update book details
    $updateQuery = "UPDATE tblbook SET 
                    title = ?, 
                    description = ?, 
                    category = ?, 
                    book_condition = ?, 
                    book_status = ?, 
                    bidding_start_price = ?, 
                    buy_out_price = ?, 
                    meeting_spot = ?, 
                    bidding_end_time = ? 
                    WHERE id = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssdsssi", 
        $newTitle, 
        $newDescription, 
        $newCategory, 
        $newCondition, 
        $newStatus, 
        $newStartPrice, 
        $newBuyOutPrice, 
        $newMeetingSpot, 
        $newEndTime, 
        $id
    );

    echo "<html><head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head><body>";

    if ($stmt->execute()) {
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Book Updated!',
            text: 'The book details have been updated successfully!',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'user.php';
        });
        </script>";
    } else {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Update Failed!',
            text: 'Error updating the book: " . $stmt->error . "',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'user.php';
        });
        </script>";
    }
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="userstyle.css">
    <style>
     /* Links */
     a {
            /*color: #2575fc;*/
            color:red;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

     
</style>
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

    <div class="content-container">
     <!-- User Profile Box -->
     <div class="user-box">
        <!--   <img src="images/profile.jpg" class="profile" alt="Profile" style="width: 80px;"> -->

        <?php if (!empty($image_data)): ?>
    <img src="data:image/jpeg;base64,<?php echo base64_encode($image_data); ?>" class="profile" alt="Profile" style="width: 120px;"> 
<?php else: ?>
    <img src="images/default-profile.jpg" class="profile" alt="Default Profile">
<?php endif; ?>

<h2>
    <?php 
    if (!empty($user_data['first_name']) && !empty($user_data['last_name'])) {
        echo htmlspecialchars($user_data['first_name'] . " " . $user_data['last_name']);
    } else {
        echo "User Name Not Available";
    }
    ?>
</h2>


            <ul class="user-menu">
                <li style= "font-size:16px"><a href="user.php">My Books</a></li>
                <li style= "font-size:16px"><a href="usersale.php">My Sales</a></li>
                <li style= "font-size:16px"><a href="userpurch.php">My Purchases</a></li>
                <li style= "font-size:16px"><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>

    
        <div class="products-box">
    <div class="products-header">
        <h2 class="edit-book-title">Edit Book</h2>
    </div>

    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post" class="edit-book-form">
    <div class="form-group">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
    </div>

    <div class="form-group">
        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea>
    </div>

    <div class="form-group">
        <label>Category:</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($category); ?>" required>
    </div>

    <div class="form-group">
        <label>Condition:</label>
        <input type="text" name="book_condition" value="<?php echo htmlspecialchars($book_condition); ?>" required>
    </div>

    <div class="form-group">
        <label>Status:</label>
        <input type="text" name="book_status" value="<?php echo htmlspecialchars($book_status); ?>" required>
    </div>

    <div class="form-group">
        <label>Bidding Start Price:</label>
        <input type="number" step="0.01" name="bidding_start_price" value="<?php echo htmlspecialchars($bidding_start_price); ?>" required>
    </div>

    <!-- <div class="form-group">
        <label>Buy Out Price:</label>
        <input type="number" step="0.01" name="buy_out_price" value="<?php echo htmlspecialchars($buy_out_price); ?>">
    </div> -->

    <div class="form-group">
        <label>Meeting Spot:</label>
        <input type="text" name="meeting_spot" value="<?php echo htmlspecialchars($meeting_spot); ?>" required>
    </div>

    <div class="form-group">
        <label>Bidding End Time:</label>
        <input type="datetime-local" name="bidding_end_time" value="<?php echo $bidding_end_time_iso ? date('c', strtotime($bidding_end_time)) : ''; ?>">
      
    </div>

    <div class="buttons">
        <input type="submit" name="update" value="Update Book" class="btn-update">
        <button type="button" class="btn-cancel" onclick="window.location.href='user.php'">Cancel</button>
    </div>
</form>

</div>

</div>

</body>
</html>
<style>
     .buttons {
                display: flex;
                gap: 10px;
            }
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

/* Navbar */
.navbar {
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    width: 100px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 15px;
}

.nav-links li {
    display: inline;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
}

/* Content Layout */
.content-container {
    display: flex;
    padding: 20px;
    gap: 20px;
}

/* User Profile Box */
.user-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 500px;
    height: 480px; /* Increased height */
    text-align: center;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

.profile {
    border-radius: 50%;
}

.user-menu {
    list-style: none;
    padding: 0;
}

.user-menu li {
    padding: 10px 0;
}

.user-menu a {
    text-decoration: none;
    color: black;
    font-weight: bold;
}

/* Products Box */
.products-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    flex-grow: 1;
    height: 480px; /* Increased height */
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}
.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-action {
    background: linear-gradient(45deg, #800000, #a52a2a); /* Maroon Gradient */
    color: white;
    font-weight: bold;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
    width: 100%;
}

.btn-action a {
    color: white;
    text-decoration: none;
}

/* Book Grid */
.book-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.book-item {
    background: white;
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 120px;
}

.book-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 6px;
}

.book-item p {
    font-size: 14px;
    margin-top: 5px;
    font-weight: bold;
}

/* Improved Form Aesthetics */
.edit-profile-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;  /* Make it fit inside products-box */
    max-width: 100%;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: none; /* Remove extra shadow since products-box has one */
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border 0.3s, box-shadow 0.3s;
}

.form-group input:focus {
    border: 1px solid #007bff;
    box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
    outline: none;
}
/* Save Button */
.btn-action {
    background: linear-gradient(45deg, #007bff, #00d4ff);
    color: white;
    font-weight: bold;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
    width: 100%;
}

.btn-action:hover {
    background: linear-gradient(45deg, #660000, #8b0000); /* Darker Maroon on Hover */
    transform: scale(1.05);
}

.products-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: auto;
    padding: 30px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

   /* Links */
   a {
            /*color: #2575fc;*/
            color:red;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .products-box {
    display: flex;
    flex-direction: column;
    align-items: center; /* Centers content */
    padding: 20px;
}

.edit-book-title {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}

.edit-book-form {
    width: 100%;
    max-width: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.form-group {
    width: 100%;
    margin-bottom: 15px;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

input, textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.btn-update {
    background-color: #007bff;
    color: white;
    padding: 10px;
    border: none;
       font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    max-width: 200px;
}

.btn-update:hover {
    background-color: #0056b3;
}

.form-group {
        margin-bottom: 15px;
    }

    .button-group {
        display: flex;
        gap: 10px; /* Adds spacing between buttons */
        align-items: center;
    }


    .btn-cancel {
        background-color: red;
        color: white;
        padding: 10px 20px;
        border: none;
        font-size: 14px;
        border-radius: 5px;
    cursor: pointer;
    width: 100%;
    max-width: 200px;
    }

    .btn-cancel:hover {
        background-color: darkred;
    }

    .book-actions {
    margin-top: 10px;
}
</style>
