<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_project':
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                $total_income = filter_input(INPUT_POST, 'total_income', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                $sql = "INSERT INTO projects (name, status, description, total_income, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssd", $name, $status, $description, $total_income);
                $stmt->execute();
                break;

            case 'update_status':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
                $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

                $sql = "UPDATE projects SET status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $status, $project_id);
                $stmt->execute();
                break;

            case 'add_expense':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
                $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

                $sql = "INSERT INTO expenses (project_id, amount, description, created_at) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ids", $project_id, $amount, $description);
                $stmt->execute();

                $sql = "UPDATE projects SET total_expenses = (SELECT SUM(amount) FROM expenses WHERE project_id = ?) WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $project_id, $project_id);
                $stmt->execute();
                break;

            case 'add_income':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
                $income_amount = filter_input(INPUT_POST, 'income_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $income_description = filter_input(INPUT_POST, 'income_description', FILTER_SANITIZE_STRING);

                // Insert into project_income table
                $sql = "INSERT INTO project_income (project_id, income_amount, income_description, income_date)
                                VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ids", $project_id, $income_amount, $income_description);
                $stmt->execute();

                // Update total_income in projects table by adding the new income amount
                $sql = "UPDATE projects SET total_income = total_income + ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $income_amount, $project_id);
                $stmt->execute();
                break;

            case 'update_project':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                $total_income = filter_input(INPUT_POST, 'total_income', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                $sql = "UPDATE projects SET name = ?, status = ?, description = ?, total_income = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdi", $name, $status, $description, $total_income, $project_id);
                $stmt->execute();
                break;

                case 'delete_project':
                    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);

                    // Delete related records in project_users first (to avoid foreign key constraint violation)
                    $sql = "DELETE FROM project_users WHERE project_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $project_id);
                    $stmt->execute();

                    // Delete related expenses next (to avoid foreign key constraint violation)
                    $sql = "DELETE FROM expenses WHERE project_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $project_id);
                    $stmt->execute();

                    // Delete project income records
                    $sql = "DELETE FROM project_income WHERE project_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $project_id);
                    $stmt->execute();

                    // Finally, delete the project itself
                    $sql = "DELETE FROM projects WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $project_id);
                    $stmt->execute();
                    break;

        }
    }
    header("Location: projects.php");
    exit();
}
// Fetch project income records
$sql_income = "SELECT * FROM project_income WHERE project_id = ?";
$stmt = $conn->prepare($sql_income);
$stmt->bind_param("i", $project['id']);
$stmt->execute();
$income_result = $stmt->get_result();

// Displaying the income data
while ($income = $income_result->fetch_assoc()) {
    echo "<p>Income Amount: ₹" . number_format($income['income_amount']) . " - " . $income['income_description'] . " (Date: " . $income['income_date'] . ")</p>";
}


// Fetch all projects
$sql = "SELECT p.*,
    GROUP_CONCAT(DISTINCT u.name) as team_members,
    COUNT(DISTINCT pu.user_id) as team_size
    FROM projects p
    LEFT JOIN project_users pu ON p.id = pu.project_id
    LEFT JOIN users u ON pu.user_id = u.id
    GROUP BY p.id
    ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Project Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class='bx bx-plus'></i> Add New Project
                </button>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($project = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <a href="#"
                                                        class="project-name-link"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#projectDetailsModal"
                                                        data-id="<?php echo $project['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($project['name']); ?>"
                                                        data-description="<?php echo htmlspecialchars($project['description']); ?>"
                                                        data-income="<?php echo number_format($project['total_income']); ?>"
                                                        data-expenses="<?php echo number_format($project['total_expenses']); ?>"
                                                        data-team="<?php echo htmlspecialchars($project['team_members']); ?>">
                                                        <?php echo htmlspecialchars($project['name']); ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm status-select"
                                                        data-project-id="<?php echo $project['id']; ?>">
                                                        <option value="current" <?php echo $project['status'] == 'current' ? 'selected' : ''; ?>>Current</option>
                                                        <option value="break" <?php echo $project['status'] == 'break' ? 'selected' : ''; ?>>Break</option>
                                                        <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    </select>
                                                </td>
                                                <td><?php echo htmlspecialchars($project['team_members']); ?></td>
                                                <td>₹<?php echo number_format($project['total_income']); ?></td>
                                                <td>₹<?php echo number_format($project['total_expenses']); ?></td>
                                                <td>₹<?php echo number_format($project['total_income'] - $project['total_expenses']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                        data-bs-target="#addExpenseModal" data-project-id="<?php echo $project['id']; ?>">
                                                        Add Expense
                                                    </button>
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                        data-bs-target="#addIncomeModal" data-project-id="<?php echo $project['id']; ?>">
                                                        Add Income
                                                    </button>
                                                    <!-- Delete Button -->
                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_project">
                                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this project?');">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
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

    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_project">
                        <div class="mb-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="current">Current</option>
                                <option value="break">Break</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Income (₹)</label>
                            <input type="number" class="form-control" name="total_income" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Income Modal -->
    <div class="modal fade" id="addIncomeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Income</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_income">
                        <input type="hidden" name="project_id" id="income-project-id">
                        <div class="mb-3">
                            <label class="form-label">Income Amount (₹)</label>
                            <input type="number" class="form-control" name="income_amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="income_description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Income</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Project Details Modal -->
    <div class="modal fade" id="projectDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Project Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4 id="project-name"></h4>
                    <p id="project-description"></p>
                    <ul>
                        <li><strong>Team Members:</strong> <span id="project-team"></span></li>
                        <li><strong>Total Income:</strong> ₹<span id="project-income"></span></li>
                        <li><strong>Total Expenses:</strong> ₹<span id="project-expenses"></span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_expense">
                        <input type="hidden" name="project_id" id="expense-project-id">
                        <div class="mb-3">
                            <label class="form-label">Expense Amount (₹)</label>
                            <input type="number" class="form-control" name="amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const projectDetailsModal = document.getElementById('projectDetailsModal');
        const expenseModal = document.getElementById('addExpenseModal');
        const incomeModal = document.getElementById('addIncomeModal');

        projectDetailsModal.addEventListener('show.bs.modal', function(event) {
            const link = event.relatedTarget;
            document.getElementById('project-name').textContent = link.getAttribute('data-name');
            document.getElementById('project-description').textContent = link.getAttribute('data-description');
            document.getElementById('project-team').textContent = link.getAttribute('data-team');
            document.getElementById('project-income').textContent = link.getAttribute('data-income');
            document.getElementById('project-expenses').textContent = link.getAttribute('data-expenses');
        });

        expenseModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('expense-project-id').value = button.getAttribute('data-project-id');
        });

        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const projectId = this.getAttribute('data-project-id');
                const newStatus = this.value;

                fetch('projects.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=update_status&project_id=${projectId}&status=${newStatus}`
                }).then(response => response.text()).then(console.log);
            });
        });

        incomeModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('income-project-id').value = button.getAttribute('data-project-id');
        });
    </script>
</body>

</html>