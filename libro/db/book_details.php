<?php
// Your PHP code here (database connection, query, etc.)
include('config.php');
session_start();
// Ensure the user is logged in
//if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
  //  echo "You need to log in to bid.";
    //exit;
//}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch book details and user who posted the book
    $stmt = $conn->prepare(
        "SELECT tblbook.title, tblbook.description, tblbook.category, tblbook.book_condition, tblbook.image, 
                tblbook.book_status, tblbook.bidding_start_price, tblbook.meeting_spot, tblbook.bidding_end_time,
                tbluser.first_name, tbluser.last_name, 
                (SELECT MAX(bid_amount) FROM bids WHERE book_id = tblbook.id) AS current_bid
         FROM tblbook 
         JOIN tbluser ON tblbook.user_id = tbluser.user_id 
         WHERE tblbook.id = ?"
    );
    $stmt->bind_param("i", $book_id);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "Book not found.";
        exit;
    }

    // Get current time and bidding end time
    $current_time = new DateTime();
    $bidding_end_time = new DateTime($book['bidding_end_time']);
    $remaining_time = $bidding_end_time->diff($current_time);
    
    // Check if the bidding has ended
    $is_bidding_closed = ($current_time > $bidding_end_time);

    // Format remaining time (hours:minutes:seconds)
    $remaining_time_str = $remaining_time->format('%H:%I:%S');

    if ($is_bidding_closed) {
        // Call the bidding expiry logic here
        include('check_bidding_end.php'); // This would execute the logic to send the email
    }
    
} else {
    echo "No book selected.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">   
    <title>Libro Compartir | Book Details</title>
       
<link rel="stylesheet" href="viewpoststyle.css">
    <style>
            /* General styles */
            body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
            background-color: #f7f8fc;
        }

        /* Header styles */
        header {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
            text-align: center;
            padding: 2rem 0;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        /* Main content styles */
        main {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Section styles */
        section {
            margin-bottom: 2rem;
        }

        section h2, section h3 {
            color: #2575fc;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        section p {
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: justify;
        }

        section ul, section ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        section ul li, section ol li {
            margin-bottom: 0.5rem;
        }

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

        /* Footer styles */
        footer {
            text-align: center;
            background: #6a11cb;
            color: #fff;
            padding: 1rem 0;
            margin-top: 2rem;
            border-top: 4px solid #2575fc;
        }

        footer p {
            margin: 0;
        }

        /* Button styles */
        button, a.button {
            display: inline-block;
            background: #2575fc;
            color: #fff;
            padding: 0.8rem 1.5rem;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 1rem;
            text-decoration: none;
        }

        button:hover, a.button:hover {
            background: #1a61c8;
            
        }

        /* Responsive design */
        @media (max-width: 768px) {
            main {
                padding: 1rem;
            }

            header h1 {
                font-size: 2rem;
            }

            section h2, section h3 {
                font-size: 1.5rem;
            }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f3e5e5;
            color: #333;
        }

        /* Header */
        .header {
            background: #800000; /* Maroon */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: bold;
        }
        .nav {
            display: flex;
            gap: 15px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            background: #a52a2a; /* Softer Maroon */
            font-size: 0.9em;
        }
        .nav a:hover {
            background: #660000; /* Darker Maroon */
        }

        /* Search Bar */
        .search-bar input[type="text"] {
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            width: 200px;
        }
        .search-bar button {
            padding: 8px 15px;
            border: none;
            background: linear-gradient(to right, #f52222, #e60000);
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background: #660000;
        }

        /* Hero Section */
        .hero {
            /*background: #ffffff;    */
            background: rgb(243, 233, 233);
            padding: 10px 10px;
            
        }
      
        .hero h1{
            color: #800000; /* Maroon */
           
            text-align: center;
        }
        .hero h2 {
            font-size: 2.2em;
            margin: 0;
           color: #800000; /* Maroon */
           
        }
        .hero h3 {
            font-size: 1.8em;
           color: #800000;
            margin-bottom: 20px;
           
        }
        .hero p {
            font-size: 1.2em;
            color: #800000;
        }

        /* How It Works Section */
        .how-it-works {
           
          /*  background: #d7bcbc; /* Light Maroon Tint */
            background: rgb(243, 233, 233);
            padding: 30px 20px;
            text-align: center;
        }
        .how-it-works h3 {
            font-size: 1.8em;
            color: #800000;
            margin-bottom: 20px;
        }
        .steps {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .step {
           /* background: #f3e5e5; /* Light Maroon Background */
           background: #ffffff;   
            padding: 20px;
            border-radius: 8px;
            width: 220px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .step h4 {
            font-size: 1.2em;
            color: #800000;
        }
        .step p {
            font-size: 0.9em;
            color: #666;
        }

        /* Call to Action */
        .cta {
            background: #800000;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .cta a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            background: #a52a2a;
            font-size: 1.2em;
            margin-top: 10px;
            display: inline-block;
        }
        .cta a:hover {
            background: #660000;
        }

        .center {
  margin-left: 180px;
  margin-right: auto;
}

    </style>
</head>
<body>
    <!-- Header -->
 
    <nav class="navbar">
  <div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
  </div>
  <div class="search-bar">
            <input type="text" placeholder="Search for books...">
            <button>Search</button>
        </div>
  <ul class="nav-links">
      <li><a href="index.php"><i class='bx bx-home'></i> Home</a></li>
      <li><a href="browse.php"><i class='bx bx-search'></i> Browse</a></li>
      <li><a href="about.php"><i class='bx bx-info-circle'></i> About</a></li>
      <li><a href="join.php"><i class='bx bx-user-plus'></i> Join</a></li>
      <li><a href="login.php"><i class='bx bx-log-in'></i> Log In</a></li>
  </ul>
</nav>
   
    <!-- Hero Section -->
    <div class="hero">     
     <main>   

     <div class="post-container">
        <div class="left-div">
            <?php
            // Output the image as base64
            $imageData = base64_encode($book['image']);
            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Book Cover" class="book-cover">';
            ?>
        </div>

        <div class="right-div">
            <h2 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h2>
            <p>Posted by: <strong><?php echo htmlspecialchars($book['first_name'] . ' ' . $book['last_name']); ?></strong></p>
            
            <div class="description-container">
                <p><strong>Description:</strong>
               <!--   
  <p class="description"> --> 
                <?php echo htmlspecialchars($book['description']); ?></p>
            </div>
            <div class="category-container">
                <p><strong>Category:</strong>
                <!--  <p class="category">-->
                    <?php echo htmlspecialchars($book['category']); ?></p>
            </div>
            <div class="condition-container">
            <p><strong>Condition:</strong>
              <!--    <p class="condition">--> 
                <?php echo htmlspecialchars($book['book_condition']); ?></p>
            </div>
            <div class="condition-container">
            <p><strong>Status:</strong>
              <!--    <p class="condition">--> 
                <?php echo htmlspecialchars($book['book_status']); ?></p>
            </div>
           
        </div>
    </div>

    <script>
    var currentBid = <?php echo $book['current_bid'] ?? 0; ?>;

    // Handle the form submission with AJAX
    document.getElementById("bid-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission

        var bidAmount = parseFloat(document.getElementById("bid-amount").value);

        // Validate the bid amount is greater than the current highest bid
        if (bidAmount <= currentBid) {
            alert("Your bid must be higher than the current highest bid of " + currentBid + " PHP.");
            return;
        }

        // Create a FormData object to send the data
        var formData = new FormData();
        formData.append("bid_amount", bidAmount);

        // Send the request via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "place_bid.php?id=<?php echo $book_id; ?>", true);

        // Handle the response
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);

                // Check if the response indicates success
                if (response.status === 'success') {
                    // Show the success modal
                    var modal = document.getElementById("success-modal");
                    var closeModal = document.getElementsByClassName("close")[0];

                    // Display the modal with a success message
                    document.getElementById("success-message").innerHTML = response.message;
                    modal.style.display = "block";

                    // Close the modal when the user clicks on the "x" button
                    closeModal.onclick = function() {
                        modal.style.display = "none";
                    }

                    // Close the modal when the user clicks anywhere outside of the modal
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                } else {
                    alert(response.message); // Show the error message
                }
            }
        };

        xhr.send(formData); // Send the FormData to place the bid
    });
</script>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
      
    </div>

    </main> 

    <!-- Call to Action -->
    <div class="cta">
        <p>Ready to start exchanging, buying or selling books?</p>
        <a href="join.php">Join Now (It's Free!)</a>
        <p>&copy; <?php echo date("Y"); ?> Libro Compartir. All Rights Reserved.</p>
    </div>
</body>
</html>
