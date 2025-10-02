<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Treatx' Pastries</title>
    <link rel="stylesheet" href="pastry_business.css" />
</head>
<body>
    <header>
        <h1>Admin Panel - Treatx'</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="#view-orders">View Orders</a></li>
                <li><a href="#manage-orders">Manage Orders</a></li>
                <li><a href="logout.php" style="color: #ff4444;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Modernized Orders Table -->
    <section id="view-orders" class="admin-section">
        <h2><i class="fas fa-clipboard-list"></i> View Orders</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Pastry</th>
                        <th>Quantity</th>
                        <th>Pickup Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="order-list">
                    <!-- Orders will be populated here from the database -->
                </tbody>
            </table>
        </div>
    </section>

    <section id="manage-orders">
        <h2>Manage Orders</h2>
        <form id="update-order-form">
            <label for="order-id">Order ID:</label>
            <input type="text" id="order-id" name="order-id" required />

            <label for="order-status">Update Status:</label>
            <select id="order-status" name="order-status" required>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <button type="submit">Update Order</button>
        </form>
    </section>

    <footer>
        <p>&copy; 2024 Treatx' Pastries</p>
    </footer>

    <script>
        // Fetch and display orders
        async function fetchOrders() {
            try {
                const response = await fetch('get_orders.php');
                const orders = await response.json();
                
                const orderList = document.getElementById('order-list');
                orderList.innerHTML = '';
                
                orders.forEach(order => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${order.id}</td>
                        <td>${order.name}</td>
                        <td>${order.email}</td>
                        <td>${order.phone || 'N/A'}</td>
                        <td>${order.address}</td>
                        <td>${order.pastry}</td>
                        <td>${order.quantity}</td>
                        <td>${order.pickup_date}</td>
                        <td>₱${order.total_price}</td>
                        <td class="status-${order.status.toLowerCase()}">${order.status}</td>
                    `;
                    orderList.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        }

        // Update order status
        async function updateOrderStatus(orderId, status) {
            try {
                const formData = new FormData();
                formData.append('order_id', orderId);
                formData.append('status', status);
                
                const response = await fetch('update_order.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Order status updated successfully!');
                    fetchOrders(); // Refresh the order list
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating order:', error);
                alert('Network error. Please try again.');
            }
        }

        // Handle update form submission
        document.getElementById('update-order-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const orderId = document.getElementById('order-id').value;
            const status = document.getElementById('order-status').value;
            
            if (orderId && status) {
                await updateOrderStatus(orderId, status);
                e.target.reset();
            } else {
                alert('Please fill in all fields');
            }
        });

        // Fetch orders on page load
        document.addEventListener('DOMContentLoaded', fetchOrders);

        // Auto-refresh orders every 30 seconds
        setInterval(fetchOrders, 30000);
    </script>
</body>
</html>
