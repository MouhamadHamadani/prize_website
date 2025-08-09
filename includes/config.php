<?php
// Main configuration file for Prize Website

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'prize_website_2');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_NAME', 'Prize Website');
define('SITE_URL', 'http://localhost');
define('ADMIN_SESSION_NAME', 'admin_logged_in');
define('USER_SESSION_NAME', 'user_logged_in');

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);

// Prize system settings
define('MAX_PERCENTAGE', 100);
define('MIN_PERCENTAGE', 0);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

