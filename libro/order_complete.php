<?php
session_start();
include('config.php');

if (!isset($_GET['orders_id'])) {
    echo "Order ID is missing.";
    exit();
}

$order_id = $_GET['orders_id'];

$order_query = "SELECT * FROM orders WHERE orders_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit();
}

$order = $order_result->fetch_assoc();

$order_details_query = "SELECT * FROM order_details WHERE order_id = ?";
$stmt = $conn->prepare($order_details_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_details_result = $stmt->get_result();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Complete</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin-top: 50px; /* Adjusted margin-top for more space */
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #2c3e50;
    text-align: center;
}

h2 {
    color: #34495e;
}

p {
    font-size: 16px;
    line-height: 1.6;
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 12px;
    text-align: left;
}

th {
    background-color: #2c3e50;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
}

.btn:hover {
    background-color: #2980b9;
}

.back-link {
    display: block;
    margin-top: 20px;
    text-align: center;
    font-size: 16px;
}

    </style>
</head>
<body>

<div class="container" style="margin-top: 10vh;">
    <h1>Order Complete</h1>

    <h2>Order Details</h2>
    <p><strong>Order ID:</strong> <?php echo $order['orders_id']; ?></p>
    <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
    <p><strong>Shipping Rate:</strong> ₱<?php echo number_format($order['ship_rate'], 2); ?></p>
    <p><strong>Total Price:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
    <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>

    <h3>Items in Your Order:</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $order_details_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td>₱<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn">Back to Homepage</a>
    <div class="back-link">
     
    </div>
</div>

</body>
</html>
