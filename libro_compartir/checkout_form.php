<?php
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Example user ID
}

// Calculate total price from cart session
$total_price = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Page</title>

    <!-- Import Boxicons and Font Awesome -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f8;
            padding: 0;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(to right, #f52222, #e60000);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 40px;
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

        /* Checkout Form Styles */
        .checkout-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 400px;
            margin: 40px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background: #38ef7d;
            color: white;
            padding: 12px;
            width: 100%;
            font-size: 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #2ecc71;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="brand-logo">
        <i class="fas fa-book-open"></i>
        <span>LibroCompartir</span>
    </div>
    <ul class="nav-links">
        <li><a href="userfyp.php"><i class='bx bx-home'></i> Home</a></li>
        <li><a href="chat.php"><i class='bx bx-chat'></i> Chat</a></li>
        <li><a href="user.php"><i class='bx bx-user'></i> Profile</a></li>
        <!-- <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li> -->
        <li><a href="cart.php"><i class='bx bx-cart'></i> Cart</a></li>
        <li><a href="login.php"><i class='bx bx-log-out'></i> Sign Out</a></li>
    </ul>
</nav>

<!-- Checkout Form -->
<div class="checkout-form">
    <h2>Checkout</h2>
    <form method="POST" action="checkout.php">

        <label for="total_price">Total Price (SAR)</label>
        <input type="text" id="total_price" name="total_price_display" value="<?= number_format($total_price, 2); ?>" readonly>

        <label for="payment_method">Select Payment Method</label>
        <select id="payment_method" name="payment_method" required>
            <option value="">-- Please Choose --</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Online Payment">Online Payment</option>
        </select>

        <button type="submit" name="submit">Place Order</button>
    </form>
</div>

</body>
</html>
