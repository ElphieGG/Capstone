<?php
session_start();
include('config.php'); // Database connection

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
  // Load SweetAlert2
  echo "<html><head>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  </head><body>";

// Handle adding a book to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0; // Get buy_out_price if set

    // Fetch book details from the database
    $query = "SELECT id, title, image, bidding_start_price, buy_out_price FROM tblbook WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();

            // Use buy_out_price if available, otherwise fallback to bidding_start_price
            $final_price = ($price > 0) ? $price : $book['bidding_start_price'];

            // Check if the book is already in the cart
            if (!isset($_SESSION['cart'][$book_id])) {
                $_SESSION['cart'][$book_id] = [
                    'name' => $book['title'],
                    'image' => $book['image'],
                    'price' => $final_price, // Set the correct price
                    'quantity' => 1
                ];
            } else {
                echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Book already in cart!',
                    text: 'Choose another book or checkout.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='cart.php';  
                });
                </script>";
                exit();
            }

            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Book added to cart!',
                text: 'Your book has been successfully added.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href='cart.php';  
            });
            </script>";
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Book not found!',
                text: 'No books found',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href='books_for_sale.php';
            });
            </script>";
        }

        $stmt->close();
    } else {
        die("Query preparation failed: " . $conn->error);
    }
}


// Handle removing a book from the cart
if (isset($_GET['remove'])) {
    $book_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$book_id]);
    header("Location: cart.php");
    exit();
}

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    foreach ($_POST['quantities'] as $book_id => $quantity) {
        $book_id = intval($book_id);
        $quantity = intval($quantity);

        if ($quantity > 0) {
            $_SESSION['cart'][$book_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$book_id]); // Remove if quantity is zero
        }
    }
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserForyouPage</title>
    <link rel="stylesheet" href="cart1.css"> 
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    line-height: 1.6;
    margin: 0;
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
    background: linear-gradient(to right, #f52222, #e60000);
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
           /*  background: #1a61c8;*/
           background:rgb(246, 72, 72);
            
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

.books-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .book-container {
            text-align: center;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            text-decoration: none;
            color: inherit;
            font-size: 14px; 
        }
     

        .book-container:hover {
            transform: scale(1.05);
        }

        .book {
            width: 100px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .book-title {
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
        }

        .book-author {
            font-size: 12px;
            color: #555;
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
    
    <!-- <div class="search-bar-container">
        <h1>Books to buy</h1>
        <input type="text" class="search-bar" placeholder="Search...">
        <button class="search-button">Search</button>
    </div>     -->
    
    <main>   
    <div class="search-bar-container">
        <h1>Shopping Cart</h1>        
    </div> 
<br>  

<nav>
            <a href="userfyp.php">Home</a> |
            <a href="books_for_sale.php">Shop</a>
        </nav> 
    <main>

        <?php if (empty($_SESSION['cart'])): ?>
            <p style="text-align: center; font-size: 18px; background-color: red; color: white; padding: 10px; border-radius: 5px;">
    Your cart is empty.
</p>
        <?php else: ?>
            <form action="cart.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Title</th>
                            <th>Price (PHP)</th>                        
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $id => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                
                                <?php
// Output the image as base64
$imageData = base64_encode($item['image']);
echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Book Cover" class="book-cover" style="width: 80px; height: 100px; object-fit: cover; border-radius: 5px;">';
?>
                                </td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo number_format($item['price'], 2); ?></td>
                              
                               
                                <td>
                                    <a href="cart.php?remove=<?php echo $id; ?>" class="btn btn-remove">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2" style="text-align: right; font-weight: bold;">Total</td>
                            <td colspan="1"><?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>               
            </form>
            <br>
            <div style="text-align: center;">
                <a href="checkout_selling.php" class="btn btn-checkout">Proceed to Checkout</a>
            </div>
        <?php endif; ?>



</body>
</html>
