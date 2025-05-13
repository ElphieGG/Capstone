<?php
session_start();
include 'config.php';

$errorMessage = ""; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);

    // Check if user exists in tbladmin (Admin Login)
    $stmt = $conn->prepare("SELECT * FROM tbladmin WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows > 0) {
        $admin_data = $adminResult->fetch_assoc();

        if ($input_password == $admin_data['password']) {
            // Set session variables for admin
            $_SESSION['user_type'] = "admin";
            $_SESSION['username'] = $admin_data['username'];
            $_SESSION['admin_id'] = $admin_data['admin_id'];

            header("Location: dashboard.php"); // Redirect admin
            exit();
        }
    }

    // Check if user exists in tbluser (Regular User Login)
    $stmt = $conn->prepare("SELECT * FROM tbluser WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        $user_data = $userResult->fetch_assoc();

        // Check if the user is deactivated
        if ($user_data['user_status'] === 'inactive') {
            $errorMessage = "Your account has been deactivated. Please contact support@librocompatir.com.";
        } 
        elseif ($user_data['registration_status'] === 'pending') {
            $errorMessage = "Your account is pending for approval. Please contact support@librocompatir.com.";
        }
        elseif (password_verify($input_password, $user_data['password'])) {
            // Set user session variables
            $_SESSION['user_type'] = "user";
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['user_id'] = $user_data['user_id'];

            header("Location: user.php"); // Redirect user
            exit();
        }
    }

    // If neither admin nor user credentials match
    if (empty($errorMessage)) {
        $errorMessage = "Invalid username or password!";
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
    <title>Libro Compartir | Login</title>
    <link rel="stylesheet" href="login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f3e5e5;
            color: #333;
        }

        /* Header */
        .header {
            background: #800000; /* Maroon */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: bold;
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

        /* Search Bar */
        .search-bar input[type="text"] {
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            width: 200px;
        }
        .search-bar button {
            padding: 8px 15px;
            border: none;
            background: linear-gradient(to right, #f52222, #e60000);
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background: #660000;
        }

        /* Hero Section */
        .hero {
            background: rgb(243, 233, 233);
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }
        .hero img {
            max-width: 100%;
            height: 100%;
            margin-bottom: 10px;
        }
        .hero h2 {
            font-size: 24px;
            margin: 0;
            color: #800000; /* Maroon */
        }
        .hero p {
            font-size: 1.2em;
            color: #800000;
        }

        /* Form Styles */
        .form-header h2 {
            color: #800000;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #800000; /* Maroon */
             color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #660000; /* Darker Maroon */
                }
        .reg a {
            color: red; /* Red */
            text-decoration: none;
            font-weight: bold;
        }
        .reg a:hover {
            text-decoration: underline;
        }
        .box2{
    background-color: rgb(209, 52, 54,0.8);
    width: 25%;
    height: 50%;
    position: absolute;
    top: 10%;
    left: 26%;
    border-radius: 30px;
}
.box1{
    background-color: white;
    width: 25%;
    height: 50%;
    position: absolute;
    top: 10%;
    left: 48%;
    border-radius: 30px;
}
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar">
  <div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
  </div>
  <ul class="nav-links">
      <li><a href="index.php"><i class='bx bx-home'></i> Home</a></li>
      <li><a href="browse.php"><i class='bx bx-search'></i> Browse</a></li>
      <li><a href="about.php"><i class='bx bx-info-circle'></i> About</a></li>
      <li><a href="join.php"><i class='bx bx-user-plus'></i> Join</a></li>
      <li><a href="login.php"><i class='bx bx-log-in'></i> Log In</a></li>
  </ul>
</nav>

    <!-- Hero Section -->
    <div class="hero">
    <!--  <div class="box2">
         <img src="images/pic1.png" class="logo">     
        </div>   
        <div class="box1"> --> 
        <form action="login.php" method="post" action="login.php">
            <div class="form-header">
                <br><br><br>
                <h2>Login to Your Account</h2>
            </div>
            <?php if (!empty($errorMessage)): ?>
                <div class="error-message" style="color: red; text-align: center; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <div class="login">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="login">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="log-btn">
                <button type="submit" class="btn btn-primary" value="Login" name="submit">Login</button>
            </div>
            <div class="reg">
                <p>Don't have an account? <a href="join.php">Click Here to Join</a></p>
            </div>
            </form>
        <!--     </div>-->
    </div>
</body>
</html>
