<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Cancel Order request
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);

    // Update the order status to 'Cancelled'
    mysqli_query($conn, "UPDATE orders SET payment_status = 'Cancelled' WHERE orders_id = '$cancel_id' AND user_id = '$user_id' AND payment_status = 'Pending'");

    // Redirect to refresh the page
    header('Location: orders.php');
    exit();
}

// Fetch orders
$result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
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

        /* Orders Table Styles */
        h2 {
            text-align: center;
            margin-top: 30px;
            font-size: 28px;
            color: #333;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #38ef7d;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
        .paid { background-color: #28a745; color: white; }
        .pending { background-color: #ffc107; color: black; }
        .failed, .cancelled { background-color: #dc3545; color: white; }

        a.cancel-btn {
            background: #dc3545;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            margin-left: 10px;
        }

        a.cancel-btn:hover {
            background: #c82333;
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

<!-- Orders Table -->
<h2>My Orders</h2>

<table>
    <tr>
        <th>Order ID</th>
        <th>Total Price</th>
        <th>Payment Method</th>
        <th>Payment Status</th>
        <th>Order Date</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?= $row['orders_id']; ?></td>
        <td><?= number_format($row['total_price'], 2); ?> SAR</td>
        <td><?= htmlspecialchars($row['payment_method']); ?></td>
        <td>
            <?php if ($row['payment_status'] == 'Paid') { ?>
                <span class="badge paid">Paid</span>
            <?php } elseif ($row['payment_status'] == 'Pending') { ?>
                <span class="badge pending">Pending</span>
            <?php } elseif ($row['payment_status'] == 'Cancelled') { ?>
                <span class="badge cancelled">Cancelled</span>
            <?php } else { ?>
                <span class="badge failed"><?= htmlspecialchars($row['payment_status']); ?></span>
            <?php } ?>
        </td>
        <td><?= date('Y-m-d H:i', strtotime($row['order_date'])); ?></td>
        <td>
            <?php if ($row['payment_status'] == 'Pending') { ?>
                <a href="orders.php?cancel_id=<?= $row['orders_id']; ?>" class="cancel-btn" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel</a>
            <?php } else { ?>
                -
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
