<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Yarn Craft Emporium</title>
    <link rel="stylesheet" href="customer_information.css" />
  </head>
  <body>
    <main>
      <div class="sidebar">
        <button
          class="accordion"
          onclick="window.location.href='dashboard.php'"
        >
          Dashboard
        </button>
        <div class="panel">
          
        </div>
        <button class="accordion">Products</button>
        <div class="panel">
          <ul>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="all_products.php">All Products</a></li>
          </ul>
        </div>
        <button class="accordion">View Customer</button>
        <div class="panel">
          <ul>
            <li>
              <a href="customer_information.php">Customer Information</a>
            </li>
          </ul>
        </div>
        <button class="accordion">View Order</button>
        <div class="panel">
          <ul>
            <li><a href="order.php">Completed Orders</a></li>
          </ul>
        </div>
        <button class="accordion">Product Categories</button>
        <div class="panel">
          <ul>
          <li><a href="add_category.php">Add Category</a></li>
            <li><a href="all_category.php">All Categories</a></li>
          </ul>        
        </div>

    <button class="accordion" onclick="window.location.href='payment.php'">
          Sales
        </button>
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

      <div class="content">
      <h2>Sales</h2>
        <br>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>             
                <th>Amount Paid</th>
                <th>Payment Method</th>             
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
        
        <?php
include('config.php');

// Query to retrieve data from the database
$sql = "SELECT orders_id, total_price, payment_method, order_date FROM orders";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Check if there are any records
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["orders_id"] . "</td>";
        echo "<td>" . $row["total_price"] . "</td>";
        echo "<td>" . $row["payment_method"] . "</td>";
        echo "<td>" . $row["order_date"] . "</td>";
          
        echo "</td>";
        
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>0 results</td></tr>";
}

// Close the database connection
$conn->close();
?>

        </tbody>
    </table>   
      </div>
    </main>
  </body>


  <script>
    // for the dashboard button
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
          panel.style.display = "none";
        } else {
          panel.style.display = "block";
        }
      });
    }
    // JavaScript for the admin dashboard functionality
    document.addEventListener("DOMContentLoaded", function () {
      var acc = document.getElementsByClassName("accordion");
      var i;
    });

    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function () {
        // Hide the "Add Product" form when another accordion button is clicked
        var panels = document.getElementsByClassName("panel");
        for (var j = 0; j < panels.length; j++) {
          if (panels[j].style.display === "block") {
            panels[j].style.display = "none";
          }
        }

        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
          panel.style.display = "none";
        } else {
          panel.style.display = "block";
        }
      });
    }

    // Sample data for demonstration
 /*   const payments = [
      {
        paymentNo: 1,
        invoiceNo: "INV-001",
        amountPaid: 100,
        paymentMethod: "Credit Card",
        referenceNo: "REF-001",
        paymentDate: "2024-04-23",
      },
      {
        paymentNo: 2,
        invoiceNo: "INV-002",
        amountPaid: 150,
        paymentMethod: "PayPal",
        referenceNo: "REF-002",
        paymentDate: "2024-04-24",
      },
      {
        paymentNo: 2,
        invoiceNo: "INV-002",
        amountPaid: 150,
        paymentMethod: "PayPal",
        referenceNo: "REF-002",
        paymentDate: "2024-04-24",
      },
      {
        paymentNo: 2,
        invoiceNo: "INV-002",
        amountPaid: 150,
        paymentMethod: "PayPal",
        referenceNo: "REF-002",
        paymentDate: "2024-04-24",
      },
      {
        paymentNo: 2,
        invoiceNo: "INV-002",
        amountPaid: 150,
        paymentMethod: "PayPal",
        referenceNo: "REF-002",
        paymentDate: "2024-04-24",
      },
      {
        paymentNo: 2,
        invoiceNo: "INV-002",
        amountPaid: 150,
        paymentMethod: "COD",
        referenceNo: "REF-002",
        paymentDate: "2024-04-24",
      },
      {
        paymentNo: 2,
        invoiceNo: "INV-002",
        amountPaid: 150,
        paymentMethod: "PayPal",
        referenceNo: "REF-002",
        paymentDate: "2024-04-24",
      },

      // Add more payment data as needed
    ];

    const tableBody = document.querySelector("#paymentTable tbody");

    // Function to create a row for each payment
    function createPaymentRow(payment) {
      const row = document.createElement("tr");
      row.innerHTML = `
            <td>${payment.paymentNo}</td>
            <td>${payment.invoiceNo}</td>
            <td>${payment.amountPaid}</td>
            <td>${payment.paymentMethod}</td>
            <td>${payment.referenceNo}</td>
            <td>${payment.paymentDate}</td>
            <td><button onclick="deletePayment(${payment.paymentNo})">Delete</button></td>
        `;
      return row;
    }

    // Function to render all payments in the table
    function renderPayments() {
      payments.forEach((payment) => {
        const row = createPaymentRow(payment);
        tableBody.appendChild(row);
      });
    }

    // Call renderPayments to initially populate the table
    renderPayments();

    // Function to delete a payment (you can implement the actual deletion logic)
    function deletePayment(paymentNo) {
      // Implement deletion logic here
      alert(`Delete payment with payment number: ${paymentNo}`);
    }*/
  </script>
</html>
