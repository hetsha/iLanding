🌐 Admin Panel Features:
Project Management:

Create/Edit/Delete projects.
Track project status (pending, in-progress, completed).
Financial Management:

Record project incomes and expenses.
Track company-wide expenses and incomes.
View project-specific financials.
Transaction Management:

View all transactions.
Filter by user, project, and type (income, expense, deposit, withdrawal).
Profit Management:

Calculate project profits.
Calculate company-wide profits/losses.
Distribute project profits equally among users.
User Wallet Management:

View user wallet balances.
Approve/Reject withdrawal requests.
Manage deposits.
Reports and Analytics:

Project-wise profit/loss summary.
User-specific profit summary.
Overall company profit/loss report.
UI Features:

Bootstrap-based design.
Pop-up modals for actions (Add/Edit/Delete/Withdraw/Deposit).
Dynamic content loading with JavaScript.
Would you like me to start coding the project management section or something else first? 🚀

10870
4970
1557


1. Company Income and Expenses
Instead of associating income and expenses with specific projects, you can treat them as general transactions. You can use the transactions table to track all types of income and expenses. For example:

Income: Record the company's general income in the transactions table with transaction_type = 'income' and the relevant category (e.g., "Company income").
Expenses: Record the company's expenses with transaction_type = 'expense' and a specific category (e.g., "Company expenses").

2. Withdrawals and Deposits (Per User)
Withdrawals and deposits are handled using the user_wallets table for each user's balance, and you can record these transactions in the transactions table as well.

Deposits: When a user deposits money into their wallet, the transaction_type is set to 'deposit'.
Withdrawals: When a user withdraws money from their wallet, the transaction_type is set to 'withdrawal'.

3. Project Profits and Losses
Project profits and losses can still be handled in the project_profit table. The profit can be recorded for each project, and the distribution of that profit can be tracked in the project_profit_distribution table.

To distribute profits equally among users:

You can calculate the total distributed profit by adding up the amounts in project_profit_distribution where status = 'paid'.

4. Equal Profit Distribution
In the project_profit_distribution table, you can distribute profits equally among users assigned to each project. Each user’s distribution is tracked, and you can query for their profits per project.

5. Company Profit/Loss Calculation
If you want to calculate the overall company profit or loss, you can:

Calculate the total income from the transactions table where transaction_type = 'income'.
Calculate the total expenses from the transactions table where transaction_type = 'expense'.

6. User Profits
User profits are tracked in the project_profit_distribution table, where you distribute the profits equally among project users. You can also track the total profit a user has received.

Workflow Example: Admin Panel
Create a Project:

Admin enters project details (e.g., name, description, start date).
Project is created in the projects table.
Assign Users to Project:

Admin selects users to assign to the project.
A record is inserted into the project_users table for each user.
Profit Distribution:

When the project generates profits, the admin calculates the total profit and equally distributes it among users by inserting records into the project_profit_distribution table.
Tracking Payment:

Admin can view the project_profit_distribution table to see which users have been paid and update the status field to 'paid' once the payment is completed.


id	User	Amount	Type	Date
1	Alice Johnson	2,223.67 USD	deposit	2025-03-14 17:34:42
2	Bob Smith	5,636.67 USD	deposit	2025-03-14 17:34:42
3	Charlie Brown	11,536.67 USD	deposit	2025-03-14 17:34:42


<?php
include 'config.php';
session_start();

// Handle User CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $addUserQuery = "INSERT INTO users (name) VALUES ('$name')";
        mysqli_query($conn, $addUserQuery);
        $_SESSION['success_message'] = "User added successfully!";
    } elseif (isset($_POST['edit_user'])) {
        $userId = $_POST['user_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $editUserQuery = "UPDATE users SET name = '$name' WHERE user_id = '$userId'";
        mysqli_query($conn, $editUserQuery);
        $_SESSION['success_message'] = "User updated successfully!";
    } elseif (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        $deleteUserQuery = "DELETE FROM users WHERE user_id = '$userId'";
        mysqli_query($conn, $deleteUserQuery);
        $_SESSION['success_message'] = "User deleted successfully!";
    }
    header("Location: user_wallets.php");
    exit();
}

// Handle Transactions (Deposit/Withdrawal)
if (isset($_POST['transaction'])) {
    $userId = $_POST['user_id'];
    $amount = floatval($_POST['amount']);
    $transactionType = $_POST['transaction_type'];

    // Fetch Current Balance
    $walletQuery = "SELECT SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) AS balance
                    FROM user_wallets WHERE user_id = '$userId'";
    $walletResult = mysqli_query($conn, $walletQuery);
    $walletRow = mysqli_fetch_assoc($walletResult);
    $currentBalance = $walletRow['balance'] ?? 0;

    if ($transactionType === 'withdrawal' && $amount > $currentBalance) {
        $_SESSION['error_message'] = "Insufficient balance for withdrawal!";
    } else {
        $insertQuery = "INSERT INTO user_wallets (user_id, amount, transaction_type)
                        VALUES ('$userId', '$amount', '$transactionType')";
        mysqli_query($conn, $insertQuery);
        $_SESSION['success_message'] = ucfirst($transactionType) . " successful!";
    }
    header("Location: user_wallets.php");
    exit();
}

// Fetch Users with Total Transactions
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

$usersQuery = "
    SELECT u.user_id, u.name,
           IFNULL(SUM(CASE WHEN uw.transaction_type = 'deposit' THEN uw.amount ELSE -uw.amount END), 0) AS balance,
           COUNT(uw.user_id) AS total_transactions
    FROM users u
    LEFT JOIN user_wallets uw ON u.user_id = uw.user_id
    WHERE u.name LIKE '%$search%'
    GROUP BY u.user_id, u.name
    LIMIT $perPage OFFSET $offset
";
$usersResult = mysqli_query($conn, $usersQuery);

// Get total users for pagination
$totalUsersQuery = "SELECT COUNT(*) AS total FROM users WHERE name LIKE '%$search%'";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];
$totalPages = ceil($totalUsers / $perPage);
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management & Wallets</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1 class="my-4">User Management & Wallets</h1>

    <!-- Success / Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <!-- Add New User Button -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">Add New User</button>

    <!-- Search Bar -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by Name..." value="<?php echo $search; ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <!-- User List Table -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Balance</th>
                <th>Transactions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($usersResult)) { ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['name']; ?></td>
                    <td><?php echo number_format($user['balance'], 2); ?> USD</td>
                    <td><?php echo $user['total_transactions']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editUserModal<?php echo $user['user_id']; ?>">Edit</button>
                        <form action="user_wallets.php" method="POST" style="display: inline-block;">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- Edit User Modal -->
                <div class="modal fade" id="editUserModal<?php echo $user['user_id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="user_wallets.php" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $user['name']; ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
