
<?php
session_start();
include 'config.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get user data based on session username
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, image FROM tbluser WHERE username = ?");
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

// Pagination setup
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total books
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM tblbook WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_books = $count_result->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = ceil($total_books / $limit);

// Fetch books for current page
$books_stmt = $conn->prepare("SELECT id, title, image FROM tblbook WHERE user_id = ? LIMIT ? OFFSET ?");
$books_stmt->bind_param("iii", $user_id, $limit, $offset);
$books_stmt->execute();
$books_result = $books_stmt->get_result();

while ($book = $books_result->fetch_assoc()) {
    echo '<div style="margin-bottom: 20px;">';
    echo '<img src="uploads/' . $book['image'] . '" alt="' . $book['title'] . '" style="width:100px;"><br>';
    echo '<strong>' . $book['title'] . '</strong>';
    echo '</div>';
}

// Pagination links
echo '<div style="text-align: center; margin-top: 20px;">';
if ($page > 1) {
    echo '<a href="?page=' . ($page - 1) . '">Previous</a> ';
}
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo '<strong>' . $i . '</strong> ';
    } else {
        echo '<a href="?page=' . $i . '">' . $i . '</a> ';
    }
}
if ($page < $total_pages) {
    echo '<a href="?page=' . ($page + 1) . '">Next</a>';
}
echo '</div>';

$books_stmt->close();
?>
