<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $updateQuery = "UPDATE users SET name = '$name', email = '$email' WHERE user_id = '$userId'";
    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['success_message'] = "User updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update user.";
    }
    header("Location: user_wallets.php");
    exit();
}
?>
