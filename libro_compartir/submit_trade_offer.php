<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offered_by_user_id = $_SESSION['user_id'];
    $offered_book_id = $_POST['offered_book_id'];
    $requested_user_id = $_POST['requested_user_id'];
    $requested_book_id = $_POST['requested_book_id'];

    $message = "";
    $success = true;

    $stmt = $conn->prepare("INSERT INTO tblbooktrades (offered_by_user_id, offered_book_id, requested_user_id, requested_book_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $offered_by_user_id, $offered_book_id, $requested_user_id, $requested_book_id);

    if ($stmt->execute()) {
        $message = "Trade offer sent!";
    } else {
        $success = false;
        $message = "Error sending trade offer.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Processing...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '<?= $success ? "success" : "error" ?>',
        title: '<?= $message ?>',
        showConfirmButton: false,
        timer: 2000
    }).then(function() {
        window.location.href = 'view_trades.php';
    });
});
</script>

</body>
</html>
