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

      <?php
include('config.php');
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    // Escape user inputs for security
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);  
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    
    // Insert text into the table
    $sql = "INSERT INTO tbladmin (first_name,last_name, username,password) VALUES ('$first_name','$last_name','$username','$password')";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect to users.php
             header("Location: admin.php");
        exit(); // Ensure script stops execution
    } else {
        echo "Error adding a user " . $sql . "<br>" . $conn->error;
    }
  
    
    // Close connection
    $conn->close();
}
?>

<div class="content">
            <h2>Add Admin</h2>
            <br>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <label for="text">First Name:</label><br>
                <input type="text" id="first_name" name="first_name" required><br>
                <label for="text">Last Name:</label><br>
                <input type="text" id="last_name" name="last_name" required><br>   
                <label for="text">Username:</label><br>             
                <input type="text" id="username" name="username" required><br>
                <label for="text">Password:</label><br>
                <input type="text" id="password" name="password" required><br>              

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <input type="submit" value="Submit" name="submit" 
                           style="padding: 10px 20px; font-size: 16px; background-color: #800000; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <a href="admin.php" class="btn btn-secondary" 
                       style="padding: 10px 20px; font-size: 16px; text-decoration: none; background-color: gray; color: white; border-radius: 5px; display: inline-block; text-align: center;">
                       Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
  </body>
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
