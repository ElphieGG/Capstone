<?php
// Your PHP code here (database connection, query, etc.)
session_start();
include 'config.php';
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <!-- <link rel="stylesheet" href="assets/css/styles.css">  Optional custom styles 
   -->
   <link rel ="stylesheet" href = "navbar.css">
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            background: #a52a2a;
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


.search-bar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    background-color: #f4f4f4;
    border-bottom: 1px solid rgb(126, 0, 0);
}

.search-bar-container h1 {
    color: rgb(126, 0, 0);
    margin: 0;
    flex: 1;
}

.search-bar {
    width: 400px;
    padding: 10px;
    border: 1px solid rgb(126, 0, 0);
    border-radius: 4px;
    font-size: 16px;
    margin-right: 10px;
}

.search-button {
    background-color: rgb(126, 0, 0);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.search-button:hover {
    opacity: 0.8;
}

.buttons-container {
    display: flex;
    gap: 10px; /* Adds spacing between buttons */
    margin-top: 15px;
}

/* Common button styles for consistency */
.review-btn, .add-to-cart-btn {
    padding: 12px 20px;  /* Ensures both buttons have the same size */
    border: none;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    color: white;
    border-radius: 5px;
    text-align: center;
    width: 150px; /* Set fixed width for both buttons */
    display: inline-block;
}

/* Specific button colors */
.review-btn {
    background-color: #007bff; /* Blue for reviews */
}

.add-to-cart-btn {
    background-color: #28a745; /* Green for add to cart */
}

/* Hover effects */
.review-btn:hover {
    background-color: #0056b3;
}

.add-to-cart-btn:hover {
    background-color: #218838;
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
    <ul class="nav-links">
        <li><a href="userfyp.php"><i class='bx bx-home'></i> Home</a></li>
        <li><a href="chat.php"><i class='bx bx-chat'></i> Chat</a></li>
        <li><a href="user.php"><i class='bx bx-user'></i> Profile</a></li>
        <li><a href="notifications.php"><i class='bx bx-bell'></i> Notification</a></li>
        <li><a href="cart.php"><i class='bx bx-cart'></i> Cart</a></li>
        <li><a href="login.php"><i class='bx bx-log-out'></i> Sign Out</a></li>
    </ul>
</nav>

    <div class="search-bar-container">
   <!---- <h1>Your Feed, <?php echo $_SESSION['username']; ?></h1> ---->
    <h1>Your Feed, <?php echo ucwords(strtolower($_SESSION['username'])); ?></h1>
         <!----  <input type="text" class="search-bar" placeholder="Search...">---->
         <div style="position: relative; width: 300px;">
    <input type="text" id="search" class="search-bar"placeholder="Search for a book..." autocomplete="off" style="width: 100%;">
    <div id="suggestions"style="width: 300px"; ></div>
   

</div>
        <button class="search-button">Search</button>
    </div>

    <div class="side-button-container" style="position: absolute; right: 20px; top: 200px;">
        <button class="search-button"><a href="userfyp.php" style="color: white; text-decoration: none;">Back</a></button>
    </div> 
   
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
               <!--  <p class="description"> --> 
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
            <?php if ($book['book_status'] === "For Sale"): ?>
            <div class="condition-container">
            <p><strong>Price:</strong>
              <!--    <p class="condition">--> 
                <?php echo htmlspecialchars($book['bidding_start_price']); ?></p>
            </div>
            <?php endif; ?>
              <!-- Buttons Container -->
       <!--   <div class="buttons-container">
            Reviews Button 
            <a href="book_reviews.php?book_id=<?php echo $book_id; ?>" class="review-btn">Reviews</a>-->

            <!-- Show "Add to Cart" button only if book is for sale 
            <?php if ($book['book_status'] === "For Sale"): ?>
            <form action="cart.php" method="post">
                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
            </form>
        <?php endif; ?>-->

 <!-- Show "Offer Trade" button if book is for sale 
 <?php if ($book['book_status'] === "For Exchange"): ?>
            <form action="request_trade.php" method="post">
                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                <button type="submit" class="add-to-cart-btn">Offer Trade</button>
            </form>
        <?php endif; ?>-->

        </div>

              
        </div>
           
        </div>
    </div>

  <!--      <script>
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
      
    </div>--> 

    </main> 

    <script>
        // for shipping
        document.addEventListener("DOMContentLoaded", function () {
          document
            .querySelector('a[href="#shipping-policy"]')
            .addEventListener("click", function (event) {
              event.preventDefault();
              document.querySelector("#shipping-policy").scrollIntoView({
                behavior: "smooth",
              });
            });
        });
        // for the filter
        document.addEventListener("DOMContentLoaded", function () {
          const productTypeFilter = document.getElementById(
            "product-type-filter"
          );
          const priceFilter = document.getElementById("price-filter");
          const sortBy = document.getElementById("sort-by");
          const productList = document.querySelector(".product-list");

          // Function to filter products based on selected criteria
          function filterProducts() {
            const productType = productTypeFilter.value;
            const priceRange = parseFloat(priceFilter.value);

            document.querySelectorAll(".product-item").forEach(function (item) {
              const type = item.getAttribute("data-type");
              const price = parseFloat(item.getAttribute("data-price"));

              if (
                (productType === "all" || productType === type) &&
                (isNaN(priceRange) || price <= priceRange)
              ) {
                item.style.display = "block";
              } else {
                item.style.display = "none";
              }
            });
          }

          // Function to sort products based on selected criteria
          function sortProducts() {
            const sortedItems = Array.from(
              document.querySelectorAll(".product-item")
            ).sort(function (a, b) {
              const nameA = a.getAttribute("data-name").toUpperCase();
              const nameB = b.getAttribute("data-name").toUpperCase();

              if (sortBy.value === "name-asc") {
                return nameA.localeCompare(nameB);
              } else if (sortBy.value === "name-desc") {
                return nameB.localeCompare(nameA);
              }
            });

            productList.innerHTML = "";
            sortedItems.forEach(function (item) {
              productList.appendChild(item);
            });
          }

          // Event listeners for filter and sort changes
          productTypeFilter.addEventListener("change", filterProducts);
          priceFilter.addEventListener("input", filterProducts);
          sortBy.addEventListener("change", sortProducts);

          // Initial filtering and sorting
          filterProducts();
          sortProducts();
        });
        
          //TODO: for product details

      function showDetails(productName) {
      document.getElementById("productName").innerText = productName;
      document.getElementById("productDetailsModal").style.display = "block";
    }

    function hideDetails() {
      document.getElementById("productDetailsModal").style.display = "none";
    }

      function addToCart() {
        // Add logic to add product to cart
        alert("Product added to cart!");
      }

      function buyNow() {
        // Add logic to proceed to checkout
        alert("Redirecting to checkout...");
      }
        
      </script>
</body>

</html>
