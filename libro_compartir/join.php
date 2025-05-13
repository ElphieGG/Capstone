<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['first_name'] = htmlspecialchars(trim($_POST['first-name']));
    $_SESSION['middle_name'] = htmlspecialchars(trim($_POST['middle-name']));
    $_SESSION['last_name'] = htmlspecialchars(trim($_POST['last-name']));
    $_SESSION['email'] = htmlspecialchars(trim($_POST['email']));
    $_SESSION['phone'] = htmlspecialchars(trim($_POST['phone']));
    $_SESSION['username'] = htmlspecialchars(trim($_POST['username']));

    $_SESSION['password'] = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    header("Location: join2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Compartir | Join</title>
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
            max-width: 80%;
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
    height: 69%;
    position: absolute;
    top: 10%;
    left: 26%;
    border-radius: 30px;
}
.box1{
    background-color: white;
    width: 25%;
    height: 65%;
    position: absolute;
    top: 10%;
    left: 48%;
    border-radius: 30px;
}
.signup-form {
   /* background-color: rgb(126, 0, 0);*/
   background-color:white;
    border-radius: 8px;
    padding: 20px;
    width: 100%;
    max-width: 400px;
}

.signup-form h1 {
    margin-top: 0;
    margin-bottom: 10px;
    color: white;
    text-align: center;
}

.signup-form h5 {
    margin-top: 0;
    margin-bottom: 30px;
    color: white;
    text-align: center;
    font-weight: initial;
}

.signup-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: initial;
}

.signup-form input {
    width: 100%;
    padding: 8px;
    margin-bottom: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.signup-form button {
    background-color: #800000; 
    color: white;
    border: none;
    padding: 10px;
    width: 100%;
    border-radius: 4px;
    cursor: pointer;
}

.signup-form button:hover {
    background-color: #660000; /* Darker Maroon */
}

.picture {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 55%;
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
            <img src="images/pic2.png" class="logo">          
        </div>
        <div class="box1"> -->
        <form class="signup-form" method="POST" action="join.php">
            <div class="form-header">
                <br>
                <h2>Join Libro Compartir</h2>
            </div>
                <!--<label for="first-name">First Name</label>-->
                <div class="login">
                <input type="text" class="form-control" id="first-name" name="first-name" placeholder="First Name" required>
                <input type="text"  class="form-control" id="middle-name" name="last-name" placeholder="Middle Name">
                <input type="text"  class="form-control" id="last-name" name="last-name" placeholder="Last Name" required>
                <input type="email"  class="form-control" id="email" name="email" placeholder="Email" required>
                <input type="tel"  class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                <input type="text"  class="form-control" id="username" name="username" placeholder="Username" required>
                <input type="password"  class="form-control" id="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary" >Next</button>
          
                </div>
                <div class="reg">
                <p>Already have an account? <a href="login.php">Log In Here </a></p>
            </div>
            </form>
        <!--</div>-->
    </div>
</body>
</html>
