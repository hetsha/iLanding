<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// 1. Fetch Project Data
$projectQuery = "
    SELECT p.project_id, p.name,
        SUM(CASE WHEN pf.type = 'income' THEN pf.amount ELSE 0 END) AS project_income,
        SUM(CASE WHEN pf.type = 'expense' THEN pf.amount ELSE 0 END) AS project_expense
    FROM projects p
    LEFT JOIN project_finances pf ON p.project_id = pf.project_id
    GROUP BY p.project_id, p.name
    ORDER BY p.name ASC";
$projectResult = mysqli_query($conn, $projectQuery);

// 2. Fetch User Data
// Fetch User Data with Correct Wallet Balance
$userQuery = "
    SELECT
        u.user_id,
        u.name,
        COALESCE(SUM(
            CASE
                WHEN w.transaction_type = 'deposit' THEN w.amount
                WHEN w.transaction_type = 'withdrawal' THEN -w.amount
                ELSE 0
            END
        ), 0) AS wallet_balance
    FROM users u
    LEFT JOIN user_wallets w ON u.user_id = w.user_id
    GROUP BY u.user_id, u.name";
$userResult = mysqli_query($conn, $userQuery);


// 3. Fetch Financial Records
$financeQuery = "
    SELECT pf.*, p.name AS project_name
    FROM project_finances pf
    JOIN projects p ON pf.project_id = p.project_id
    ORDER BY pf.finance_id DESC";
$financeResult = mysqli_query($conn, $financeQuery);

// Prepare data for charts
$projectNames = [];
$projectIncomes = [];
$projectExpenses = [];
$userNames = [];
$userBalances = [];

while ($project = mysqli_fetch_assoc($projectResult)) {
    $projectNames[] = $project['name'];
    $projectIncomes[] = $project['project_income'];
    $projectExpenses[] = $project['project_expense'];
}

while ($user = mysqli_fetch_assoc($userResult)) {
    $userNames[] = $user['name'];
    $userBalances[] = $user['wallet_balance'];
}


?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Report</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    /* Set fixed dimensions for chart containers */
    #projectChart, #userChart {
        width: 100% !important;
        height: 300px !important;
    }
</style>
<body>
<div class="main-content mt-5">
    <h1 class="text-center mb-4">Finance Report</h1>

    <!-- Charts Section -->
    <div class="row">
        <!-- Project Income vs Expense Chart -->
        <div class="col-md-6">
            <h4 class="text-center">Project Income vs Expense</h4>
            <canvas id="projectChart"></canvas>
        </div>

        <!-- User Wallet Balances -->
        <div class="col-md-6">
            <h4 class="text-center">User Wallet Balances</h4>
            <canvas id="userChart"></canvas>
        </div>
    </div>

    <!-- Projects Table -->
    <h3 class="mt-5">Projects Overview</h3>
    <div class="finance-card">
    <table class="table table-responsive">
        <thead >
            <tr>
                <th>Project Name</th>
                <th>Income </th>
                <th>Expense </th>
                <th>Profit </th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($projectResult, 0);
            while ($project = mysqli_fetch_assoc($projectResult)) {
                $profit = $project['project_income'] - $project['project_expense'];
                ?>
                <tr>
                    <td><?php echo $project['name']; ?></td>
                    <td class="text-success">₹ <?php echo number_format($project['project_income'], 2); ?></td>
                    <td class="text-danger">₹ <?php echo number_format($project['project_expense'], 2); ?></td>
                    <td class="text-primary">₹ <?php echo number_format($profit, 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table></div>

    <!-- Users Table -->
    <h3 class="mt-5">User Wallets</h3>
    <div class="finance-card">
    <table class="table table-responsive">
        <thead class="thead-dark">
            <tr>
                <th>User Name</th>
                <th>Wallet Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($userResult, 0);
            while ($user = mysqli_fetch_assoc($userResult)) { ?>
                <tr>
                    <td><?php echo $user['name']; ?></td>
                    <td class="text-primary">₹ <?php echo number_format($user['wallet_balance'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table></div>

    <!-- Finance Records Table -->
    <h3 class="mt-5">All Financial Records</h3>
    <div class="finance-card">
    <table class="table table-responsive">
        <thead class="thead-dark">
            <tr>
                <th>Project</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($finance = mysqli_fetch_assoc($financeResult)) { ?>
                <tr>
                    <td><?php echo $finance['project_name']; ?></td>
                    <td>₹ <?php echo number_format($finance['amount'], 2); ?></td>
                    <td><?php echo ucfirst($finance['type']); ?></td>
                    <td><?php echo $finance['description']; ?></td>
                    <td><?php echo $finance['finance_date']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</div>

<script>
// Project Chart
const projectCtx = document.getElementById('projectChart').getContext('2d');
new Chart(projectCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($projectNames); ?>,
        datasets: [
            {
                label: 'Income',
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                data: <?php echo json_encode($projectIncomes); ?>
            },
            {
                label: 'Expense',
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                data: <?php echo json_encode($projectExpenses); ?>
            }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// User Wallet Chart
const userCtx = document.getElementById('userChart').getContext('2d');
new Chart(userCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($userNames); ?>,
        datasets: [{
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'],
            data: <?php echo json_encode($userBalances); ?>
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>

</body>
</html>
