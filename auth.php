<?php
// Shared login checks. Include this at the top of pages that need a logged-in user.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function require_admin() {
    require_login();
    if ($_SESSION["role"] !== "admin") {
        header("Location: homepage.php");
        exit;
    }
}
?>
