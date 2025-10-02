<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "treatx_orders";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$createDbSql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($createDbSql)) { // <-- FIXED: added missing parenthesis
    // Select the database
    $conn->select_db($dbname);
    
    // Create orders table if it doesn't exist (for backward compatibility)
    $createTableSql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        customer_phone VARCHAR(20),
        pickup_address TEXT NOT NULL,
        pastry_id INT(6) UNSIGNED NOT NULL DEFAULT 1,
        quantity INT(3) NOT NULL,
        pickup_date DATE NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTableSql)) {
        die("Error creating table: " . $conn->error);
    }
} else {
    die("Error creating database: " . $conn->error);
}
?>
