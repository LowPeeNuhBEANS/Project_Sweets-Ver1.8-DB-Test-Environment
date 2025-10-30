# Treatx' Pastries - Admin Panel Setup Guide

## Prerequisites
1. XAMPP or WAMP server installed on your computer
2. MySQL database server running
3. Web server (Apache) running

## Setup Steps

### 1. Start Your Local Server
- Open XAMPP/WAMP control panel
- Start Apache and MySQL services

### 2. Place Files in Web Directory
- Copy all files to your web server directory:
  - XAMPP: `C:\xampp\htdocs\treatx\`
  - WAMP: `C:\wamp\www\treatx\`

### 3. Test the Setup
1. Open your web browser and go to: `http://localhost/treatx/`
2. Test the order form by submitting a sample order
3. Check the admin panel at: `http://localhost/treatx/admin.html`

### 4. Database Configuration
The system will automatically:
- Create a database named `treatx_orders`
- Create an `orders` table with the necessary structure
- Use default MySQL credentials (root user with no password)

### 5. Customize Database Credentials (Optional)
If you need to change database credentials, edit `db_connection.php`:
```php
$servername = "localhost";
$username = "your_username"; // Change this
$password = "your_password"; // Change this
$dbname = "treatx_orders";
```

## Features

### Customer Side (`index.html`)
- Order form that submits to MySQL database
- Real-time price calculation
- Form validation
- Order confirmation messages

### Admin Panel (`admin.html`)
- View all orders in a table format
- Update order status (Pending, Completed, Cancelled)
- Auto-refresh every 30 seconds
- Responsive design for mobile devices

### Database Structure
The `orders` table includes:
- Order ID (auto-increment)
- Customer information (name, email, phone, address)
- Order details (pastry type, quantity, pickup date)
- Total price calculation
- Order status and timestamp

## Troubleshooting

### Common Issues:
1. **Database connection error**: Make sure MySQL is running
2. **Permission denied**: Check file permissions in web directory
3. **Form not submitting**: Check if JavaScript is enabled in browser

### Testing:
1. Submit a test order through the main website
2. Check the admin panel to see if the order appears
3. Try updating the order status

## Security Notes
- This is a basic implementation for local development
- For production use, consider:
  - Adding proper input validation and sanitization
  - Implementing user authentication for admin panel
  - Using prepared statements for database queries
  - Setting up proper file permissions

## Support
If you encounter any issues, check:
- PHP error logs in XAMPP/WAMP
- Browser console for JavaScript errors
- MySQL error logs

Enjoy managing your pastry orders! 🍰
