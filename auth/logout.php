<?php
require_once '../includes/functions.php';

// Clear user session
if (isset($_SESSION[USER_SESSION_NAME])) {
    unset($_SESSION[USER_SESSION_NAME]);
}

// Destroy session if no other data
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect to home page
redirect('/');
?>

