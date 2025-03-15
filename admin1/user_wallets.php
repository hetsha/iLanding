<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Handle deposit/withdrawal transactions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $amount = floatval($_POST['amount']);
    $transactionType = $_POST['transaction_type'];

    // Fetch current wallet balance
    $walletQuery = "SELECT SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) AS balance
                    FROM user_wallets WHERE user_id = '$userId'";
    $walletResult = mysqli_query($conn, $walletQuery);
    $walletRow = mysqli_fetch_assoc($walletResult);
    $currentBalance = $walletRow['balance'] ?? 0;

    if ($transactionType === 'withdrawal' && $amount > $currentBalance) {
        $_SESSION['error_message'] = "Insufficient balance for withdrawal!";
    } else {
        // Insert transaction record
        $insertQuery = "INSERT INTO user_wallets (user_id, amount, transaction_type)
                        VALUES ('$userId', '$amount', '$transactionType')";
        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION['success_message'] = ucfirst($transactionType) . " successful!";
        } else {
            $_SESSION['error_message'] = "Transaction failed.";
        }
    }
    header("Location: user_wallets.php");
    exit();
}

// Fetch all users with their balances
$usersBalanceQuery = "
    SELECT u.user_id, u.name ,u.email,
           IFNULL(SUM(CASE WHEN uw.transaction_type = 'deposit' THEN uw.amount ELSE -uw.amount END), 0) AS balance
    FROM users u
    LEFT JOIN user_wallets uw ON u.user_id = uw.user_id
    GROUP BY u.user_id, u.name
";
$usersBalanceResult = mysqli_query($conn, $usersBalanceQuery);

// Fetch all user wallet transactions
$walletQuery = "
    SELECT uw.user_id, u.name, uw.amount, uw.transaction_type, uw.transaction_date
    FROM user_wallets uw
    JOIN users u ON uw.user_id = u.user_id
    ORDER BY uw.transaction_date DESC
";
$walletResult = mysqli_query($conn, $walletQuery);
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

    <!-- User Balances -->
    <h3 class="my-3">User Balances</h3>
    <div class="row">
        <?php
        mysqli_data_seek($usersBalanceResult, 0);  // Reset pointer to reuse the query result
        while ($userBalance = mysqli_fetch_assoc($usersBalanceResult)) { ?>
            <div class="col-md-4 mb-3">
                    <div class="stat-card text-center">
                        <h5 class="stat-number"><?php echo $userBalance['name']; ?></h5>
                        <h4 class="card-subtitle mb-2 text-muted">Balance</h4>
                        <h3 class="card-text">₹ <?php echo number_format($userBalance['balance'], 2); ?></h3>
                    </div>
            </div>
        <?php } ?>
    </div>
    <!-- Wallet Transactions Button -->
    <button class="btn btn-info mb-3" data-toggle="modal" data-target="#walletTransactionsModal">View All Transactions</button>

    <!-- User List with Edit and Delete Options -->
<div class="finance-card">
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
        <?php
        mysqli_data_seek($usersBalanceResult, 0);
        while ($user = mysqli_fetch_assoc($usersBalanceResult)) { ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td>₹ <?php echo number_format($user['balance'], 2); ?></td>
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
        <?php } ?>
    </tbody>
    </table>
    </div>
    <!-- Add Transaction Form -->
    <form action="user_wallets.php" method="POST" class="mb-4">
        <h3 class="my-3">Add Transaction</h3>
        <div class="form-group">
            <label for="user_id">User</label>
            <select class="form-control" name="user_id" id="user_id">
                <?php
                mysqli_data_seek($usersBalanceResult, 0);  // Reset pointer again for dropdown
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
                    <h5 class="modal-title" id="walletTransactionsModalLabel">All Wallet Transactions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
</div>
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="edit_user.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="form-group">
                        <label for="editUserName">Name</label>
                        <input type="text" class="form-control" name="name" id="editUserName" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserEmail">Email</label>
                        <input type="email" class="form-control" name="email" id="editUserEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserBalance">Balance (USD)</label>
                        <input type="text" class="form-control" id="editUserBalance" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Populate the Edit Modal with user data
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
