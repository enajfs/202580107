<?php
require "auth.php";
require_login();
include "db.php";

$user_id = $_SESSION["user_id"];

// Cancel a pending order (only the user's own, only while still Pending)
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") === "cancel") {
    $order_id = (int) $_POST["order_id"];
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ? AND status = 'Pending'");
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: homepage.php?cancelled=" . $order_id . "#my-orders");
    exit;
}

// Count orders per status
$counts = ["Pending" => 0, "Completed" => 0, "Cancelled" => 0];
$total_orders = 0;

$stmt = mysqli_prepare($conn, "SELECT status, COUNT(*) AS n FROM orders WHERE user_id = ? GROUP BY status");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $counts[$row["status"]] = $row["n"];
    $total_orders += $row["n"];
}
mysqli_stmt_close($stmt);

// Order history with the items in each order
$orders = [];

$stmt = mysqli_prepare($conn,
    "SELECT o.id, o.order_date, o.status, o.total_amount, p.name, oi.quantity
     FROM orders o
     JOIN order_items oi ON oi.order_id = o.id
     JOIN products p ON p.id = oi.product_id
     WHERE o.user_id = ?
     ORDER BY o.order_date DESC, o.id DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row["id"];
    if (!isset($orders[$id])) {
        $orders[$id] = [
            "id"     => $id,
            "date"   => $row["order_date"],
            "status" => $row["status"],
            "total"  => $row["total_amount"],
            "items"  => [],
        ];
    }
    $orders[$id]["items"][] = $row["name"] . " x" . $row["quantity"];
}
mysqli_stmt_close($stmt);

// Featured products on the homepage
$featured = ["Classic Vanilla Bean Cake", "Double Chocolate Cookies", "Rich Chocolate Fudge Cake", "Chocolate Babka"];
$placeholders = implode(",", array_fill(0, count($featured), "?"));
$stmt = mysqli_prepare($conn, "SELECT id, name, price, description, image FROM products WHERE name IN ($placeholders) ORDER BY FIELD(name, $placeholders)");
$params = array_merge($featured, $featured);
mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
mysqli_stmt_execute($stmt);
$featured_products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

function two_digits($n) {
    return str_pad($n, 2, "0", STR_PAD_LEFT);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home | Vonita Pastry</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
     </head>

<body>
    <header>
    <div class="logo-block">
        <img class="logo-img" src="images/vonita-pastry-logo.png" alt="Vonita Pastry Logo">
        <h1 class="hero-logo">Vonita Pastry</h1>
    </div>
            <nav>
                <a href="#my-orders" class ="nav">My Orders</a> 
                <a href="index.html#contact" class ="nav">Contact Us</a>
                <a href="shop.php" class="nav-cta">Shop Now</a>
                <a href="cart.php" class ="nav">Cart (<?php echo cart_count(); ?>)</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </nav>

            <button class="hamburger" aria-label="Menu">
           <span></span><span></span><span></span>
           </button>
            <div class="mobile-menu">
                <a href="#my-orders" class ="nav">My Orders</a> 
                <a href="index.html#contact" class ="nav">Contact Us</a>
                <a href="shop.php" class="nav">Shop Now</a>
                <a href="cart.php" class ="nav">Cart (<?php echo cart_count(); ?>)</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </div>
    </header>

 <main>
        <section class="section-white hero-section2" id="order">
    <div class="dashboard-container">
        <?php if (isset($_GET["ordered"])) { ?>
            <p class="cart-notice">Order #<?php echo (int) $_GET["ordered"]; ?> placed! We'll start preparing it soon.</p>
        <?php } ?>
        <?php if (isset($_GET["cancelled"])) { ?>
            <p class="cart-notice">Order #<?php echo (int) $_GET["cancelled"]; ?> has been cancelled.</p>
        <?php } ?>
        <h1 class="header-title">Welcome back, <?php echo htmlspecialchars($_SESSION["first_name"]); ?>!</h1>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h4 class="dashboard-title">Pending Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($counts["Pending"]); ?></p>
                <p class="dashboard-description">View your current orders that are being processed or awaiting delivery.</p>
            </div>

            <div class="dashboard-card">
                <h4 class="dashboard-title">Completed Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($counts["Completed"]); ?></p>
                <p class="dashboard-description">View your completed orders and download receipts.</p>
            </div>

            <div class="dashboard-card">
                <h4 class="dashboard-title">Cancelled Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($counts["Cancelled"]); ?></p>
                <p class="dashboard-description">View your cancelled orders and reasons for cancellation.</p>
            </div>

            <div class="dashboard-card">
                <h4 class="dashboard-title">Total Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($total_orders); ?></p>
                <p class="dashboard-description">View your complete order history and track your past purchases.</p>
            </div>
        </div>
    </div>
</section>

        <section class="section-light orders-section" id="my-orders">
            <h2 class="section-title1">My Orders</h2>
            <div class="orders-container">
            <?php if (empty($orders)) { ?>
                <div class="cart-empty">
                    <p>You haven't placed any orders yet.</p>
                    <a href="shop.php" class="cart-btn">Start Shopping</a>
                </div>
            <?php } else { ?>
                <table class="cart-table orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td data-label="Order #">#<?php echo $order["id"]; ?></td>
                            <td data-label="Date"><?php echo date("M j, Y g:i A", strtotime($order["date"])); ?></td>
                            <td data-label="Items"><?php echo htmlspecialchars(implode(", ", $order["items"])); ?></td>
                            <td data-label="Total">Php<?php echo number_format($order["total"], 2); ?></td>
                            <td data-label="Status">
                                <span class="status-badge status-<?php echo strtolower($order["status"]); ?>"><?php echo htmlspecialchars($order["status"]); ?></span>
                            </td>
                            <td>
                                <?php if ($order["status"] === "Pending") { ?>
                                    <form method="post" onsubmit="return confirm('Cancel order #<?php echo $order["id"]; ?>?');">
                                        <input type="hidden" name="action" value="cancel">
                                        <input type="hidden" name="order_id" value="<?php echo $order["id"]; ?>">
                                        <button type="submit" class="cart-remove">Cancel</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
            </div>
        </section>


        <section class="section-light" id="products">
                <h2 class="section-title1">Our Products</h2>
                <?php if (isset($_GET["added"])) { ?>
                    <p class="cart-notice">Added to your cart! <a href="cart.php">View cart</a></p>
                <?php } ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); align-items: center; gap: 20px; padding: 20px;">      
                <?php foreach ($featured_products as $product) { ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product["image"]); ?>" alt="<?php echo htmlspecialchars($product["name"]); ?>">
                    <p class="product-title"><?php echo htmlspecialchars($product["name"]); ?></p>
                    <p class="product-description"><?php echo htmlspecialchars($product["description"]); ?></p>
                    <p class="product-price">Php<?php echo number_format($product["price"], 2); ?></p>
                    <form method="post" action="cart.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
                        <input type="hidden" name="from" value="homepage">
                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                </div>
                <?php } ?>
            </div>
            <button class="view-all-btn" onclick="window.location.href='shop.php'">View All Products</button>
        </section>
    
        <section class="section-white faq-section">
    <div class="faq-container">
        <h2 class="section-title1">Frequently Asked Questions</h2>
        <p class="faq-subtitle">Everything you need to know before ordering your favorite treats.</p>

        <div class="faq-list">
            <details class="faq-item">
                <summary class="faq-question">How far in advance should I order?</summary>
                <p class="faq-answer">We recommend placing your order at least 3 days in advance, especially for custom cakes and special events.</p>
            </details>

            <details class="faq-item">
                <summary class="faq-question">Do you deliver outside Puerto Princesa?</summary>
                <p class="faq-answer">Currently, we offer delivery within Puerto Princesa City only.</p>
            </details>

            <details class="faq-item">
                <summary class="faq-question">Can I customize my cake design?</summary>
                <p class="faq-answer">Yes! You can request a custom cake design when placing your order. Please provide your preferred design and details in advance.</p>
            </details>

            <details class="faq-item">
                <summary class="faq-question">What payment methods do you accept?</summary>
                <p class="faq-answer">We accept cash on delivery and available online payment options. Please check the payment options at checkout for details.</p>
            </details>

            <details class="faq-item">
                <summary class="faq-question">Can I cancel or reschedule my order?</summary>
                <p class="faq-answer">Cancellation and rescheduling requests are subject to our Cancellation Policy. Please review the policy or contact us for assistance.</p>
            </details>
        </div>
    </div>
</section>
    </main>

    <footer>
            <div class="footer-columns">
                <div class="footer-col">
                        <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;" >Shop</a>
                        <a href="shop.php#cakes-section" class="footer-nav" >Cakes</a>
                        <a href="shop.php#pastries-section" class="footer-nav" >Pastries</a>
                        <a href="shop.php#cookies-section" class="footer-nav" >Cookies & Treats</a>
                        <a href="shop.php#breads-section" class="footer-nav" >Breads</a>
                </div>
                <div class="footer-col">
                        <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;" >Company</a>
                        <a href="index.html#about" class="footer-nav">About</a>
                        <a href="index.html#faqs" class="footer-nav">FAQs</a>
                        <a href="index.html#contact" class="footer-nav">Contact Us</a>
                        <a href="index.html#contact" class="footer-nav">Location</a>
                </div>
                <div class="footer-col">
                        <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;" >Legal</a>
                        <a class="footer-nav">Privacy Policy</a>
                        <a class="footer-nav">Terms of Service</a>
                        <a class="footer-nav">Refund Policy</a>
                        <a class="footer-nav">Cancellation Policy</a>
                </div>
                <div class="footer-col">
                        <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;" >Follow Us</a>
                        <a class="footer-nav">Facebook</a>
                        <a class="footer-nav">Instagram</a>
                        <a class="footer-nav">TikTok</a>
                </div>
                <div class="footer-col">
                        <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;" >Contact</a>
                        <a class="footer-nav">Email: vonitapastry@gmail.com</a>
                        <a class="footer-nav">Phone: +63 912 345 6789</a>
                        <a class="footer-nav">Address: 123 Main Street, Puerto Princesa City, Palawan</a>
                </div>
            </div>

            <div class="footer-bottom">
                <p style="color: white;">© 2026 Vonita Pastry. All rights reserved.</p>
            </div>
    </footer>
    <script src="script.js"></script>
 </body>
</html>