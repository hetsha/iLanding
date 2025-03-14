<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch inputs safely
    $project_id = isset($_POST['project_id']) && $_POST['project_id'] !== "" ? $_POST['project_id'] : NULL;
    $user_id = isset($_POST['user_id']) && $_POST['user_id'] !== "" ? $_POST['user_id'] : NULL;
    $amount = floatval($_POST['amount']);
    $transaction_type = $_POST['transaction_type']; // 'income', 'expense', 'deposit', 'withdraw'
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate the transaction type
    $allowed_types = ['income', 'expense', 'deposit', 'withdraw'];
    if (!in_array($transaction_type, $allowed_types)) {
        header("Location: financials.php?error=Invalid transaction type");
        exit();
    }

    // Start transaction to ensure both inserts succeed
    $conn->begin_transaction();

    try {
        // Insert into transactions table
        $stmt = $conn->prepare("
            INSERT INTO transactions (project_id, user_id, amount, transaction_type, description, transaction_date)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iidss", $project_id, $user_id, $amount, $transaction_type, $description);
        $stmt->execute();
        $stmt->close();

        // Insert into project_finances if project_id is provided
        if ($project_id !== NULL && in_array($transaction_type, ['income', 'expense'])) {
            $financeStmt = $conn->prepare("
                INSERT INTO project_finances (project_id, amount, type, description)
                VALUES (?, ?, ?, ?)
            ");
            $financeStmt->bind_param("idss", $project_id, $amount, $transaction_type, $description);
            $financeStmt->execute();
            $financeStmt->close();
        }

        // Commit transaction if both inserts succeed
        $conn->commit();
        header("Location: financials.php?success=Transaction added successfully");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: financials.php?error=Failed to add transaction: " . $e->getMessage());
    }

    $conn->close();
}
?>
