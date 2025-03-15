<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Calculate total income, expenses, and profit for all projects
$totalQuery = "
    SELECT
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense
    FROM project_finances";
$totalResult = mysqli_fetch_assoc(mysqli_query($conn, $totalQuery));
$totalIncome = $totalResult['total_income'] ?? 0;
$totalExpense = $totalResult['total_expense'] ?? 0;
$totalProfit = $totalIncome - $totalExpense;

// Calculate per-project totals
$projectTotalsQuery = "
    SELECT p.project_id, p.name,
        SUM(CASE WHEN pf.type = 'income' THEN pf.amount ELSE 0 END) AS project_income,
        SUM(CASE WHEN pf.type = 'expense' THEN pf.amount ELSE 0 END) AS project_expense
    FROM projects p
    LEFT JOIN project_finances pf ON p.project_id = pf.project_id
    GROUP BY p.project_id, p.name
    ORDER BY p.name ASC";
$projectTotalsResult = mysqli_query($conn, $projectTotalsQuery);

// Fetch project finances
$financeQuery = "
    SELECT pf.*, p.name
    FROM project_finances pf
    JOIN projects p ON pf.project_id = p.project_id
    ORDER BY pf.finance_id DESC";
$financeResult = mysqli_query($conn, $financeQuery);

// Fetch all projects for filter dropdown
$projectsQuery = "SELECT * FROM projects";
$projectsResult = mysqli_query($conn, $projectsQuery);

// Handle form submission for adding finance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = intval($_POST['project_id']);
    $amount = floatval($_POST['amount']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $financeDate = mysqli_real_escape_string($conn, $_POST['finance_date']);

    // Insert into project_finances
    $insertQuery = "
        INSERT INTO project_finances (project_id, amount, type, description, finance_date)
        VALUES ('$projectId', '$amount', '$type', '$description', '$financeDate')";

    if (!$conn->query($insertQuery)) {
        die("Finance Insert Error: " . $conn->error);
    }

    // Calculate Profit Change
    $profitChange = ($type === 'income') ? $amount : -$amount;

    // Insert or Update Project Profit
    $profitQuery = "
        INSERT INTO project_profit (project_id, net_profit, distributed_profit)
        VALUES ($projectId, $profitChange, 0)
        ON DUPLICATE KEY UPDATE net_profit = net_profit + VALUES(net_profit)";

    if (!$conn->query($profitQuery)) {
        die("Profit Update Error: " . $conn->error);
    }

    header("Location: project_finances.php"); // Refresh after insertion
    exit();
}

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

        <a href="projects.php" class="btn btn-primary mb-3">View Products</a>

        <!-- Add Finance Form -->
        <h3 class="my-4">Add Finance Record</h3>
        <form action="project_finances.php" method="POST">
            <div class="form-group">
                <label for="project_id">Project</label>
                <select class="form-control" name="project_id" id="project_id" required>
                    <?php
                    mysqli_data_seek($projectsResult, 0);
                    while ($project = mysqli_fetch_assoc($projectsResult)) { ?>
                        <option value="<?php echo $project['project_id']; ?>"><?php echo $project['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" name="amount" id="amount" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" name="type" id="type" required>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description"></textarea>
            </div>
            <div class="form-group">
                <label for="finance_date">Date</label>
                <input type="date" class="form-control" name="finance_date" id="finance_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Finance</button>
        </form>

        <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transactionModalLabel">All Financial Records</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
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
                                        <td><?php echo $finance['name']; ?></td>
                                        <td>₹ <?php echo number_format($finance['amount'], 2); ?> </td>
                                        <td><?php echo ucfirst($finance['type']); ?></td>
                                        <td><?php echo $finance['description']; ?></td>
                                        <td><?php echo $finance['finance_date']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>