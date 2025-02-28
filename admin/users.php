<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_staff':
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

                $sql = "INSERT INTO users (name, email, role, created_at) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $name, $email, $role);
                $stmt->execute();
                break;

            case 'assign_project':
                $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);

                $sql = "INSERT INTO project_users (project_id, user_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $project_id, $user_id);
                $stmt->execute();
                break;

            case 'unassign_project':
                $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);

                $sql = "DELETE FROM project_users WHERE project_id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $project_id, $user_id);
                $stmt->execute();
                break;

            case 'view_profits':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);

                // Calculate total profit for the selected project
                $sqlGetProfit = "SELECT
                                        (COALESCE(SUM(CASE WHEN transaction_type = 'income' THEN amount ELSE 0 END), 0) -
                                         COALESCE(SUM(CASE WHEN transaction_type = 'expense' THEN amount ELSE 0 END), 0))
                                         AS total_profit
                                     FROM transactions
                                     WHERE project_id = ?";

                $stmtGetProfit = $conn->prepare($sqlGetProfit);
                $stmtGetProfit->bind_param("i", $project_id);
                $stmtGetProfit->execute();
                $stmtGetProfit->bind_result($total_profit);
                $stmtGetProfit->fetch();
                $stmtGetProfit->close();

                $_SESSION['profit_message'] = "Total Profit for Selected Project: ₹" . number_format($total_profit);
                header("Location: users.php");
                exit();
        }
    }
    header("Location: users.php");
    exit();
}

// Fetch all staff members with their project assignments and profits
$sql = "SELECT
    u.id AS id,
    u.name AS name,
    u.email AS email,
    u.role AS role,
    GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ') AS assigned_projects,
    (SUM(CASE WHEN t.transaction_type = 'income' THEN t.amount ELSE 0 END) -
     SUM(CASE WHEN t.transaction_type = 'expense' THEN t.amount ELSE 0 END))
     AS total_project_profit,
    COUNT(DISTINCT pu.project_id) AS assigned_project_count,
    IFNULL(SUM(
        (CASE WHEN t.transaction_type = 'income' THEN t.amount ELSE 0 END) -
        (CASE WHEN t.transaction_type = 'expense' THEN t.amount ELSE 0 END)
    ) / NULLIF((SELECT COUNT(*) FROM project_users WHERE project_id = pu.project_id), 0), 0)
    AS profit_per_user
FROM users u
LEFT JOIN project_users pu ON u.id = pu.user_id
LEFT JOIN projects p ON pu.project_id = p.id
LEFT JOIN transactions t ON pu.project_id = t.project_id
GROUP BY u.id, u.name, u.email, u.role
ORDER BY total_project_profit DESC";

$result = $conn->query($sql);

// Fetch all projects for assignment
$sql_projects = "SELECT id, name FROM projects WHERE status != 'completed'";
$projects = $conn->query($sql_projects);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <?php if (isset($_SESSION['profit_message'])): ?>
            <div class="alert alert-info">
                <?php
                echo $_SESSION['profit_message'];
                unset($_SESSION['profit_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Staff Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class='bx bx-plus'></i> Add New Staff
                </button>
            </div>
            <div class="row mb-4">
                <!-- View Profits Form -->
                <div class="col-6">
                    <form method="POST">
                        <input type="hidden" name="action" value="view_profits">
                        <div class="input-group">
                            <select class="form-select" name="project_id" required>
                                <option value="" selected disabled>Select Project</option>
                                <?php
                                // Reset pointer to reuse the project query
                                $projects->data_seek(0);
                                while ($project = $projects->fetch_assoc()): ?>
                                    <option value="<?php echo $project['id']; ?>">
                                        <?php echo htmlspecialchars($project['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn btn-info">View Profit</button>
                        </div>
                    </form>
                </div>


                <div class="gap" style="height:20px;"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr data-aos="fade-down" data-aos-delay="100">
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Assigned Projects</th>
                                                <th>Total project Profit</th>
                                                <th>Total Profit</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($staff = $result->fetch_assoc()): ?>
                                                <tr data-aos="fade-right" data-aos-delay="300">
                                                    <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['role']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['assigned_projects']); ?></td>
                                                    <td>₹<?php echo number_format($staff['total_project_profit'] ?? 0); ?></td>
                                                    <td>₹<?php echo number_format($staff['profit_per_user'] ?? 0); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                            data-bs-target="#assignProjectModal"
                                                            data-staff-id="<?php echo $staff['id']; ?>">
                                                            Assign Project
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#unassignProjectModal"
                                                            data-staff-id="<?php echo $staff['id']; ?>">
                                                            Unassign Project
                                                        </button>
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

        <!-- Unassign Project Modal -->
        <div class="modal fade" id="unassignProjectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Unassign Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="unassign_project">
                            <input type="hidden" id="unassignStaffId" name="user_id">
                            <div class="mb-3">
                                <label class="form-label">Select Project</label>
                                <select class="form-select" name="project_id" required>
                                    <option value="" selected disabled>Select Project</option>
                                    <?php
                                    $projects->data_seek(0);
                                    while ($project = $projects->fetch_assoc()): ?>
                                        <option value="<?php echo $project['id']; ?>">
                                            <?php echo htmlspecialchars($project['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Unassign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Staff Modal -->
        <div class="modal fade" id="addStaffModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="add_staff">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="Designer">Designer</option>
                                    <option value="Connector">Connector</option>
                                    <option value="Coder">Coder</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Project Modal -->
        <div class="modal fade" id="assignProjectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="assign_project">
                            <input type="hidden" id="assignStaffId" name="user_id">
                            <div class="mb-3">
                                <label class="form-label">Select Project</label>
                                <select class="form-select" name="project_id" required>
                                    <option value="" selected disabled>Select Project</option>
                                    <?php
                                    // Reset the pointer again for the modal
                                    $projects->data_seek(0);
                                    while ($project = $projects->fetch_assoc()): ?>
                                        <option value="<?php echo $project['id']; ?>">
                                            <?php echo htmlspecialchars($project['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Handle project assignment modal
            document.getElementById('assignProjectModal').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const staffId = button.getAttribute('data-staff-id');
                document.getElementById('assignStaffId').value = staffId;
            });

            document.getElementById('unassignProjectModal').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const staffId = button.getAttribute('data-staff-id');
                document.getElementById('unassignStaffId').value = staffId;
            });
        </script>
</body>

</html>
