<?php
// Include necessary files for DB connection and session management
include 'config.php';
session_start();

// Fetch total income, expenses, and profit
$totalIncomeQuery = "SELECT SUM(amount) as total_income FROM transactions WHERE transaction_type = 'income'";
$totalExpenseQuery = "SELECT SUM(amount) as total_expense FROM transactions WHERE transaction_type = 'expense'";

$totalIncomeResult = mysqli_query($conn, $totalIncomeQuery);
$totalExpenseResult = mysqli_query($conn, $totalExpenseQuery);

$totalIncome = mysqli_fetch_assoc($totalIncomeResult)['total_income'];
$totalExpense = mysqli_fetch_assoc($totalExpenseResult)['total_expense'];
$totalProfit = $totalIncome - $totalExpense;

// Fetch active projects
$projectsQuery = "SELECT * FROM projects WHERE status = 'in_progress'";
$projectsResult = mysqli_query($conn, $projectsQuery);
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Income</div>
                <div class="card-body"><?php echo number_format($totalIncome, 2); ?> USD</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Expenses</div>
                <div class="card-body"><?php echo number_format($totalExpense, 2); ?> USD</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Profit</div>
                <div class="card-body"><?php echo number_format($totalProfit, 2); ?> USD</div>
            </div>
        </div>
    </div>

    <h3>Active Projects</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($project = mysqli_fetch_assoc($projectsResult)) { ?>
                <tr>
                    <td><?php echo $project['name']; ?></td>
                    <td><?php echo $project['status']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
