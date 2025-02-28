<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

// Get total revenue, expenses, and profit
$sql_totals = "SELECT
    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) as total_revenue,
    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) as total_expenses
FROM transactions
WHERE status = 'completed'";

$result_totals = $conn->query($sql_totals);
$totals = $result_totals->fetch_assoc();
$total_profit = $totals['total_revenue'] - $totals['total_expenses'];

// Get monthly revenue data for the chart
$sql_monthly = "SELECT
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END) as revenue,
    SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END) as expenses
FROM transactions
WHERE status = 'completed'
GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
ORDER BY month DESC
LIMIT 12";

$result_monthly = $conn->query($sql_monthly);
$monthly_data = [];
while($row = $result_monthly->fetch_assoc()) {
    $monthly_data[] = $row;
}

// Get project-wise financial data
$sql_projects = "SELECT
    p.name,
    p.status,
    (SELECT COUNT(DISTINCT pu.user_id) FROM project_users pu WHERE pu.project_id = p.id) AS team_size,
    COALESCE(financial_data.total_income, 0) AS total_income,
    COALESCE(financial_data.total_expenses, 0) AS total_expenses
FROM projects p
LEFT JOIN (
    SELECT
        t.project_id,
        SUM(CASE WHEN t.transaction_type = 'income' THEN t.amount ELSE 0 END) AS total_income,
        SUM(CASE WHEN t.transaction_type = 'expense' THEN t.amount ELSE 0 END) AS total_expenses
    FROM transactions t
    WHERE t.status = 'completed'
    GROUP BY t.project_id
) AS financial_data ON p.id = financial_data.project_id
ORDER BY p.created_at DESC";

$result_projects = $conn->query($sql_projects);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #333;
            padding-top: 20px;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: white;
            padding: 10px 20px;
        }
        .nav-link:hover {
            background: #444;
            color: white;
        }
        .nav-link.active {
            background: #007bff;
        }
        .finance-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>  <!-- Include the navbar component -->

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4" data-aos="fade-right" data-aos-delay="100">Financial Overview</h2>

            <div class="row" data-aos="fade-up" data-aos-delay="200">
                <div class="col-md-4">
                    <div class="finance-card">
                        <h6>Total Revenue</h6>
                        <h3 class="text-primary">₹<?php echo number_format($totals['total_revenue']); ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="finance-card">
                        <h6>Total Expenses</h6>
                        <h3 class="text-danger">₹<?php echo number_format($totals['total_expenses']); ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="finance-card">
                        <h6>Net Profit</h6>
                        <h3 class="text-success">₹<?php echo number_format($total_profit); ?></h3>
                    </div>
                </div>
            </div>

            <div class="row mt-4" data-aos="fade-right" data-aos-delay="300">
                <div class="col-md-8">
                    <div class="finance-card">
                        <h5>Monthly Revenue vs Expenses</h5>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="finance-card">
                        <h5>Project Distribution</h5>
                        <canvas id="projectChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-4" data-aos="fade-up" data-aos-delay="100">
                <div class="col-12">
                    <div class="finance-card">
                        <h5>Project-wise Financial Summary</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Status</th>
                                        <th>Team Size</th>
                                        <th>Revenue</th>
                                        <th>Expenses</th>
                                        <th>Profit</th>
                                        <th>Profit Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($project = $result_projects->fetch_assoc()): ?>
                                    <?php
                                        $profit = $project['total_income'] - $project['total_expenses'];
                                        $margin = $project['total_income'] > 0 ?
                                            ($profit / $project['total_income'] * 100) : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($project['name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $project['status'] == 'current' ? 'primary' :
                                                    ($project['status'] == 'break' ? 'warning' : 'success');
                                            ?>">
                                                <?php echo ucfirst($project['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $project['team_size']; ?></td>
                                        <td>₹<?php echo number_format($project['total_income']); ?></td>
                                        <td>₹<?php echo number_format($project['total_expenses']); ?></td>
                                        <td>₹<?php echo number_format($profit); ?></td>
                                        <td><?php echo number_format($margin, 1); ?>%</td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const monthlyData = <?php echo json_encode($monthly_data); ?>;
        const labels = monthlyData.map(item => item.month);
        const revenues = monthlyData.map(item => item.revenue);
        const expenses = monthlyData.map(item => item.expenses);

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenues,
                    borderColor: '#0d6efd',
                    tension: 0.1
                }, {
                    label: 'Expenses',
                    data: expenses,
                    borderColor: '#dc3545',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Project Distribution Chart
        const projectData = {
            current: <?php echo $conn->query("SELECT COUNT(*) as count FROM projects WHERE status = 'current'")->fetch_assoc()['count']; ?>,
            break: <?php echo $conn->query("SELECT COUNT(*) as count FROM projects WHERE status = 'break'")->fetch_assoc()['count']; ?>,
            completed: <?php echo $conn->query("SELECT COUNT(*) as count FROM projects WHERE status = 'completed'")->fetch_assoc()['count']; ?>
        };

        new Chart(document.getElementById('projectChart'), {
            type: 'doughnut',
            data: {
                labels: ['Current', 'Break', 'Completed'],
                datasets: [{
                    data: [projectData.current, projectData.break, projectData.completed],
                    backgroundColor: ['#0d6efd', '#ffc107', '#198754']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>