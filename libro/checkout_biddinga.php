<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    Swal.fire({
        title: 'Invalid Access',
        text: 'Please checkout properly through the system.',
        icon: 'warning',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'userfyp.php';
        }
    });
    </script>";
    exit();
}

if (isset($_POST['confirm_checkout'])) {
    $book_id = intval($_POST['book_id']);
    $title = $_POST['title'];
    $price = floatval($_POST['price']);
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    $conn->begin_transaction();

    try {
        $insertOrder = "INSERT INTO orders (user_id, total_price, payment_method, ship_rate, order_date) 
                        VALUES (?, ?, ?, 0, NOW())";
        $stmtOrder = $conn->prepare($insertOrder);
        $stmtOrder->bind_param("ids", $user_id, $price, $payment_method);
        $stmtOrder->execute();
        $order_id = $conn->insert_id;

        $insertDetail = "INSERT INTO order_details (order_id, product_id, product_name, quantity, price, status)
                         VALUES (?, ?, ?, 1, ?, 'Pending')";
        $stmtDetail = $conn->prepare($insertDetail);
        $stmtDetail->bind_param("iisd", $order_id, $book_id, $title, $price);
        $stmtDetail->execute();

        $conn->commit();

        // Output a full mini HTML page with SweetAlert
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Order Success</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                title: 'Order Successful!',
                text: 'Thank you for your payment!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'userfyp.php';
                }
            });
        </script>
        </body>
        </html>
        ";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
