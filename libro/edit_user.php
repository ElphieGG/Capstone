<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Yarn Craft Emporium</title>
    <link rel="stylesheet" href="add_product.css" />
    <style>
        .accordion.active {
    background-color: #007bff; /* Highlight color */
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
            <li><a href="add_books.php">Add Books</a></li>
            <li><a href="all_books.php">All Books&nbsp;</a></li>
          </ul>
        </div>

        <button class="accordion">Users</button>
        <div class="panel">
          <ul>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="users.php">All Users&nbsp;</a></li>
          </ul>
        </div>   
                
        <button class="accordion">Book Categories</button>
        <div class="panel">
          <ul>
          <li><a href="add_category.php">Add Category</a></li>
            <li><a href="all_category.php">All Categories</a></li>
          </ul>
        </div>                

        <button class="accordion" onclick="window.location.href='login.php'">
          Log out
        </button>
      </div>
      

      <div class="content">
        <h2>Edit User Role</h2>
        <br>
   

        <?php
        // Include the database configuration file
        include('config.php');

        // Check if the detail ID is set and valid
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $user_id = $_GET['id'];
            
            // Fetch order information from the database
            $sql = "SELECT * FROM login WHERE user_id = $user_id";
            $result = $conn->query($sql);
            
            if ($result->num_rows == 1) {
                // Order found, fetch data
                $row = $result->fetch_assoc();
                $role = $row['role'];
            } else {
                // Order not found
                header("Location: error.php");
                exit();
            }
        } else {
            // Invalid ID
            header("Location: error.php");
            exit();
        }

        // Check if form is submitted
        if (isset($_POST['submit'])) {
            // Retrieve form data
            $role = $_POST['role'];
            
            // Update order role in the database
            $sql = "UPDATE login SET role = '$role WHERE detail_id = $detail_id";
            if ($conn->query($sql) === TRUE) {
                // Successfully updated
                header("Location: orders.php");
                exit();
            } else {
                // Error updating role
                echo "Error: " . $conn->error;
            }
        }

        // Close database connection
        $conn->close();
        ?>

        <form method="post">
          <label for="role">Role:</label><br>
          <select id="role" name="role">
            <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>admin</option>
            <option value="buyer" <?php echo $role === 'buyer' ? 'selected' : ''; ?>>buyer</option>
          </select><br><br>

          <div style="display: flex; gap: 10px; margin-top: 10px;">
            <input
              type="submit"
              value="Submit"
              name="submit"
              style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;"
            />
            <a
              href="users.php"
              class="btn btn-secondary"
              style="padding: 10px 20px; font-size: 16px; text-decoration: none; background-color: gray; color: white; border-radius: 5px; display: inline-block; text-align: center;"
            >
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