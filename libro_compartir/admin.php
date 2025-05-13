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
            <li><a href="books.php">All Books</a></li>
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
    <h2 class="mb-4">Admin Information</h2>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Admin ID</th>  
                    <th>First Name</th>     
                    <th>Last Name</th>             
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include('config.php');
                $sql = "SELECT admin_id, first_name, last_name FROM tbladmin";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Query failed: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["admin_id"] . "</td>";
                        echo "<td>" . $row["first_name"] . "</td>";
                        echo "<td>" . $row["last_name"] . "</td>";                       
                        echo "<td>";
                        echo "<a class='btn btn-success btn-sm' href='edit_user.php?id=" . $row['admin_id'] . "'><i class='fas fa-edit'></i> Edit</a> ";
                       // echo "<a class='btn btn-danger btn-sm' href='delete_user.php?id=" . $row['admin_id'] . "' onclick='return confirm(\"Are you sure you want to delete this user?\")'><i class='fas fa-trash-alt'></i> Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No users found</td></tr>";
                }
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
