<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Update order status
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    
    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Order status updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

$conn->close();
?>
