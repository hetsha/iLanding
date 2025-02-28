<?php
// Database connection
include 'config/db.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);
session_start();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination setup
$limit = 5; // Number of projects per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch projects with income and expense calculations
$sql_projects = "SELECT p.*,
    (SELECT SUM(amount) FROM transactions WHERE project_id = p.id AND transaction_type = 'income') AS total_income,
    (SELECT SUM(amount) FROM transactions WHERE project_id = p.id AND transaction_type = 'expense') AS total_expenses
    FROM projects p LIMIT $limit OFFSET $offset";

$result_projects = $conn->query($sql_projects);

// Fetch total number of projects for pagination
$sql_total_projects = "SELECT COUNT(*) AS total FROM projects";
$result_total = $conn->query($sql_total_projects);
$total_projects = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_projects / $limit);

// Fetch projects grouped by status
$statuses = ['current', 'break', 'completed'];
$projects_by_status = [];

foreach ($statuses as $status) {
    $sql = "SELECT p.*,
        (SELECT SUM(amount) FROM transactions WHERE project_id = p.id AND transaction_type = 'income') AS total_income,
        (SELECT SUM(amount) FROM transactions WHERE project_id = p.id AND transaction_type = 'expense') AS total_expenses
        FROM projects p WHERE status = '$status' LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    $projects_by_status[$status] = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/upparac6.png" rel="icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }

        .main-content {
            max-width: 100vw;
            margin: 0 auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: bold;
            color: #333;
        }

        .status-box {
            margin-bottom: 50px;
            padding: 20px;
            border-radius: 8px;
            background-color: #eef1f7;
        }

        .status-box h3 {
            text-align: center;
            padding-bottom: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #444;
        }

        .project-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .project-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 30%;
            min-width: 300px;
            transition: transform 0.3s ease;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .project-card:hover {
            transform: translateY(-8px);
        }

        .card-body {
            padding: 25px;
        }
        .card-body button {
            margin-top: 10px;
            width: 100%;
            font-size: 14px;
            text-align:center;
        }

        .card-title {
            font-size: 22px;
            font-weight: bold;
            color: #222;
            text-align: center;
        }

        .card-body p {
            font-size: 15px;
            color: #555;
            margin-top: 10px;
            margin-bottom: 20px;
            margin-left: 5px;
        }

        .card-body button {
            margin-top: 10px;
            width: 100%;
            font-size: 14px;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background: #007bff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 15px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid #ddd;
        }

        .table {
            margin-top: 10px;
        }

        .table thead {
            background: #007bff;
            color: white;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: left;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background: #f9f9f9;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 5px;
            border: 1px solid #007bff;
            border-radius: 5px;
            color: #007bff;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1 class="text-center">Project Income and Expense Report</h1>

        <?php foreach ($projects_by_status as $status => $projects): ?>
            <div class="status-box">
                <h3 class="text-center"><?= ucfirst(str_replace('_', ' ', $status)) ?> Projects</h3>
                <div class="project-grid">
                    <?php
                    if (count($projects) > 0) {
                        foreach ($projects as $project) {
                            $project_id = $project['id'];
                            $project_name = $project['name'];
                            $project_description = $project['description'];
                            $total_income = $project['total_income'] ?? 0;
                            $total_expenses = $project['total_expenses'] ?? 0;
                            $created_at = $project['created_at'];
                            $formatted_created_at = date("d M, Y (h:i A)", strtotime($created_at));
                    ?>
                            <div class="project-card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($project_name) ?></h5>
                                    <p><strong>Description:</strong> <?= htmlspecialchars($project_description) ?></p>
                                    <p><strong>Status:</strong> <?= ucfirst($status) ?></p>
                                    <p><strong>Created At:</strong> <?= $formatted_created_at ?></p>
                                    <div class="text-center">
                                    <button class="btn btn-primary" style="width: 120px;" data-bs-toggle="modal" data-bs-target="#projectModal<?= $project_id ?>">View Details</button>
                                    </div>
                                    </div>
                            </div>

                            <!-- Project Details Modal -->
                            <div class="modal fade" id="projectModal<?= $project_id ?>" tabindex="-1" aria-labelledby="projectModalLabel<?= $project_id ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Project: <?= htmlspecialchars($project_name) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Income</h5>
                                            <?php
                                            $sql_income = "SELECT amount, description, created_at FROM transactions WHERE project_id = $project_id AND transaction_type = 'income'";
                                            $result_income = $conn->query($sql_income);
                                            if ($result_income->num_rows > 0) {
                                                echo "<table class='table table-striped'>
                                                        <thead>
                                                            <tr><th>Amount</th><th>Description</th><th>Date</th></tr>
                                                        </thead><tbody>";
                                                while ($income = $result_income->fetch_assoc()) {
                                                    echo "<tr><td>₹" . number_format($income['amount'], 2) . "</td>
                                                        <td>" . htmlspecialchars($income['description']) . "</td>
                                                    <td>" . date("d-m-Y", strtotime($income['created_at'])) . "</td></tr>";
                                                }
                                                echo "</tbody></table>";
                                            } else {
                                                echo "<p>No income records found.</p>";
                                            }
                                            ?>

                                            <h5>Expenses</h5>
                                            <?php
                                            $sql_expenses = "SELECT amount, description, created_at FROM transactions WHERE project_id = $project_id AND transaction_type = 'expense'";
                                            $result_expenses = $conn->query($sql_expenses);
                                            if ($result_expenses->num_rows > 0) {
                                                echo "<table class='table table-striped'>
                                                        <thead>
                                                            <tr><th>Amount</th><th>Description</th><th>Date</th></tr>
                                                        </thead><tbody>";
                                                while ($expense = $result_expenses->fetch_assoc()) {
                                                    echo "<tr>
                                                                    <td>₹" . number_format($expense['amount'], 2) . "</td>
                                                                    <td>" . htmlspecialchars($expense['description']) . "</td>
                                                                    <td>" . date("d M, Y (h:i A)", strtotime($expense['created_at'])) . "</td>
                                                                  </tr>";
                                                }

                                                echo "</tbody></table>";
                                            } else {
                                                echo "<p>No expense records found.</p>";
                                            }
                                            ?>

                                            <h5>Summary</h5>
                                            <p><strong>Total Income:</strong> ₹<?= number_format($total_income, 2) ?></p>
                                            <p><strong>Total Expenses:</strong> ₹<?= number_format($total_expenses, 2) ?></p>
                                            <p><strong>Balance:</strong> ₹<?= number_format($total_income - $total_expenses, 2) ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } else {
                        echo "<p>No projects found in this status.</p>";
                    } ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php $conn->close(); ?>