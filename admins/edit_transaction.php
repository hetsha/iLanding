<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    // Validate inputs
    if (empty($transaction_id) || empty($amount) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Update query
    $stmt = $conn->prepare("UPDATE transactions SET amount = ?, description = ? WHERE transaction_id = ?");
    $stmt->bind_param("dsi", $amount, $description, $transaction_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Transaction updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update transaction.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
