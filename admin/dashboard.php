<?php
require_once 'config/db.php';
// include 'includes/auth_check.php';

// Get total projects
$sql_projects = "SELECT COUNT(*) as total FROM projects";
$result_projects = $conn->query($sql_projects);
$total_projects = $result_projects->fetch_assoc()['total'];

// Get total users
$sql_users = "SELECT COUNT(*) as total FROM users";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total'];

// Get total messages
$sql_messages = "SELECT COUNT(*) as total FROM contacts";
$result_messages = $conn->query($sql_messages);
$total_messages = $result_messages->fetch_assoc()['total'];

// Get recent projects
$sql_recent = "SELECT * FROM projects ORDER BY created_at DESC LIMIT 5";
$result_recent = $conn->query($sql_recent);

// Get recent messages
$sql_recent_messages = "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5";
$result_recent_messages = $conn->query($sql_recent_messages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <style>
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Dashboard Overview</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-project-diagram stat-icon text-primary"></i>
                    <div class="stat-number"><?php echo $total_projects; ?></div>
                    <div class="stat-label">Total Projects</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-users stat-icon text-success"></i>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-envelope stat-icon text-info"></i>
                    <div class="stat-number"><?php echo $total_messages; ?></div>
                    <div class="stat-label">Total Messages</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Projects</h5>
                        <a href="projects.php" class="btn btn-sm btn-primary">View All</a>
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

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Messages</h5>
                        <a href="view_contacts.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($result_recent_messages->num_rows > 0): ?>
                            <?php while($message = $result_recent_messages->fetch_assoc()): ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>