<?php
include 'config.php';
session_start();

// Fetch project finances
$financeQuery = "SELECT * FROM project_finances ORDER BY finance_date DESC";
$financeResult = mysqli_query($conn, $financeQuery);

// Fetch all projects for filter dropdown
$projectsQuery = "SELECT * FROM projects";
$projectsResult = mysqli_query($conn, $projectsQuery);

// Handle form submission for adding finance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = $_POST['project_id'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $financeDate = $_POST['finance_date'];

    $insertQuery = "INSERT INTO project_finances (project_id, amount, type, description, finance_date)
                    VALUES ('$projectId', '$amount', '$type', '$description', '$financeDate')";
    mysqli_query($conn, $insertQuery);
    header("Location: project_finances.php"); // Refresh after insertion
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Finances</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>Project Finances</h1>
    <form action="project_finances.php" method="POST">
        <div class="form-group">
            <label for="project_id">Project</label>
            <select class="form-control" name="project_id" id="project_id">
                <?php while ($project = mysqli_fetch_assoc($projectsResult)) { ?>
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
            <select class="form-control" name="type" id="type">
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

    <h3>All Financial Records</h3>
    <table class="table">
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
                    <td><?php echo $finance['project_id']; ?></td>
                    <td><?php echo number_format($finance['amount'], 2); ?> USD</td>
                    <td><?php echo $finance['type']; ?></td>
                    <td><?php echo $finance['description']; ?></td>
                    <td><?php echo $finance['finance_date']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
