<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['name'], $_POST['email'])) {
    $user_id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User updated successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to update user.";
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Name and Email are required.";
    }
}

header("Location: user_wallets.php");
exit;
