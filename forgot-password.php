<?php
session_start();
include "db.php";

// Step 1: confirm the account using email + contact number
// Step 2: set a new password
// (XAMPP has no email server, so we verify with the contact number instead of a reset link)

$step = isset($_SESSION["reset_user_id"]) && $_SESSION["reset_expires"] > time() ? 2 : 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "verify") {
        $email   = trim($_POST["email"]);
        $contact = trim($_POST["contact_number"]);

        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND contact_number = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $contact);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $id);

        if (mysqli_stmt_fetch($stmt)) {
            $_SESSION["reset_user_id"] = $id;
            $_SESSION["reset_expires"] = time() + 600; // 10 minutes to finish
            $step = 2;
        } else {
            $error = "We couldn't find an account with that email and contact number.";
            $step  = 1;
        }
        mysqli_stmt_close($stmt);
    }

    if ($action === "reset" && $step === 2) {
        $password = $_POST["password"];
        $confirm  = $_POST["confirm_password"];

        if (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $hash, $_SESSION["reset_user_id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            unset($_SESSION["reset_user_id"], $_SESSION["reset_expires"]);
            header("Location: login.php?reset=1");
            exit;
        }
    }

    if ($action === "start_over") {
        unset($_SESSION["reset_user_id"], $_SESSION["reset_expires"]);
        header("Location: forgot-password.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password | Vonita Pastry</title>
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
                <a href="login.php" class="nav">Log In</a>
                <a href="registration.php" class="nav">Sign Up</a>
            </nav>

   <button class="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
           </button>
         
            <div class="mobile-menu">
                <a href="login.php" class="nav">Log In</a>
                <a href="registration.php" class="nav">Sign Up</a>
            </div>
    </header>

<main class="auth-main">
        <div class="auth-card">
            <h4>Forgot Your Password?</h4>

            <?php if ($step === 1) { ?>
                <p>Enter the email and contact number you used when you signed up.</p>
            <?php } else { ?>
                <p>Account found! Enter your new password below.</p>
            <?php } ?>

            <?php if (isset($error)) { ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>

            <?php if ($step === 1) { ?>
                <form method="post">
                    <input type="hidden" name="action" value="verify">

                    <div class="form-group">
                        <label for="fp-email">Email</label>
                        <input type="email" id="fp-email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="fp-contact">Contact Number</label>
                        <input type="text" id="fp-contact" name="contact_number" placeholder="Enter your contact number" value="<?php echo htmlspecialchars($_POST["contact_number"] ?? ""); ?>" required>
                    </div>

                    <button type="submit" class="auth-btn">Continue</button>
                </form>
            <?php } else { ?>
                <form method="post">
                    <input type="hidden" name="action" value="reset">

                    <div class="form-group">
                        <label for="fp-password">New Password</label>
                        <input type="password" id="fp-password" name="password" placeholder="At least 8 characters" minlength="8" required>
                    </div>

                    <div class="form-group">
                        <label for="fp-confirm">Confirm New Password</label>
                        <input type="password" id="fp-confirm" name="confirm_password" placeholder="Type it again" minlength="8" required>
                    </div>

                    <button type="submit" class="auth-btn">Reset Password</button>
                </form>

                <form method="post">
                    <input type="hidden" name="action" value="start_over">
                    <button type="submit" class="cart-remove" style="margin-top: 16px;">Use a different account</button>
                </form>
            <?php } ?>

            <p class="auth-footer-text">Remember your password? <a href="login.php">Log In</a></p>
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
