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
    <title>Responsive Book Store</title>
    <link rel="stylesheet" href="styles.css">
    <script defer src="script.js"></script>
    <style>
/* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f4f4f4;
    text-align: center;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #333;
    color: white;
    padding: 15px 20px;
}

/* Logo */
.logo {
    font-size: 1.5rem;
    font-weight: bold;
}

/* Navigation */
.nav {
    display: flex;
    gap: 20px;
}

.nav a {
    color: white;
    text-decoration: none;
    font-size: 1rem;
}

.nav a:hover {
    text-decoration: underline;
}

/* Hamburger Menu (Hidden by Default) */
.hamburger {
    display: none;
    background: none;
    border: none;
    font-size: 1.8rem;
    color: white;
    cursor: pointer;
}

/* Search Bar */
.search-bar {
    margin: 20px;
}

.search-bar input {
    width: 50%;
    padding: 10px;
    font-size: 1rem;
}

.search-bar button {
    padding: 10px 15px;
    font-size: 1rem;
    cursor: pointer;
}

/* Books Grid */
.books-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 20px;
}

.book-container {
    background: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
}

.book {
    width: 120px;
    height: 180px;
    background: gray;
    margin: auto;
    border-radius: 5px;
}

.book-container p {
    margin-top: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    /* Stack header items */
    .header {
        flex-direction: column;
        text-align: center;
    }

    .nav {
        display: none; /* Hide menu initially */
        flex-direction: column;
        width: 100%;
        text-align: center;
        padding: 10px 0;
    }

    .nav a {
        display: block;
        padding: 10px;
        background: #444;
        color: white;
        border-bottom: 1px solid #555;
    }

    .hamburger {
        display: block;
    }

    /* Show menu when active */
    .nav.active {
        display: flex;
    }

    /* Adjust book grid */
    .books-wrapper {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }

    .search-bar input {
        width: 80%;
    }
}

    </style>
</head>
<body>

    <header class="header">
        <div class="logo">📚 Book Store</div>
        <button class="hamburger" aria-label="Toggle Menu">&#9776;</button>
        <nav class="nav">
            <a href="#">Home</a>
            <a href="#">Categories</a>
            <a href="#">Best Sellers</a>
            <a href="#">Contact</a>
        </nav>
    </header>

    <section class="search-bar">
        <input type="text" placeholder="Search for books...">
        <button>🔍 Search</button>
    </section>

    <section class="books-wrapper">
        <div class="book-container">
            <div class="book"></div>
            <p>Book Title 1</p>
        </div>
        <div class="book-container">
            <div class="book"></div>
            <p>Book Title 2</p>
        </div>
        <div class="book-container">
            <div class="book"></div>
            <p>Book Title 3</p>
        </div>
        <div class="book-container">
            <div class="book"></div>
            <p>Book Title 4</p>
        </div>
    </section>

</body>
</html>

