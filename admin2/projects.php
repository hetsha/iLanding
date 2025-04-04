<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_project'])) {
    $projectId = $_POST['project_id'] ?? null;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $users = $_POST['users'] ?? [];

    if ($projectId) {
        // Update Project
        $updateQuery = "UPDATE projects SET name='$name', description='$description' WHERE id=$projectId";
        mysqli_query($conn, $updateQuery);

        // Remove old assignments
        mysqli_query($conn, "DELETE FROM project_assignments WHERE project_id=$projectId");

        // Re-insert new assignments
        foreach ($users as $userId) {
            mysqli_query($conn, "INSERT INTO project_assignments (project_id, user_id) VALUES ($projectId, $userId)");
        }
    } else {
        // Insert Project
        $createdBy = $_SESSION['user_id']; // assuming you store this in session
        $insertQuery = "INSERT INTO projects (name, description, created_by) VALUES ('$name', '$description', $createdBy)";
        mysqli_query($conn, $insertQuery);
        $newProjectId = mysqli_insert_id($conn);

        // Assign users
        foreach ($users as $userId) {
            mysqli_query($conn, "INSERT INTO project_assignments (project_id, user_id) VALUES ($newProjectId, $userId)");
        }
    }

    header("Location: projects.php");
    exit;
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['project_id'])) {
    $deleteId = intval($_GET['project_id']);
    mysqli_query($conn, "DELETE FROM projects WHERE id = $deleteId");
    header("Location: projects.php");
    exit;
}

// Fetch projects with assigned users
$projectsQuery = "
    SELECT
        p.id AS project_id,
        p.name AS project_name,
        p.description,
        GROUP_CONCAT(u.name SEPARATOR ', ') AS assigned_users
    FROM projects p
    LEFT JOIN project_assignments pa ON p.id = pa.project_id
    LEFT JOIN users u ON pa.user_id = u.id
    GROUP BY p.id, p.name, p.description
    ORDER BY p.id DESC
";
$projectsResult = mysqli_query($conn, $projectsQuery);

// Fetch users for dropdown
$usersQuery = "SELECT id, name FROM users ORDER BY name";
$usersResult = mysqli_query($conn, $usersQuery);
?>


<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
</head>

<body>
    <div class="main-content">
        <h1 class="text-center mb-4">Project Management</h1>

        <!-- Add Project Button -->
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProjectModal">Add Project</button>

        <!-- Projects Table -->
        <!-- Projects Table -->
        <div class="financial-card">
            <table class="table table-responsive">
                <thead class="thead-dark">
                    <tr>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Assigned Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($projectsResult)) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['project_name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <?= !empty($row['assigned_users']) ? htmlspecialchars($row['assigned_users']) : 'No users assigned' ?>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    onclick="editProject(
                                <?= $row['project_id']; ?>,
                                '<?= addslashes($row['project_name']); ?>',
                                '<?= addslashes($row['description']); ?>',
                                '<?= addslashes($row['assigned_users']); ?>'
                            )">
                                    Edit
                                </button>
                                <a href="projects.php?action=delete&project_id=<?= $row['project_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Project Modal -->
    <div class="modal fade" id="addProjectModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="projects.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Project</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="project_id" id="projectId">
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" name="name" id="projectName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="projectDescription" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Assign Users</label>
                            <select name="users[]" id="projectUsers" class="form-control" multiple required>
                                <?php
                                mysqli_data_seek($usersResult, 0);
                                while ($user = mysqli_fetch_assoc($usersResult)) {
                                    echo "<option value='{$user['id']}'>{$user['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="save_project" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        function editProject(id, name, description, assignedUsers) {
            $('#projectId').val(id);
            $('#projectName').val(name);
            $('#projectDescription').val(description);

            // Clear and assign users
            $('#projectUsers option').each(function() {
                $(this).prop('selected', false);
            });

            if (assignedUsers) {
                const userNames = assignedUsers.split(', ');
                $('#projectUsers option').each(function() {
                    if (userNames.includes($(this).text())) {
                        $(this).prop('selected', true);
                    }
                });
            }

            $('#modalTitle').text('Edit Project');
            $('#addProjectModal').modal('show'); // this was previously incorrect
        }
    </script>
</body>

</html>