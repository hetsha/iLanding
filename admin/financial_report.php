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

// Fetch projects with pagination, grouped by status
$sql_projects = "SELECT * FROM projects LIMIT $limit OFFSET $offset";
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
    $sql = "SELECT * FROM projects WHERE status = '$status' LIMIT $limit OFFSET $offset";
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
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 40px;
        }

        .status-box {
            margin-bottom: 50px;
        }

        .status-box h3 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #555;
        }

        .project-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .project-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 30%;
            min-width: 280px;
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .project-card:hover {
            transform: translateY(-10px);
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        .project-card p {
            color: #555;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        .pagination .page-link {
            border-radius: 50%;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
        }

        .pagination .active .page-link {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1 class="text-center">Project Income and Expense Report</h1>

        <!-- Display projects grouped by status -->
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
                            $total_income = $project['total_income'];
                            $total_expenses = $project['total_expenses'];
                            $created_at = $project['created_at'];
                            $status = ucfirst($project['status']);
                            ?>
                            <div class="project-card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($project_name) ?></h5>
                                    <p><strong>Description:</strong> <?= htmlspecialchars($project_description) ?></p>
                                    <p><strong>Status:</strong> <?= $status ?></p>
                                    <p><strong>Created At:</strong> <?= $created_at ?></p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal<?= $project_id ?>">View Details</button>
                                </div>
                            </div>

                            <!-- Project Details Modal -->
                            <div class="modal fade" id="projectModal<?= $project_id ?>" tabindex="-1" aria-labelledby="projectModalLabel<?= $project_id ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="projectModalLabel<?= $project_id ?>">Project: <?= htmlspecialchars($project_name) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Income</h5>
                                            <?php
                                            // Fetch project income details
                                            $sql_income = "SELECT income_amount, income_description, income_date FROM project_income WHERE project_id = $project_id";
                                            $result_income = $conn->query($sql_income);
                                            if ($result_income->num_rows > 0) {
                                                echo "<table class='table table-striped'>
                                                        <thead>
                                                            <tr>
                                                                <th>Amount</th>
                                                                <th>Description</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>";
                                                while ($income = $result_income->fetch_assoc()) {
                                                    echo "<tr>
                                                            <td>₹" . number_format($income['income_amount'], 2) . "</td>
                                                            <td>" . htmlspecialchars($income['income_description']) . "</td>
                                                            <td>" . $income['income_date'] . "</td>
                                                        </tr>";
                                                }
                                                echo "</tbody></table>";
                                            } else {
                                                echo "<p>No income records found.</p>";
                                            }
                                            ?>

                                            <h5>Expenses</h5>
                                            <?php
                                            // Fetch project expense details
                                            $sql_expenses = "SELECT amount, description, created_at FROM expenses WHERE project_id = $project_id";
                                            $result_expenses = $conn->query($sql_expenses);
                                            if ($result_expenses->num_rows > 0) {
                                                echo "<table class='table table-striped'>
                                                        <thead>
                                                            <tr>
                                                                <th>Amount</th>
                                                                <th>Description</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>";
                                                while ($expense = $result_expenses->fetch_assoc()) {
                                                    echo "<tr>
                                                            <td>₹" . number_format($expense['amount'], 2) . "</td>
                                                            <td>" . htmlspecialchars($expense['description']) . "</td>
                                                            <td>" . $expense['created_at'] . "</td>
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
                            <hr>
                        <?php
                        }
                    } else {
                        echo "<p>No projects found in this status.</p>";
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>
