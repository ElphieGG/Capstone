<?php
//session_start();
include 'config.php';

// Fetch books and user information
$sql = "
    SELECT tblbook.id, tblbook.title, tblbook.image, tbluser.first_name, tbluser.last_name,tblbook.book_status
    FROM tblbook 
    JOIN tbluser ON tblbook.user_id = tbluser.user_id
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
    <title>Libro Compartir | Browse</title>
    <link rel="stylesheet" href="browse.css">
    <link rel="stylesheet" href="login.css">
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
     <h2>All Books</h2>
     <div class="books-wrapper">
                <?php
                foreach ($books as $book) {
                    $imageData = base64_encode($book['image']);
                    echo '<a href="book_details.php?id=' . $book['id'] . '" class="book-container">';
                    echo '<span class="tooltip-text">Status: ' . htmlspecialchars($book['book_status']) . '</span>';
                    echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="book" alt="book">';
                    echo '<p class="book-title">' . $book['title'] . '</p>';
                    echo '<p class="book-author">Posted by: ' . $book['first_name'] . ' ' . $book['last_name'] . '</p>';
                    echo '</a>';
                }
                ?>
            </div>

    </main> 
            </div>

    <!-- Call to Action -->
    <div class="cta">
        <p>Ready to start exchanging, buying or selling books?</p>
        <a href="join.php">Join Now (It's Free!)</a>
        <p>&copy; <?php echo date("Y"); ?> Libro Compartir. All Rights Reserved.</p>
    </div>
</body>
</html>
