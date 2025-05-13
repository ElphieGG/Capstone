<?php
session_start();
include 'config.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


// Get user data based on session username
$username = $_SESSION['username'];
//$stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM tbluser WHERE username = ?");
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, image,college,course,year_level FROM tbluser WHERE username = ?");
//$stmt = $conn->prepare("SELECT * FROM tbluser WHERE username = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_id = $user_data['user_id'];
    $image_data = $user_data['image'];
    $college = $user_data['college'];
    $course = $user_data['course'];
    $year_level = $user_data['year_level'];
} else {
    die("No user data found!");
}
$stmt->close();

// Fetch books posted by the user
//$books_stmt = $conn->prepare("SELECT title, image FROM tblbook WHERE user_id = ?");
$books_stmt = $conn->prepare("SELECT id, title, image FROM tblbook WHERE user_id = ?");
if (!$books_stmt) {
    die("Prepare failed: " . $conn->error);
}
$books_stmt->bind_param("i", $user_id);
$books_stmt->execute();
$books_result = $books_stmt->get_result();

$books = [];
if ($books_result->num_rows > 0) {
    while ($row = $books_result->fetch_assoc()) {
        $books[] = $row;
    }
}


$books_stmt->close();
$userId = $_SESSION['user_id']; // Get the logged-in user ID

// Fetch unread notifications
$sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Compatir | User Profile</title>
    <!-- <link rel="stylesheet" href="userfypstyle.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .navbar {
    background: linear-gradient(to right, #f52222, #e60000);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 40px;
    font-family: 'Segoe UI', sans-serif;
}

.logo {
    height: 30px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
   font-weight:bold; 
}

.nav-links li a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s, padding 0.3s;
    padding: 6px 10px;
    border-radius: 5px;
}

.nav-links li a:hover {
    background: rgba(255, 255, 255, 0.2);
}

.nav-links li a i {
    font-size: 18px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  font-size: 22px;
  font-weight: bold;
  font-style: italic;
}

.brand-logo i {
  font-size: 28px;
}

</style>
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
         <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li>
        <li><a href="cart.php"><i class='bx bx-cart'></i> Cart</a></li>
        <li><a href="login.php"><i class='bx bx-log-out'></i> Sign Out</a></li>
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

         <!--     <h2><?php echo $user_data['first_name'] . " " . $user_data['last_name']; ?></h2>-->
            <h2 style="color: #800000;">
    <?php 
    if (!empty($user_data['first_name']) && !empty($user_data['last_name'])) {
        echo htmlspecialchars($user_data['first_name'] . " " . $user_data['last_name']);
    } else {
        echo "User Name Not Available";
    }
    ?>
   

</h2>
            <ul class="user-menu">
            <li style="color: #800000;">
    <?php 
    if (!empty($user_data['college'])) {
        echo htmlspecialchars($user_data['college'] );
    } else {
        echo "College Not Available";
    }
    ?> </li>
   
 <li style="color: #800000;"> <?php 
    if (!empty($user_data['course'])) {
        echo htmlspecialchars($user_data['course'] );
    } else {
        echo "Course Not Available";
    }
    ?> </li>

<li style="color: #800000;"> <?php 
    if (!empty($user_data['year_level'])) {
        echo htmlspecialchars($user_data['year_level'] );
    } else {
        echo "Year Level Not Available";
    }
    ?> </li>
               <li><a href="user.php">My Books</a></li>
                <li><a href="usersale.php">My Sales</a></li>
                <li><a href="userpurchase.php">My Purchases</a></li>
                <li><a href="mytrade_history.php">My Trades</a></li>
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
            
        </div>

 
        
   <!-- My Products Section -->
   <div class="products-box">
            <div class="products-header">
                <h2 style="color: #800000;">My Books</h2>
                <button class="btn-action"><a href="create_bidding_post.php" style="color: white; text-decoration: none;">Add Your Own Books</a></button>
            </div>

            <div class="book-container">
    <?php if (!empty($books)): ?>
        <?php foreach ($books as $book): ?>
            <div class="book-item">
                <?php if (!empty($book['image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($book['image']); ?>" class="book" alt="Book">
                <?php else: ?>
                    <img src="images/default-book.jpg" class="book" alt="Default Book">
                <?php endif; ?>
                <p class="book-title"><?php echo htmlspecialchars($book['title']); ?></p>
                <div class="book-actions">
                    <a href="editbook.php?id=<?php echo $book['id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="deletebook.php?id=<?php echo $book['id']; ?>" class="btn btn-delete delete-btn">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not posted any books yet.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // prevent the default link action
            const deleteUrl = this.getAttribute('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this book? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',  // green for confirm
                cancelButtonColor: '#dc3545',   // red for cancel
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });
});
</script>
   
    
</body>
</html>
<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

/* Navbar
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
} */

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
    width: 350px;
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
   /* background: #a52a2a;*/
    /* background: #660000; */
    background: linear-gradient(to right, #f52222, #e60000);
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
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
    width: 200px;
}

.book-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 6px;
}

.book-item p {
    font-size: 14px;
    margin-top: 5px;
    font-weight: bold;
}

.book-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Space between books */
    justify-content: flex-start; /* Align books horizontally */
    padding: 20px;
}

.book-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 180px; /* Adjust as needed */
    padding: 15px;
    background-color: #f8f9fa; /* Light background */
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.book-item img {
    width: 100px; /* Adjust image size */
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
}

.book-title {
    margin-top: 10px;
    font-size: 16px;
    font-weight: bold;
}

.book-actions {
    margin-top: 10px;
}

.btn {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
}

.btn-edit {
    background-color: #28a745;
    color: white;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}


    .btn-delete:hover {
        background-color: #c82333;
    }

.navbar {
    background: linear-gradient(to right, #f52222, #e60000);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 40px;
    font-family: 'Segoe UI', sans-serif;
}

.logo {
    height: 30px;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s, padding 0.3s;
    padding: 6px 10px;
    border-radius: 5px;
}

.nav-links li a:hover {
    background: rgba(255, 255, 255, 0.2);
}

.nav-links li a i {
    font-size: 18px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
  font-size: 22px;
  font-weight: bold;
  font-style: italic;
}

.brand-logo i {
  font-size: 28px;
}

</style>

