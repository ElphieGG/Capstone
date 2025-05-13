<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Libro Compatir | Checkout</title>
    <link rel="icon" type="image/png" href="images/LOGO/LOGO.jpg" />
    <link href="cart1.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://www.paypal.com/sdk/js?client-id=AcjnzZQgA6loFWAXb3l3_1skyj-6Z3rvFxcLl-9GpQt4YnX088JQOHFoYumFm8kIKLgdNURtJukSphIl&currency=PHP"></script>



    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 400px;
            max-height: 400px;
        }

        .checkout-button-container {
            display: flex;
            justify-content: flex-start;
            /* Aligns button to the left */
            margin-top: 20px;
        }

        .checkout-button {
            background-color: #28a745;
            /* Green background */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }

        .checkout-button:hover {
            background-color: #218838;
            /* Darker green on hover */
        }
    </style>
</head>

<body>
    
    <?php
    session_start();
    include('config.php');


    if (empty($_SESSION['cart'])) {
        header("Location: cart.php");
        exit();
    }


    if (!isset($_SESSION['user_id'])) {
        header("Location:userfyp.php");
        exit();
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $ship_location = "";
    $ship_rate = 0;

    if ($user_id) {
        $query = "SELECT ship_location FROM tbluser WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($ship_location);
        $stmt->fetch();
        $stmt->close();

        if ($ship_location) {
            $ship_query = "SELECT ship_rate FROM shipping_rate WHERE ship_location = ?";
            $stmt = $conn->prepare($ship_query);
            $stmt->bind_param("s", $ship_location);
            $stmt->execute();
            $stmt->bind_result($ship_rate);
            $stmt->fetch();
            $stmt->close();
        }
    }


    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    }


 //   $final_total = $total + $ship_rate;
    $final_total = $total;


   
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['complete_order'])) {
                $payment_method = $_POST['payment_method'] ?? '';
        
              if (!empty($payment_method)) {

            // Generate random unique order_id for COD
            $order_id = null;
            if ($payment_method === 'Cash on Delivery') {
                do {
                    // Generate a random order_id starting with '2024'
                    $random_number = rand(10000, 99999); // 5 random digits
                    $order_id = "2024" . $random_number;

                    // Check uniqueness in the database
                    $query = "SELECT COUNT(*) FROM orders WHERE orders_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $order_id);
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();
                } while ($count > 0); // Ensure the order_id is unique
            }
           // Store order in the database
           $order_query = "INSERT INTO orders (user_id, total_price, payment_method, ship_rate, order_date, orders_id) VALUES (?, ?, ?, ?, NOW(), ?)";
           $stmt = $conn->prepare($order_query);
           
           if (!$stmt) {
               die("Error preparing statement: " . $conn->error);
           }

           $stmt->bind_param("idsds", $user_id, $final_total, $payment_method, $ship_rate, $order_id);
           $stmt->execute();

           if ($stmt->error) {
               die("Error executing statement: " . $stmt->error);
           }

           $order_id = $stmt->insert_id; // Get the order ID for order details
           $stmt->close();

           // Store each product in the order details table
           $order_details_query = "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
           $stmt = $conn->prepare($order_details_query);

           

           if (!$stmt) {
               die("Error preparing statement for order details: " . $conn->error);
           }

           foreach ($_SESSION['cart'] as $product_id => $item) {
               $stmt->bind_param("iisid", $order_id, $product_id, $item['name'], $item['quantity'], $item['price']);
               $stmt->execute();

               if ($stmt->error) {
                   die("Error executing order details statement: " . $stmt->error);
               }
           }
           $stmt->close();

           // Update book_status to "Sold"
/*$update_status_query = "UPDATE tblbook SET book_status = 'Sold' WHERE id = ?";
//$stmt = $conn->prepare($update_status_query);

if (!$stmt) {
    die("Error preparing update statement: " . $conn->error);
}

foreach ($_SESSION['cart'] as $product_id => $item) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    
    if ($stmt->error) {
        die("Error updating book status: " . $stmt->error);
    }
}

$stmt->close();*/

           // Clear the cart
           unset($_SESSION['cart']);
 // Load SweetAlert2
 echo "<html><head>
 <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
 </head><body>";
   // Show success alert
   echo "<script>
   Swal.fire({
       icon: 'success',
       title: 'Your payment has been successfull!',
       text: 'Thank you for shopping with us.',
       confirmButtonText: 'OK'
   }).then(() => {
       window.location.href = 'userfyp.php';
   });
 </script>";
 exit();

            
        //    // Display success prompt and redirect
        //    echo "<script>
        //        alert('Your order has been successfully placed! Thank you for shopping with us.');
        //        window.location.href = 'userfyp.php';
        //    </script>";

        //    exit();

       } else {
           echo "<script>alert('Please select a payment method.');</script>";
       }
   } elseif (isset($_POST['cancel_order'])) {
       // Handle order cancellation
       header("Location: cart.php");
       exit();
   }
}

  
    if (isset($_POST['logout'])) {
   
        session_unset();
        session_destroy();
        header("Location: signin.php");
        exit();
    }
    ?>



    <!DOCTYPE html>
    <html lang="en">

    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserForyouPage</title>
    <link rel="stylesheet" href="cart1.css"> <!-- Link to external CSS -->
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
    background-color: rgb(126, 0, 0);
    width: 100%;
    padding: 15px 20px; 
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.navbar .logo {
    width: 200px;
    cursor: pointer;
}

.navbar .nav-links {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

.navbar .nav-links li {
    margin: 0 15px;
}

.navbar .nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
}

.navbar .nav-links a:hover {
    text-decoration: underline;
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
        .payment-method {
                margin: 20px 0;
            }

            .buttons {
                display: flex;
                gap: 10px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 16px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .btn-primary {
                background-color: #007bff;
                color: white;
            }

            .btn-danger {
                background-color: #dc3545;
                color: white;
            }

    </style>
</head>
<body>
 <!-- Header -->
 <nav class="navbar">
        <img src="images/logo2.png" class="logo" alt="Logo">
        <ul class="nav-links">
        <li><a href="userfyp.php">Home</a></li>
 <li><a href="chat.php">Chat</a></li>
 <li><a href="user.php">Profile</a></li>
 <li><a href="cart.php">Cart</a></li>
            <li><a href="login.php">Sign Out</a></li>
        </ul>
    </nav>
    
    <div class="search-bar-container">
        <h1>Checkout</h1>
        <input type="text" class="search-bar" placeholder="Search...">
        <button class="search-button">Search</button>
    </div>    
    
    <main>   
    <div class="search-bar-container">
        <h1>Order Summary</h1>        
    </div>  
    <br>
            <table>
                <thead>
                    <tr>
                    <tr> <th>Book</th>
                        <th>Title</th>
                        <th>Price (PHP)</th>                       
                       
                    </tr>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
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
                         <!---  <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td> --->
                        </tr>
                    <?php endforeach; ?>
                     <!---   <tr>
                        <td colspan="2"><strong>Additional Fee (Meeting Spot Outside WMSU Campus)</strong></td>
                        <td><?php echo number_format($ship_rate, 2); ?></td>
                    </tr>--->
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><?php echo number_format($final_total, 2); ?></td>
                    </tr>
                </tbody>

            </table>



            <form method="post" action="checkout_selling.php" onsubmit="return validateForm(event)">
                <h2>Select Payment Method</h2>
                <div class="payment-method">
                    <div class="payment-option">
                        <input type="radio" id="cash" name="payment_method" value="Online Payment">
                        <label for="cash">Online Payment</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" id="cash" name="payment_method" value="Cash on Delivery">
                        <label for="cash">Cash on Delivery</label>
                    </div>
                  <!-- <div class="payment-option">
                        <input type="radio" id="paypal" name="payment_method" value="PayPal">
                        <label for="paypal">PayPal</label>
                    </div>--->
                </div>

                <!-- PayPal Button Container -->
                <div id="paypal-button-container" style="display: none; margin-top: 20px;"></div>

                <div class="buttons">
                    <button type="submit" id="complete-order-button" name="complete_order" class="btn btn-primary">Complete Order</button>
                    <button type="submit" name="cancel_order" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>

            <script>
                function validateForm(event) {
                    const form = event.target;
                    const submitter = event.submitter;

                    if (submitter && submitter.name === 'complete_order') {
                        const paymentMethods = document.getElementsByName('payment_method');
                        let isSelected = false;

                        for (const method of paymentMethods) {
                            if (method.checked) {
                                isSelected = true;
                                break;
                            }
                        }


                        if (!isSelected) {
                            alert('Please select a payment method before completing the order.');
                            return false;
                        }
                    }

                    return true;
                }

                // eto po dito para sa buttons ni paypal payment and complete order
                document.querySelectorAll('input[name="payment_method"]').forEach(method => {
                    method.addEventListener('change', function() {
                        const paypalContainer = document.getElementById('paypal-button-container');
                        const completeOrderButton = document.getElementById('complete-order-button');

                        if (this.value === 'PayPal') {
                            paypalContainer.style.display = 'block'; // para e pa kita ang paypal button if paypal ang payment method
                            completeOrderButton.style.display = 'none'; // then e hide ang complete order button if paypal payment
                        } else { // Vice Versa lang dito
                            paypalContainer.style.display = 'none';
                            completeOrderButton.style.display = 'inline-block';
                        }
                    });
                });
             

                paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?php echo $final_total; ?>'
                }
            }]
        });
    },
 onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
        alert('Order is successful! Thank you, ' + details.payer.name.given_name + '.');

        const orderData = {
            user_id: <?php echo json_encode($user_id); ?>,
            total_price: '<?php echo $final_total; ?>',
            payment_method: 'PayPal',
            ship_rate: '<?php echo $ship_rate; ?>',
            order_details: <?php echo json_encode($_SESSION['cart']); ?>,
        };

 

        fetch('save_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData),
        })
        .then(response => response.json())
        .then(result => {
         
            if (result.success) {
              
                window.location.href = 'order_complete.php?orders_id=' + result.order_id;
            } else {
                alert('Failed to save the order. Please contact support.');
            }
        })
        .catch(error => {
            console.error('Error saving the order:', error);
            alert('An error occurred. Please try again.');
        });
    });
},

    onError: function(err) {
        console.error(err);
        alert('An error occurred during payment. Please try again.');
    }
}).render('#paypal-button-container');



            </script>


        </main>

      
    </body>

    </html>