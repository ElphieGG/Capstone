<?php
session_start();
include('config.php');

// Check if pending order exists
if (!isset($_SESSION['pending_order'])) {
    header('Location: checkout.php');
    exit();
}

// Fake payment processing delay
sleep(2); // wait 2 seconds to simulate payment

$order = $_SESSION['pending_order'];
$user_id = $order['user_id'];
$total_price = $order['total_price'];
$payment_method = $order['payment_method'];
$ship_rate = $order['ship_rate'];

// Insert order with 'Paid' status
$insert_order = "INSERT INTO orders (user_id, total_price, payment_method, ship_rate, payment_status, order_date)
    VALUES ('$user_id', '$total_price', '$payment_method', '$ship_rate', 'Paid', NOW())";

if (mysqli_query($conn, $insert_order)) {
    unset($_SESSION['pending_order']);
    $payment_status = "success";
} else {
    $payment_status = "failed";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
    <style>
        body {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            color: white;
            text-align: center;
            overflow: hidden;
        }
        .checkmark {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #fff;
            stroke-miterlimit: 10;
            margin: 10px auto;
            box-shadow: inset 0px 0px 0px #fff;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #fff;
            fill: none;
            animation: stroke .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke .3s cubic-bezier(0.65, 0, 0.45, 1) .6s forwards;
        }
        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
        @keyframes scale {
            0%, 100% {
                transform: none;
            }
            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }
        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #fff;
            }
        }
        h1 {
            font-size: 2.5em;
            margin-top: 20px;
        }
        p {
            font-size: 1.2em;
            margin-top: 10px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<?php if ($payment_status == "success"): ?>
    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
      <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
      <path class="checkmark__check" fill="none" d="M14 27l7 7 16-16"/>
    </svg>
    <h1>Payment Successful!</h1>
    <p>Thank you for your purchase. Redirecting to your orders...</p>
    <script>
        setTimeout(function(){
            window.location.href = 'orders.php';
        }, 4000); // Redirect after 4 seconds
    </script>
<?php else: ?>
    <h1>Payment Failed!</h1>
    <p>Something went wrong. Redirecting back...</p>
    <script>
        setTimeout(function(){
            window.location.href = 'checkout.php';
        }, 3000); // Redirect after 3 seconds
    </script>
<?php endif; ?>

</body>
</html>
