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
          <li><a href="books.php">All Books&nbsp;</a></li>
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

      

      <div class="content">
    <h2 class="mb-4 d-flex justify-content-between">
        Books Information
        <button class="btn btn-primary" onclick="printBooksReport()">
            <i class="fas fa-print"></i> Print Report
        </button>
    </h2>

    <div class="table-responsive">
        <table id="booksTable" class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Posted by</th>  
                    <th>Title</th>     
                    <th>Description</th>   
                    <th>Image</th>                          
                    <th>Category</th>
                    <th>Book Condition</th>             
                    <th>Book Status</th>                              
                </tr>
            </thead>
            <tbody>
                <?php
                include('config.php');

                $sql = "SELECT b.id, b.title, b.description, b.category, b.book_condition, b.book_status, b.image, 
                            u.first_name, u.last_name 
                        FROM tblbook b
                        JOIN tbluser u ON b.user_id = u.user_id";

                $result = $conn->query($sql);

                if (!$result) {
                    die("Query failed: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                        echo "<td>" . $row["title"] . "</td>";
                        echo "<td>" . $row["description"] . "</td>";

                        echo "<td>";
                        if (!empty($row["image"])) {
                            $imageData = base64_encode($row["image"]);
                            echo '<img src="data:image/jpeg;base64,' . $imageData . '" width="80" height="80" class="img-thumbnail">';
                        } else {
                            echo '<img src="images/default.png" alt="No Image" width="80" height="80" class="img-thumbnail">';
                        }
                        echo "</td>";

                        echo "<td>" . $row["category"] . "</td>";
                        echo "<td>" . $row["book_condition"] . "</td>";
                        echo "<td>" . $row["book_status"] . "</td>";     
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No books found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript Function for Printing -->
<script>
function printBooksReport() {
    var brandLogo = document.querySelector('.brand-logo').outerHTML;
    var printContent = document.getElementById('booksTable').outerHTML;
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = brandLogo + "<h2>Books Information Report</h2>" + printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload(); // Reload to restore the page
}
</script>

  
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
