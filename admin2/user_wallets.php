<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Fetch totals by type
$income_result = $conn->query("SELECT SUM(amount) AS total FROM transactions WHERE type = 'income'");
$income_row = $income_result->fetch_assoc();
$income = $income_row['total'] ?? 0;

$expense_result = $conn->query("SELECT SUM(amount) AS total FROM transactions WHERE type = 'expense'");
$expense_row = $expense_result->fetch_assoc();
$expense = $expense_row['total'] ?? 0;

$profit = $income - $expense;
$partner_share = $profit / 4;

// Partner withdrawals
$withdrawals_result = $conn->query("
    SELECT u.name, SUM(t.amount) AS total
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE t.type = 'withdrawal' AND u.role = 'partner'
    GROUP BY u.id
");
$withdrawals = [];
while ($row = $withdrawals_result->fetch_assoc()) {
    $withdrawals[] = $row;
}

// Total partner withdrawals
$total_withdrawals_result = $conn->query("
    SELECT SUM(t.amount) AS total
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE t.type = 'withdrawal' AND u.role = 'partner'
");
$total_withdrawals_row = $total_withdrawals_result->fetch_assoc();
$total_partner_withdrawals = $total_withdrawals_row['total'] ?? 0;

$company_balance = $profit - $total_partner_withdrawals;

// Get all users with calculated balances
$usersBalanceResult = $conn->query("
    SELECT u.id AS user_id, u.name, u.email, u.role,
        COALESCE(SUM(CASE WHEN t.type = 'deposit' THEN t.amount ELSE 0 END), 0) AS total_deposit,
        COALESCE(SUM(CASE WHEN t.type = 'withdrawal' THEN t.amount ELSE 0 END), 0) AS total_withdrawal
    FROM users u
    LEFT JOIN transactions t ON t.user_id = u.id
    GROUP BY u.id
");

$user_balances = [];
while ($user = $usersBalanceResult->fetch_assoc()) {
    $user_id = $user['user_id'];
    $role = $user['role'];
    $deposit = $user['total_deposit'];
    $withdrawal = $user['total_withdrawal'];
    $balance = 0;

    if ($role == 'partner') {
        // Partner: Their balance includes share of profit - their withdrawals
        $partner_withdraw_result = $conn->query("
            SELECT SUM(amount) AS total
            FROM transactions
            WHERE type = 'withdrawal' AND user_id = $user_id
        ");
        $partner_withdraw = $partner_withdraw_result->fetch_assoc()['total'] ?? 0;
        $balance = $partner_share - $partner_withdraw;
    } else {
        // Regular user: balance = deposit - withdrawal
        $balance = $deposit - $withdrawal;
    }

    $user_balances[] = [
        'user_id' => $user_id,
        'name' => $user['name'],
        'email' => $user['email'],
        'withdrawal' => $withdrawal,
        'balance' => $balance
    ];
}


// Get all wallet transactions
$walletResult = $conn->query("
    SELECT t.user_id, u.name, t.amount, t.type AS transaction_type, t.date AS transaction_date
    FROM transactions t
    JOIN users u ON u.id = t.user_id
    ORDER BY t.date DESC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['amount'], $_POST['transaction_type'])) {
    $user_id = intval($_POST['user_id']);
    $amount = floatval($_POST['amount']);
    $transaction_type = $_POST['transaction_type'];
    $created_by = $_SESSION['user_id']; // Assuming session contains logged-in admin ID
    $date = date('Y-m-d H:i:s');

    if ($amount > 0 && in_array($transaction_type, ['deposit', 'withdrawal'])) {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, type, created_by, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $user_id, $amount, $transaction_type, $created_by, $date);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Transaction added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding transaction.";
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Invalid transaction data.";
    }

    header("Location: user_wallets.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 'partner'; // Default role

    if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $role);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User added successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to add user.";
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Email already exists.";
        }

        $check->close();
    } else {
        $_SESSION['error_message'] = "Please enter a valid name and email.";
    }

    header("Location: user_wallets.php");
    exit;
}


// Handle Delete User
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id_to_delete = intval($_GET['id']);

    // Optional: Delete user's transactions first (depends on your DB constraints)
    $conn->query("DELETE FROM transactions WHERE user_id = $user_id_to_delete");

    $deleteUser = $conn->query("DELETE FROM users WHERE id = $user_id_to_delete");

    if ($deleteUser) {
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting user.";
    }

    header("Location: user_wallets.php");
    exit;
}

?>

<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users</title>
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
<h1 class="my-4">User Wallets</h1>

<!-- Success / Error Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php elseif (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<h3 class="my-3">Partner Balances</h3>
<div class="row">
    <?php foreach ($withdrawals as $userBalance): ?>
        <div class="col-md-4 mb-3">
            <div class="stat-card text-center">
                <h5 class="stat-number"><?php echo $userBalance['name']; ?></h5>
                <h4 class="card-subtitle mb-2 text-muted">Balance</h4>
                <h3 class="card-text">₹ <?php echo number_format($partner_share - $userBalance['total'], 2); ?></h3>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Wallet Transactions Button -->
<button class="btn btn-info mb-3" data-toggle="modal" data-target="#walletTransactionsModal">View All Transactions</button>
<button class="btn btn-success mb-3" data-toggle="modal" data-target="#addUserModal">Add New User</button>

<!-- Manage Users -->
<h3 class="my-3">Manage Users</h3>
<table class="table table-responsive">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Balance</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($user_balances as $user): ?>
    <tr>
        <td><?php echo $user['user_id']; ?></td>
        <td><?php echo $user['name']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td>₹ <?php echo number_format($user['withdrawal'], 2); ?></td>
        <td>
            <button class="btn btn-warning btn-sm editUserBtn"
                    data-id="<?php echo $user['user_id']; ?>"
                    data-name="<?php echo $user['name']; ?>"
                    data-email="<?php echo $user['email']; ?>"
                    data-balance="<?php echo $user['balance']; ?>">
                Edit
            </button>
            <a href="user_wallets.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="user_wallets.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="add_user" value="1">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" name="role">
                        <option value="user">User</option>
                        <option value="partner">Partner</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Add User</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Transaction Form -->
<form action="user_wallets.php" method="POST" class="mb-4">
    <h3 class="my-3">Add Transaction</h3>
    <div class="form-group">
        <label for="user_id">User</label>
        <select class="form-control" name="user_id" id="user_id">
            <?php
            mysqli_data_seek($usersBalanceResult, 0);
            while ($user = mysqli_fetch_assoc($usersBalanceResult)) {
                echo "<option value='" . $user['user_id'] . "'>" . $user['name'] . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
    </div>
    <div class="form-group">
        <label for="transaction_type">Transaction Type</label>
        <select class="form-control" name="transaction_type" id="transaction_type">
            <option value="deposit">Deposit</option>
            <option value="withdrawal">Withdrawal</option>
        </select>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Add Transaction</button>
</form>

<!-- Wallet Transactions Modal -->
<div class="modal fade" id="walletTransactionsModal" tabindex="-1" aria-labelledby="walletTransactionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">All Wallet Transactions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($wallet = mysqli_fetch_assoc($walletResult)) { ?>
                            <tr>
                                <td><?php echo $wallet['user_id']; ?></td>
                                <td><?php echo $wallet['name']; ?></td>
                                <td>₹ <?php echo number_format($wallet['amount'], 2); ?></td>
                                <td><?php echo ucfirst($wallet['transaction_type']); ?></td>
                                <td><?php echo $wallet['transaction_date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="edit_user.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="editUserName" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" id="editUserEmail" required>
                </div>
                <div class="form-group">
                    <label>Balance</label>
                    <input type="text" class="form-control" id="editUserBalance" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div></div>

<script>
    $(document).on('click', '.editUserBtn', function () {
        $('#editUserId').val($(this).data('id'));
        $('#editUserName').val($(this).data('name'));
        $('#editUserEmail').val($(this).data('email'));
        $('#editUserBalance').val($(this).data('balance'));
        $('#editUserModal').modal('show');
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>