<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy session if active
if (session_status() == PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Redirect to home page
header("Location: /../index.php");
exit();
?>