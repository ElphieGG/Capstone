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
// Database connection
//include('config.php');// Include your database connection

session_start();
include('config.php');

// Fetching total registered users (count of user_id in login table)
$registeredUsersQuery = "SELECT COUNT(user_id) as total_users FROM tbluser";
$registeredUsersResult = $conn->query($registeredUsersQuery);
$registeredUsers = $registeredUsersResult->fetch_assoc()['total_users'] ?? 0;

// Fetching the count of books sold
$bookSoldCountQuery = "SELECT COUNT(*) AS books_sold FROM tblbook WHERE book_status = 'Sold'";
$bookSoldCountResult = $conn->query($bookSoldCountQuery);
$bookSoldCount = $bookSoldCountResult->fetch_assoc()['books_sold']?? 0;

// Fetching the count of book exchange
$bookExchangedCountQuery = "SELECT COUNT(*) AS book_exchanged FROM tblbook WHERE book_status = 'Exchanged'";
$bookExchangedCountResult = $conn->query($bookExchangedCountQuery);
$bookExchangedCount = $bookExchangedCountResult->fetch_assoc()['book_exchanged']?? 0;



$conn->close();
?>

      <div class="content">
    <h2>Admin Dashboard</h2>
    <div class="dashboard-items">
       
        <div class="dashboard-item">
            <div class="icon"><button class="image-button"><a href="all_users.php"><i class="fas fa-user" style=color:white></i></a></button></div>
            <div class="data">Registered Users: <?php echo $registeredUsers; ?></div>
        </div>

        <div class="dashboard-item">
            <div class="icon"><button class="image-button"><a href="books_sold.php"><i class= "fas fa-regular fa-money-bill" style=color:white></i></a></button></div>
            <div class="data">Books Sold: <?php echo $bookSoldCount; ?></div>
        </div>

        <div class="dashboard-item">
            <div class="icon"><button class="image-button"><a href="books_exchanged.php"><i class="fas fa-exchange-alt" style=color:white></i></a></button></div>
            <div class="data">Books Exchanged: <?php echo $bookExchangedCount ; ?></div>
        </div>       

       

       
    </div>
</div>            
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
