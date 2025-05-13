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
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    // Escape user inputs for security
    $book_condition = $conn->real_escape_string($_POST['book_condition']);
// Insert into the database
    $sql = "INSERT INTO tblbook_condition (book_condition)  VALUES ('$book_condition')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to all_shippingrate.php (ensure no output before this)
       header("Location: book_condition.php");
        exit(); // Prevent further execution after redirection
    } else {
        // Log the error instead of printing it to the page
        error_log("Error in query: " . $sql . " - " . $conn->error);
    }

    // Close connection
    $conn->close();


}
?>

<div class="content">
            <h2>Add Book Condition</h2>
            <br>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <label for="text">Book Condition :</label><br>
                <input type="text" id="book_condition" name="book_condition" required><br>
                

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <input type="submit" value="Submit" name="submit" 
                           style="padding: 10px 20px; font-size: 16px; background-color: #800000; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <a href="book_condition.php" class="btn btn-secondary" 
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
