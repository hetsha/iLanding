<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

$admin_id = $_SESSION['admin_id'];
$success_message = '';
$error_message = '';

// Fetch admin details
$stmt = $conn->prepare("SELECT username, created_at FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Handle password update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_password':
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                // Verify current password
                $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();

                if (password_verify($current_password, $result['password'])) {
                    if ($new_password === $confirm_password) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                        $stmt->bind_param("si", $hashed_password, $admin_id);

                        if ($stmt->execute()) {
                            $success_message = "Password updated successfully!";
                        } else {
                            $error_message = "Failed to update password. Please try again.";
                        }
                    } else {
                        $error_message = "New passwords do not match.";
                    }
                } else {
                    $error_message = "Current password is incorrect.";
                }
                break;
        }
    }
}

// Get admin activity
$sql_activity = "SELECT
    (SELECT COUNT(*) FROM projects) as total_projects,
    (SELECT COUNT(*) FROM users) as total_staff,
    (SELECT COUNT(*) FROM transactions WHERE transaction_type = 'expense') as total_transactions";

$activity = $conn->query($sql_activity)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #333;
            padding-top: 20px;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: white;
            padding: 10px 20px;
        }
        .nav-link:hover {
            background: #444;
            color: white;
        }
        .nav-link.active {
            background: #007bff;
        }
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .activity-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Admin Profile</h2>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="profile-card">
                        <h5 class="mb-4">Profile Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Created</label>
                            <input type="text" class="form-control" value="<?php echo date('F j, Y', strtotime($admin['created_at'])); ?>" readonly>
                        </div>
                    </div>

                    <div class="profile-card">
                        <h5 class="mb-4">Change Password</h5>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_password">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="profile-card">
                        <h5 class="mb-4">Activity Overview</h5>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="activity-icon text-primary">
                                    <i class='bx bxs-briefcase'></i>
                                </div>
                                <h3><?php echo $activity['total_projects']; ?></h3>
                                <p>Total Projects</p>
                            </div>
                            <div class="col-md-4">
                                <div class="activity-icon text-success">
                                    <i class='bx bxs-user'></i>
                                </div>
                                <h3><?php echo $activity['total_staff']; ?></h3>
                                <p>Total Staff</p>
                            </div>
                            <div class="col-md-4">
                                <div class="activity-icon text-info">
                                    <i class='bx bxs-bank'></i>
                                </div>
                                <h3><?php echo $activity['total_transactions']; ?></h3>
                                <p>Transactions</p>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card">
                        <h5 class="mb-4">Security Tips</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class='bx bxs-check-circle text-success'></i> Use a strong, unique password</li>
                            <li class="mb-2"><i class='bx bxs-check-circle text-success'></i> Change your password regularly</li>
                            <li class="mb-2"><i class='bx bxs-check-circle text-success'></i> Never share your login credentials</li>
                            <li class="mb-2"><i class='bx bxs-check-circle text-success'></i> Log out when not using the system</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>