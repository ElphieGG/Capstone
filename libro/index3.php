<?php
include 'config.php';

if (isset($_GET['search'])) {
    $searchTerm = "%" . $_GET['search'] . "%";
    $stmt = $conn->prepare("SELECT tblbook.id, tblbook.title, tbluser.first_name, tbluser.last_name, tblbook.image 
                            FROM tblbook 
                            JOIN tbluser ON tblbook.user_id = tbluser.user_id 
                            WHERE tblbook.title LIKE ?");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result(); // ✅ Correct result set
} else {
    $result = $conn->query("SELECT tblbook.id, tblbook.title, tbluser.first_name, tbluser.last_name, tblbook.image 
                            FROM tblbook 
                            JOIN tbluser ON tblbook.user_id = tbluser.user_id"); // ✅ Fetch all books by default
}

// Fetch book titles for live search suggestions
$suggestions = [];
$suggestionQuery = $conn->query("SELECT title FROM tblbook LIMIT 10");
while ($row = $suggestionQuery->fetch_assoc()) {
    $suggestions[] = $row['title'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Compartir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .hero {
            background: linear-gradient(45deg, #800000, #ff5e00);
            color: white;
            text-align: center;
            padding: 60px 20px;
        }
        .search-container {
            position: relative;
            max-width: 500px;
            margin: auto;
        }
        .suggestions {
            position: absolute;
            width: 100%;
            background: white;
            border: 1px solid #ccc;
            display: none;
            z-index: 1000;
        }
        .suggestions div {
            padding: 10px;
            cursor: pointer;
        }
        .suggestions div:hover {
            background: #f0f0f0;
        }
        .book-card {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .book-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero">
    <h1>Welcome to Libro Compartir</h1>
    <p>Find and share your favorite books</p>
</div>

<!-- Search Section -->
<div class="container mt-4">
    <div class="search-container">
        <input type="text" id="searchInput" class="form-control" placeholder="Search books by title...">
        <div class="suggestions" id="suggestionsBox"></div>
    </div>
</div>

<!-- Book Display Section -->
<div class="container mt-4">
    <div class="row">
        <?php while ($book = $result->fetch_assoc()) : ?>
            <div class="col-md-4">
                <div class="card book-card mb-4">
                    <img src="uploads/<?php echo htmlspecialchars($book['image']); ?>" class="card-img-top" alt="Book Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text"><small>By <?php echo htmlspecialchars($book['first_name'] . " " . $book['last_name']); ?></small></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        let suggestions = <?php echo json_encode($suggestions); ?>;
        
        $("#searchInput").on("keyup", function () {
            let input = $(this).val().toLowerCase();
            let matches = suggestions.filter(title => title.toLowerCase().includes(input));

            if (input.length === 0 || matches.length === 0) {
                $("#suggestionsBox").hide();
                return;
            }

            let suggestionHTML = matches.map(title => `<div onclick="selectSuggestion('${title}')">${title}</div>`).join("");
            $("#suggestionsBox").html(suggestionHTML).show();
        });

        $(document).click(function (e) {
            if (!$(e.target).closest('.search-container').length) {
                $("#suggestionsBox").hide();
            }
        });
    });

    function selectSuggestion(title) {
        $("#searchInput").val(title);
        $("#suggestionsBox").hide();
        window.location.href = `index.php?search=${encodeURIComponent(title)}`;
    }
</script>

</body>
</html>
