<?php
// Test database connection and tables
include 'db_connection.php';

echo "<h1>Database Connection Test</h1>";

// Test connection
if ($conn->connect_error) {
    echo "✗ Connection failed: " . $conn->connect_error;
} else {
    echo "✓ Database connected successfully<br><br>";
}

// Show tables
echo "<h2>Tables in database:</h2>";
$result = $conn->query("SHOW TABLES");
echo "<ul>";
while ($row = $result->fetch_array()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

// Show pastries
echo "<h2>Pastries available:</h2>";
$result = $conn->query("SELECT id, name, price_per_box FROM pastries");
echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
echo "<tr><th>ID</th><th>Name</th><th>Price</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>₱" . number_format($row['price_per_box'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show recent orders
echo "<h2>Recent orders:</h2>";
$result = $conn->query("SELECT o.id, o.customer_name, p.name as pastry, o.quantity, o.total_price, o.status
                        FROM orders o
                        JOIN pastries p ON o.pastry_id = p.id
                        ORDER BY o.order_date DESC LIMIT 10");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; padding: 10px;'>";
    echo "<tr><th>Order ID</th><th>Customer</th><th>Pastry</th><th>Qty</th><th>Total</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . $row['pastry'] . "</td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td>₱" . number_format($row['total_price'], 2) . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders yet. Create your first order from the <a href='index.html'>customer page</a>!</p>";
}

$conn->close();
?>
