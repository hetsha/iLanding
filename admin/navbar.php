<!-- navbar.php -->
<?php
// include 'includes/auth_check.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/upparac6.png" rel="icon">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: var(--sidebar-width);
            background: var(--primary-color);
            padding-top: 20px;
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header img {
            max-width: 120px;
            margin-bottom: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            color: white;
            background: var(--secondary-color);
            padding-left: 25px;
        }

        .nav-link.active {
            background: var(--accent-color);
            color: white;
        }

        .toggle-sidebar {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar">
        <div class="sidebar-header">
            <img src="assets/upparac6.png" alt="upparac-web-devlopment">
            <h5>Admin Panel</h5>
        </div>
        <nav class="mt-3">
            <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="projects.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>">
                <i class="fas fa-project-diagram"></i> Projects
            </a>
            <a href="portfolio.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i> Portfolio
            </a>
            <a href="companies.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i> companies
            </a>
            <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="finance.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'finance.php' ? 'active' : ''; ?>">
                <i class="fas fa-dollar-sign"></i> Finance
            </a>
            <a href="financial_report.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'financial_report.php' ? 'active' : ''; ?>">
                <i class="fas fa-file"></i> Finance_report
            </a>
            <a href="view_contacts.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view_contacts.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Messages
            </a>
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <!-- Content will be injected here -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
