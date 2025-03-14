<?php
    require 'db_connect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = ['success' => false, 'message' => ''];

        // Sanitize inputs
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $action = strtolower(trim($_POST['transaction_type']));  // Ensure lowercase and no spaces
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        // Validate amount
        if ($amount <= 0) {
            $response['message'] = 'Invalid amount.';
            echo json_encode($response);
            exit();
        }

        // Validate transaction type
        if (!in_array($action, ['withdraw', 'deposit'])) {
            $response['message'] = 'Invalid transaction type.';
            echo json_encode($response);
            exit();
        }

        // Start transaction to ensure wallet and transaction updates happen together
        $conn->begin_transaction();
        try {
            // Get user's current wallet balance
            $walletQuery = $conn->prepare("SELECT amount FROM user_wallets WHERE user_id = ?");
            $walletQuery->bind_param("i", $user_id);
            $walletQuery->execute();
            $walletResult = $walletQuery->get_result();
            $wallet = $walletResult->fetch_assoc();
            $current_balance = $wallet ? floatval($wallet['amount']) : 0;
            $walletQuery->close();

            // Handle withdraw or deposit logic
            if ($action === 'withdraw') {
                if ($current_balance < $amount) {
                    $conn->rollback();
                    $response['message'] = 'Insufficient funds.';
                    echo json_encode($response);
                    exit();
                }
                $new_balance = $current_balance - $amount;
            } else { // Deposit
                $new_balance = $current_balance + $amount;
            }

            // Update or insert user wallet
            if ($wallet) {
                $updateWallet = $conn->prepare("UPDATE user_wallets SET amount = ? WHERE user_id = ?");
                $updateWallet->bind_param("di", $new_balance, $user_id);
                $updateWallet->execute();
                $updateWallet->close();
            } else {
                $insertWallet = $conn->prepare("INSERT INTO user_wallets (user_id, amount) VALUES (?, ?)");
                $insertWallet->bind_param("id", $user_id, $new_balance);
                $insertWallet->execute();
                $insertWallet->close();
            }

            // Record the transaction
            $stmt = $conn->prepare("
                INSERT INTO transactions (user_id, amount, transaction_type, description, transaction_date)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("idss", $user_id, $amount, $action, $description);
            $stmt->execute();

            // Check if transaction was recorded
            if ($stmt->affected_rows === 0) {
                throw new Exception('Transaction not recorded.');
            }
            $stmt->close();

            // Commit everything if successful
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Transaction successful!';
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Transaction failed: ' . $e->getMessage();
        }

        $conn->close();
        echo json_encode($response);
    }
    ?>
