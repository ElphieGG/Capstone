<?php
// Database connection
include 'config.php';

$books = [];
$showResults = false; // Track if search is performed

// Check if search query exists and is not empty
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search']) && !empty($_POST['search'])) {
    $searchTerm = $conn->real_escape_string($_POST['search']);
    $showResults = true; // Show results when search is performed

    // Fetch matching books
    $query = "SELECT tblbook.id, tblbook.title, tbluser.first_name, tbluser.last_name, tblbook.image 
          FROM tblbook 
          JOIN tbluser ON tblbook.user_id = tbluser.user_id 
          WHERE tblbook.title LIKE '%$searchTerm%'";

$result = $conn->query($query);

// Check for errors
if (!$result) {
    die("Query Error: " . $conn->error);
}
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row; // Store book details in an array
        }
    }
}

// Fetch book titles for suggestions
$suggestionQuery = "SELECT title FROM tblbook";
$suggestionResult = $conn->query($suggestionQuery);
$suggestionArray = [];

if ($suggestionResult->num_rows > 0) {
    while ($row = $suggestionResult->fetch_assoc()) {
        $suggestionArray[] = $row['title'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Compartir | Home</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
 <style>
            /* General styles */
            body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
            background-color: #f7f8fc;
        }

        /* Header styles */
        header {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
            text-align: center;
            padding: 2rem 0;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        /* Main content styles */
        main {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Section styles */
        section {
            margin-bottom: 2rem;
        }

        section h2, section h3 {
            color: #2575fc;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        section p {
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: justify;
        }

        section ul, section ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        section ul li, section ol li {
            margin-bottom: 0.5rem;
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

        /* Footer styles */
        footer {
            text-align: center;
            background: #6a11cb;
            color: #fff;
            padding: 1rem 0;
            margin-top: 2rem;
            border-top: 4px solid #2575fc;
        }

        footer p {
            margin: 0;
        }

        /* Button styles */
        button, a.button {
            display: inline-block;
            background: #2575fc;
            color: #fff;
            padding: 0.8rem 1.5rem;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 1rem;
            text-decoration: none;
        }

        button:hover, a.button:hover {
            background: #1a61c8;
            
        }

        /* Responsive design */
        @media (max-width: 768px) {
            main {
                padding: 1rem;
            }

            header h1 {
                font-size: 2rem;
            }

            section h2, section h3 {
                font-size: 1.5rem;
            }
        }
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
            color:  #800000;
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
          background: #ffffff;    
             /* background: rgb(243, 233, 233);*/
            padding: 10px 10px;
            text-align: center;
            
        }
      
        .hero h1{
            color: #800000; /* Maroon */
            text-align: center;
        }
        .hero h2 {
            font-size: 2.2em;
            margin: 0;
            color: #800000; /* Maroon */
        }
        .hero h3 {
            font-size: 1.8em;
            color: #800000;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.2em;
            color: #800000;
        }
        .hero img {
            max-width: 100%;
            height: 500px;
            margin-bottom: 20px;
        }

        /* How It Works Section */
        .how-it-works {
           
          /*  background: #d7bcbc; /* Light Maroon Tint */
            background: rgb(243, 233, 233);
            padding: 30px 20px;
            text-align: center;
        }
        .how-it-works h3 {
            font-size: 1.8em;
            color: #800000;
            margin-bottom: 20px;
        }
        .steps {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .step {
           /* background: #f3e5e5; /* Light Maroon Background */
           background: #ffffff;   
            padding: 20px;
            border-radius: 8px;
            width: 220px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .step h4 {
            font-size: 1.2em;
            color: #800000;
        }
        .step p {
            font-size: 0.9em;
            color: #666;
        }

        /* Call to Action */
        .cta {
            background: #800000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .cta a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            background: #a52a2a;
            font-size: 1.2em;
            margin-top: 10px;
            display: inline-block;
        }
        .cta a:hover {
            background: #660000;
        }

        .center {
  margin-left: 180px;
  margin-right: auto;
}

.books-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .book-container {
            text-align: center;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
            font-size: 14px; 
        }
     

        .book-container:hover {
            transform: scale(1.05);
        }

        .book {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .book-title {
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
        }

        .book-author {
            font-size: 12px;
            color: #555;
        }

 .suggestions {
            background: #ffffff;
            padding: 10px;
            border-radius: 5px;
            position: absolute;
            z-index: 100;
            width: 200px;
            display: none;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .suggestions div {
            padding: 8px;
            cursor: pointer;
        }
        .suggestions div:hover {
            background: #ddd;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let suggestions = <?php echo json_encode($suggestionArray); ?>;
            let searchInput = document.getElementById("searchInput");
            let suggestionsBox = document.getElementById("suggestionsBox");
            let heroSection = document.getElementById("hero");
            let howitworksSection = document.getElementById("howitworks");
            let searchResults = document.getElementById("searchResults");

            // Hide hero section and show search results if search was performed
            <?php if ($showResults) { ?>
                heroSection.style.display = "none";
                howitworksSection.style.display = "none";
                searchResults.style.display = "block";
            <?php } ?>

            searchInput.addEventListener("input", function () {
                let query = searchInput.value.toLowerCase();
                suggestionsBox.innerHTML = "";
                suggestionsBox.style.display = "none";

                if (query.length > 0) {
                    let filtered = suggestions.filter(book => book.toLowerCase().includes(query));
                    if (filtered.length > 0) {
                        suggestionsBox.style.display = "block";
                        filtered.forEach(book => {
                            let div = document.createElement("div");
                            div.textContent = book;
                            div.addEventListener("click", function () {
                                searchInput.value = book;
                                suggestionsBox.style.display = "none";
                            });
                            suggestionsBox.appendChild(div);
                        });
                    }
                }
            });
        });
    </script>
</head>
<body>

    <!-- <div class="header">
        <img src="images/logo2.png" alt="Logo">
        <div class="search-bar">
            <form method="POST" action="">
                <input type="text" id="searchInput" name="search" placeholder="Search for books..." autocomplete="off">
                <button type="submit">Search</button>
            </form>
            <div class="suggestions" id="suggestionsBox"></div>
        </div>

        <div class="nav">
            <a href="index.php">Home</a>
            <a href="browse.php">Browse</a>
            <a href="about.php">About</a>
            <a href="join.php">Join</a>
            <a href="login.php">Log In</a>
        </div>
    </div> -->

    <nav class="navbar">
  <div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
  </div>
  <div class="search-bar">
            <form method="POST" action="">
                <input type="text" id="searchInput" name="search" placeholder="Search for books..." autocomplete="off">
                <button type="submit">Search</button>
            </form>
            <div class="suggestions" id="suggestionsBox"></div>
        </div>
  <ul class="nav-links">
      <li><a href="index.php"><i class='bx bx-home'></i> Home</a></li>
      <li><a href="browse.php"><i class='bx bx-search'></i> Browse</a></li>
      <li><a href="about.php"><i class='bx bx-info-circle'></i> About</a></li>
      <li><a href="join.php"><i class='bx bx-user-plus'></i> Join</a></li>
      <li><a href="login.php"><i class='bx bx-log-in'></i> Log In</a></li>
  </ul>
</nav>

    <!-- Hero Section (Hidden After Search) -->
    <div class="hero" id="hero">
        <img src="images/libro.png" alt="Libro Compartir Illustration">
        <h2>Exchange, Buy, or Sell Books.</h2>
        <p>Connect with fellow readers and explore endless possibilities.</p>
    </div>

    <!-- Search results will only be displayed after a search -->
    <div class="search-results" id="searchResults">
        <?php if ($showResults): ?>
            <h3>Search Results:</h3>
            <div class="books-wrapper">
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <?php $imageData = base64_encode($book['image']); ?>
                        <a href="book_details.php?id=<?= $book['id'] ?>" class="book-container">
                            <img src="data:image/jpeg;base64,<?= $imageData ?>" class="book" alt="Book">
                            <p class="book-title"><?= htmlspecialchars($book['title']) ?></p>
                            <p class="book-author">Posted by: <?= htmlspecialchars($book['first_name']) . ' ' . htmlspecialchars($book['last_name']) ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No books found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- How It Works Section -->
<div class="how-it-works" id="howitworks">
        <h3>How It Works</h3>
        <div class="steps">
            <div class="step">
          <h4>1. Sign up</h4>
            <p>Join us for free and create your profile.</p>
            </div>
            <div class="step">
                <h4>2. List Your Books</h4>
                <p>Type in the books you want to exchange, buy or sell.</p>
            </div>
            <div class="step">
                <h4>3. Browse Catalog</h4>
                <p>Browse the catalog to find books that interest you.</p>
            </div>
            <div class="step">
                <h4>4. Connect With  Others</h4>
                <p>Connect with other users to arrange swaps,buy or sell.</p>
            </div>
        </div>
    </div>

    <div class="cta">
        <p>Ready to start exchanging, buying or selling books?</p>
        <a href="join.php">Join Now (It's Free!)</a>
        <p>&copy; <?php echo date("Y"); ?> Libro Compartir. All Rights Reserved.</p>
    </div>

</body>
</html>
