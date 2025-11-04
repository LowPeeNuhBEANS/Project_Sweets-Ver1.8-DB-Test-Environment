<?php
// Database setup script - Run this once to initialize the database
$servername = "localhost";
$username = "root";
$password = "";

// Create connection without selecting database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read SQL file
$sql = file_get_contents('treatx_database.sql');

// Split into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success_count = 0;
$error_count = 0;
$errors = [];

// Execute each statement
foreach ($statements as $statement) {
    if (!empty($statement)) {
        // Skip comments and empty statements
        if (strpos($statement, '--') === 0 || strpos($statement, '/*') === 0) {
            continue;
        }

        if ($conn->multi_query($statement . ';')) {
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
            $success_count++;
        } else {
            $error_count++;
            $errors[] = "Error executing statement: " . $conn->error . "\nStatement: " . substr($statement, 0, 100) . "...";
        }
    }
}

echo "<h1>Database Setup Complete</h1>";
echo "<p>Successfully executed: $success_count statements</p>";
echo "<p>Errors: $error_count</p>";

if (!empty($errors)) {
    echo "<h2>Errors:</h2><pre>";
    foreach ($errors as $error) {
        echo $error . "\n\n";
    }
    echo "</pre>";
}

// Verify tables were created
$conn->select_db('treatx_orders');
$result = $conn->query("SHOW TABLES");
echo "<h2>Tables in treatx_orders database:</h2><ul>";
while ($row = $result->fetch_array()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

// Check pastries count
$result = $conn->query("SELECT COUNT(*) as count FROM pastries");
$row = $result->fetch_assoc();
echo "<h2>Pastries in database: " . $row['count'] . "</h2>";

$conn->close();

echo "<p><strong>Setup complete! You can now:</strong></p>";
echo "<ul>";
echo "<li>Visit <a href='index.html'>Customer Page</a> to place orders</li>";
echo "<li>Visit <a href='login.php'>Admin Login</a> to manage orders</li>";
echo "<li>Default admin credentials: username='admin', password='treatx123'</li>";
echo "</ul>";
?>
