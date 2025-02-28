<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

// Handle project deletion
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["delete_id"])) {
    $project_id = $_GET["delete_id"];

    // Delete the project from the database
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);

    if ($stmt->execute()) {
        // Set success message in session and redirect back to the projects page
        $_SESSION['message'] = "Project deleted successfully.";
    } else {
        // Set error message in session and redirect back to the projects page
        $_SESSION['error'] = "Failed to delete project.";
    }

    header("Location: projects.php");
    exit();
}

// Handle new project addition
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_project"])) {
    $name = $_POST["project_name"];
    $description = $_POST["project_description"];
    $status = $_POST["project_status"];

    $stmt = $conn->prepare("INSERT INTO projects (name, description, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $description, $status);
    $stmt->execute();
    header("Location: projects.php");
    exit();
}

// Handle project status update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $project_id = $_POST["project_id"];
    $new_status = $_POST["new_status"];

    $stmt = $conn->prepare("UPDATE projects SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $project_id);
    $stmt->execute();
    header("Location: projects.php");
    exit();
}

// Fetch all projects
$sql = "SELECT
    p.*,
    COALESCE(GROUP_CONCAT(DISTINCT u.name SEPARATOR ', '), 'No Members') AS team_members,
    COUNT(DISTINCT pu.user_id) AS team_size,
    (SELECT COALESCE(SUM(t.amount), 0) FROM transactions t WHERE t.project_id = p.id AND t.transaction_type = 'income') AS total_income,
    (SELECT COALESCE(SUM(t.amount), 0) FROM transactions t WHERE t.project_id = p.id AND t.transaction_type = 'expense') AS total_expenses
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
    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content container mt-4">

        <h2>Project Management</h2>
        <!-- Add New Project Form -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            Add New Project
        </button>

        <!-- Project List -->
        <div class="table-responsive finance-card">
            <!-- Projects Table -->
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
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td><?php echo htmlspecialchars($project['status']); ?></td>
                            <td><?php echo htmlspecialchars($project['team_members']); ?></td>
                            <td>₹<?php echo number_format($project['total_income']); ?></td>
                            <td>₹<?php echo number_format($project['total_expenses']); ?></td>
                            <td>₹<?php echo number_format($project['total_income'] - $project['total_expenses']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn"
                                    data-id="<?php echo $project['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($project['name']); ?>"
                                    data-description="<?php echo htmlspecialchars($project['description']); ?>"
                                    data-status="<?php echo $project['status']; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editProjectModal">
                                    Edit
                                </button>
                                <a href="delete_project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="project_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="project_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="project_status">
                                <option value="current">Current</option>
                                <option value="break">Break</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" name="add_project">Add Project</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- edit project modal -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="edit_project_id" name="project_id">

                        <div class="mb-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="edit_project_name" name="project_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="edit_project_description" name="project_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_project_status" name="project_status">
                                <option value="current">Current</option>
                                <option value="break">Break</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_project">Update Project</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButtons = document.querySelectorAll(".edit-btn");

            editButtons.forEach(button => {
                button.addEventListener("click", function() {
                    document.getElementById("edit_project_id").value = this.getAttribute("data-id");
                    document.getElementById("edit_project_name").value = this.getAttribute("data-name");
                    document.getElementById("edit_project_description").value = this.getAttribute("data-description");
                    document.getElementById("edit_project_status").value = this.getAttribute("data-status");
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>