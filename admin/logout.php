<?php
require_once '../includes/functions.php';

// Clear admin session
if (isset($_SESSION[ADMIN_SESSION_NAME])) {
    unset($_SESSION[ADMIN_SESSION_NAME]);
}

// Destroy session if no other data
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect to admin login
redirect('/admin/login.php');
?>

