<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Fetch Projects and Assigned Users
$projectsQuery = "
    SELECT p.project_id, p.name, p.description, GROUP_CONCAT(u.user_id) AS assigned_user_ids, GROUP_CONCAT(u.name) AS assigned_users
    FROM projects p
    LEFT JOIN project_users pu ON p.project_id = pu.project_id
    LEFT JOIN users u ON pu.user_id = u.user_id
    GROUP BY p.project_id, p.name, p.description
    ORDER BY p.project_id DESC";
$projectsResult = mysqli_query($conn, $projectsQuery);

// Fetch Users for Dropdown
$usersQuery = "SELECT user_id, name FROM users ORDER BY name";
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
                <?php while ($project = mysqli_fetch_assoc($projectsResult)) { ?>
                    <tr>
                        <td><?php echo $project['name']; ?></td>
                        <td><?php echo $project['description']; ?></td>
                        <td><?php echo $project['assigned_users'] ?: 'No users assigned'; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm"
                                    onclick="editProject(
                                        <?php echo $project['project_id']; ?>,
                                        '<?php echo addslashes($project['name']); ?>',
                                        '<?php echo addslashes($project['description']); ?>',
                                        '<?php echo $project['assigned_user_ids']; ?>'
                                    )">
                                Edit
                            </button>
                            <a href="project_crud.php?action=delete&project_id=<?php echo $project['project_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Project Modal -->
<div class="modal fade" id="addProjectModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="project_crud.php" method="POST">
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
                                echo "<option value='{$user['user_id']}'>{$user['name']}</option>";
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

    // Clear all options first
    $('#projectUsers').val(null);

    // Assign users
    if (assignedUsers) {
        const userIds = assignedUsers.split(',');
        $('#projectUsers').val(userIds);
    }

    $('#modalTitle').text('Edit Project');
    $('#projectModal').modal('show');
}
</script>
</body>
</html>
