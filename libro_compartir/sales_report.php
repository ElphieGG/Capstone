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
    <main>
    <div class="sidebar">
         <!-- <img src="images/logo2.png" alt="Logo" class="logo"> -->
         <div class="brand-logo">
<i class="fas fa-book-open"></i>
  <span>LibroCompartir</span>
</div>
        <button class="accordion" onclick="window.location.href='dashboard.php'">
          Dashboard
        </button>
        <div class="panel">
         
        </div>

        <button class="accordion">Books</button>
        <div class="panel">
          <ul>
            <li><a href="books.php">All Books</a></li>
            <li><a href="books_sold.php">Books Sold</a></li>
            <li><a href="books_exchanged.php">Books Exchanged</a></li>            
          </ul>
        </div>

        <button class="accordion" onclick="window.location.href='sales_report.php'">
          Sales Report</button>
     

        <button class="accordion">Users</button>
        <div class="panel">
          <ul>
          <li><a href="all_users.php">All Users&nbsp;</a></li>
            <li><a href="add_user.php">Add User</a></li>
            
          </ul>
        </div>  
        <button class="accordion">Settings</button>
        <div class="panel">
          <ul>
          <li><a href="book_category.php">Book Category&nbsp;</a></li>
          <li><a href="book_condition.php">Book Condition</a></li>
          <li><a href="book_status.php">Book Status</a></li>
          <li><a href="meeting_spot.php">Meeting Spot</a></li>
          </ul>
        </div>  
       
        <button class="accordion" onclick="window.location.href='login.php'">
          Log out
        </button>
      </div>

    <?php
include('config.php');

// Get user-selected dates
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : "";
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : "";

// Base SQL Query
$sql = "
SELECT 
    MIN(o.order_date) AS start_date,
    MAX(o.order_date) AS end_date,
    SUM(od.quantity) AS total_quantity_sold,
    SUM(od.quantity * od.price) AS total_sales
FROM order_details od
JOIN orders o ON od.order_id = o.orders_id
JOIN tblbook b ON od.product_id = b.id
WHERE b.book_status = 'sold'";

// Apply date range filter if provided
if (!empty($fromDate) && !empty($toDate)) {
    $sql .= " AND DATE(o.order_date) BETWEEN ? AND ?";
}

$stmt = $conn->prepare($sql);

// Check for errors in SQL statement preparation
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

// Bind parameters if dates are selected
if (!empty($fromDate) && !empty($toDate)) {
    $stmt->bind_param("ss", $fromDate, $toDate);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="content">
<h2 class="mb-4 d-flex justify-content-between">
        Sales Report
        <button class="btn btn-primary" onclick="printBooksReport()">
            <i class="fas fa-print"></i> Print Report
    </button>
    </h2>


    <!-- Date Filter Inputs -->
    <div class="date-filter mb-3">
        <label for="fromDate">From Date:</label>
        <input type="date" id="fromDate" class="form-control d-inline-block w-auto" value="<?php echo $fromDate; ?>">
        <label for="toDate">To Date:</label>
        <input type="date" id="toDate" class="form-control d-inline-block w-auto" value="<?php echo $toDate; ?>">
        <button class="btn btn-primary" onclick="filterSales()">Filter</button>
    </div>

    <!-- Sales Report Table -->
    <div class="table-responsive">
    <table id="booksTable" class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Total Books Sold</th>
                    <th>Total Sales (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row["total_quantity_sold"] > 0) {
                        echo "<tr>
                                <td>{$row["start_date"]}</td>
                                <td>{$row["end_date"]}</td>
                                <td>{$row["total_quantity_sold"]}</td>
                                <td>PHP " . number_format($row["total_sales"], 2) . "</td>
                              </tr>";
                    } else {
                        echo "<tr><td colspan='4'>No records found for the selected date range.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No records found for the selected date range.</td></tr>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript for Filtering -->
<script>
function filterSales() {
    let fromDate = document.getElementById('fromDate').value;
    let toDate = document.getElementById('toDate').value;

    if (fromDate && toDate) {
        window.location.href = `sales_report.php?fromDate=${fromDate}&toDate=${toDate}`;
    } else {
        alert("Please select both From Date and To Date.");
    }
}
</script>
<!-- JavaScript Function for Printing -->
<script>
function printBooksReport() {
    var brandLogo = document.querySelector('.brand-logo').outerHTML;
    var printContent = document.getElementById('booksTable').outerHTML;
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = brandLogo + "<h2>Sales Report</h2>" + printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload(); // Reload to restore the page
}
</script>
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


</html>
