<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// 1. Fetch Project Data
$projectQuery = "
    SELECT p.id, p.name,
        SUM(CASE WHEN pf.type = 'income' THEN pf.amount ELSE 0 END) AS project_income,
        SUM(CASE WHEN pf.type = 'expense' THEN pf.amount ELSE 0 END) AS project_expense
    FROM projects p
    LEFT JOIN transactions pf ON p.id = pf.project_id
    GROUP BY p.id, p.name
    ORDER BY p.name ASC";
$projectResult = mysqli_query($conn, $projectQuery);

// 2. Fetch Financial Records
$financeQuery = "
    SELECT
        pf.*,
        p.name AS project_name,
        u1.name AS created_by_name,
        u2.name AS user_name
    FROM transactions pf
    LEFT JOIN projects p ON pf.project_id = p.id
    LEFT JOIN users u1 ON pf.created_by = u1.id
    LEFT JOIN users u2 ON pf.user_id = u2.id
    ORDER BY pf.id DESC";
$financeResult = mysqli_query($conn, $financeQuery);

// Prepare data for chart
$projectNames = [];
$projectIncomes = [];
$projectExpenses = [];

while ($project = mysqli_fetch_assoc($projectResult)) {
    $projectNames[] = $project['name'];
    $projectIncomes[] = $project['project_income'];
    $projectExpenses[] = $project['project_expense'];
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    #projectChart {
        width: 100% !important;
        height: 300px !important;
    }
</style>

<body>
    <div class="main-content mt-5">
        <h1 class="text-center mb-4">Finance Report</h1>

        <!-- Chart Section -->
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center">Project Income vs Expense</h4>
                <canvas id="projectChart"></canvas>
            </div>
        </div>

        <!-- Projects Table -->
        <h3 class="mt-5">Projects Overview</h3>
        <div class="finance-card">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Income</th>
                        <th>Expense</th>
                        <th>Profit</th>
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
            </table>
        </div>

        <!-- All Financial Records Table -->
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
        <th>Created By</th>
    </tr>
</thead>
<tbody>
    <?php while ($finance = mysqli_fetch_assoc($financeResult)) { ?>
        <tr>
            <td>
                <?php
                if (!empty($finance['project_name'])) {
                    echo $finance['project_name'];
                } elseif (!empty($finance['user_name'])) {
                    echo $finance['user_name'];
                } else {
                    echo '—';
                }
                ?>
            </td>
            <td>₹ <?php echo number_format($finance['amount'], 2); ?></td>
            <td><?php echo ucfirst($finance['type']); ?></td>
            <td><?php echo $finance['description'] ?? '—'; ?></td>
            <td><?php echo $finance['date']; ?></td>
            <td><?php echo $finance['created_by_name'] ?? '—'; ?></td>
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
                datasets: [{
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
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

</body>

</html>