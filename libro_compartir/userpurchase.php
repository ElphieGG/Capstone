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
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, image,college,course FROM tbluser WHERE username = ?");

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
} else {
    die("No user data found!");
}
$stmt->close();



$result = $conn->query("SELECT * FROM order_details");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Sales</title>
    <!-- <link rel="stylesheet" href="userstyle.css"> -->
    <!-- <link rel="stylesheet" href="userfyp.css"> -->
    <!-- <link rel="stylesheet" href="navbar.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function confirmAction(action) {
            return confirm("Are you sure you want to " + action + " this order?");
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
                <li><a href="user.php">My Books</a></li>
                <li><a href="usersale.php">My Sales</a></li>
                <li><a href="userpurchase.php">My Purchases</a></li>
                <li><a href="mytrade_history.php">My Trades</a></li>
                <li><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>

<!-- My Sales Section -->
<div class="products-box">
    <div class="products-header">
        <h2 style="color: #800000;">My Purchase Transactions</h2>
      
    </div>
   
    <?php
include("config.php"); // Adjust if your connection file is different

$user_id = $_SESSION['user_id']; // Make sure user_id is stored in session

// Fetch the data
$sql = "SELECT 
            od.detail_id,
            b.image,
            b.title,
            b.book_status,
            u.first_name,
            u.last_name,
            od.price,
            o.order_date,
            o.payment_method,
            od.status,
            b.id AS book_id,
            (SELECT COUNT(*) FROM tblreviews r WHERE r.user_id = ? AND r.id = b.id) AS review_count
        FROM order_details od
        JOIN tblbook b ON od.product_id = b.id
        JOIN orders o ON od.order_id = o.orders_id
        JOIN tbluser u ON b.user_id = u.user_id
         LEFT JOIN tblreviews r ON od.product_id = r.id AND o.user_id = r.user_id
        WHERE o.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id); // <- bind two times!
$stmt->execute();
$result = $stmt->get_result();
?>

<?php
if ($result->num_rows > 0) {
    // Display the table only if results exist
?>

<div class="book-container">
        <table class="sales-table">
            <thead>
                <tr>
        <!-- <th>Detail ID</th> -->
        <th>Book</th>
        <th>Title</th>
        <th>Seller Name</th>
        <th>Price </th>
        <th>Order Date</th>
        <th>Payment Method</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

  </thead>
  <tbody>
    

  <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <!-- <td><?= $row['detail_id']; ?></td> -->
            <td>
                <?php if (!empty($row['image'])) {
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" width="60" height="80"/>';
                } else {
                    echo 'No Image';
                } ?>
            </td>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?= number_format($row['price'], 2); ?></td>
            <td><?= date('Y-m-d', strtotime($row['order_date'])); ?></td>
            <td><?= htmlspecialchars($row['payment_method']); ?></td>
            <td><?= htmlspecialchars($row['status']); ?></td>
            <td>
            <?php if ($row['status'] === 'Approved') {
    if ($row['book_status'] === 'Sold') { ?>
        <!-- Already received -->
        <button disabled 
         
        style="padding:5px 10px; background-color: blue; color: white;">Received</button>
    <?php } else { ?>
        <!-- Can still receive -->
        <button 
            class="receive-btn" 
            data-book-id="<?= $row['book_id']; ?>" 
            style="padding:5px 10px;"
        >Receive</button>
    <?php }
} else { ?>
    <!-- Not yet approved -->
    <button disabled 
    title="Status must Approved first"
    style="padding:5px 10px; background-color: #ccc;">Receive</button>
<?php } ?>

    <!-- Rate Button (Enabled only if book_status is 'Sold') -->
    <?php
if ($row['book_status'] === 'Sold') {
    if ($row['review_count'] == 0) { ?>
        <button 
            class="rate-btn" 
            data-title="<?= htmlspecialchars($row['title']); ?>" 
            data-bookid="<?= $row['book_id']; ?>"
            style="margin-left: 5px; padding:5px 10px; background-color: orange; color: white; border: none; border-radius: 3px;"
        >Rate</button>
    <?php } else { ?>
        <button 
            disabled
            title="You have already rated this book."
            style="margin-left: 5px; padding:5px 10px; background-color: #ccc; color: white; border: none; border-radius: 3px;"
        >Rated</button>
    <?php }
} else { ?>
    <button 
        disabled
        title="You must receive the book first"
        style="margin-left: 5px; padding:5px 10px; background-color: #ccc; color: white; border: none; border-radius: 3px;"
    >Rate</button>
<?php } ?>
</td>
        </tr>
    <?php } ?>
</table>

<?php
} else {
    // No purchases
    echo '
    <div style="text-align: center; margin-top: 50px; color: #666;">
        <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" alt="No Purchases" width="100" height="100" style="opacity: 0.6;">
        <h3 style="margin-top: 20px;">You have not made any purchases yet.</h3>
        <p>Start exploring and add books to your cart!</p>
        <a href="books_for_sale.php" style="
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #3b4cca;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        ">Shop Now</a>
    </div>';
}
?>

<?php
// Process the Received button
if (isset($_POST['mark_received'])) {
    $book_id = intval($_POST['book_id']);

    $update = $conn->prepare("UPDATE tblbook SET book_status = 'Sold' WHERE id = ?");
    $update->bind_param("i", $book_id);
    
    if ($update->execute()) {
        echo "<script>alert('Book status updated to Sold successfully.'); window.location.href=window.location.href;</script>";
    } else {
        echo "<script>alert('Failed to update book status.');</script>";
    }
}
?>

<script>
document.querySelectorAll('.receive-btn').forEach(button => {
    button.addEventListener('click', function () {
        const bookId = this.getAttribute('data-book-id');
        const btn = this;

        if (confirm('Are you sure you received this book?')) {
            // AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_received.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200 && xhr.responseText.trim() === 'success') {
                    // Update Receive button
                    btn.textContent = "Received";
                    btn.style.backgroundColor = "blue";
                    btn.style.color = "white";
                    btn.disabled = true;

                    // Enable Rate button next to it
                    const rateBtn = btn.parentElement.querySelector('.rate-btn');
                    if (rateBtn) {
                        rateBtn.disabled = false;
                        rateBtn.style.backgroundColor = "orange";
                        rateBtn.style.color = "white";
                        rateBtn.title = "";
                    }
                } else {
                    alert('Failed to update. Please try again.');
                }
            };
            xhr.send("book_id=" + bookId);
        }
    });
});
</script>
<div id="rateModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%);
background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 20px rgba(0,0,0,0.3); z-index:999;">
    <span style="position:absolute; top:10px; right:15px; cursor:pointer;" onclick="closeRateModal()">×</span>
    <h3 style="color:#800000; text-align:center;">Rate the Book and Seller</h3>
    <p id="bookTitle" style="text-align:center; margin-bottom:15px;"></p>

   <form id="rateForm">
        <input type="hidden" name="book_id" id="rate_book_id">
        <input type="number" name="book_rating" placeholder="Book Rating (1-5)" min="1" max="5" required style="width:100%; margin-bottom:10px;">
        <textarea name="book_review" placeholder="Write a book review" style="width:100%; margin-bottom:10px;"></textarea>
        <input type="number" name="seller_rating" placeholder="Seller Rating (1-5)" min="1" max="5" required style="width:100%; margin-bottom:10px;">
        <textarea name="seller_review" placeholder="Write a seller review" style="width:100%; margin-bottom:15px;"></textarea>
        <button type="submit" style="background-color:green; color:white; padding:8px 20px; border:none; border-radius:4px;">Submit</button>
    </form>
</div>

<!-- Modal Background -->
<div id="modalBackground" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:998;" onclick="closeRateModal()"></div>

<script>
function closeRateModal() {
    document.getElementById('rateModal').style.display = 'none';
    document.getElementById('modalBackground').style.display = 'none';
}

document.querySelectorAll('.rate-btn').forEach(button => {
    button.addEventListener('click', function() {
        const bookTitle = this.getAttribute('data-title');
        const bookId = this.getAttribute('data-bookid');
        document.getElementById('bookTitle').innerText = bookTitle;
        document.getElementById('rate_book_id').value = bookId;
        document.getElementById('rateModal').style.display = 'block';
        document.getElementById('modalBackground').style.display = 'block';
    });
});
</script>

<script>
document.getElementById('rateForm').addEventListener('submit', function (e) {
    e.preventDefault(); // prevent full page reload

    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerText = "Submitting...";

    const bookId = document.getElementById('rate_book_id').value;
    const formData = new FormData(this);
    formData.append("ajax", "true"); // optional tag

    fetch("submit_rating.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === "success") {
            // Close the modal
            closeRateModal();

            // Update the Rate button to "Rated"
            document.querySelectorAll(`.rate-btn[data-bookid="${bookId}"]`).forEach(btn => {
                btn.disabled = true;
                btn.innerText = "Rated";
                btn.style.backgroundColor = "#ccc";
                btn.title = "You have already rated this book.";
            });

            alert("Thanks for your review!");
        } else {
            alert("Failed to submit review. Try again.");
            submitButton.disabled = false;
            submitButton.innerText = "Submit";
        }
    })
    .catch(() => {
        alert("Error submitting review.");
        submitButton.disabled = false;
        submitButton.innerText = "Submit";
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

/* Navbar */
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
     
/* Table Styling */
.sales-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Table Header */
.sales-table thead {
    background-color: #007bff;
    color: white;
}

.sales-table th {
    padding: 12px;
    text-align: left;
}

/* Table Body */
.sales-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

/* Alternating row colors */
.sales-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* No sales message */
.no-sales {
    text-align: center;
    padding: 15px;
    font-size: 16px;
    color: #666;
    font-weight: bold;
}

/* Rating and review alignment */
.rating {
    text-align: center;
    font-weight: bold;
}

/* Not Rated / No Review styling */
.no-review {
    color: #888;
    font-style: italic;
}

/* Book Image Styling */
.book-image {
    width: 60px;
    height: auto;
    border-radius: 5px;
}

/* Image Cell Styling */
.image-cell {
    text-align: center;
}
</style>