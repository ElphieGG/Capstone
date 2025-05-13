
<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $book_condition = $_POST['book_condition'];
    $book_status = $_POST['book_status'];
    $meeting_spot = $_POST['meeting_spot'];
    $bidding_start_price = $_POST['bidding_start_price'] ?? NULL;
    $bidding_end_time = $_POST['bidding_end_time'] ?? NULL;
    $user_id = $_SESSION['user_id'];

    $imageData = file_get_contents($_FILES['image']['tmp_name']);

    $query = "INSERT INTO tblbook (title, description, category, book_condition, book_status, image, user_id, bidding_start_price, bidding_end_time, meeting_spot)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssdss", $title, $description, $category, $book_condition, $book_status, $imageData, $user_id, $bidding_start_price, $bidding_end_time, $meeting_spot);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
              Swal.fire({
                  icon: 'success',
                  title: 'Book Posted Successfully!',
                  showConfirmButton: true,
              }).then(() => {
                  window.location.href = 'user.php';
              });
              </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel ="stylesheet" href = "navbar.css">
    
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px;">
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
<div style="max-width: 600px; margin: 40px auto; padding: 30px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background-color: #fff;">
    <h2 style="text-align: center; margin-bottom: 20px;">Create New Book</h2>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" style="display: flex; flex-direction: column; gap: 15px;">
        
        <input type="text" name="title" placeholder="Title" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">

        <textarea name="description" placeholder="Description" rows="4" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

        <select name="category" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <option value="">Select Category</option>
            <option value="Educational">Educational</option>
            <option value="Fiction">Fiction</option>
            <option value="Non-Fiction">Non-Fiction</option>
            <option value="Mystery">Mystery</option>
            <option value="Fantasy">Fantasy</option>
        </select>

        <select name="book_condition" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <option value="">Select Book Condition</option>
            <option value="New">New</option>
            <option value="Used">Used</option>
            <option value="Worn">Worn</option>
        </select>

        <select name="book_status" id="book_status" onchange="toggleFields()" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <option value="">--Select Status--</option>
            <option value="For Sale">For Sale</option>
            <option value="For Exchange">For Exchange</option>
            <option value="For Bidding">For Bidding</option>
        </select>

        <select name="meeting_spot" placeholder="Meeting Spot" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <option value="">--Select Meeting Spot--</option>
            <option value="WMSU Campus A">WMSU Campus A</option>
            <option value="WMSU Campus B  ">WMSU Campus B</option>
            <option value="Outside WMSU">Outside WMSU</option>
        </select>
        <div id="price_section" style="display: none;">
            <label id="price_label" for="bidding_start_price">Bidding Start Price:</label><br>
            <input type="number" name="bidding_start_price" id="bidding_start_price" step="0.01" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%;">
        </div>

        <div id="end_time_section" style="display: none;">
            <label for="bidding_end_time">Bidding End Time:</label><br>
            <input type="datetime-local" name="bidding_end_time" id="bidding_end_time" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%;">
        </div>

        <input type="file" name="image" required style="padding: 10px;">

        <div style="display: flex; justify-content: center; gap: 10px;">
            <button type="submit" name="submit" style="padding: 12px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Submit
            </button>
            <a href="user.php" style="text-decoration: none;">
                <button type="button" style="padding: 12px 20px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Cancel
                </button>
            </a>
        </div>
    </form>
</div>

<script>
function toggleFields() {
    var status = document.getElementById("book_status").value;
    var priceSection = document.getElementById("price_section");
    var endTimeSection = document.getElementById("end_time_section");
    var priceLabel = document.getElementById("price_label");

    if (status === "For Exchange") {
        priceSection.style.display = "none";
        endTimeSection.style.display = "none";
    } else if (status === "For Sale") {
        priceSection.style.display = "block";
        endTimeSection.style.display = "none";
        priceLabel.innerHTML = "Price:";
    } else if (status === "For Bidding") {
        priceSection.style.display = "block";
        endTimeSection.style.display = "block";
        priceLabel.innerHTML = "Bidding Start Price:";
    } else {
        priceSection.style.display = "none";
        endTimeSection.style.display = "none";
    }
}

function validateForm() {
    var status = document.getElementById("book_status").value;
    var price = document.getElementById("bidding_start_price").value;
    var endTime = document.getElementById("bidding_end_time").value;

    if ((status === "For Sale" || status === "For Bidding") && price === "") {
        alert("Price is required for For Sale or For Bidding.");
        return false;
    }
    if (status === "For Bidding" && endTime === "") {
        alert("Bidding End Time is required for For Bidding.");
        return false;
    }
    return true;
}

document.addEventListener("DOMContentLoaded", toggleFields);
</script>

</body>
</html>
