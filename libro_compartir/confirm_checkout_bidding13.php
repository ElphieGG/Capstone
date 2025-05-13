<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('config.php');
?>

<?php
// session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    // Insert into orders table
    $query = "INSERT INTO orders (user_id, total_price, order_date, payment_method) VALUES (?, 0, NOW(), 'Cash on Delivery')";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert into order_details table
    $query2 = "INSERT INTO order_details (order_id, product_id, status) VALUES (?, ?, 'pending')";
    $stmt2 = $conn->prepare($query2);

    if (!$stmt2) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt2->bind_param("ii", $order_id, $book_id);
    $stmt2->execute();
    
    // Success SweetAlert
    echo '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        title: "Checkout Successful!",
        text: "Thank you for proceeding. You will be redirected shortly.",
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = "userfyp.php";
    });
    </script>
    ';

    $stmt->close();
    $stmt2->close();
    $conn->close();
} else {
    echo '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        title: "Error!",
        text: "Invalid request.",
        icon: "error",
        confirmButtonText: "Back"
    }).then(() => {
        window.location.href = "notifications.php";
    });
    </script>
    ';
}
?>
