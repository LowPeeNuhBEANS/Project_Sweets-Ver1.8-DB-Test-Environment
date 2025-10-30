<?php
/**
 * Database Connection Test Script
 * This file tests if the database connection is working properly
 * Access this file via: http://localhost/Project_Sweets-Ver1.8-/test_db_connection.php
 */

echo "<h1>Database Connection Test</h1>";
echo "<p>Testing connection to MySQL database...</p>";

// Include the database connection file
require_once 'db_connection.php';

// Test 1: Check if connection exists
if (isset($conn) && $conn instanceof mysqli) {
    echo "<p style='color: green;'>✓ Database connection object created successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to create database connection object.</p>";
    exit;
}

// Test 2: Check connection status
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection is active!</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection is not active.</p>";
    exit;
}

// Test 3: Get database info
$result = $conn->query("SELECT DATABASE() as dbname");
$row = $result->fetch_assoc();
echo "<p style='color: green;'>✓ Connected to database: <strong>" . htmlspecialchars($row['dbname']) . "</strong></p>";

// Test 4: Check if tables exist
$tables = ['users', 'pastries', 'orders'];
echo "<h2>Table Structure:</h2>";
echo "<ul>";

foreach ($tables as $table) {
    $checkTable = $conn->query("SHOW TABLES LIKE '$table'");
    if ($checkTable->num_rows > 0) {
        // Get row count
        $countResult = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        $countRow = $countResult->fetch_assoc();
        echo "<li style='color: green;'>✓ Table '<strong>$table</strong>' exists (Records: {$countRow['count']})</li>";
    } else {
        echo "<li style='color: red;'>✗ Table '<strong>$table</strong>' does not exist</li>";
    }
}
echo "</ul>";

// Test 5: Show users
echo "<h2>Admin Users:</h2>";
$usersResult = $conn->query("SELECT id, username, email, full_name, role, is_active, created_at FROM users");
if ($usersResult->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Role</th><th>Active</th><th>Created At</th></tr>";
    while ($user = $usersResult->fetch_assoc()) {
        $activeStatus = $user['is_active'] ? '✓' : '✗';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($user['username']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>" . $activeStatus . "</td>";
        echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><em>Default login: username=<strong>admin</strong>, password=<strong>admin123</strong></em></p>";
} else {
    echo "<p>No users found.</p>";
}

// Test 6: Show pastries
echo "<h2>Available Pastries:</h2>";
$pastriesResult = $conn->query("SELECT id, name, description, price_per_box, category, is_available FROM pastries");
if ($pastriesResult->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Category</th><th>Available</th></tr>";
    while ($pastry = $pastriesResult->fetch_assoc()) {
        $availableStatus = $pastry['is_available'] ? '✓' : '✗';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($pastry['id']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($pastry['name']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($pastry['description']) . "</td>";
        echo "<td>₱" . number_format($pastry['price_per_box'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($pastry['category']) . "</td>";
        echo "<td>" . $availableStatus . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No pastries found.</p>";
}

// Test 7: Show recent orders
echo "<h2>Recent Orders:</h2>";
$ordersResult = $conn->query("SELECT o.id, o.customer_name, o.customer_email, o.quantity, o.total_price, o.status, o.order_date, p.name as pastry_name 
                               FROM orders o 
                               LEFT JOIN pastries p ON o.pastry_id = p.id 
                               ORDER BY o.order_date DESC 
                               LIMIT 10");
if ($ordersResult->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Order ID</th><th>Customer</th><th>Email</th><th>Pastry</th><th>Qty</th><th>Total</th><th>Status</th><th>Date</th></tr>";
    while ($order = $ordersResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($order['id']) . "</td>";
        echo "<td>" . htmlspecialchars($order['customer_name']) . "</td>";
        echo "<td>" . htmlspecialchars($order['customer_email']) . "</td>";
        echo "<td>" . htmlspecialchars($order['pastry_name']) . "</td>";
        echo "<td>" . htmlspecialchars($order['quantity']) . "</td>";
        echo "<td>₱" . number_format($order['total_price'], 2) . "</td>";
        echo "<td><strong>" . htmlspecialchars($order['status']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($order['order_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found yet.</p>";
}

// Test 8: MySQL version
$versionResult = $conn->query("SELECT VERSION() as version");
$versionRow = $versionResult->fetch_assoc();
echo "<hr>";
echo "<p><strong>MySQL Version:</strong> " . htmlspecialchars($versionRow['version']) . "</p>";
echo "<p><strong>Character Set:</strong> " . $conn->character_set_name() . "</p>";

echo "<hr>";
echo "<h2 style='color: green;'>✓ All tests completed successfully!</h2>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ul>";
echo "<li>Your database is ready to use</li>";
echo "<li>Access phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
echo "<li>Admin login page: <a href='admin_login.html'>admin_login.html</a></li>";
echo "<li>Main website: <a href='index.html'>index.html</a></li>";
echo "</ul>";

// Close connection
$conn->close();
?>
