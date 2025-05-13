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
/* Style for the button container */
.image-button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            outline: none;
        }
        .accordion.active {
   /* background-color: #007bff; /* Highlight color 
    background-color: #800000; /* Maroon */
    background-color:rgb(238, 65, 65);
    color: white;
  }
  * {
    box-sizing: border-box;
    padding: 0;
    margin: 0;
}

.admin-dashboard-container {
    margin-top: 20px;
    padding: 20px;
    background-color: #800000; /* Maroon */
    border-radius: 5px;
    color: white;
}

.admin-dashboard-container h2 {
    margin-bottom: 10px;
    color: #ffd700; /* Gold for contrast */
}

.admin-dashboard-item {
    margin-bottom: 20px;
}

.admin-dashboard-item label {
    display: block;
    font-weight: bold;
    color: white;
}

.admin-dashboard-item input[type="text"],
.admin-dashboard-item textarea {
    width: 100%;
    padding: 5px;
    border: 1px solid #d32f2f;
    border-radius: 3px;
    background-color: #fff5f5;
}

.admin-dashboard-item button {
    padding: 8px 15px;
    background-color: #d32f2f;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.admin-dashboard-item button:hover {
    background-color: #b71c1c;
}

/* Sidebar */
.sidebar {
    float: left;
    width: 20%;
    background-color: #800000;
    padding: 15px;
    height: 100vh;
    overflow-y: auto;
    color: white;
}

.sidebar ul {
    padding: 0;
}

.sidebar li {
    list-style: none;
    margin-bottom: 5px;
}

.sidebar li a {
    display: block;
    padding: 10px;
    background-color: #d32f2f;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 5px;
}

.sidebar li a:hover {
    background-color: #b71c1c;
}

/* Content */
.content {
    float: left;
    width: 80%;
    padding: 15px;
    height: 100vh;
    overflow-y: auto;
    background-color: #fff5f5;
}

/* Accordion */
.accordion {
   /* background-color: #d32f2f;*/
    background-color: #800000; /* Maroon */
    color: white;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}

.active,
.accordion:hover {
    background-color: #b71c1c;
}

.panel {
    padding: 0 18px;
   /* background-color: #fff5f5;*/
    background-color: #800000; /* Maroon */
    display: none;
    overflow: hidden;
}

.panel p {
    padding: 10px;
    color: black;
}

/* Logout */
.logout {
    text-align: center;
    margin-top: 20px;
}

.logout button {
    padding: 8px 15px;
    background-color: #d32f2f;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.logout button:hover {
    background-color: #b71c1c;
}

/* Dashboard Items */
.dashboard-items {
    display: flex;
    flex-wrap: wrap;
}

.dashboard-item {
    flex: 1 1 calc(33.33% - 20px);
    margin: 10px;
    padding: 20px;
    background-color: #800000;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    color: white;
}

.dashboard-item:hover {
    transform: translateY(-5px);
}

.dashboard-item .icon {
    font-size: 24px;
    margin-bottom: 10px;
    color: #ffd700; /* Gold */
}

.dashboard-item .data {
    font-size: 18px;
    color: white;
}

     
</style>
</head>
<body>
<main>
      <div class="sidebar">
         <img src="images/logo2.png" alt="Logo" class="logo">
        <button class="accordion" onclick="window.location.href='dashboard.php'">
          Dashboard
        </button>
        <div class="panel">
         
        </div>

        <button class="accordion">Books</button>
        <div class="panel">
          <ul>
            <li><a href="books.php">All Books</a></li>
            <li><a href="books_sold.php">Books Sold</a></li>
            <li><a href="books_exchanged.php">Books Exchanged</a></li>            
          </ul>
        </div>
        <button class="accordion">Sales Report</button>
        <div class="panel">
          <ul>
          <li><a href="weekly_sales.php">Weekly Sales</a></li>
            <li><a href="monthly_sales.php">Monthly Sales</a></li>
          </ul>
        </div>


        <button class="accordion">Users</button>
        <div class="panel">
          <ul>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="all_users.php">All Users&nbsp;</a></li>
          </ul>
        </div>   

        <button class="accordion">Admin</button>
        <div class="panel">
          <ul>
            <li><a href="add_admin.php">Add Admin</a></li>
            <li><a href="admin.php">All Admin&nbsp;</a></li>
          </ul>
        </div>  
                
        <button class="accordion">Book Categories</button>
        <div class="panel">
          <ul>
          <li><a href="add_category.php">Add Category</a></li>
            <li><a href="all_category.php">All Categories</a></li>
          </ul>
        </div>                

        <button class="accordion" onclick="window.location.href='login.php'">
          Log out
        </button>
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

    <div class="form-group">
        <label>Buy Out Price:</label>
        <input type="number" step="0.01" name="buy_out_price" value="<?php echo htmlspecialchars($buy_out_price); ?>">
    </div>

    <div class="form-group">
        <label>Meeting Spot:</label>
        <input type="text" name="meeting_spot" value="<?php echo htmlspecialchars($meeting_spot); ?>" required>
    </div>

    <div class="form-group">
        <label>Bidding End Time:</label>
        <input type="datetime-local" name="bidding_end_time" value="<?php echo $bidding_end_time ? date('Y-m-d\TH:i', strtotime($bidding_end_time)) : ''; ?>">
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
