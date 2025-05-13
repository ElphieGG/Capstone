<?php
include "config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro Compatir | Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="navbar.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
         /* Links */
         a {
            /*color: #2575fc;*/
            color:red;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
        body {
            background-color: #f8f9fa;
        }
        .main-container {
            display: flex;
            height: 100vh;
        }
        .dashboard {
            width: 35%;
            padding: 20px;
            overflow-y: auto;
            border-right: 2px solid #ddd;
            background: white;
        }
        .chat-container {
            width: 65%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: white;
        }
        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            background: #fff;
            border-radius: 5px;
        }
        .chat-input-container {
            display: flex;
            margin-top: 90px;
        }
        .chat-input {
            width: 80%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 100px;/* Reduce space between chat box and input */
            
            
        }
        .chat-send {
            width: 20%;
            padding: 8px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-left: 5px;
            margin-bottom: 100px; /* Reduce space between chat box and input */
        }
        .sent {
            text-align: right;
            background: #dcf8c6;
            padding: 5px;
            margin: 5px;
            border-radius: 8px;
            max-width: 70%;
            float: right;
            clear: both;
        }
        .received {
            text-align: left;
            background: #f1f0f0;
            padding: 5px;
            margin: 5px;
            border-radius: 8px;
            max-width: 70%;
            float: left;
            clear: both;
        }
        .selected-user {
            font-weight: bold;
            font-size: 1.2rem;
        }
        .navbar {
    background: linear-gradient(to right, #f52222, #e60000);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 40px;
    font-family: 'Segoe UI', sans-serif;
}

.logo {
    height: 30px;
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

       
    </style>
</head>
<body>
<nav class="navbar">
<div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
</div>
    <ul class="nav-links">
        <li><a href="userfyp.php"><i class='bx bx-home'></i> Home</a></li>
        <li><a href="chat.php"><i class='bx bx-chat'></i> Chat</a></li>
        <li><a href="user.php"><i class='bx bx-user'></i> Profile</a></li>
        <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li>
        <li><a href="cart.php"><i class='bx bx-cart'></i> Cart</a></li>
        <li><a href="login.php"><i class='bx bx-log-out'></i> Sign Out</a></li>
    </ul>
</nav>
<div class="main-container">
    <!-- Left Side - Users List -->
    <div class="dashboard">
        <h2 class="text-center mb-3">Welcome, <?php echo htmlspecialchars($username); ?> 👋</h2>
       

        <!-- Available Users for Chat -->
        <div class="card">
            <div class="card-header bg-info text-white">Available Users for Chat 💬</div>
            <div class="card-body">
                <ul class="list-group">
                    <?php
                    $result = $conn->query("SELECT * FROM tbluser WHERE user_id != $user_id");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                    <span>" . htmlspecialchars($row['username']) . "</span>
                                    <button class='btn btn-sm btn-secondary start-chat' data-id='" . $row['user_id'] . "' data-name='" . htmlspecialchars($row['username']) . "'>Chat</button>
                                  </li>";
                        }
                    } else {
                        echo "<p class='text-muted'>No users available to chat.</p>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Side - Chat Window -->
    <div class="chat-container">
        <h3 id="chat-header">Select a user to chat</h3>
        <div id="chat-box" class="chat-box"></div>
        <div class="chat-input-container">
            <input type="text" id="message" class="chat-input" placeholder="Type a message" disabled>
            <button id="send" class="chat-send" disabled>Send</button>
        </div>
    </div>
</div>

<script>
    let receiverId = null;

    // Fetch messages dynamically
    function fetchMessages() {
        if (!receiverId) return;
        $.get("fetch_messages.php", { receiver_id: receiverId }, function(data) {
            $("#chat-box").html(data);
            $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
        });
    }

    // Handle chat button click
    $(".start-chat").click(function() {
        receiverId = $(this).data("id");
        let receiverName = $(this).data("name");
        $("#chat-header").html("Chat with " + receiverName);
        $("#message").prop("disabled", false);
        $("#send").prop("disabled", false);
        fetchMessages();
    });

    // Send message
    $("#send").click(function() {
        let message = $("#message").val().trim();
        if (message !== "" && receiverId) {
            $.post("send_message.php", { receiver_id: receiverId, message: message }, function(response) {
                let res = JSON.parse(response);
                if (res.status === "success") {
                    $("#message").val("");
                    fetchMessages();
                } else {
                    alert("Error: " + res.message);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
            });
        }
    });

    setInterval(fetchMessages, 2000);
</script>

</body>
</html>
