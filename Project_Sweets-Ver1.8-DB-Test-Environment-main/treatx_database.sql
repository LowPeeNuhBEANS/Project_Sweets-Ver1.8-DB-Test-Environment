-- Treatx' Pastries Database Schema
-- Created for Treatx' Pastries Order Management System

-- Create database
CREATE DATABASE IF NOT EXISTS treatx_orders;
USE treatx_orders;

-- Users table for admin authentication
CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Pastry types table
CREATE TABLE IF NOT EXISTS pastries (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    price_per_box DECIMAL(10,2) NOT NULL DEFAULT 150.00,
    image_filename VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    category ENUM('mochi', 'donuts', 'special') DEFAULT 'mochi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table (main table)
CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    pickup_address TEXT NOT NULL,
    pastry_id INT(6) UNSIGNED NOT NULL,
    quantity INT(3) NOT NULL CHECK (quantity > 0),
    pickup_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled') DEFAULT 'Pending',
    payment_status ENUM('Pending', 'Paid', 'Partial', 'Refunded') DEFAULT 'Pending',
    payment_method ENUM('Cash', 'GCash', 'Bank Transfer', 'Credit Card') DEFAULT 'Cash',
    special_instructions TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pastry_id) REFERENCES pastries(id) ON DELETE RESTRICT
);

-- Order status history table
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    status ENUM('Pending', 'Confirmed', 'In Progress', 'Ready for Pickup', 'Completed', 'Cancelled') NOT NULL,
    changed_by INT(6) UNSIGNED,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Customer queries/contact form submissions
CREATE TABLE IF NOT EXISTS customer_queries (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('New', 'In Progress', 'Resolved') DEFAULT 'New',
    assigned_to INT(6) UNSIGNED,
    response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Inventory tracking (optional for future expansion)
CREATE TABLE IF NOT EXISTS inventory (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pastry_id INT(6) UNSIGNED NOT NULL,
    quantity INT(6) NOT NULL DEFAULT 0,
    minimum_stock_level INT(6) DEFAULT 10,
    last_restocked TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pastry_id) REFERENCES pastries(id) ON DELETE CASCADE
);

-- Insert default admin user
-- Password: treatx123 (hashed with bcrypt)
INSERT INTO users (username, password_hash, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'treatx.buiz@gmail.com', 'Treatx Administrator', 'admin');

-- Insert pastry types with correct pricing
INSERT INTO pastries (name, description, price_per_box, image_filename, category) VALUES
('Berry Blush Mochi', '🍓 Berry Blush – A strawberry cheesecake in mochi form, sweet berries and tangy cream cheese for a refreshing classic bite.', 50.00, 'red.jpg', 'mochi'),
('Berry XD Mochi', '🍇 Berry XD – A burst of juicy blueberries balanced with velvety cream cheese; just like your favorite blueberry cheesecake.', 50.00, 'blue.jpg', 'mochi'),
('Cookie Cloud Mochi', '🍪 Cookie Cloud – Oreo cheesecake vibes in every bite, crushed cookies blended into smooth cream cheese inside pillowy mochi.', 50.00, 'brown.jpg', 'mochi'),
('Sunny Munch Mochi', '🥭 Sunny Munch – A tropical cheesecake twist! sweet mango meets creamy cheese for a sunny, melt-in-your-mouth experience.', 50.00, 'mango.jpg', 'mochi'),
('Assorted Mochi', 'Mixed variety of our delicious mochi flavors.', 50.00, 'assorted.jpg', 'mochi'),
('Box of Mini Donuts', 'Assorted mini donuts in various flavors and toppings.', 150.00, 'mochi.jpg', 'donuts'),
('Coming soon....', 'New pastry items coming soon!', 0.00, NULL, 'special');

-- Insert initial inventory
INSERT INTO inventory (pastry_id, quantity, minimum_stock_level) VALUES
(1, 50, 10), -- Berry Blush Mochi
(2, 50, 10), -- Berry XD Mochi
(3, 50, 10), -- Cookie Cloud Mochi
(4, 50, 10), -- Sunny Munch Mochi
(5, 50, 10), -- Assorted Mochi
(6, 30, 5);  -- Box of Mini Donuts

-- Create indexes for better performance
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_customer_email ON orders(customer_email);
CREATE INDEX idx_orders_pickup_date ON orders(pickup_date);
CREATE INDEX idx_orders_order_date ON orders(order_date);
CREATE INDEX idx_pastries_name ON pastries(name);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_customer_queries_status ON customer_queries(status);
CREATE INDEX idx_order_status_history_order_id ON order_status_history(order_id);

-- Create views for common queries
CREATE VIEW order_details AS
SELECT 
    o.id,
    o.customer_name,
    o.customer_email,
    o.customer_phone,
    o.pickup_address,
    p.name as pastry_name,
    o.quantity,
    o.pickup_date,
    o.total_price,
    o.status,
    o.payment_status,
    o.payment_method,
    o.order_date,
    o.updated_at
FROM orders o
JOIN pastries p ON o.pastry_id = p.id;

CREATE VIEW low_stock_alert AS
SELECT 
    p.name as pastry_name,
    i.quantity,
    i.minimum_stock_level,
    i.last_restocked
FROM inventory i
JOIN pastries p ON i.pastry_id = p.id
WHERE i.quantity <= i.minimum_stock_level AND p.is_available = TRUE;

-- Create stored procedures
DELIMITER //

CREATE PROCEDURE GetOrdersByStatus(IN status_filter VARCHAR(20))
BEGIN
    SELECT * FROM order_details WHERE status = status_filter ORDER BY order_date DESC;
END //

CREATE PROCEDURE UpdateOrderStatus(IN order_id INT, IN new_status VARCHAR(20), IN user_id INT, IN notes TEXT)
BEGIN
    -- Update the order status
    UPDATE orders SET status = new_status WHERE id = order_id;
    
    -- Record the status change in history
    INSERT INTO order_status_history (order_id, status, changed_by, notes)
    VALUES (order_id, new_status, user_id, notes);
END //

CREATE PROCEDURE GetDailyOrders(IN target_date DATE)
BEGIN
    SELECT * FROM order_details WHERE DATE(order_date) = target_date ORDER BY order_date DESC;
END //

CREATE PROCEDURE GetMonthlyRevenue(IN year INT, IN month INT)
BEGIN
    SELECT 
        MONTH(order_date) as month,
        SUM(total_price) as total_revenue,
        COUNT(*) as total_orders
    FROM orders 
    WHERE YEAR(order_date) = year AND MONTH(order_date) = month
    GROUP BY MONTH(order_date);
END //

DELIMITER ;

-- Create triggers
DELIMITER //

CREATE TRIGGER before_order_insert
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    -- Ensure pickup date is not in the past
    IF NEW.pickup_date < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Pickup date cannot be in the past';
    END IF;
    
    -- Ensure quantity is positive
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Quantity must be greater than 0';
    END IF;
END //

CREATE TRIGGER after_order_status_change
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO order_status_history (order_id, status, notes)
        VALUES (NEW.id, NEW.status, CONCAT('Status changed from ', OLD.status, ' to ', NEW.status));
    END IF;
END //

CREATE TRIGGER after_inventory_update
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    IF NEW.quantity <= NEW.minimum_stock_level THEN
        -- Log low stock alert (could be extended to send notifications)
        INSERT INTO system_logs (message, level) 
        VALUES (CONCAT('Low stock alert for pastry ID: ', NEW.pastry_id, '. Current quantity: ', NEW.quantity), 'WARNING');
    END IF;
END //

DELIMITER ;

-- System logs table (for future expansion)
CREATE TABLE IF NOT EXISTS system_logs (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    level ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') DEFAULT 'INFO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_system_logs_level (level),
    INDEX idx_system_logs_created_at (created_at)
);

-- Insert sample orders for testing
INSERT INTO orders (customer_name, customer_email, customer_phone, pickup_address, pastry_id, quantity, pickup_date, total_price, status, payment_status) VALUES
('Juan Dela Cruz', 'juan.delacruz@email.com', '09171234567', '123 Main Street, Quezon City', 1, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 300.00, 'Pending', 'Pending'),
('Maria Santos', 'maria.santos@email.com', '09182345678', '456 Oak Avenue, Makati City', 3, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 150.00, 'Confirmed', 'Paid'),
('Pedro Reyes', 'pedro.reyes@email.com', '09293456789', '789 Pine Road, Manila', 6, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 450.00, 'In Progress', 'Partial');

-- Insert sample status history
INSERT INTO order_status_history (order_id, status, notes) VALUES
(2, 'Pending', 'Order received'),
(2, 'Confirmed', 'Order confirmed and payment received'),
(3, 'Pending', 'Order received'),
(3, 'Confirmed', 'Order confirmed'),
(3, 'In Progress', 'Order being prepared');

-- Display database creation confirmation
SELECT 'Treatx'' Pastries Database created successfully!' as Message;
SELECT COUNT(*) as 'Total Tables Created' FROM information_schema.tables WHERE table_schema = 'treatx_orders';
