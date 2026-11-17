<?php
require "auth.php";
require_admin();
include "db.php";

$statuses = ["Pending", "Completed", "Cancelled"];

// Update an order's status
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") === "update_status") {
    $order_id   = (int) $_POST["order_id"];
    $new_status = $_POST["status"] ?? "";

    if (in_array($new_status, $statuses, true)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $back = $_POST["filter"] ?? "";
    header("Location: admin.php?updated=" . $order_id . (in_array($back, $statuses, true) ? "&status=" . $back : "") . "#all-orders");
    exit;
}

// Overview numbers
$counts = ["Pending" => 0, "Completed" => 0, "Cancelled" => 0];
$result = mysqli_query($conn, "SELECT status, COUNT(*) AS n FROM orders GROUP BY status");
while ($row = mysqli_fetch_assoc($result)) {
    $counts[$row["status"]] = $row["n"];
}

// Revenue only counts completed orders
$result  = mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) AS revenue FROM orders WHERE status = 'Completed'");
$revenue = mysqli_fetch_assoc($result)["revenue"];

// Order list (optionally filtered by status)
$filter = $_GET["status"] ?? "";
if (!in_array($filter, $statuses, true)) {
    $filter = "";
}

$sql = "SELECT o.id, o.order_date, o.status, o.total_amount,
               u.first_name, u.last_name, u.email, u.contact_number, u.address,
               p.name AS product_name, oi.quantity
        FROM orders o
        JOIN users u ON u.id = o.user_id
        JOIN order_items oi ON oi.order_id = o.id
        JOIN products p ON p.id = oi.product_id";
if ($filter !== "") {
    $sql .= " WHERE o.status = ?";
}
$sql .= " ORDER BY o.order_date DESC, o.id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($filter !== "") {
    mysqli_stmt_bind_param($stmt, "s", $filter);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row["id"];
    if (!isset($orders[$id])) {
        $orders[$id] = [
            "id"       => $id,
            "date"     => $row["order_date"],
            "status"   => $row["status"],
            "total"    => $row["total_amount"],
            "customer" => $row["first_name"] . " " . $row["last_name"],
            "email"    => $row["email"],
            "contact"  => $row["contact_number"],
            "address"  => $row["address"],
            "items"    => [],
        ];
    }
    $orders[$id]["items"][] = $row["product_name"] . " x" . $row["quantity"];
}
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
        <title>Admin Dashboard | Vonita Pastry</title>
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
                <a href="#all-orders" class ="nav">Orders</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </nav>

            <button class="hamburger" aria-label="Menu">
           <span></span><span></span><span></span>
           </button>
            <div class="mobile-menu">
                <a href="#all-orders" class ="nav">Orders</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </div>
    </header>

 <main>
   <section class="section-white hero-section2" id="order">
    <div class="dashboard-container">
        <h1 class="header-title">Vonita Pastry Overview</h1>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h4 class="dashboard-title">Pending Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($counts["Pending"]); ?></p>
                <p class="dashboard-description">Orders that are still being prepared or awaiting delivery.</p>
            </div>

            <div class="dashboard-card">
                <h4 class="dashboard-title">Completed Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($counts["Completed"]); ?></p>
                <p class="dashboard-description">Orders that have been delivered or picked up.</p>
            </div>

            <div class="dashboard-card">
                <h4 class="dashboard-title">Cancelled Orders</h4>
                <p class="dashboard-number"><?php echo two_digits($counts["Cancelled"]); ?></p>
                <p class="dashboard-description">Orders cancelled by customers or by the shop.</p>
            </div>

            <div class="dashboard-card">
                <h4 class="dashboard-title">Total Revenue</h4>
                <p class="dashboard-number dashboard-money">Php<?php echo number_format($revenue, 2); ?></p>
                <p class="dashboard-description">Total sales from completed orders.</p>
            </div>
        </div>
    </div>
</section>
        <section class="section-light orders-section" id="all-orders">
            <h2 class="section-title1">All Orders</h2>

            <?php if (isset($_GET["updated"])) { ?>
                <p class="cart-notice">Order #<?php echo (int) $_GET["updated"]; ?> updated.</p>
            <?php } ?>

            <div class="orders-container">
                <div class="order-filters">
                    <a href="admin.php#all-orders" class="filter-btn<?php if ($filter === "") echo " active"; ?>">All</a>
                    <?php foreach ($statuses as $s) { ?>
                        <a href="admin.php?status=<?php echo $s; ?>#all-orders" class="filter-btn<?php if ($filter === $s) echo " active"; ?>"><?php echo $s; ?></a>
                    <?php } ?>
                </div>

            <?php if (empty($orders)) { ?>
                <div class="cart-empty">
                    <p>No orders<?php if ($filter !== "") echo " with status " . $filter; ?> yet.</p>
                </div>
            <?php } else { ?>
                <table class="cart-table orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td data-label="Order #">#<?php echo $order["id"]; ?></td>
                            <td data-label="Date"><?php echo date("M j, Y g:i A", strtotime($order["date"])); ?></td>
                            <td data-label="Customer" class="order-customer">
                                <strong><?php echo htmlspecialchars($order["customer"]); ?></strong>
                                <span><?php echo htmlspecialchars($order["contact"]); ?></span>
                                <span><?php echo htmlspecialchars($order["address"]); ?></span>
                            </td>
                            <td data-label="Items"><?php echo htmlspecialchars(implode(", ", $order["items"])); ?></td>
                            <td data-label="Total">Php<?php echo number_format($order["total"], 2); ?></td>
                            <td data-label="Status">
                                <form method="post" class="status-form">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="order_id" value="<?php echo $order["id"]; ?>">
                                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                    <select name="status" class="status-select status-<?php echo strtolower($order["status"]); ?>" onchange="this.form.submit()">
                                        <?php foreach ($statuses as $s) { ?>
                                            <option value="<?php echo $s; ?>"<?php if ($order["status"] === $s) echo " selected"; ?>><?php echo $s; ?></option>
                                        <?php } ?>
                                    </select>
                                    <noscript><button type="submit" class="cart-remove">Save</button></noscript>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
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