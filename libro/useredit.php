<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT user_id, first_name, middle_name,last_name, phone, email, birthday,baraaddress,ship_location,image,college,course FROM tbluser WHERE username = ?");
$stmt = $conn->prepare("SELECT * FROM tbluser WHERE username = ?");
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $birthday = trim($_POST['birthday']);
    $baranggay = trim($_POST['baranggay']);
    $city = trim($_POST['city']);
    // $year_level = trim($_POST['year_level']);
    // $ship_location = trim($_POST['ship_location']);

    // Validate input fields
    if (empty($first_name) || empty($last_name) || empty($phone) || empty($email) || empty($birthday) || empty($baranggay)|| empty($city)) {
        echo "<script>alert('All fields are required!');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!');</script>";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        echo "<script>alert('Invalid phone number!');</script>";
    } else {
        // Update the user profile
        $update_stmt = $conn->prepare("UPDATE tbluser SET first_name = ?, middle_name = ?,last_name = ?, phone = ?, email = ?,image= ?, birthday = ?, baranggay = ?, city = ?, year_level = ? WHERE user_id = ?");
        if (!$update_stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $update_stmt->bind_param("ssssssssssi", $first_name, $middle_name, $last_name, $phone, $email,$image, $birthday,$baranggay,$city,$year_level, $user_id);

          // Load SweetAlert2
    echo "<html><head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head><body>";

        if ($update_stmt->execute()) {
           // echo "<script>alert('Profile updated successfully!'); window.location='user.php';</script>";
           echo "<script>
           Swal.fire({
               icon: 'success',
               title: 'Profle Updated!',
               text: 'Profile updated successfully!',
               confirmButtonText: 'OK'
           }).then(() => {
               window.location.href = 'user.php';
           });
         </script>";
        } else {
            //echo "Error updating profile: " . $update_stmt->error;
            echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Profle not Updated!',
                text: 'Error updating profile:',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'user.php';
            });
          </script>";

        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- <link rel="stylesheet" href="userstyle.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

                <li style= "font-size:16px"><a href="user.php">My Books</a></li>
                <li style= "font-size:16px"><a href="usersale.php">My Sales</a></li>
                <li style= "font-size:16px"><a href="userpurchase.php">My Purchases</a></li>
                <li><a href="mytrade_history.php">My Trades</a></li>
                <li style= "font-size:16px"><a href="useredit.php">Edit Profile</a></li>
            </ul>
        </div>
        
        <div class="products-box">
            <div class="products-header">
                <h2 style="color: #800000;">Edit Profile</h2>
            </div>
            <form action="useredit.php" method="POST" class="edit-profile-form">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Middle Name:</label>
                    <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user_data['middle_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Profile Image:</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Birthday:</label>
                    <input type="date" name="birthday" value="<?php echo htmlspecialchars($user_data['birthday']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Baranggay:</label>
                    <input type="text" name="baranggay" value="<?php echo htmlspecialchars($user_data['baranggay']); ?>" required>
                </div>
                

                <div class="form-group">
                    <label>City:</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($user_data['city']); ?>" required>
                </div>

                <!-- <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($user_data['address']); ?>" required>
                </div> -->

                <div class="buttons">
                <button type="submit" class="btn-update">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='user.php'">Cancel</button>
                </div>

            </form>
        </div>
    </div>
</body>
</html>
<style>
/* General Styles */

.buttons {
                display: flex;
                gap: 10px;
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
</style>
