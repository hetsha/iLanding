<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

// Fetch project statistics
$sql_stats = "SELECT
    COUNT(CASE WHEN status = 'current' THEN 1 END) as current_projects,
    COUNT(CASE WHEN status = 'break' THEN 1 END) as break_projects,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_projects,
    SUM(total_income) as total_income,
    SUM(total_expenses) as total_expenses
FROM projects";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();

// Fetch recent projects
$sql_recent = "SELECT p.*, GROUP_CONCAT(u.name) as team_members
    FROM projects p
    LEFT JOIN project_users pu ON p.id = pu.project_id
    LEFT JOIN users u ON pu.user_id = u.id
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 5";
$result_recent = $conn->query($sql_recent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

</head>
<body>
<?php include 'navbar.php'; ?> <!-- Include the navbar component -->


    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Dashboard Overview</h2>

            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h6>Current Projects</h6>
                        <h3><?php echo $stats['current_projects']; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h6>Projects on Break</h6>
                        <h3><?php echo $stats['break_projects']; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h6>Completed Projects</h6>
                        <h3><?php echo $stats['completed_projects']; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h6>Total Profit</h6>
                        <h3>₹<?php echo number_format($stats['total_income'] - $stats['total_expenses']); ?></h3>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Projects</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Project Name</th>
                                            <th>Status</th>
                                            <th>Team Members</th>
                                            <th>Income</th>
                                            <th>Expenses</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($project = $result_recent->fetch_assoc()): ?>
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
                                            <td><?php echo htmlspecialchars($project['team_members']); ?></td>
                                            <td>₹<?php echo number_format($project['total_income']); ?></td>
                                            <td>₹<?php echo number_format($project['total_expenses']); ?></td>
                                            <td>₹<?php echo number_format($project['total_income'] - $project['total_expenses']); ?></td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>