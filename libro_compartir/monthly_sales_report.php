<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Libro Compartir | Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    />

    <style>
        /* Style for the button container */
        .image-button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            outline: none;
        }
        .accordion.active {
   /* background-color: #007bff; /* Highlight color 
    background-color: #800000; /* Maroon */
    background-color:rgb(238, 65, 65);
    color: white;
  }
                
    </style>
  </head>
  <body>
    <main>
      <div class="sidebar">
         <img src="images/logo2.png" alt="Logo" class="logo">
        <button class="accordion" onclick="window.location.href='dashboard.php'">
          Dashboard
        </button>
        <div class="panel">
         
        </div>

        <button class="accordion">Books</button>
        <div class="panel">
          <ul>
          <li><a href="books.php">All Books&nbsp;</a></li>
            <li><a href="books_sold.php">Books Sold</a></li>
            <li><a href="books_exchanged.php">Books Exchanged</a></li>  
          </ul>
        </div>
        <button class="accordion">Sales Report</button>
        <div class="panel">
          <ul>
          <li><a href="weekly_sales_report.php">Weekly Sales</a></li>
          <li><a href="monthly_sales_report.php">Monthly Sales</a></li>
          </ul>
        </div>
        <button class="accordion">Users</button>
        <div class="panel">
          <ul>
          <li><a href="all_users.php">All Users&nbsp;</a></li>
            <li><a href="add_user.php">Add User</a></li>
           
          </ul>
        </div>   

        <button class="accordion">Admin</button>
        <div class="panel">
          <ul>
          <li><a href="admin.php">All Admin&nbsp;</a></li>
            <li><a href="add_admin.php">Add Admin</a></li>
          
          </ul>
        </div>  
                
                     

        <button class="accordion" onclick="window.location.href='login.php'">
          Log out
        </button>
      </div>

      

      <div class="content">
    <h2 class="mb-4">Monthly Sales Report</h2>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
               
                <th>Year</th>
                <th>Month</th>
                <th>Total Books Sold</th>
                <th>Total Sales (PHP)</th>
                           
                   
                </tr>
            </thead>
            <tbody>


            <?php
include('config.php');

// Get year from user input (e.g., via GET request)
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// SQL Query to fetch monthly sales report for a specific year
$sql = "
SELECT 
    YEAR(o.order_date) AS year_number,
    MONTH(o.order_date) AS month_number,
    SUM(od.quantity) AS total_quantity_sold,
    SUM(od.quantity * od.price) AS total_sales,
    COUNT(DISTINCT o.orders_id) AS total_orders
FROM order_details od
JOIN orders o ON od.order_id = o.orders_id
WHERE YEAR(o.order_date) = ?
GROUP BY year_number, month_number
ORDER BY year_number DESC, month_number ASC;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

// Function to format month name
function get_month_name($month_number) {
    return date("F", mktime(0, 0, 0, $month_number, 1));
}

// Display results in an HTML table
if ($result->num_rows > 0) {
   
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["year_number"] . "</td>
                <td>" . get_month_name($row["month_number"]) . "</td>
                <td>" . $row["total_quantity_sold"] . "</td>
                <td>PHP " . number_format($row["total_sales"], 2) . "</td>
               
              </tr>";
    }
    echo "</table>";
} else {
    echo "No records found for the selected year.";
}

$stmt->close();
$conn->close();
?>



            </tbody>
        </table>
    </div>
</div>
  
  <  <script>
    
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
</html>
