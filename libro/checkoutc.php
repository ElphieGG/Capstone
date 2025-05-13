<?php
session_start();
include('config.php');

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Calculate total price from cart
$total_price = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}

if (isset($_POST['submit'])) {
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    $payment_status = ($payment_method == 'Cash on Delivery') ? 'Pending' : 'Paid';

    // Step 1: Insert into orders
    $order_insert_query = "INSERT INTO orders (user_id, total_price, payment_method, order_date, payment_status) 
                           VALUES (?, ?, ?, NOW(), ?)";
    $stmt_order = $conn->prepare($order_insert_query);
    $stmt_order->bind_param("isss", $user_id, $total_price, $payment_method, $payment_status);

    if ($stmt_order->execute()) {
        $order_id = $stmt_order->insert_id; // Get the generated order_id

        // Step 2: Insert each cart item into order_details
        $order_details_query = "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) 
                                VALUES (?, ?, ?, ?, ?)";
        $stmt_details = $conn->prepare($order_details_query);

        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['product_id'];
            $product_name = $item['product_name'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $stmt_details->bind_param("iisid", 
                $order_id,
                $product_id,
                $product_name,
                $quantity,
                $price
            );

            if (!$stmt_details->execute()) {
                echo "Error inserting order details: " . $stmt_details->error;
            }
        }

        // Step 3: Clear cart
        unset($_SESSION['cart']);

        // Step 4: Success
        echo "<script>alert('Order placed successfully!'); window.location.href = 'orders.php';</script>";
    } else {
        echo "<script>alert('Error placing order.'); window.location.href = 'checkout_form.php';</script>";
    }
}
?>
