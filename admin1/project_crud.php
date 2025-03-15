<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Add or Edit Project
if (isset($_POST['save_project'])) {
    $projectId = isset($_POST['project_id']) ? intval($_POST['project_id']) : null;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $users = $_POST['users'];

    if ($projectId) {
        // Update Project
        $conn->query("UPDATE projects SET name = '$name', description = '$description' WHERE project_id = $projectId");

        // Remove old assignments and assign new users
        $conn->query("DELETE FROM project_users WHERE project_id = $projectId");
        foreach ($users as $userId) {
            $conn->query("INSERT INTO project_users (project_id, user_id) VALUES ($projectId, $userId)");
        }
    } else {
        // Insert Project
        $conn->query("INSERT INTO projects (name, description) VALUES ('$name', '$description')");
        $projectId = $conn->insert_id;

        // Assign Users
        foreach ($users as $userId) {
            $conn->query("INSERT INTO project_users (project_id, user_id) VALUES ($projectId, $userId)");
        }
    }

    header("Location: projects.php");
    exit();
}

// Delete Project
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['project_id'])) {
    $projectId = intval($_GET['project_id']);

    // Remove assigned users and delete project
    $conn->query("DELETE FROM project_users WHERE project_id = $projectId");
    $conn->query("DELETE FROM projects WHERE project_id = $projectId");

    header("Location: projects.php");
    exit();
}
// Add Transaction with Project Profit Handling


?>
