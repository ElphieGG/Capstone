<?php
include("db_connect.php");

if (isset($_POST['query'])) {
    $search = $conn->real_escape_string($_POST['query']);
    $sql = "SELECT title FROM tblbook WHERE title LIKE '%$search%' LIMIT 5";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='suggestion-item'>{$row['title']}</div>";
        }
    } else {
        echo "<div class='suggestion-item'>No suggestions</div>";
    }
}
?>
