<?php
// Include necessary files for DB connection and session management
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Get totals
$income = 0;
$expense = 0;
$withdrawal = 0;
$deposit = 0;

// Income
$result = $conn->query("SELECT SUM(amount) AS total FROM transactions WHERE type = 'income'");
if ($row = $result->fetch_assoc()) {
    $income = $row['total'] ?? 0;
}

// Expense
$result = $conn->query("SELECT SUM(amount) AS total FROM transactions WHERE type = 'expense'");
if ($row = $result->fetch_assoc()) {
    $expense = $row['total'] ?? 0;
}
// withdrawal
$result = $conn->query("SELECT SUM(amount) AS total FROM transactions WHERE type = 'withdrawal'");
if ($row = $result->fetch_assoc()) {
    $withdrawal = $row['total'] ?? 0;
}

// deposit
$result = $conn->query("SELECT SUM(amount) AS total FROM transactions WHERE type = 'deposit'");
if ($row = $result->fetch_assoc()) {
    $deposit = $row['total'] ?? 0;
}


$profit = $income - $expense - $withdrawal + $deposit;

// Fetch active projects
$projectsQuery = "SELECT * FROM projects ORDER BY created_at DESC LIMIT 5";
$projectsResult = mysqli_query($conn, $projectsQuery);


// Get recent projects
$sql_recent = "SELECT * FROM projects ORDER BY created_at DESC LIMIT 5";
$result_recent = $conn->query($sql_recent);

// Get recent messages
$sql_recent_messages = "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5";
$result_recent_messages = $conn->query($sql_recent_messages);
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
</head>
<style>
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .recent-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .recent-item:last-child {
        border-bottom: none;
    }
</style>

<body>

    <div class="main-content">
        <h1>Admin Dashboard</h1>
        <div class="row">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card text-center">
                    <i class="fas fa-project-diagram stat-icon text-primary"></i>
                    <div class="stat-number">Total Income</div>
                    <div class="stat-label">₹ <?php echo number_format($income, 2); ?> </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card text-center">
                    <i class="fas fa-project-diagram stat-icon text-primary"></i>
                    <div class="stat-number">Total Expenses</div>
                    <div class="stat-label">₹ <?php echo number_format($expense, 2); ?> </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card text-center">
                    <i class="fas fa-project-diagram stat-icon text-primary"></i>
                    <div class="stat-number">Total Profit</div>
                    <div class="stat-label">₹ <?php echo number_format($profit, 2); ?> </div>
                </div>
            </div>
        </div>

        <h3>Active Projects</h3>
        <div class="table-responsive finance-card">
        <table class="table no-border">
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
        <div class="row mt-4">
            <div class="col-md-6"  data-aos="fade-right" data-aos-delay="100">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Projects</h5>
                        <a href="project_finances.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($result_recent->num_rows > 0): ?>
                            <?php while($project = $result_recent->fetch_assoc()): ?>
                                <div class="recent-item">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($project['name']); ?></h6>
                                    <small class="text-muted">Added: <?php echo date('M d, Y', strtotime($project['created_at'])); ?></small>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No recent projects</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <div class="col-md-6" data-aos="fade-left" data-aos-delay="100">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Messages</h5>
                    <a href="view_contacts.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if ($result_recent_messages->num_rows > 0): ?>
                        <?php while ($message = $result_recent_messages->fetch_assoc()): ?>
                            <div class="recent-item">
                                <h6 class="mb-1"><?php echo htmlspecialchars($message['name']); ?></h6>
                                <p class="mb-1 small"><?php echo substr(htmlspecialchars($message['message']), 0, 100) . '...'; ?></p>
                                <small class="text-muted">Received: <?php echo date('M d, Y', strtotime($message['created_at'])); ?></small>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No recent messages</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>