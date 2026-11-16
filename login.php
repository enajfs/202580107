<?php
session_start();
include "db.php";

// Already logged in? Go straight to the right page
if (isset($_SESSION["user_id"])) {
    header("Location: " . ($_SESSION["role"] === "admin" ? "admin.php" : "homepage.php"));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare($conn, "SELECT id, first_name, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $id, $first_name, $hash, $role);

    if (mysqli_stmt_fetch($stmt) && password_verify($password, $hash)) {
        session_regenerate_id(true);
        $_SESSION["user_id"]    = $id;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["role"]       = $role;

        if ($role === "admin") {
            header("Location: admin.php");
        } else {
            header("Location: homepage.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vonita Pastry</title>

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
            <a href="index.html" class="nav">(Landing)</a>
            <a href="login.php" class="nav">Log In</a>
            <a href="registration.php" class="nav">Sign Up</a>
        </nav>

        <button class="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <div class="mobile-menu">
            <a href="index.html" class="nav">(Landing)</a>
            <a href="login.php" class="nav">Log In</a>
            <a href="registration.php" class="nav">Sign Up</a>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-card">

            <h4>Welcome Back</h4>

            <p>Log in to your account to place an order or check your order history.</p>

            <?php if (isset($_GET["registered"])) { ?>
                <p style="color: green;">Account created! You can now log in.</p>
            <?php } ?>

            <?php if (isset($error)) { ?>
                <p style="color: red;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php } ?>

            <form method="post">

                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Enter your password" required>
                </div>

                <a href="forgot-password.html" class="auth-link">Forgot Password?</a>

                <button type="submit" class="auth-btn">Log In</button>

            </form>

            <p class="auth-footer-text">
                Don't have an account yet? <a href="registration.php">Sign Up</a>
            </p>

        </div>
    </main>

    <footer>
        <div class="footer-columns">

            <div class="footer-col">
                <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;">Shop</a>
                <a href="shop.php#cakes-section" class="footer-nav">Cakes</a>
                <a href="shop.php#pastries-section" class="footer-nav">Pastries</a>
                <a href="shop.php#cookies-section" class="footer-nav">Cookies & Treats</a>
                <a href="shop.php#breads-section" class="footer-nav">Breads</a>
            </div>

            <div class="footer-col">
                <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;">Company</a>
                <a href="index.html#about" class="footer-nav">About</a>
                <a href="index.html#faqs" class="footer-nav">FAQs</a>
                <a href="index.html#contact" class="footer-nav">Contact Us</a>
                <a href="index.html#contact" class="footer-nav">Location</a>
            </div>

            <div class="footer-col">
                <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;">Legal</a>
                <a class="footer-nav">Privacy Policy</a>
                <a class="footer-nav">Terms of Service</a>
                <a class="footer-nav">Refund Policy</a>
                <a class="footer-nav">Cancellation Policy</a>
            </div>

            <div class="footer-col">
                <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;">Follow Us</a>
                <a class="footer-nav">Facebook</a>
                <a class="footer-nav">Instagram</a>
                <a class="footer-nav">TikTok</a>
            </div>

            <div class="footer-col">
                <a class="footer-nav" style="font-weight: bold; letter-spacing: 2px;">Contact</a>
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
