<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name     = trim($_POST["first_name"]);
    $last_name      = trim($_POST["last_name"]);
    $email          = trim($_POST["email"]);
    $raw_password   = $_POST["password"];
    $contact_number = trim($_POST["contact_number"]);
    $address        = trim($_POST["address"]);
    $role           = "customer";

    // Validate inputs
    if ($first_name === "" || $last_name === "" || $email === "" || $raw_password === "" || $contact_number === "" || $address === "") {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($raw_password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif (!isset($_POST["terms"])) {
        $error = "You must agree to the Terms & Services.";
    } else {
        // Check if email already exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Email is already registered.";
        } else {
            $password = password_hash($raw_password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (first_name, last_name, email, password, role, contact_number, address)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $first_name, $last_name, $email, $password, $role, $contact_number, $address);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header("Location: login.php?registered=1");
            exit;
        }

        mysqli_stmt_close($check);
    }
}

// Keep typed values in the form when there is an error
function old($key) {
    return isset($_POST[$key]) ? htmlspecialchars($_POST[$key]) : "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Vonita Pastry</title>
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
        <div class="auth-card" style="max-width: 480px;">
            <h4>Create Your Account</h4>
            <p>Sign up to start ordering your dream cake today.</p>

            <?php if (isset($error)) { ?>
                <p style="color: red;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php } ?>

            <form method="post">
                <div class="form-group">
                    <label for="reg-first-name">First Name</label>
                    <input type="text" id="reg-first-name" name="first_name" placeholder="Enter your first name" value="<?php echo old('first_name'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="reg-last-name">Last Name</label>
                    <input type="text" id="reg-last-name" name="last_name" placeholder="Enter your last name" value="<?php echo old('last_name'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="reg-email">Email</label>
                    <input type="email" id="reg-email" name="email" placeholder="Enter your email" value="<?php echo old('email'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input type="password" id="reg-password" name="password" placeholder="Enter your password" minlength="8" required>
                </div>

                <div class="form-group">
                    <label for="reg-contact">Contact Number</label>
                    <input type="text" id="reg-contact" name="contact_number" placeholder="Enter your contact number" value="<?php echo old('contact_number'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="reg-address">Address</label>
                    <textarea id="reg-address" name="address" placeholder="Enter your address" required><?php echo old('address'); ?></textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the Terms &amp; Services</label>
                </div>

                <button type="submit" class="auth-btn">Create Account</button>
            </form>

            <p class="auth-footer-text">Already have an account? <a href="login.php">Log In</a></p>
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
