<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">   
    <title>Libro Compartir | About</title>
    <link rel="stylesheet" href="about.css">
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
     <main>   
        <h2>About Us</h2>
        <table>
<tr>
    <td>
    <h2>Welcome to</h2>
    </td>
    <td>
<img src="images/logo.png" alt="Logo image">
    </td>
</tr>       
       
        </table>   
            <p>At Libro Compartir, we believe that every book has a story to tell, not just within its pages, but through its journey from one reader to another.</p>
       
            <h3>Our Main Objective</h3>
            <p>The main objective is to enhance accessibility to textbooks for all students at Western Mindanao State University by creating a platform for easy exchanging, buying, and selling of books, thereby removing financial barriers and promoting sustainability through the reuse of educational materials.</p>
       
               
     
            <h3>Our Story</h3>
            <p>Libro Compartir encourages the reuse of textbooks, ensuring that books do not remain unused after a single semester, maximizing the utility of existing resources and preventing waste. </p>    
            
        
            <h3>Contact Us</h3>
           <p>Have questions or suggestions? Reach out to us at <a href="mailto:info@yourwebsite.com">info@librocompatir.com</a>.</p>
      
    </div>

    </main> 

    <!-- Call to Action -->
    <div class="cta">
        <p>Ready to start exchanging, buying or selling books?</p>
        <a href="join.php">Join Now (It's Free!)</a>
        <p>&copy; <?php echo date("Y"); ?> Libro Compartir. All Rights Reserved.</p>
    </div>
</body>
</html>
