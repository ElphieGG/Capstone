<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trade_id = $_POST['trade_id'];
    $response = $_POST['response'];

    $message = "";
    $success = true;

    $stmt = $conn->prepare("UPDATE tblbooktrades SET status = ?, decision_date = NOW() WHERE trade_id = ?");
    $stmt->bind_param("si", $response, $trade_id);

    if ($stmt->execute()) {
        if ($response === 'accepted') {
            // Swap ownership
            $select = $conn->prepare("SELECT offered_book_id, requested_book_id FROM tblbooktrades WHERE trade_id = ?");
            $select->bind_param("i", $trade_id);
            $select->execute();
            $select->bind_result($offered_book_id, $requested_book_id);
            $select->fetch();
            $select->close();

            $get_users = $conn->prepare("SELECT offered_by_user_id, requested_user_id FROM tblbooktrades WHERE trade_id = ?");
            $get_users->bind_param("i", $trade_id);
            $get_users->execute();
            $get_users->bind_result($offered_by_user_id, $requested_user_id);
            $get_users->fetch();
            $get_users->close();

            $update1 = $conn->prepare("UPDATE tblbook SET user_id = ? WHERE id = ?");
            $update1->bind_param("ii", $requested_user_id, $offered_book_id);
            $update1->execute();

            $update2 = $conn->prepare("UPDATE tblbook SET user_id = ? WHERE id = ?");
            $update2->bind_param("ii", $offered_by_user_id, $requested_book_id);
            $update2->execute();

            $message = "Trade accepted and ownership swapped!";
        } else {
            $message = "Trade declined.";
        }
    } else {
        $success = false;
        $message = "Failed to respond.";
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
