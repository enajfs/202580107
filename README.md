# Vonita Pastry
> HOME OF THE WORLD'S MOST AESTHETICALLY DELICIOUS CAKE. WE SELL CAKE LOCAL.

## Project Purpose
Serve people by baking cakes they love, with an easy ordering process built for faster, hassle-free celebrations.

Perfect for your celebration needs like:
- Birthday
- Weddings
- Anniversary
- Mother's Day
- Father's Day
- Valentine's Day

## Features
- **User Registration** - Create an account with your name, email, password, contact number, and address. All fields are validated.
- **Login and Logout** - Customers and admins log in on the same page and are sent to their own dashboard.
- **Forgot Password** - Reset your password by confirming your email and contact number.
- **Shop** - Browse cakes, pastries, cookies, and breads loaded from the database.
- **Cart and Checkout** - Add items, change quantities, remove items, and place an order.
- **User Dashboard** - See your pending, completed, and cancelled orders, view your order history, and cancel pending orders.
- **Admin Dashboard** - See order totals and revenue, view every order with customer details, filter by status, and update order status.

## Tech Stack & Tools
- HTML
- CSS
- JavaScript
- PHP
- MySQL (MariaDB)
- XAMPP
- phpMyAdmin
- GitHub
- Visual Studio Code

## Website Pages

| Page | File | Access |
| --- | --- | --- |
| Landing Page | `index.html` | Everyone |
| Registration | `registration.php` | Everyone |
| Login | `login.php` | Everyone |
| Forgot Password | `forgot-password.php` | Everyone |
| Homepage / User Dashboard | `homepage.php` | Logged-in users |
| Shop | `shop.php` | Logged-in users |
| Cart | `cart.php` | Logged-in users |
| Admin Dashboard | `admin.php` | Admins only |

### Landing Page
The landing page is the first page visitors will see.

**Header**
- Menu
- Logo
- Description
- Photo hero

**Body**
- Product Brief
- Why Vonita Pastry
- Reviews
- CTA
- FAQs

**Footer**
- Shop
- About
- FAQs
- Location
- Contact
- Social Media Links
- Privacy Policy
- Refund Policy
- Cancellation Policy
- Terms Of Service
- © 2026 VONITA PASTRY

### Registration Page
Allows the user to create their account.

**Registration Form Fields**
- First Name
- Last Name
- Email
- Password (at least 8 characters)
- Contact Number
- Address
- Tick box for Terms & Services

Passwords are stored as secure hashes using PHP `password_hash()`. An email can only be registered once.

### Login Page
Allows the registered user to access their account.

**Login Form Fields**
- Email
- Password
- Forgot Password link

Customers are sent to the Homepage. Admins are sent to the Admin Dashboard.

### Forgot Password Page
1. Enter the email and contact number used during registration.
2. If they match an account, enter and confirm a new password.
3. Log in with the new password.

### Homepage / User Dashboard
Only available after the user logs in.
- Welcome message with the user's name
- Pending, Completed, Cancelled, and Total Orders
- My Orders (order history with items, total, and status)
- Cancel button for pending orders
- Featured products
- FAQs

### Shop Page
- Category tabs: Cakes, Pastries, Cookies & Treats, Breads
- Products are loaded from the `products` table
- Add to Cart button on every product

### Cart Page
- List of items with price, quantity, and subtotal
- Update quantities or remove items
- Order total
- Place Order button

### Admin Dashboard Page
- Pending, Completed, and Cancelled Orders
- Total Revenue (completed orders only)
- All Orders with customer name, contact number, address, and items
- Filter orders by status
- Update order status (Pending, Completed, Cancelled)

## How It Works
1. Create an account by accessing the registration form.
2. Log in to your account.
3. Browse the shop page to find the products you want.
4. Click Add to Cart on the items you like.
5. Open your cart, adjust the quantities, and click Place Order.
6. Track your order status in My Orders on the homepage.
7. The admin updates the order status from the Admin Dashboard.

## Database

Database name: `vonita_pastry`

| Table | Description |
| --- | --- |
| `users` | Customer and admin accounts |
| `products` | Items sold in the shop |
| `orders` | One row per order (date, status, total) |
| `order_items` | The products and quantities in each order |

## Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache, PHP, and MySQL)

### Installation
1. Clone the repository into the XAMPP `htdocs` folder:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/enajfs/202580107.git
   ```
2. Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
3. Go to http://localhost/phpmyadmin, click **Import**, choose `vonita_pastry.sql` from the project folder, and click **Import**. This creates the database, all tables, the products, and the admin account.

### Running Locally
Open http://localhost/202580107/ in your browser.

### Default Admin Account
| Email | Password |
| --- | --- |
| admin@vonitapastry.com | admin12345 |

Customers can create their own account on the registration page.

## Configuration
Database settings are in `db.php`:

| Setting | Description | Default |
| --- | --- | --- |
| `$host` | Database server | `localhost` |
| `$user` | Database username | `root` |
| `$pass` | Database password | *(empty)* |
| `$dbname` | Database name | `vonita_pastry` |
