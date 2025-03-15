<?php
include 'config.php';
session_start();

// Fetch all projects
$projectsQuery = "SELECT * FROM projects";
$projectsResult = mysqli_query($conn, $projectsQuery);

// Handle profit distribution
if (isset($_POST['distribute'])) {
    $projectId = $_POST['project_id'];
    $totalProfit = $_POST['total_profit'];

    // Get all users assigned to the project
    $usersQuery = "SELECT user_id FROM project_users WHERE project_id = '$projectId'";
    $usersResult = mysqli_query($conn, $usersQuery);
    $totalUsers = mysqli_num_rows($usersResult);

    if ($totalUsers > 0) {
        $individualShare = $totalProfit / $totalUsers;

        // Distribute the profit equally among all users
        while ($user = mysqli_fetch_assoc($usersResult)) {
            $userId = $user['user_id'];
            $insertQuery = "INSERT INTO user_wallets (user_id, amount, transaction_type)
                            VALUES ('$userId', '$individualShare', 'deposit')";
            mysqli_query($conn, $insertQuery);
        }
        // Mark the distribution as completed
        $updateQuery = "UPDATE projects SET profit_distributed = 1 WHERE project_id = '$projectId'";
        mysqli_query($conn, $updateQuery);

        header("Location: profit_distribution.php"); // Refresh after distribution
    }
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit Distribution</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>Project Profit Distribution</h1>

    <form action="profit_distribution.php" method="POST">
        <div class="form-group">
            <label for="project_id">Select Project</label>
            <select class="form-control" name="project_id" id="project_id">
                <?php while ($project = mysqli_fetch_assoc($projectsResult)) { ?>
                    <option value="<?php echo $project['project_id']; ?>"><?php echo $project['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="total_profit">Total Project Profit</label>
            <input type="number" class="form-control" name="total_profit" id="total_profit" required>
        </div>
        <button type="submit" class="btn btn-primary" name="distribute">Distribute Profit</button>
    </form>
</div>

</body>
</html>
