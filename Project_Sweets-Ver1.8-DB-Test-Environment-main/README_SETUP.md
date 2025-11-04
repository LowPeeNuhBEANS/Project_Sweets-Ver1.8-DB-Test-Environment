# Treatx' Pastries - Database Setup Complete ✓

## 🌐 Your Application URLs

### **Main Port: 80 (HTTP)**
- **Customer Page**: `http://localhost/Project_Sweets-Ver1.8-DB-Test-Environment/index.html`
- **Admin Login**: `http://localhost/Project_Sweets-Ver1.8-DB-Test-Environment/login.php`
- **Admin Panel**: `http://localhost/Project_Sweets-Ver1.8-DB-Test-Environment/admin.php`

### **Database Port: 3306 (MySQL)**
- Database Name: `treatx_orders`
- Host: `localhost`
- Username: `root`
- Password: (empty)

---

## ✅ What's Working Now

### 1. **Database Connection** ✓
- Database `treatx_orders` created
- All required tables exist:
  - `pastries` (7 products configured)
  - `orders` (customer orders with JOIN to pastries)
  - `users` (admin authentication)

### 2. **Customer Order Flow** ✓
- Customer fills form on [index.html](index.html)
- Form submits to [submit_order.php](submit_order.php)
- Order is validated and saved to database
- Pastry pricing is fetched from `pastries` table
- Total price calculated automatically

### 3. **Admin Panel** ✓
- Login page: [login.php](login.php)
  - **Username**: `admin`
  - **Password**: `treatx123`
- Admin dashboard: [admin.php](admin.php)
  - Fetches orders from [get_orders.php](get_orders.php)
  - Displays all orders with JOIN to show pastry names
  - Can update order status via [update_order.php](update_order.php)
  - Auto-refreshes every 30 seconds

---

## 📋 Current Orders in Database

You already have **9 orders** in the system:
- 6 orders from "Jay Jamora"
- 3 sample orders from test data
- Orders are showing correctly in admin panel

---

## 🔧 Files Modified/Created

### Fixed Files:
1. **login.php** - Removed `is_active` column check (not in simple schema)
2. **get_orders.php** - Removed stray HTML form tag

### New Files:
1. **simple_setup.php** - One-click database setup (already run)
2. **test_connection.php** - Database verification tool
3. **README_SETUP.md** - This file

---

## 🚀 How to Use

### For Customers:
1. Go to: `http://localhost/Project_Sweets-Ver1.8-DB-Test-Environment/index.html`
2. Scroll to "Place Your Order" section
3. Fill in details and submit
4. Order will appear in admin panel immediately

### For Admin:
1. Go to: `http://localhost/Project_Sweets-Ver1.8-DB-Test-Environment/login.php`
2. Login with:
   - Username: `admin`
   - Password: `treatx123`
3. View all orders in the dashboard
4. Update order status using the form

---

## 🍰 Available Pastries

| ID | Name | Price |
|----|------|-------|
| 1 | Berry Blush Mochi | ₱50.00 |
| 2 | Berry XD Mochi | ₱50.00 |
| 3 | Cookie Cloud Mochi | ₱50.00 |
| 4 | Sunny Munch Mochi | ₱50.00 |
| 5 | Assorted Mochi | ₱150.00 |
| 6 | Box of Mini Donuts | ₱150.00 |
| 7 | Coming soon.... | ₱0.00 |

---

## 🐛 Troubleshooting

### If orders don't appear:
1. Check Apache is running on port 80
2. Check MySQL is running on port 3306
3. Visit `test_connection.php` to verify database

### If login fails:
- Ensure credentials are exactly: `admin` / `treatx123`
- Check that `users` table has the admin user

### If prices are wrong:
- Prices are pulled from `pastries` table
- Update prices in database, not in HTML

---

## 📊 Database Schema

```
pastries (id, name, price_per_box, description, image_filename, category)
    ↓ (foreign key: pastry_id)
orders (id, customer_name, customer_email, pastry_id, quantity, total_price, status)
    ↑ (JOIN in get_orders.php)
```

---

## ✨ Everything is Connected and Working!

- ✅ Database exists with all tables
- ✅ Pastries configured with correct pricing
- ✅ Orders can be created from customer page
- ✅ Orders appear in admin panel with pastry names
- ✅ Admin can login and manage orders
- ✅ No overengineering - simple and functional!
