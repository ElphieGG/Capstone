# 📚 Libro Compartir

Libro Compartir is a PHP-based web application built to share, browse, and manage a collection of books. This project runs on XAMPP and utilizes MySQL as its backend database.

## 🔧 Requirements

Before you begin, ensure the following software is installed on your machine:

- [XAMPP](https://www.apachefriends.org/) (PHP >= 7.4, MySQL >= 5.7)
- Web browser (Chrome, Firefox, etc.)
- Code editor (e.g., VS Code, Sublime Text - optional)

## 🚀 Installation & Setup (Local Deployment)

Follow the steps below to deploy the project locally using XAMPP:

### 1. Clone or Download the Project

```bash
# Clone via Git (if applicable)
git clone https://github.com/yourusername/libro_compartir.git

# OR download the ZIP file and extract it
```

### 2. Move Project to XAMPP Directory

Place the project folder inside the `htdocs` directory:

```bash
C:\xampp\htdocs\libro_compartir
```

### 3. Start Apache and MySQL

- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

### 4. Import the Database

1. Open [phpMyAdmin](http://localhost/phpmyadmin)
2. Create a new database named: `libro_compartir_db`
3. Import the SQL file:

   ```
   /libro_compartir/database/libro_compartir_db.sql
   ```

### 5. Configure Database Credentials

Open the database connection file (commonly `db_connect.php` or `config.php`) and set the credentials:

```php
$servername = "localhost";
$username = "root";
$password = "";
$database = "libro_compartir_db";
```

> Make sure your MySQL password is empty or updated accordingly if you have one set.

### 6. Run the Application

Navigate to:

```
http://localhost/libro_compartir/
```

You should now see the Libro Compartir home page.


