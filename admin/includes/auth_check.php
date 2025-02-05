<?php

// Include database connection
require_once 'config/db.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // If not, redirect to the login page
    header("Location: index.php");
    exit();
}
?>
