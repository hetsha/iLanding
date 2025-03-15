<?php
require 'db_connect.php';

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Fetch total distributed profit for the project
    $result = $conn->query("SELECT net_profit, distributed_profit FROM project_profit WHERE project_id = $project_id");
    $project_profit = $result->fetch_assoc();

    if ($project_profit) {
        $net_profit = $project_profit['net_profit'];
        $distributed_profit = $project_profit['distributed_profit'];

        // Fetch the users assigned to the project
        $assigned_users_result = $conn->query("SELECT user_id FROM project_users WHERE project_id = $project_id");
        $total_users = $assigned_users_result->num_rows;

        if ($total_users > 0) {
            // Calculate the distributed amount for each user
            $amount_per_user = $distributed_profit / $total_users;

            // Update the wallet of each user
            while ($user = $assigned_users_result->fetch_assoc()) {
                $user_id = $user['user_id'];

                // Check if the user already has an entry in the user_wallets table
                $check_stmt = $conn->prepare("SELECT * FROM user_wallets WHERE user_id = ?");
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                // If no record exists, insert a new record into the user_wallets table
                if ($check_result->num_rows === 0) {
                    $stmt = $conn->prepare("INSERT INTO user_wallets (user_id, amount, transaction_type, status) VALUES (?, ?, 'deposit', 'approved')");
                    $stmt->bind_param("id", $user_id, $amount_per_user);
                    $stmt->execute();
                } else {
                    // Update the user's existing wallet balance
                    $update_stmt = $conn->prepare("UPDATE user_wallets SET amount = amount + ? WHERE user_id = ?");
                    $update_stmt->bind_param("di", $amount_per_user, $user_id);
                    $update_stmt->execute();
                }
            }

            echo "Profits successfully distributed to users' wallets.";
        } else {
            echo "No users assigned to this project.";
        }
    } else {
        echo "No profit record found for this project.";
    }
}
?>
