<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Calculate total income, expenses, and profit for all projects
$totalQuery = "
    SELECT
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense
    FROM transactions
    WHERE project_id IS NOT NULL
";

$totalResult = mysqli_fetch_assoc(mysqli_query($conn, $totalQuery));
$totalIncome = $totalResult['total_income'] ?? 0;
$totalExpense = $totalResult['total_expense'] ?? 0;
$totalProfit = $totalIncome - $totalExpense;


// Calculate per-project totals
$projectTotalsQuery = "
    SELECT p.id, p.name,
        SUM(CASE WHEN pf.type = 'income' THEN pf.amount ELSE 0 END) AS project_income,
        SUM(CASE WHEN pf.type = 'expense' THEN pf.amount ELSE 0 END) AS project_expense
    FROM projects p
    LEFT JOIN transactions pf ON p.id = pf.project_id
    GROUP BY p.id, p.name
    ORDER BY p.name ASC";
$projectTotalsResult = mysqli_query($conn, $projectTotalsQuery);

// Fetch project finances
$financeQuery = "
    SELECT pf.*, p.name AS project_name
    FROM transactions pf
    JOIN projects p ON pf.project_id = p.id
    ORDER BY pf.date DESC
";
$financeResult = mysqli_query($conn, $financeQuery);


// Fetch all projects for filter dropdown
$projectsQuery = "SELECT * FROM projects";
$projectsResult = mysqli_query($conn, $projectsQuery);

?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Project Finances</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
</head>
<style>
    .finance-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
</style>
<body>

    <div class="main-content">
        <h1 class="my-4">Project Finances</h1>

        <!-- Overall Totals -->

        <div class="row" data-aos="fade-up" data-aos-delay="200">
            <div class="col-md-4">
                <div class="finance-card">
                        <h5>Total Income</h5>
                        <h3 class="text-primary">₹ <?php echo number_format($totalIncome, 2); ?> </h3>

                </div>
            </div>
            <div class="col-md-4">
                <div class="finance-card">
                        <h5>Total Expenses</h5>
                        <h3 class="text-primary">₹ <?php echo number_format($totalExpense, 2); ?> </h3>

                </div>
            </div>
            <div class="col-md-4">
                <div class="finance-card">
                        <h5>Total Profit</h5>
                        <h3 class="text-primary">₹ <?php echo number_format($totalProfit, 2); ?> </h3>

                </div>
            </div>
        </div>

        <!-- Per-Project Totals -->
        <h3 class="my-4">Per-Project Summary</h3>
        <div class="row">
            <?php while ($project = mysqli_fetch_assoc($projectTotalsResult)) {
                $projectProfit = $project['project_income'] - $project['project_expense'];
            ?>
                <div class="col-md-4 mb-4">
                    <div class="finance-card">
                        <div class="stat-card text-center">
                            <h5 class="card-title"><?php echo $project['name']; ?></h5>
                            <h5 class="text-success">Income: ₹ <?php echo number_format($project['project_income'], 2); ?> </h5>
                            <h5 class="text-danger">Expenses: ₹ <?php echo number_format($project['project_expense'], 2); ?> </h5>
                            <h5 class="text-primary">Profit: ₹ <?php echo number_format($projectProfit, 2); ?> </h5>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#transactionModal">
            View All Transactions
        </button>

        <a href="projects.php" class="btn btn-primary mb-3">View projects</a>

</body>

</html>