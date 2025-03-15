<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- AOS CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
      padding-top: 10px;
      color: white;
      transition: all 0.3s ease;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* Scrollable navigation */
    .sidebar nav {
      flex-grow: 1;
      overflow-y: auto;
      max-height: calc(100vh - 130px);
      /* Adjust based on header height */
    }

    /* Optional: Customize scrollbar */
    .sidebar nav::-webkit-scrollbar {
      width: 8px;
    }

    .sidebar nav::-webkit-scrollbar-thumb {
      background: var(--secondary-color);
      border-radius: 10px;
    }

    .sidebar nav::-webkit-scrollbar-thumb:hover {
      background: var(--accent-color);
    }

    .sidebar-header {
      padding: 15px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header img {
      max-width: 90px;
      margin-bottom: 1px;
    }

    .main-content {
      margin-left: var(--sidebar-width);
      padding: 20px;
      transition: all 0.3s ease;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.8);
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

    .finance-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
      <a href="project_finances.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'project_finances.php' ? 'active' : ''; ?>">
        <i class="fas fa-project-diagram"></i> Projects
      </a>
      <a href="projects.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>">
        <i class="fas fa-project-diagram"></i> Add Projects
      </a>
      <a href="transactions_log.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transactions_log.php' ? 'active' : ''; ?>">
        <i class="fas fa-exchange"></i> transections
      </a>
      <a href="portfolio.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'active' : ''; ?>">
        <i class="fas fa-briefcase"></i> Portfolio
      </a>
      <a href="companies.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>">
        <i class="fas fa-building"></i> companies
      </a>
      <a href="user_wallets.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_wallets.php' ? 'active' : ''; ?>">
        <i class="fas fa-users"></i> Users
      </a>
      <a href="user_management.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'active' : ''; ?>">
        <i class="fas fa-users"></i>Add Users
      </a>
      <a href="profit_distribution.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profit_distribution.php' ? 'active' : ''; ?>">
        <i class="fas fa-dollar-sign"></i> distribute
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
  <!-- AOS JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init();
  </script>

</body>

</html>