<?php
session_start();
include 'config.php';

// Get the seller ID (ensure it is set)
$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id) {
    echo json_encode(["error" => "Seller ID not found."]);
    exit;
}

// Fetch sales with reviews
$query = "
    SELECT s.*, r.book_rating, r.book_review, r.seller_rating, r.seller_review
    FROM sales s
    LEFT JOIN tblreviews r ON s.book_id = r.id
    WHERE s.seller_id = :seller_id
    ORDER BY s.created_at DESC";

$statement = $pdo->prepare($query);
$statement->execute(['seller_id' => $seller_id]);
$sales = $statement->fetchAll(PDO::FETCH_ASSOC);

// Output as JSON (for debugging)
echo json_encode($sales);
?>
