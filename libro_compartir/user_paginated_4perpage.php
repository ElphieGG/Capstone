<?php
include 'config.php';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$limit = 4;
$offset = ($page - 1) * $limit;

// Count total books
$countResult = $conn->query("SELECT COUNT(*) as total FROM tblbook");
$totalBooks = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $limit);

// Fetch paginated books
$sql = "
    SELECT tblbook.id, tblbook.title, tblbook.image, tbluser.first_name, tbluser.last_name
    FROM tblbook 
    JOIN tbluser ON tblbook.user_id = tbluser.user_id
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

if ($result === false) {
    die("SQL Error: " . $conn->error);
}

$books = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
} else {
    echo "No books found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Compatir | User Profile</title>
    <link rel="stylesheet" href="userstyle.css">
    
</head>
<body>
    <nav class="navbar">
        <img src="images/logo2.png" class="logo" alt="Logo">
        <ul class="nav-links">
        <li><a href="userfyp.php">Home</a></li>
        <li><a href="chat.php">Chat</a></li>
        <li><a href="user.php">Profile</a></li>
        <li><a href="notifications.php">Notification</a></li>
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

         <!--     <h2><?php echo $user_data['first_name'] . " " . $user_data['last_name']; ?></h2>-->
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
                <li><a href="user.php">My Books</a></li>
                <li><a href="usersale.php">My Sales</a></li>
                <li><a href="userpurch.php">My Purchases</a></li>
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>

 
        
   <!-- My Products Section -->
   <div class="products-box">
            <div class="products-header">
                <h2>My Books</h2>
                <button class="btn-action"><a href="createpost.php" style="color: white; text-decoration: none;">Add Your Own Books</a></button>
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
   
    

<div style="display: flex; justify-content: center; margin: 30px 0;">
<?php if ($totalPages > 1): ?>
    <div class="pagination" style="display: flex; gap: 10px;">
        <?php if ($page > 1): ?>
            <!--- <a href="?page=<?= $page - 1 ?>" style="padding: 8px 12px; background: #eee; text-decoration: none;">&laquo; Previous</a> --->
            <a href="?page=<?= $page - 1 ?>" style="margin: 0 10px;">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <!---   <a href="?page=<?= $i ?>" style="padding: 8px 12px; background: <?= ($i == $page) ? '#ccc' : '#fff' ?>; text-decoration: none; border: 1px solid #ddd;"> --->
            <a href="?page=<?= $i ?>" style="margin: 0 5px; <?= ($i == $page) ? 'font-weight: bold; text-decoration: underline;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
           <!---  <a href="?page=<?= $page + 1 ?>" style="padding: 8px 12px; background: #eee; text-decoration: none;">Next &raquo;</a> --->
           <a href="?page=<?= $page + 1 ?>" style="margin: 0 10px;">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>


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
    background: #660000;
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

</style>

