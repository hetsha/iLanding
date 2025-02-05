<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Implement rate limiting
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5 && time() - $_SESSION['last_attempt'] < 300) {
        $error = "Too many failed attempts. Please try again after 5 minutes.";
    } else {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = "Please fill in all fields";
        } else {
            $sql = "SELECT id, username, password FROM admins WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    // Reset login attempts on successful login
                    unset($_SESSION['login_attempts']);
                    unset($_SESSION['last_attempt']);
                    
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Set last login time
                    $update_sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $user['id']);
                    $update_stmt->execute();
                    
                    header("Location: dashboard.php");
                    exit();
                }
            }
            
            // Increment login attempts
            $_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;
            $_SESSION['last_attempt'] = time();
            
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 90%;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            max-width: 150px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
            background: #f8f9fa;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            background: #2c3e50;
            border: none;
        }
        .btn-login:hover {
            background: #34495e;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="assets/upparac6.png" alt="Logo">
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control" name="username" placeholder="Username" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                Login <i class="fas fa-sign-in-alt ms-2"></i>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>