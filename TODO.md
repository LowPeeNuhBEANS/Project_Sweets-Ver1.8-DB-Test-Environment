# TODO: Make Website Fully Functional with Database Connections

## Tasks
- [x] Update login.php to authenticate against users table in database, restrict to role 'admin'
- [x] Update admin.php to check for new session variable 'logged_in'
- [x] Fix submit_order.php to insert correct column names and pastry_id
- [x] Fix get_orders.php to join with pastries table and alias columns for admin display
- [x] Test the full client-admin flow

## Completed
- [x] Analyze current login system and database schema
- [x] Create plan for database integration
- [x] Implement database schema fixes


✔️ How Your System Works
Customer places an order on index.html

The form submits to submit_order.php.
submit_order.php inserts the order into the orders table in your database.
Admin views orders on admin.php (or admin.html if you want static, but PHP is better)

The admin panel fetches orders from the database using get_orders.php.
Orders are displayed in a table, live from the database.
🟢 What You Need To Check
1. Order Form Submits Correctly
The form in index.html should have:
The JS in index.html already uses AJAX to submit the form and shows a message.
2. submit_order.php Inserts Into Database
Your submit_order.php is correct and inserts into the orders table.
3. Admin Panel Fetches and Shows Orders
Your admin.php (or admin.html with JS) fetches orders from get_orders.php and displays them in a table.
The table headers and JS code are correct.
4. Database Schema Matches
Your orders table has all the columns needed (customer_name, pastry_id, quantity, etc.).
The pastries table has all the pastry names and IDs.
🟢 What Happens When You Place an Order
Order is added to the database.
Admin panel (admin.php) will show the new order automatically (after refresh or auto-refresh).
🟡 If You Use admin.html Instead of admin.php
admin.html will work for viewing orders if you keep the JS that fetches from get_orders.php.
But for security (login required), use admin.php.
🟢 Final Checklist
 Place an order on the website.
 Log in as admin and go to admin.php.
 See the new order in the table.
🟠 Troubleshooting
If orders do not appear:
Check your browser console for errors.
Check your database to see if the order was inserted.
Make sure get_orders.php is returning the correct data (visit it directly in your browser to test).
Make sure your admin panel is loading get_orders.php and updating the table.
✅ You are all set!
When you add an order using the client side (index.html), it will appear in the database and on the admin side (admin.php).

If you want to use admin.html instead of admin.php, just make sure you copy the JS code from admin.php and understand that it won’t have login protection.


