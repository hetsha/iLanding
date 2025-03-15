<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $transaction_type = $_POST['transaction_type'];
    $description = $_POST['description'];
    $transaction_date = date("Y-m-d H:i:s");

    $query = "INSERT INTO transactions (amount, transaction_type, description, transaction_date)
              VALUES ('$amount', '$transaction_type', '$description', '$transaction_date')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Transaction added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding transaction: " . mysqli_error($conn);
    }

    header("Location: transactions_log.php");
    exit();
}
?>
