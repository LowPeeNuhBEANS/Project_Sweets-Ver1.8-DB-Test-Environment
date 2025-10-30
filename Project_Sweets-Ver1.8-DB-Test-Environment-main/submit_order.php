<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $customer_name = $conn->real_escape_string($_POST['name']);
    $customer_email = $conn->real_escape_string($_POST['email']);
    $customer_phone = $conn->real_escape_string($_POST['phone']);
    $pickup_address = $conn->real_escape_string($_POST['address']);
    $pastry_name = $conn->real_escape_string($_POST['Mochi']);
    $quantity = intval($_POST['quantity']);
    $pickup_date = $conn->real_escape_string($_POST['pickup-date']);

    // Look up pastry_id from pastries table
    $pastry_stmt = $conn->prepare("SELECT id, price_per_box FROM pastries WHERE name = ?");
    $pastry_stmt->bind_param("s", $pastry_name);
    $pastry_stmt->execute();
    $pastry_result = $pastry_stmt->get_result();

    if ($pastry_result->num_rows === 1) {
        $pastry_data = $pastry_result->fetch_assoc();
        $pastry_id = $pastry_data['id'];
        $price_per_box = $pastry_data['price_per_box'];
    } else {
        echo json_encode(["success" => false, "message" => "Invalid pastry selection"]);
        exit;
    }

    $pastry_stmt->close();

    $total_price = $quantity * $price_per_box;

    // Insert order into database
    $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, pickup_address, pastry_id, quantity, pickup_date, total_price)
            VALUES ('$customer_name', '$customer_email', '$customer_phone', '$pickup_address', $pastry_id, $quantity, '$pickup_date', $total_price)";
    
    if ($conn->query($sql)) {
        // Send confirmation email (optional)
        $to = $email;
        $subject = "Order Confirmation - Treatx' Pastries";
        $message = "Dear $name,\n\nThank you for your order!\n\nOrder Details:\n- Pastry: $pastry\n- Quantity: $quantity boxes\n- Total: ₱$total_price\n- Pickup Date: $pickup_date\n\nWe will contact you soon to confirm your order.\n\nBest regards,\nTreatx' Pastries Team";
        $headers = "From: treatx.buiz@gmail.com";
        
        // Uncomment to send email (requires proper email configuration)
        // mail($to, $subject, $message, $headers);
        
        echo json_encode(["success" => true, "message" => "Order submitted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

$conn->close();
?>
