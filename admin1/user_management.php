<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $insertUserQuery = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
    mysqli_query($conn, $insertUserQuery);

    header("Location: user_management.php");
    exit();
}

// Handle Delete User
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $deleteUserQuery = "DELETE FROM users WHERE user_id = $userId";
    mysqli_query($conn, $deleteUserQuery);

    header("Location: user_management.php");
    exit();
}

// Fetch all users
$usersQuery = "SELECT * FROM users";
$usersResult = mysqli_query($conn, $usersQuery);
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
</head>
<style>
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .recent-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .recent-item:last-child {
        border-bottom: none;
    }
</style>
<body>
<div class="main-content">
    <h1 class="text-center">User Management</h1>

    <!-- Add User Button -->
    <div class="mb-3 text-right">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Add User</button>
    </div>

    <!-- All Users Table -->
    <div class="financial-card">
        <h3>All Users</h3>
        <table class="table table-responsive">
            <thead class="thead-dark">
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($usersResult)) { ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <a href="user_management.php?action=edit&user_id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="user_management.php?action=delete&user_id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="user_management.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
