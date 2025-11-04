<?php
/**
 * Robust DB connection bootstrap for XAMPP environment.
 * - Connects to MySQL server
 * - Creates database if missing
 * - Creates `pastries` and `orders` tables if missing
 * - Inserts sample pastries if none exist
 * Exposes $conn (mysqli) for included scripts.
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treatx_orders";

// Connect to MySQL server (do not select DB yet)
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    // fail early with clear message
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist and select it
$createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$conn->query($createDbSql)) {
    die("Error creating database: " . $conn->error);
}

// Select the database
if (!$conn->select_db($dbname)) {
    die("Error selecting database $dbname: " . $conn->error);
}

// Create pastries table
$createPastries = "CREATE TABLE IF NOT EXISTS pastries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    price_per_box DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    description TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (!$conn->query($createPastries)) {
    die("Error creating pastries table: " . $conn->error);
}

// Create orders table
$createOrders = "CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    pickup_address TEXT NOT NULL,
    pastry_id INT UNSIGNED NOT NULL DEFAULT 1,
    quantity INT NOT NULL,
    pickup_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending','Completed','Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_pastry FOREIGN KEY (pastry_id) REFERENCES pastries(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (!$conn->query($createOrders)) {
    die("Error creating orders table: " . $conn->error);
}

// Insert sample pastries if table is empty
$res = $conn->query("SELECT COUNT(*) AS cnt FROM pastries");
$row = $res ? $res->fetch_assoc() : null;
if (!$row || intval($row['cnt']) === 0) {
    $samples = [
        ['Berry Blush Mochi', '50.00', 'Strawberry cheesecake inspired mochi.'],
        ['Berry XD Mochi', '50.00', 'Blueberry cheesecake inspired mochi.'],
        ['Cookie Cloud Mochi', '50.00', 'Cookie & cream mochi.'],
        ['Sunny Munch Mochi', '50.00', 'Mango cheesecake mochi.'],
        ['Assorted Mochi', '150.00', 'Assorted box (4 pieces).'],
        ['Box of Mini Donuts', '150.00', 'Box of mini donuts.']
    ];

    $stmt = $conn->prepare("INSERT INTO pastries (name, price_per_box, description) VALUES (?, ?, ?)");
    if ($stmt) {
        foreach ($samples as $p) {
            $stmt->bind_param('sds', $p[0], $p[1], $p[2]);
            $stmt->execute();
        }
        $stmt->close();
    }
}

// Now $conn is ready for includes
?>
