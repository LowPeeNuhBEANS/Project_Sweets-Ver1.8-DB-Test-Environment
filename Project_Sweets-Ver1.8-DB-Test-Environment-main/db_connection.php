<?php
/**
 * Database Connection Configuration
 * Treatx' Pastries Order Management System
 * 
 * This file establishes a connection to the MySQL database
 * Make sure XAMPP MySQL service is running before accessing this file
 */

// Database configuration
$servername = "localhost";  // Or 127.0.0.1
$username = "root";         // Default XAMPP username
$password = "";             // Default XAMPP password (empty by default)
$dbname = "treatx_orders";  // Your database name

// Set error reporting for development (remove in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Step 1: Connect to MySQL server WITHOUT selecting a database first
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for full Unicode support (including emojis)
    $conn->set_charset("utf8mb4");
    
    // Step 2: Create database if it doesn't exist
    $createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!$conn->query($createDbSql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Step 3: Select the database
    if (!$conn->select_db($dbname)) {
        throw new Exception("Error selecting database: " . $conn->error);
    }
    
    // Step 4: Create tables if they don't exist (basic structure for backward compatibility)
    
    // Create pastries table
    $createPastriesTable = "CREATE TABLE IF NOT EXISTS pastries (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        price_per_box DECIMAL(10,2) NOT NULL DEFAULT 150.00,
        image_filename VARCHAR(255),
        is_available BOOLEAN DEFAULT TRUE,
        category ENUM('mochi', 'donuts', 'special') DEFAULT 'mochi',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($createPastriesTable)) {
        throw new Exception("Error creating pastries table: " . $conn->error);
    }
    
    // Create orders table with foreign key to pastries
    $createOrdersTable = "CREATE TABLE IF NOT EXISTS orders (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        customer_phone VARCHAR(20),
        pickup_address TEXT NOT NULL,
        pastry_id INT(6) UNSIGNED NOT NULL,
        quantity INT(3) NOT NULL CHECK (quantity > 0),
        pickup_date DATE NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('Pending', 'Confirmed', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled') DEFAULT 'Pending',
        payment_status ENUM('Pending', 'Paid', 'Partial', 'Refunded') DEFAULT 'Pending',
        payment_method ENUM('Cash', 'GCash', 'Bank Transfer', 'Credit Card') DEFAULT 'Cash',
        special_instructions TEXT,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pastry_id) REFERENCES pastries(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($createOrdersTable)) {
        throw new Exception("Error creating orders table: " . $conn->error);
    }
    
    // Create users table for admin authentication
    $createUsersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'staff') DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT TRUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($createUsersTable)) {
        throw new Exception("Error creating users table: " . $conn->error);
    }
    
    // Insert default admin user if users table is empty (password: admin123)
    $checkAdmin = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $checkAdmin->fetch_assoc();
    
    if ($row['count'] == 0) {
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = "INSERT INTO users (username, password_hash, email, full_name, role, is_active) 
                        VALUES ('admin', '$defaultPassword', 'admin@treatx.com', 'Administrator', 'admin', TRUE)";
        
        if (!$conn->query($insertAdmin)) {
            throw new Exception("Error creating default admin user: " . $conn->error);
        }
    }
    
    // Insert default pastries if pastries table is empty
    $checkPastries = $conn->query("SELECT COUNT(*) as count FROM pastries");
    $row = $checkPastries->fetch_assoc();
    
    if ($row['count'] == 0) {
        $defaultPastries = [
            ['Mochi Box', 'Assorted mochi flavors', 150.00, 'mochi.jpg', 'mochi'],
            ['Donut Box', 'Assorted donut flavors', 150.00, 'donuts.jpg', 'donuts'],
            ['Special Box', 'Mixed pastries', 180.00, 'special.jpg', 'special']
        ];
        
        $insertStmt = $conn->prepare("INSERT INTO pastries (name, description, price_per_box, image_filename, category) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($defaultPastries as $pastry) {
            $insertStmt->bind_param("ssdss", $pastry[0], $pastry[1], $pastry[2], $pastry[3], $pastry[4]);
            $insertStmt->execute();
        }
        
        $insertStmt->close();
    }
    
    // Connection successful - $conn is now ready to use in other files
    
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("Database Error: " . $e->getMessage());
    die("Database connection error. Please make sure XAMPP MySQL is running. Error: " . $e->getMessage());
}

// Note: Don't close the connection here - it will be used by other files that include this one
// The connection will be closed automatically at the end of script execution
?>
