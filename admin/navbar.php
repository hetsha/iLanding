<!-- navbar.php -->
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
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
<?php include 'includes/auth_check.php'?>
<div class="sidebar">
    <div class="text-center mb-4">
        <h4>Admin Panel</h4>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>" href="projects.php"><i class='bx bxs-briefcase'></i> Projects</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php"><i class='bx bxs-user'></i> Staff</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'finance.php' ? 'active' : ''; ?>" href="finance.php"><i class='bx bxs-bank'></i> Finance</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'financial_report.php' ? 'active' : ''; ?>" href="financial_report.php"><i class='bx bxs-bank'></i> financial_report</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php"><i class='bx bxs-user-circle'></i> Profile</a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>" href="logout.php"><i class='bx bxs-log-out'></i> Logout</a>
    </nav>
</div>
