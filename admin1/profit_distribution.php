<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Fetch projects with undistributed profit
$projectsQuery = "
    SELECT p.project_id, p.name, pp.net_profit, pp.distributed_profit
    FROM projects p
    JOIN project_profit pp ON p.project_id = pp.project_id
";
$projectsResult = mysqli_query($conn, $projectsQuery);

// Handle individual profit collection
if (isset($_POST['collect_profit'])) {
    $projectId = $_POST['project_id'];
    $userId = $_POST['user_id'];

    // Fetch remaining profit
    $profitQuery = "SELECT net_profit, distributed_profit FROM project_profit WHERE project_id = '$projectId'";
    $profitResult = mysqli_query($conn, $profitQuery);
    $profitRow = mysqli_fetch_assoc($profitResult);

    if (!$profitRow) {
        $_SESSION['error_message'] = "No profit found for this project.";
        header("Location: profit_distribution.php");
        exit();
    }

    $remainingProfit = $profitRow['net_profit'] - $profitRow['distributed_profit'];

   // Count total users
$usersQuery = "SELECT user_id FROM project_users WHERE project_id = '$projectId'";
$usersResult = mysqli_query($conn, $usersQuery);
$totalUsers = mysqli_num_rows($usersResult);

if ($totalUsers > 0 && $remainingProfit > 0) {
    $individualShare = $remainingProfit / $totalUsers;

    // Start transaction to avoid partial updates
    mysqli_begin_transaction($conn);
    $success = true;

    while ($user = mysqli_fetch_assoc($usersResult)) {
        $userId = $user['user_id'];

        // Check if user wallet exists
        $checkWalletQuery = "SELECT user_id FROM user_wallets WHERE user_id = '$userId'";
        $walletResult = mysqli_query($conn, $checkWalletQuery);

        if (mysqli_num_rows($walletResult) > 0) {
            // Update existing wallet
            $updateWalletQuery = "
                UPDATE user_wallets
                SET amount = amount + $individualShare
                WHERE user_id = '$userId'
            ";
            if (!mysqli_query($conn, $updateWalletQuery)) {
                $success = false;
                break;
            }
        } else {
            // Insert new wallet record
            $insertQuery = "
                INSERT INTO user_wallets (user_id, amount, transaction_type)
                VALUES ('$userId', '$individualShare', 'deposit')
            ";
            if (!mysqli_query($conn, $insertQuery)) {
                $success = false;
                break;
            }
        }
    }

    if ($success) {
        // Update distributed profit
        $updateQuery = "
            UPDATE project_profit
            SET distributed_profit = distributed_profit + $remainingProfit
            WHERE project_id = '$projectId'
        ";
        mysqli_query($conn, $updateQuery);
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Profit distributed successfully!";
    } else {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Failed to distribute profit.";
    }
} else {
    $_SESSION['error_message'] = "No profit remaining or no users assigned.";
}

// Redirect after process
header("Location: profit_distribution.php");
exit();
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Distribution</title>
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
    <h1>Project Profit Distribution</h1>

    <!-- Success / Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <?php while ($project = mysqli_fetch_assoc($projectsResult)): ?>
        <?php
        $remainingProfit = $project['net_profit'] - $project['distributed_profit'];

        // Fetch users assigned to this project
        $usersQuery = "
            SELECT u.user_id, u.name
            FROM project_users pu
            JOIN users u ON pu.user_id = u.user_id
            WHERE pu.project_id = {$project['project_id']}
        ";
        $usersResult = mysqli_query($conn, $usersQuery);
        $totalUsers = mysqli_num_rows($usersResult);
        $individualShare = $totalUsers > 0 ? $remainingProfit / $totalUsers : 0;
        ?>

        <div class="stat-card my-3">
            <div class="stat-card bg-info text-white">
                <h5 class="mb-0"><?php echo $project['name']; ?></h5>
            </div>
            <div class="card-body table table-responsive">
                <p><strong>Net Profit:</strong> <?php echo number_format($project['net_profit'], 2); ?> USD</p>
                <p><strong>Distributed Profit:</strong> <?php echo number_format($project['distributed_profit'], 2); ?> USD</p>
                <p><strong>Remaining Profit:</strong> <?php echo number_format($remainingProfit, 2); ?> USD</p>
                <p><strong>Total Users:</strong> <?php echo $totalUsers; ?></p>
                <p><strong>Per User Share:</strong> <?php echo number_format($individualShare, 2); ?> USD</p>

                <h6>Assigned Users:</h6>
                <ul class="finance-card">
                    <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo $user['name']; ?>
                            <form action="profit_distribution.php" method="POST" class="d-inline">
                                <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm" name="collect_profit">Collect</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
