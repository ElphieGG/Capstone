<?php
include('config.php');
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You must be logged in to make a trade request!');
            window.location.href = 'login.php';
          </script>";
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);
    $user_id = $_SESSION['user_id'];

    // Check if user has at least one book listed
    $check_books_sql = "SELECT COUNT(*) AS book_count FROM tblbook WHERE user_id = ?";
    $stmt = $conn->prepare($check_books_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_books = $result->fetch_assoc();
    $stmt->close();

    // Load SweetAlert2
    echo "<html><head>
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>";

    if ($user_books['book_count'] == 0) {
        // User has no books posted
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'No Books Listed',
                    text: 'You must post at least one book before making a trade request.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'user.php';
                });
              </script>";
        exit();
    }

    // Check if a trade request already exists
    $check_sql = "SELECT COUNT(*) AS count FROM trade_requests 
                  WHERE user_id_from = ? AND book_id_to = ? AND status = 'pending'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['count'] > 0) {
        // Show alert for duplicate trade request
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Trade Request Already Sent',
                    text: 'You can only send a trade request for this book once.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.history.back();
                });
              </script>";
    } else {
        // Insert new trade request
        $insert_sql = "INSERT INTO trade_requests (user_id_from, book_id_from, user_id_to, book_id_to, status) 
                       VALUES (?, ?, (SELECT user_id FROM tblbook WHERE id = ?), ?, 'pending')";
        
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiii", $user_id, $book_id, $book_id, $book_id);

        if ($stmt->execute()) {
            // Show success alert
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Trade Request Sent!',
                        text: 'Your trade request has been successfully sent.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.history.back();
                    });
                  </script>";
        } else {
            // Show error alert
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please try again.',
                    });
                  </script>";
        }

        $stmt->close();
    }

    echo "</body></html>"; // Ensures HTML structure is complete
}
?>
