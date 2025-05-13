<?php
session_start();
include('config.php');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'], $data['total_price'], $data['payment_method'], $data['ship_rate'], $data['order_details'])) {
    echo json_encode(["success" => false, "message" => "Invalid request data"]);
    exit();
}

$user_id = $data['user_id'];
$total_price = $data['total_price'];
$payment_method = $data['payment_method'];
$ship_rate = $data['ship_rate'];
$order_details = $data['order_details'];

// Generate order ID for PayPal
$order_id = "2024" . rand(10000, 99999);

// Insert Order into Database
$query = "INSERT INTO orders (user_id, total_price, payment_method, ship_rate, order_date, orders_id) VALUES (?, ?, ?, ?, NOW(), ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("idsds", $user_id, $total_price, $payment_method, $ship_rate, $order_id);
$stmt->execute();
$order_db_id = $stmt->insert_id; // Get database order ID
$stmt->close();

// Insert Order Details
$query_details = "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query_details);
foreach ($order_details as $product_id => $item) {
    $stmt->bind_param("iisid", $order_db_id, $product_id, $item['name'], $item['quantity'], $item['price']);
    $stmt->execute();
}
$stmt->close();

// Clear session cart
unset($_SESSION['cart']);

echo json_encode(["success" => true, "order_id" => $order_id]);
?>
