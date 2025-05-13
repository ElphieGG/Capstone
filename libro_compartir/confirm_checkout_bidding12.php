<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = intval($_POST['book_id']);
    $price = floatval($_POST['price']);
    $paymentMethod = $_POST['payment_method'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO orders (user_id, book_id, price, payment_method, order_date) VALUES (?, ?, ?, ?, NOW())");

    if ($stmt) {
        $stmt->bind_param("iids", $userId, $bookId, $price, $paymentMethod);
        if ($stmt->execute()) {
            $update = $conn->prepare("UPDATE tblbook SET book_status = 'Sold' WHERE id = ?");
            $update->bind_param("i", $bookId);
            $update->execute();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Checkout Successful!',
                    text: 'Thank you for your winning bid!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Go to My Profile'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='userfyp.php';
                    }
                });
            </script>";
        } else {
            echo "<script>alert('Failed to confirm checkout. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Invalid access.";
}
?>