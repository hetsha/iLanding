<?php
require 'db_connect.php';

if (isset($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']);

    // Fetch financials (replace with your actual queries)
    $income = $conn->query("SELECT SUM(amount) AS total_income FROM project_finances WHERE project_id = $project_id AND type = 'income'")->fetch_assoc()['total_income'] ?? 0;
    $expenses = $conn->query("SELECT SUM(amount) AS total_expenses FROM project_finances WHERE project_id = $project_id AND type = 'expense'")->fetch_assoc()['total_expenses'] ?? 0;
    $profit = $income - $expenses;

    // Fetch transaction list
    $transactions = $conn->query("SELECT description, amount, type, created_at FROM project_finances WHERE project_id = $project_id ORDER BY created_at DESC");

    $transaction_list = [];
    while ($transaction = $transactions->fetch_assoc()) {
        $transaction_list[] = $transaction;
    }

    echo json_encode([
        'income' => $income,
        'expenses' => $expenses,
        'profit' => $profit,
        'transactions' => $transaction_list
    ]);
}
?>
