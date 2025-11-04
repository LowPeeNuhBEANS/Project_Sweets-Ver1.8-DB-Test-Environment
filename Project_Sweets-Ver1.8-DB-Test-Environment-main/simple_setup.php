<?php
// Simple database setup without complex SQL features
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treatx_orders";

// Create connection
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql)) {
    echo "✓ Database '$dbname' created or already exists<br>";
} else {
    echo "✗ Error creating database: " . $conn->error . "<br>";
}

$conn->select_db($dbname);

// Create pastries table
$sql = "CREATE TABLE IF NOT EXISTS pastries (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    price_per_box DECIMAL(10,2) NOT NULL DEFAULT 150.00,
    image_filename VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    category ENUM('mochi', 'donuts', 'special') DEFAULT 'mochi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "✓ Table 'pastries' created<br>";
} else {
    echo "✗ Error creating pastries table: " . $conn->error . "<br>";
}

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    pickup_address TEXT NOT NULL,
    pastry_id INT(6) UNSIGNED NOT NULL,
    quantity INT(3) NOT NULL,
    pickup_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "✓ Table 'orders' created<br>";
} else {
    echo "✗ Error creating orders table: " . $conn->error . "<br>";
}

// Create users table for admin
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "✓ Table 'users' created<br>";
} else {
    echo "✗ Error creating users table: " . $conn->error . "<br>";
}

// Insert pastries
$pastries = [
    ['Berry Blush Mochi', '🍓 Berry Blush – A strawberry cheesecake in mochi form, sweet berries and tangy cream cheese for a refreshing classic bite.', 50.00, 'strawberry.jpg', 'mochi'],
    ['Berry XD Mochi', '🍇 Berry XD – A burst of juicy blueberries balanced with velvety cream cheese; just like your favorite blueberry cheesecake.', 50.00, 'blueberry.jpg', 'mochi'],
    ['Cookie Cloud Mochi', '🍪 Cookie Cloud – Oreo cheesecake vibes in every bite, crushed cookies blended into smooth cream cheese inside pillowy mochi.', 50.00, 'cookiecloud.jpg', 'mochi'],
    ['Sunny Munch Mochi', '🥭 Sunny Munch – A tropical cheesecake twist! sweet mango meets creamy cheese for a sunny, melt-in-your-mouth experience.', 50.00, 'mango.jpg', 'mochi'],
    ['Assorted Mochi', 'Mixed variety of our delicious mochi flavors.', 150.00, 'Assorted.jpg', 'mochi'],
    ['Box of Mini Donuts', 'Assorted mini donuts in various flavors and toppings.', 150.00, 'd1.jpg', 'donuts'],
    ['Coming soon....', 'New pastry items coming soon!', 0.00, NULL, 'special']
];

$inserted = 0;
foreach ($pastries as $pastry) {
    $stmt = $conn->prepare("INSERT INTO pastries (name, description, price_per_box, image_filename, category) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=name");
    $stmt->bind_param("ssdss", $pastry[0], $pastry[1], $pastry[2], $pastry[3], $pastry[4]);
    if ($stmt->execute()) {
        $inserted++;
    }
    $stmt->close();
}
echo "✓ Inserted $inserted pastries<br>";

// Insert admin user (password: treatx123)
$admin_password = password_hash('treatx123', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, full_name, role) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE username=username");
$username = 'admin';
$email = 'treatx.buiz@gmail.com';
$fullname = 'Treatx Administrator';
$role = 'admin';
$stmt->bind_param("sssss", $username, $admin_password, $email, $fullname, $role);
if ($stmt->execute()) {
    echo "✓ Admin user created<br>";
}
$stmt->close();

// Verify setup
$result = $conn->query("SELECT COUNT(*) as count FROM pastries");
$row = $result->fetch_assoc();
echo "<br><h2>✓ Setup Complete!</h2>";
echo "<p><strong>Pastries in database:</strong> " . $row['count'] . "</p>";

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='index.html' target='_blank'>Customer Page</a> - Place orders here</li>";
echo "<li><a href='login.php' target='_blank'>Admin Login</a> - Manage orders (username: admin, password: treatx123)</li>";
echo "</ul>";

echo "<h3>Your Application URL:</h3>";
echo "<p><strong>http://localhost/Project_Sweets-Ver1.8-DB-Test-Environment/</strong></p>";

$conn->close();
?>
