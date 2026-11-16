<?php
require "auth.php";
require_login();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shop | Vonita Pastry</title>
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
                <a href="index.html"class ="nav">(Landing)</a>
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
          </nav>
            <div class="mobile-menu">
                 <a href="homepage.php" class ="nav">Home</a>
                <a href="shop.php" class ="nav">Shop</a> 
                <a href="index.html#about" class ="nav">About</a> 
                <a href="index.html#contact" class ="nav">Contact Us</a>
                <a href="cart.php" class ="nav">Cart (<?php echo cart_count(); ?>)</a>
                <a href="logout.php" class ="nav">Log Out</a>
            </div>
    </header>

        <section class="section-light">
            <?php if (isset($_GET["added"])) { ?>
                <p class="cart-notice">Added to your cart! <a href="cart.php">View cart</a></p>
            <?php } ?>
            <nav class="category-tabs">
                <button class="tab-arrow-left">&lt;</button>
                <button class="tab-btn active" data-category="cakes">Cakes</button>
                <button class="tab-btn" data-category="pastries">Pastries</button>
                <button class="tab-btn" data-category="cookies">Cookies & Treats</button>
                <button class="tab-btn" data-category="breads">Breads</button>
                <button class="tab-arrow-right">&gt;</button>
            </nav>
            <?php
            // Category key in the database => heading shown on the page
            $categories = [
                "cakes"    => "Our Cakes",
                "pastries" => "Our Pastries",
                "cookies"  => "Our Cookies and Treats",
                "breads"   => "Our Breads",
            ];

            $first = true;
            foreach ($categories as $key => $heading) {
                $stmt = mysqli_prepare($conn, "SELECT id, name, price, description, image FROM products WHERE category = ? ORDER BY id");
                mysqli_stmt_bind_param($stmt, "s", $key);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            ?>
            <section id="<?php echo $key; ?>-section" class="category-section"<?php if (!$first) echo ' style="display: none;"'; ?>>
                <h2 class="product-category"><?php echo $heading; ?></h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <?php while ($product = mysqli_fetch_assoc($result)) { ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product["image"]); ?>" alt="<?php echo htmlspecialchars($product["name"]); ?>">
                        <p class="product-title"><?php echo htmlspecialchars($product["name"]); ?></p>
                        <p class="product-description"><?php echo htmlspecialchars($product["description"]); ?></p>
                        <p class="product-price">Php<?php echo number_format($product["price"], 2); ?></p>
                        <form method="post" action="cart.php">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
                            <input type="hidden" name="category" value="<?php echo $key; ?>">
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php } ?>
                </div>
            </section>
            <?php
                mysqli_stmt_close($stmt);
                $first = false;
            }
            ?>
        </section>

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
                        <a class="footer-nav">Email: vonitapastry@gmail.com</p>
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