<?php
header('Content-Type: application/json');
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    <?php
    header('Content-Type: application/json');
    include 'db_connection.php';

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        exit;
    }

    // Get and validate form data
    $customer_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $customer_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $customer_phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $pickup_address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $pastry_name = isset($_POST['Mochi']) ? trim($_POST['Mochi']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $pickup_date = isset($_POST['pickup-date']) ? trim($_POST['pickup-date']) : '';

    if ($customer_name === '' || $customer_email === '' || $pickup_address === '' || $pastry_name === '' || $quantity <= 0 || $pickup_date === '') {
        echo json_encode(["success" => false, "message" => "Please fill in all required fields"]);
        exit;
    }

    // Look up pastry_id and price
    $pastry_stmt = $conn->prepare("SELECT id, price_per_box FROM pastries WHERE name = ? LIMIT 1");
    if (!$pastry_stmt) {
        echo json_encode(["success" => false, "message" => "Server error (pastry lookup)"]);
        exit;
    }
    $pastry_stmt->bind_param("s", $pastry_name);
    $pastry_stmt->execute();
    $pastry_result = $pastry_stmt->get_result();

    if ($pastry_result->num_rows !== 1) {
        echo json_encode(["success" => false, "message" => "Invalid pastry selection"]);
        $pastry_stmt->close();
        exit;
    }

    $pastry_data = $pastry_result->fetch_assoc();
    $pastry_id = (int)$pastry_data['id'];
    $price_per_box = (float)$pastry_data['price_per_box'];
    $pastry_stmt->close();

    $total_price = $quantity * $price_per_box;

    // Insert order using prepared statement
    $insert_sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, pickup_address, pastry_id, quantity, pickup_date, total_price)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Server error (order prepare): " . $conn->error]);
        exit;
    }

    $stmt->bind_param('ssssiisd', $customer_name, $customer_email, $customer_phone, $pickup_address, $pastry_id, $quantity, $pickup_date, $total_price);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order submitted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error inserting order: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
    ?>
