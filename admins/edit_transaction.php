<?php
require 'db_connect.php';
header('Content-Type: application/json');

// Error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'] ?? null;
    $amount = floatval($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : null;

    // Validate inputs
    if (!$transaction_id || !$amount || !$type) {
        echo json_encode(['success' => false, 'message' => 'Transaction ID, amount, and type are required.']);
        exit;
    }

    $conn->begin_transaction();
    try {
        // Fetch the original transaction
        $stmt = $conn->prepare("SELECT amount, transaction_type, user_id, project_id FROM transactions WHERE transaction_id = ?");
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldTransaction = $result->fetch_assoc();
        $stmt->close();

        if (!$oldTransaction) {
            throw new Exception('Transaction not found.');
        }

        $oldAmount = floatval($oldTransaction['amount']);
        $oldType = $oldTransaction['transaction_type'];
        $oldUserId = $oldTransaction['user_id'];
        $oldProjectId = $oldTransaction['project_id'];

        // Reverse old transaction effect (wallet or project finances)
        if ($oldType === 'deposit' || $oldType === 'withdrawal') {
            $walletUpdate = ($oldType === 'deposit') ? -$oldAmount : $oldAmount;
            $stmt = $conn->prepare("UPDATE user_wallets SET balance = balance + ? WHERE user_id = ?");
            $stmt->bind_param("di", $walletUpdate, $oldUserId);
            $stmt->execute();
            $stmt->close();
        } elseif ($oldType === 'income' || $oldType === 'expense') {
            // Reverse only if project_id exists
            if ($oldProjectId) {
                $stmt = $conn->prepare("DELETE FROM project_finances WHERE project_id = ? AND amount = ? AND type = ?");
                $stmt->bind_param("ids", $oldProjectId, $oldAmount, $oldType);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Apply new transaction effect
        if ($type === 'deposit' || $type === 'withdrawal') {
            if (!$user_id) throw new Exception('User ID is required for deposits and withdrawals.');

            // Check if user_id exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $userResult = $stmt->get_result();
            if ($userResult->num_rows === 0) {
                throw new Exception("User ID $user_id does not exist.");
            }
            $stmt->close();

            // Update user wallet
            $walletUpdate = ($type === 'deposit') ? $amount : -$amount;
            $stmt = $conn->prepare("UPDATE user_wallets SET balance = balance + ? WHERE user_id = ?");
            $stmt->bind_param("di", $walletUpdate, $user_id);
            $stmt->execute();
            $stmt->close();
        } elseif (($type === 'income' || $type === 'expense') && $project_id) {
            // ✅ Only add to `project_finances` if project_id is available
            $stmt = $conn->prepare("INSERT INTO project_finances (project_id, amount, type, description, finance_date, created_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("idss", $project_id, $amount, $type, $description);
            $stmt->execute();
            $stmt->close();
        }

        // ✅ Update the transaction record in `transactions` table (always)
        if ($project_id === null) {
            $stmt = $conn->prepare("UPDATE transactions SET amount = ?, description = ?, transaction_type = ?, user_id = ?, project_id = NULL WHERE transaction_id = ?");
            $stmt->bind_param("dssii", $amount, $description, $type, $user_id, $transaction_id);
        } else {
            $stmt = $conn->prepare("UPDATE transactions SET amount = ?, description = ?, transaction_type = ?, user_id = NULL, project_id = ? WHERE transaction_id = ?");
            $stmt->bind_param("dssii", $amount, $description, $type, $project_id, $transaction_id);
        }
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Transaction updated successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
