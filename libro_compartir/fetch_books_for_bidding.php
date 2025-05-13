<?php
include 'config.php'; // Ensure this connects to your database

if (isset($_POST['query'])) {
    $search = $conn->real_escape_string($_POST['query']);

    $sql = "SELECT id, title FROM tblbook WHERE title LIKE '%$search%' AND book_status = 'For Bidding' LIMIT 5";
    $result = $conn->query($sql);

    $suggestions = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = ["id" => $row['id'], "title" => $row['title']];
        }
    }

    echo json_encode($suggestions);
}
?>
