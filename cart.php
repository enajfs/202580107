<?php
require "auth.php";
require_login();
include "db.php";

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    // Add one item from the shop page
    if ($action === "add") {
        $id = (int) $_POST["product_id"];
        if ($id > 0) {
            $_SESSION["cart"][$id] = ($_SESSION["cart"][$id] ?? 0) + 1;
        }
        if (($_POST["from"] ?? "") === "homepage") {
            header("Location: homepage.php?added=1#products");
            exit;
        }
        $category = preg_replace("/[^a-z]/", "", $_POST["category"] ?? "");
        header("Location: shop.php?added=1" . ($category ? "#" . $category . "-section" : ""));
        exit;
    }

    // Change quantities (0 removes the item)
    if ($action === "update") {
        foreach ($_POST["qty"] ?? [] as $id => $qty) {
            $id  = (int) $id;
            $qty = (int) $qty;
            if ($qty <= 0) {
                unset($_SESSION["cart"][$id]);
            } else {
                $_SESSION["cart"][$id] = min($qty, 99);
            }
        }
        header("Location: cart.php");
        exit;
    }

    // Remove one item
    if ($action === "remove") {
        unset($_SESSION["cart"][(int) $_POST["product_id"]]);
        header("Location: cart.php");
        exit;
    }
}

// Load the products that are in the cart
$items = [];
$total = 0;

if (!empty($_SESSION["cart"])) {
    $ids    = array_map("intval", array_keys($_SESSION["cart"]));
    $result = mysqli_query($conn, "SELECT id, name, price, image FROM products WHERE id IN (" . implode(",", $ids) . ")");

    while ($row = mysqli_fetch_assoc($result)) {
        $row["qty"]      = $_SESSION["cart"][$row["id"]];
        $row["subtotal"] = $row["price"] * $row["qty"];
        $total          += $row["subtotal"];
        $items[]         = $row;
    }
}

// Place the order
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") === "checkout") {
    if (empty($items)) {
        $error = "Your cart is empty.";
    } else {
        mysqli_begin_transaction($conn);

        $user_id = $_SESSION["user_id"];
        $status  = "Pending";

        $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, order_date, status, total_amount) VALUES (?, NOW(), ?, ?)");
        mysqli_stmt_bind_param($stmt, "isd", $user_id, $status, $total);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            mysqli_stmt_bind_param($stmt, "iii", $order_id, $item["id"], $item["qty"]);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);

        $_SESSION["cart"] = [];
        header("Location: homepage.php?ordered=" . $order_id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cart | Vonita Pastry</title>
        <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
     </head>

 <body>
   <header>
    <div class="logo-block">
        <img class="logo-img" src="images/vonita-pastry-logo.png" alt="Vonita Pastry Logo">
        <h1 class="hero-logo">Vonita Pastry</h1>
    </div>
            <nav>
                <a href="homepage.php" class ="nav">Home</a>
                <a href="homepage.php#my-orders" class ="nav">My Orders</a> 
                <a href="index.html#about" class ="nav">About</a> 
                <a href="index.html#contact" class ="nav">Contact Us</a>
                <a href="cart.php" class ="nav">Cart (<?php echo cart_count(); ?>)</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </nav>

    <button class="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
           </button>
            <div class="mobile-menu">
                 <a href="homepage.php" class ="nav">Home</a>
                <a href="shop.php" class ="nav">Shop</a> 
                <a href="index.html#about" class ="nav">About</a> 
                <a href="index.html#contact" class ="nav">Contact Us</a>
                <a href="cart.php" class ="nav">Cart (<?php echo cart_count(); ?>)</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </div>
    </header>

    <main class="cart-main">
        <div class="cart-container">
            <h2 class="cart-title">Your Cart</h2>

            <?php if (isset($error)) { ?>
                <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>

            <?php if (empty($items)) { ?>
                <div class="cart-empty">
                    <p>Your cart is empty.</p>
                    <a href="shop.php" class="cart-btn">Browse the Shop</a>
                </div>
            <?php } else { ?>
                <form method="post">
                    <input type="hidden" name="action" value="update">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item) { ?>
                            <tr>
                                <td class="cart-product">
                                    <img src="<?php echo htmlspecialchars($item["image"]); ?>" alt="<?php echo htmlspecialchars($item["name"]); ?>">
                                    <span><?php echo htmlspecialchars($item["name"]); ?></span>
                                </td>
                                <td data-label="Price">Php<?php echo number_format($item["price"], 2); ?></td>
                                <td data-label="Quantity">
                                    <input type="number" class="cart-qty" name="qty[<?php echo $item["id"]; ?>]" value="<?php echo $item["qty"]; ?>" min="0" max="99">
                                </td>
                                <td data-label="Subtotal">Php<?php echo number_format($item["subtotal"], 2); ?></td>
                                <td>
                                    <button type="submit" form="remove-<?php echo $item["id"]; ?>" class="cart-remove">Remove</button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <div class="cart-actions">
                        <a href="shop.php" class="cart-link">&larr; Continue Shopping</a>
                        <button type="submit" class="cart-btn cart-btn-outline">Update Cart</button>
                    </div>
                </form>

                <?php foreach ($items as $item) { ?>
                    <form method="post" id="remove-<?php echo $item["id"]; ?>">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="<?php echo $item["id"]; ?>">
                    </form>
                <?php } ?>

                <div class="cart-summary">
                    <p>Total: <strong>Php<?php echo number_format($total, 2); ?></strong></p>
                    <form method="post">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="cart-btn">Place Order</button>
                    </form>
                </div>
            <?php } ?>
        </div>
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