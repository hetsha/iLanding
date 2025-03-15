<?php
require 'db_connect.php';

// Fetch projects and users
$projects = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
$users = $conn->query("SELECT * FROM users ORDER BY name");

// Handle project creation and updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $assigned_users = $_POST['assigned_users'] ?? [];

    if ($project_id) {
        $stmt = $conn->prepare("UPDATE projects SET name = ?, description = ?, start_date = ?, end_date = ?, status = ? WHERE project_id = ?");
        $stmt->bind_param("sssssi", $name, $description, $start_date, $end_date, $status, $project_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (name, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $description, $start_date, $end_date, $status);
        $stmt->execute();
        $project_id = $stmt->insert_id;
    }
    $stmt->execute();

    // Manage project users
    $conn->query("DELETE FROM project_users WHERE project_id = $project_id");
    foreach ($assigned_users as $user_id) {
        $stmt = $conn->prepare("INSERT INTO project_users (project_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $project_id, $user_id);
        $stmt->execute();
    }

    header("Location: project_management.php");
    exit();
}

// Handle project deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM project_users WHERE project_id = $delete_id");
    $conn->query("DELETE FROM projects WHERE project_id = $delete_id");
    header("Location: project_management.php");
    exit();
}


// Handle project deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM project_users WHERE project_id = $delete_id");
    $conn->query("DELETE FROM projects WHERE project_id = $delete_id");
    header("Location: project_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Project Management</title>
</head>

<body>
    <div class="container my-4">
        <h2 class="mb-4">Project Management</h2>

        <!-- Button to open add project modal -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#projectModal" onclick="openProjectModal()">Add Project</button>

        <!-- Project Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($project = $projects->fetch_assoc()): ?>
                    <?php
                    $assigned_users = $conn->query("SELECT name FROM project_users JOIN users ON project_users.user_id = users.user_id WHERE project_id = {$project['project_id']}");
                    $user_list = [];
                    while ($user = $assigned_users->fetch_assoc()) {
                        $user_list[] = $user['name'];
                    }
                    ?>
                    <tr>
                        <td><?= $project['project_id'] ?></td>
                        <td><?= htmlspecialchars($project['name']) ?></td>
                        <td><?= htmlspecialchars($project['description']) ?></td>
                        <td><?= $project['start_date'] ?></td>
                        <td><?= $project['end_date'] ?></td>
                        <td>
                            <span class="badge bg-<?= $project['status'] === 'completed' ? 'success' : ($project['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                            </span>
                        </td>
                        <td><?= implode(', ', $user_list) ?: 'No Users' ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal"
                                onclick='openProjectModal(<?= json_encode($project) ?>, <?= json_encode(array_column($user_list, 'user_id')) ?>)'>Edit</button>
                            <a href="?delete_id=<?= $project['project_id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#projectDetailsModal" onclick="fetchProjectDetails(<?= $project['project_id'] ?>)">
                                View Details
                            </button>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Project Details Modal -->
    <div class="modal fade" id="projectDetailsModal" tabindex="-1" aria-labelledby="projectDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Project Financial Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Income: <span id="projectIncome" class="text-success"></span></h5>
                    <h5>Expenses: <span id="projectExpenses" class="text-danger"></span></h5>
                    <h5>Profit: <span id="projectProfit" class="text-primary"></span></h5>
                    <hr>
                    <h6>Transaction List:</h6>
                    <ul id="transactionList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="distributeProfit()">Distribute Profit Equally</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectModalLabel">Add Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="project_id" name="project_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="assigned_users" class="form-label">Assign Users</label>
                            <select class="form-select" id="assigned_users" name="assigned_users[]" multiple>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to distribute the profit equally among assigned users?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmProfitDistribution">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="alertModalMessage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openProjectModal(project = null, assignedUsers = []) {
            document.getElementById('project_id').value = project ? project.project_id : '';
            document.getElementById('name').value = project ? project.name : '';
            document.getElementById('description').value = project ? project.description : '';
            document.getElementById('start_date').value = project ? project.start_date : '';
            document.getElementById('end_date').value = project ? project.end_date : '';
            document.getElementById('status').value = project ? project.status : 'pending';

            const assignedUsersSelect = document.getElementById('assigned_users');
            Array.from(assignedUsersSelect.options).forEach(option => {
                // Ensure the selected users' IDs match the assigned user IDs
                option.selected = assignedUsers.includes(parseInt(option.value));
            });
        }
    </script>
    <script>
        function fetchProjectDetails(projectId) {
            // Store the project_id in a hidden input for later use
            document.getElementById('project_id').value = projectId;

            fetch(`fetch_project_details.php?project_id=${projectId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('projectIncome').textContent = `₹${data.income}`;
                    document.getElementById('projectExpenses').textContent = `₹${data.expenses}`;
                    document.getElementById('projectProfit').textContent = `₹${data.profit}`;

                    const transactionList = document.getElementById('transactionList');
                    transactionList.innerHTML = ''; // Clear old entries

                    if (data.transactions.length > 0) {
                        data.transactions.forEach(transaction => {
                            const item = document.createElement('li');
                            item.className = `list-group-item d-flex justify-content-between align-items-center`;
                            item.innerHTML = `
                        <div>
                            <strong>${transaction.description}</strong>
                            <small class="text-muted">${transaction.created_at}</small>
                        </div>
                        <span class="badge bg-${transaction.type === 'income' ? 'success' : 'danger'}">
                            ₹${transaction.amount}
                        </span>`;
                            transactionList.appendChild(item);
                        });
                    } else {
                        transactionList.innerHTML = '<li class="list-group-item text-muted">No transactions found.</li>';
                    }
                })
                .catch(error => console.error('Error fetching project details:', error));
        }

        function distributeProfit() {
            const projectId = document.getElementById('project_id').value;

            // Check if project_id is available
            if (!projectId) {
                alert("Project ID is missing.");
                return; // Stop further execution if the project_id is missing
            }

            if (!confirm('Are you sure you want to distribute the profit equally among assigned users?')) {
                return;
            }

            fetch(`distribute_profit.php?project_id=${projectId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profit distributed successfully!');
                        location.reload();
                    } else {
                        alert(`Error: ${data.message}`);
                    }
                })
                .catch(error => console.error('Error distributing profit:', error));
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>