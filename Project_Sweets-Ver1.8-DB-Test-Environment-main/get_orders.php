<?php
include 'db_connection.php';

header('Content-Type: application/json');

// Get all orders from database with pastry names
$sql = "SELECT 
    o.id,
    o.customer_name AS name,
    o.customer_email AS email,
    o.customer_phone AS phone,
    o.pickup_address AS address,
    p.name AS pastry,
    o.quantity,
    o.pickup_date,
    o.total_price,
    o.status
FROM orders o
JOIN pastries p ON o.pastry_id = p.id
ORDER BY o.order_date DESC";
$result = $conn->query($sql);

$orders = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

echo json_encode($orders);

$conn->close();
?>
