<?php
include('config.php');

if (isset($_GET['orders_id'])) {
    $order_id = (int)$_GET['orders_id'];  

    $sql = "
        SELECT o.orders_id, o.total_price, o.order_date, o.user_id
        FROM orders o
        WHERE o.orders_id = ?
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $order_id); 
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order_details = $result->fetch_assoc();
        } else {
            die("Order not found.");
        }

        $sql_items = "
            SELECT od.product_name, od.quantity, od.price, od.status
            FROM order_details od
            WHERE od.order_id = ?
        ";

        if ($stmt_items = $conn->prepare($sql_items)) {
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
        } else {
            die("Failed to fetch order items.");
        }

        $user_id = $order_details['user_id'];
        $sql_user = "
            SELECT name, email, phone, gender, ship_address, ship_location
            FROM login
            WHERE user_id = ?
        ";

        if ($stmt_user = $conn->prepare($sql_user)) {
            $stmt_user->bind_param("i", $user_id); 
            $stmt_user->execute();
            $result_user = $stmt_user->get_result();
            $buyer_details = $result_user->fetch_assoc();
        } else {
            die("Failed to fetch buyer details.");
        }

    
        $first_item = $result_items->fetch_assoc();
        $order_status = $first_item['status']; 

        $stmt->close();
        $stmt_items->close();
        $stmt_user->close();
    } else {
        die("Query failed: " . $conn->error);
    }
} else {
    die("Order ID not provided.");
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Yarn Craft Emporium</title>
    <link rel="stylesheet" href="customer_information.css" />
  </head>
  <style>
    .accordion.active {
      background-color: #007bff; /* Highlight color */
      color: white;
    }
  </style>

  <body>
    <main>
      <div class="sidebar">
        <button class="accordion" onclick="window.location.href='dashboard.php'">
          Dashboard
        </button>
        <div class="panel"></div>

        <button class="accordion">Products</button>
        <div class="panel">
          <ul>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="all_products.php">All Products&nbsp;</a></li>
          </ul>
        </div>

        <button class="accordion">Users</button>
        <div class="panel">
          <ul>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="users.php">All Users&nbsp;</a></li>
          </ul>
        </div>

        <button class="accordion" onclick="window.location.href='customers.php'">
          View Customers
        </button>

        <button class="accordion">View Orders</button>
        <div class="panel">
          <ul>
            <li><a href="orders.php">List of Orders&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
            <li><a href="delivered.php">Delivered Orders</a></li>
            <li><a href="pending.php">Pending Orders&nbsp;&nbsp;</a></li>
          </ul>
        </div>

        <button class="accordion">Product Categories</button>
        <div class="panel">
          <ul>
            <li><a href="add_category.php">Add Category</a></li>
            <li><a href="all_category.php">All Categories</a></li>
          </ul>
        </div>

        <button class="accordion">Sales</button>
        <div class="panel">
          <ul>
            <li><a href="sales_date.php">By Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
            <li><a href="sales_name.php">By Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
            <li><a href="sales_method.php">By Payment Method</a></li>
          </ul>
        </div>

        <button class="accordion">Shipping Rates</button>
        <div class="panel">
          <ul>
            <li><a href="add_shippingrate.php">Add Shipping Rate</a></li>
            <li><a href="all_shippingrate.php">All Shipping Rates</a></li>
          </ul>
        </div>

        <button class="accordion" onclick="window.location.href='signin.php'">
          Log out
        </button>
      </div>
      <div class="container mt-5">
            <h2>Order Summary</h2>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order ID: <?php echo $order_details['orders_id']; ?></h5>
                    <p><strong>Buyer Name:</strong> <?php echo $buyer_details['name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $buyer_details['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $buyer_details['phone']; ?></p>
                    <p><strong>Shipping Address:</strong> <?php echo $buyer_details['ship_address']; ?></p>
                    <p><strong>Shipping Location:</strong> <?php echo $buyer_details['ship_location']; ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('F-d-Y h:i A', strtotime($order_details['order_date'])); ?></p>

                    <p><strong>Total Price:</strong> ₱<?php echo $order_details['total_price']; ?></p>
                  

                    <h5>Order Items:</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                          
                            mysqli_data_seek($result_items, 0); 
                            while ($item = $result_items->fetch_assoc()) {
                                echo "
                                    <tr>
                                        <td>{$item['product_name']}</td>
                                        <td>{$item['quantity']}</td>
                                        <td>₱{$item['price']}</td>
                                    </tr>
                                ";
                            }
                            ?>
                        </tbody>
                    </table>

               
                    <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-primary">Back</a>

                </div>
            </div>
        </div>
    </main>

<script>
   
    document.addEventListener("DOMContentLoaded", function () {
      var acc = document.getElementsByClassName("accordion");
      var i;

      for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
          // Remove the active class from all buttons
          for (var j = 0; j < acc.length; j++) {
            acc[j].classList.remove("active");
          }

          // Add the active class to the clicked button
          this.classList.add("active");

          // Handle panel visibility
          var panel = this.nextElementSibling;
          if (panel && panel.classList.contains("panel")) {
            // Hide all panels
            var panels = document.getElementsByClassName("panel");
            for (var k = 0; k < panels.length; k++) {
              panels[k].style.display = "none";
            }

            // Toggle the clicked panel
            if (panel.style.display === "block") {
              panel.style.display = "none";
            } else {
              panel.style.display = "block";
            }
          }
        });
      }
    });

</script>

</body>
</html>

<?php
$conn->close();
?>
