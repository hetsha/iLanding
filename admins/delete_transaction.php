<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = intval($_POST['transaction_id']);

    // Step 1: Fetch transaction details
    $transactionQuery = $conn->prepare("
        SELECT transaction_id, amount, transaction_type, project_id, user_id
        FROM transactions
        WHERE transaction_id = ?
    ");
    $transactionQuery->bind_param("i", $transaction_id);
    $transactionQuery->execute();
    $transaction = $transactionQuery->get_result()->fetch_assoc();
    $transactionQuery->close();

    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        exit;
    }

    $amount = $transaction['amount'];
    $transaction_type = $transaction['transaction_type'];
    $project_id = $transaction['project_id'];
    $user_id = $transaction['user_id'];

    $conn->begin_transaction();
    try {
        // Case 1: If the transaction is project-related (income/expense)
        if (in_array($transaction_type, ['income', 'expense'])) {
            // Delete the corresponding finance record properly
            $deleteProjectFinance = $conn->prepare("
                DELETE FROM project_finances
                WHERE project_id = ? AND amount = ? AND finance_id = (
                    SELECT finance_id FROM project_finances
                    WHERE project_id = ? AND amount = ?
                    ORDER BY created_at DESC LIMIT 1
                )
            ");
            $deleteProjectFinance->bind_param("idid", $project_id, $amount, $project_id, $amount);
            $deleteProjectFinance->execute();
            $deleteProjectFinance->close();
        }
        // Case 2: If the transaction is user-related (deposit/withdrawal)
        elseif (in_array($transaction_type, ['deposit', 'withdrawal'])) {
            // Reverse the wallet balance adjustment
            $walletAdjustment = $transaction_type === 'deposit' ? -$amount : $amount;
            $updateUserWallet = $conn->prepare("
                UPDATE user_wallets
                SET balance = balance + ?
                WHERE user_id = ?
            ");
            $updateUserWallet->bind_param("di", $walletAdjustment, $user_id);
            $updateUserWallet->execute();
            $updateUserWallet->close();
        }

        // Step 3: Delete the transaction
        $deleteTransaction = $conn->prepare("
            DELETE FROM transactions WHERE transaction_id = ?
        ");
        $deleteTransaction->bind_param("i", $transaction_id);
        $deleteTransaction->execute();
        $deleteTransaction->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Transaction deleted successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error deleting transaction: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
