<?php
// Database Connection
require 'db_connect.php';

// Fetch Dashboard Data
// Total Projects
$projectsQuery = $conn->query("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress,
    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed
    FROM projects");
$projects = $projectsQuery->fetch_assoc();

// Total Transactions
$transactionsQuery = $conn->query("SELECT
    SUM(CASE WHEN transaction_type='income' THEN amount ELSE 0 END) AS total_income,
    SUM(CASE WHEN transaction_type='expense' THEN amount ELSE 0 END) AS total_expense,
    SUM(CASE WHEN transaction_type='deposit' THEN amount ELSE 0 END) AS total_deposits,
    SUM(CASE WHEN transaction_type='withdrawal' THEN amount ELSE 0 END) AS total_withdrawals
    FROM transactions");
$transactions = $transactionsQuery->fetch_assoc();

// Company Profit Calculation
$company_profit = $transactions['total_income'] - $transactions['total_expense'];

// User Wallets
$walletsQuery = $conn->query("SELECT SUM(wallet_balance) AS total_wallet_balance FROM users");
$wallets = $walletsQuery->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Admin Dashboard</h2>

        <!-- Stats Overview -->
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Projects</div>
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $projects['total']; ?></h3>
                        <p>Pending: <?php echo $projects['pending']; ?> | In Progress: <?php echo $projects['in_progress']; ?> | Completed: <?php echo $projects['completed']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Company Profit</div>
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($company_profit, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Withdrawals</div>
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($transactions['total_withdrawals'], 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Wallet Balance</div>
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($wallets['total_wallet_balance'], 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <canvas id="projectStatusChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </div>

    </div>

    <script>
        // Project Status Pie Chart
        var ctx1 = document.getElementById('projectStatusChart').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [<?php echo $projects['pending']; ?>, <?php echo $projects['in_progress']; ?>, <?php echo $projects['completed']; ?>],
                    backgroundColor: ['#ffcc00', '#007bff', '#28a745']
                }]
            }
        });

        // Income vs Expense Bar Chart
        var ctx2 = document.getElementById('incomeExpenseChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Total Income', 'Total Expenses'],
                datasets: [{
                    label: 'Amount ($)',
                    data: [<?php echo $transactions['total_income']; ?>, <?php echo $transactions['total_expense']; ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
