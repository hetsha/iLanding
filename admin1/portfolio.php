<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_project':
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                $project_link = filter_input(INPUT_POST, 'project_link', FILTER_SANITIZE_URL);

                // Handle image uploads
                $images = [];
                $upload_dir = 'uploads/';
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($_FILES["image{$i}"]['name'])) {
                        $image_name = time() . '_' . basename($_FILES["image{$i}"]["name"]);
                        $target_file = $upload_dir . $image_name;
                        if (move_uploaded_file($_FILES["image{$i}"]["tmp_name"], $target_file)) {
                            $images[$i] = $image_name;
                        } else {
                            $images[$i] = null;
                        }
                    } else {
                        $images[$i] = null;
                    }
                }

                // Insert into the database
                $sql = "INSERT INTO portfolio_projects (project_name, status, project_description, project_link, created_at, image1, image2, image3, image4)
                        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", $name, $status, $description, $project_link, $images[1], $images[2], $images[3], $images[4]);
                $stmt->execute();
                break;

            case 'update_project':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                $project_link = filter_input(INPUT_POST, 'project_link', FILTER_SANITIZE_URL);

                // Retrieve old images
                $sql = "SELECT image1, image2, image3, image4 FROM portfolio_projects WHERE project_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $project_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_images = $result->fetch_assoc();

                // Handle image updates
                $upload_dir = 'uploads/';
                $images = [$old_images['image1'], $old_images['image2'], $old_images['image3'], $old_images['image4']];
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($_FILES["image{$i}"]['name'])) {
                        $image_name = time() . '_' . basename($_FILES["image{$i}"]["name"]);
                        $target_file = $upload_dir . $image_name;
                        if (move_uploaded_file($_FILES["image{$i}"]["tmp_name"], $target_file)) {
                            if ($old_images["image{$i}"]) unlink($upload_dir . $old_images["image{$i}"]); // Remove old image
                            $images[$i - 1] = $image_name;
                        }
                    }
                }

                // Update the project
                $sql = "UPDATE portfolio_projects SET project_name = ?, status = ?, project_description = ?, project_link = ?,
                        image1 = ?, image2 = ?, image3 = ?, image4 = ? WHERE project_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssi", $name, $status, $description, $project_link, $images[0], $images[1], $images[2], $images[3], $project_id);
                $stmt->execute();
                break;

            case 'delete_project':
                $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);

                // Get project images
                $sql = "SELECT image1, image2, image3, image4 FROM portfolio_projects WHERE project_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $project_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $project = $result->fetch_assoc();

                // Delete images from server
                foreach (['image1', 'image2', 'image3', 'image4'] as $image) {
                    if ($project[$image]) {
                        unlink('uploads/' . $project[$image]);
                    }
                }

                // Delete project from database
                $sql = "DELETE FROM portfolio_projects WHERE project_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $project_id);
                $stmt->execute();
                break;
        }
    }
    header("Location: portfolio.php");
    exit();
}

// Fetch all projects
$sql = "SELECT * FROM portfolio_projects ORDER BY created_at DESC";
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
    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>

<div?>
    <?php include 'nav.php'; ?>

    <div class="main-content container" data-aos="fade-down" data-aos-delay="100">
        <h2>Project Management</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">Add New Project</button>

        <div class="row" data-aos="fade-up" data-aos-delay="200">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($project = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                                            <td><?php echo htmlspecialchars($project['project_description']); ?></td>
                                            <td><?php echo htmlspecialchars($project['status']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editProjectModal" onclick="editProject(<?php echo $project['project_id']; ?>)">Edit</button>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_project">
                                                    <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this project?');">Delete</button>
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

        <!-- Add Project Modal -->
        <div class="modal fade" id="addProjectModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_project">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>Project Name:</label>
                            <input type="text" class="form-control" name="name" required>
                            <label>Status:</label>
                            <select class="form-control" name="status" required>
                                <option value="current">Current</option>
                                <option value="break">Break</option>
                                <option value="completed">Completed</option>
                            </select>
                            <label>Description:</label>
                            <textarea class="form-control" name="description" required></textarea>
                            <label>Project Link:</label>
                            <input type="url" class="form-control" name="project_link" required>
                            <label>Images:</label>
                            <input type="file" class="form-control" name="image1">
                            <input type="file" class="form-control" name="image2">
                            <input type="file" class="form-control" name="image3">
                            <input type="file" class="form-control" name="image4">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Project Modal -->
        <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_project">
                        <input type="hidden" name="project_id" id="edit_project_id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label>Project Name:</label>
                            <input type="text" class="form-control" name="name" id="edit_project_name" required>
                            <label>Status:</label>
                            <select class="form-control" name="status" id="edit_project_status" required>
                                <option value="current">Current</option>
                                <option value="break">Break</option>
                                <option value="completed">Completed</option>
                            </select>
                            <label>Description:</label>
                            <textarea class="form-control" name="description" id="edit_project_description" required></textarea>
                            <label>Project Link:</label>
                            <input type="url" class="form-control" name="project_link" id="edit_project_link" required>
                            <label>Images:</label>
                            <input type="file" class="form-control" name="image1">
                            <input type="file" class="form-control" name="image2">
                            <input type="file" class="form-control" name="image3">
                            <input type="file" class="form-control" name="image4">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        </body>

</html>