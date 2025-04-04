<?php

// Include database connection
require_once 'config.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_id'])) {
    // If not, redirect to the login page
    header("Location: index.php");
    exit();
}
?>
