<?php
require 'db_connect.php';

// Fetch users and projects once
$allUsers = $conn->query("SELECT user_id, name FROM users")->fetch_all(MYSQLI_ASSOC);
$allProjects = $conn->query("SELECT project_id, name FROM projects")->fetch_all(MYSQLI_ASSOC);

// Fetch Projects for Dropdown
$projects = $conn->query("SELECT project_id, name FROM projects");

// Fetch Users for Withdraw/Deposit
$users = $conn->query("SELECT user_id, name FROM users");

// Company-wide Financial Overview with Deposits and Withdrawals
$companyFinanceQuery = $conn->query("
    SELECT
        SUM(CASE WHEN transaction_type IN ('income', 'deposit') THEN amount ELSE 0 END) AS total_income,
        SUM(CASE WHEN transaction_type IN ('expense', 'withdrawal') THEN amount ELSE 0 END) AS total_expense
    FROM transactions
");
$companyFinance = $companyFinanceQuery->fetch_assoc();
$company_profit = $companyFinance['total_income'] - $companyFinance['total_expense'];

// Fetch Project-Specific Financials
$projectFinancials = $conn->query("
    SELECT p.name,
           SUM(CASE WHEN t.transaction_type='income' THEN t.amount ELSE 0 END) AS income,
           SUM(CASE WHEN t.transaction_type='expense' THEN t.amount ELSE 0 END) AS expense
    FROM projects p
    LEFT JOIN transactions t ON p.project_id = t.project_id
    GROUP BY p.project_id
");

// Fetch All Transactions with User Info for Withdraw/Deposit
// Fetch All Transactions with User Info for Withdraw/Deposit
$transactions = $conn->query("
    SELECT t.transaction_id, t.amount, t.transaction_type, t.description, t.transaction_date,
           p.name AS project_name, u.name AS user_name
    FROM transactions t
    LEFT JOIN projects p ON t.project_id = p.project_id
    LEFT JOIN users u ON t.user_id = u.user_id
    ORDER BY t.transaction_date DESC
");
// Fetch all users with their wallet balances
$userWalletsQuery = $conn->query("
    SELECT u.name, uw.amount
    FROM users u
    LEFT JOIN user_wallets uw ON u.user_id = uw.user_id
    ORDER BY uw.amount DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Financial Management</h2>

        <!-- Company-Wide Overview -->
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Income</div>
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($companyFinance['total_income'], 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Expenses</div>
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($companyFinance['total_expense'], 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Company Profit</div>
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($company_profit, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Per User Wallet</div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        <?php while ($userWallet = $userWalletsQuery->fetch_assoc()): ?>
                            <p>
                                <strong><?php echo htmlspecialchars($userWallet['name']); ?>:</strong>
                                $<?php echo number_format($userWallet['amount'] ?? 0, 2); ?>
                            </p>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addFinanceModal">Add Income/Expense</button>
        <button class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#userTransactionModal">User Deposit/Withdraw</button>

        <!-- Transactions Table -->
        <h4>All Transactions</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Project</th>
                    <th>Amount ($)</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = $transactions->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $transaction['transaction_id']; ?></td>
                        <td><?php echo $transaction['project_name'] ?? 'Company-Wide'; ?></td>
                        <td><?php echo number_format($transaction['amount'], 2); ?></td>
                        <td class="<?php echo $transaction['transaction_type'] === 'income' ? 'text-success' : 'text-danger'; ?>">
                            <?php echo ucfirst($transaction['transaction_type']); ?>
                        </td>
                        <td><?php echo $transaction['description']; ?></td>
                        <td>
                            <?php
                            if ($transaction['transaction_type'] === 'withdrawal' || $transaction['transaction_type'] === 'deposit') {
                                echo $transaction['user_name'] ?? 'N/A';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php echo date("Y-m-d", strtotime($transaction['transaction_date'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn"
                                data-id="<?php echo $transaction['transaction_id']; ?>"
                                data-amount="<?php echo $transaction['amount']; ?>"
                                data-type="<?php echo $transaction['transaction_type']; ?>"
                                data-description="<?php echo $transaction['description']; ?>"
                                data-user-id="<?php echo $transaction['user_id'] ?? ''; ?>"
                                data-project-id="<?php echo $transaction['project_id'] ?? ''; ?>"
                                data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn"
                                data-id="<?php echo $transaction['transaction_id']; ?>"
                                data-bs-toggle="modal" data-bs-target="#deleteTransactionModal">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Transaction Modal -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTransactionForm">
                    <div id="transactionAlert" class="alert d-none" role="alert"></div>
                    <div class="modal-body">
                        <input type="hidden" name="transaction_id" id="editTransactionId">

                        <!-- Type Selection -->
                        <div class="mb-3">
                            <label for="editType" class="form-label">Transaction Type</label>
                            <select name="type" id="editType" class="form-select" required>
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>

                        <!-- User Selection (for deposit/withdrawal) -->
                        <div class="mb-3" id="userSelectContainer">
                            <label for="editUserId" class="form-label">User</label>
                            <select name="user_id" id="editUserId" class="form-select">
                                <option value="">Select User</option>
                                <?php foreach ($allUsers as $user): ?>
                                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Project Selection (for income/expense) -->
                        <div class="mb-3" id="projectSelectContainer">
                            <label for="editProjectId" class="form-label">Project</label>
                            <select name="project_id" id="editProjectId" class="form-select">
                                <option value="">Company-Wide</option>
                                <?php foreach ($allProjects as $project): ?>
                                    <option value="<?php echo $project['project_id']; ?>"><?php echo $project['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="mb-3">
                            <label for="editAmount" class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" id="editAmount" class="form-control" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Delete Transaction Modal -->
<div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-labelledby="deleteTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="delete_transaction.php" method="POST">
                    <div class="modal-body">
                        <div id="transactionAlert" class="alert d-none" role="alert"></div>
                        <input type="hidden" name="transaction_id" id="deleteTransactionId">
                        <p>Are you sure you want to delete this transaction?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Add Income/Expense Modal -->
        <div class="modal fade" id="addFinanceModal" tabindex="-1" aria-labelledby="addFinanceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFinanceModalLabel">Add Income/Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="add_finance.php" method="POST">
                        <div class="modal-body">
                            <div id="transactionAlert" class="alert d-none" role="alert"></div>
                            <div class="mb-3">
                                <label for="transactionType" class="form-label">Transaction Type</label>
                                <select name="transaction_type" id="transactionType" class="form-select" required>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="projectId" class="form-label">Project (Optional)</label>
                                <select name="project_id" id="projectId" class="form-select">
                                    <option value="">Company-Wide</option>
                                    <?php while ($project = $projects->fetch_assoc()): ?>
                                        <option value="<?php echo $project['project_id']; ?>"><?php echo $project['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Transaction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Deposit/Withdraw Modal -->
        <div class="modal fade" id="userTransactionModal" tabindex="-1" aria-labelledby="userTransactionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userTransactionModalLabel">User Deposit/Withdraw</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="userTransactionForm" action="add_user_transaction.php" method="POST">
                        <div class="modal-body">
                            <div id="transactionAlert" class="alert d-none" role="alert"></div>
                            <div class="mb-3">
                                <label for="userId" class="form-label">User</label>
                                <select name="user_id" id="userId" class="form-select" required>
                                    <option value="">Select User</option>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo $user['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="transactionTypeUser" class="form-label">Transaction Type</label>
                                <select name="transaction_type" id="transactionTypeUser" class="form-select" required>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdraw</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="userAmount" class="form-label">Amount</label>
                                <input type="number" step="0.01" name="amount" id="userAmount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="userDescription" class="form-label">Description</label>
                                <textarea name="description" id="userDescription" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script>

        // Handle User Deposit/Withdraw Form Submission
        document.getElementById('userTransactionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const alertBox = form.querySelector('#transactionAlert');

            fetch('add_user_transaction.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alertBox.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                    alertBox.textContent = data.message;
                    alertBox.classList.remove('d-none');

                    if (data.success) {
                        form.reset();
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('userTransactionModal')).hide();
                            window.location.reload();
                        }, 1000);
                    }
                })
                .catch(error => {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'An error occurred. Please try again.';
                    alertBox.classList.remove('d-none');
                });
        });


        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('editTransactionForm');
            const alertBox = document.getElementById('transactionAlert');
            const typeSelect = document.getElementById('editType');
            const userSelectContainer = document.getElementById('userSelectContainer');
            const projectSelectContainer = document.getElementById('projectSelectContainer');

            // Show/hide fields based on transaction type
            const handleTypeChange = () => {
                const selectedType = typeSelect.value;
                userSelectContainer.style.display = (selectedType === 'deposit' || selectedType === 'withdrawal') ? 'block' : 'none';
                projectSelectContainer.style.display = (selectedType === 'income' || selectedType === 'expense') ? 'block' : 'none';
            };

            typeSelect.addEventListener('change', handleTypeChange);

            // Populate modal fields on edit button click
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const transactionId = button.dataset.id;
                    const transactionType = button.dataset.type;
                    const userId = button.dataset.userId;
                    const projectId = button.dataset.projectId;

                    editForm.reset();
                    document.getElementById('editTransactionId').value = transactionId;
                    typeSelect.value = transactionType;
                    document.getElementById('editAmount').value = button.dataset.amount;
                    document.getElementById('editDescription').value = button.dataset.description;

                    if (userId) document.getElementById('editUserId').value = userId;
                    if (projectId) document.getElementById('editProjectId').value = projectId;

                    const handleTypeChange = () => {
                        const selectedType = typeSelect.value;
                        userSelectContainer.style.display = (selectedType === 'deposit' || selectedType === 'withdrawal') ? 'block' : 'none';
                        projectSelectContainer.style.display = (selectedType === 'income' || selectedType === 'expense') ? 'block' : 'none';
                    };

                });
            });

            // Form submission with improved error handling
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                alertBox.classList.add('d-none'); // Hide alert initially

                try {
                    const formData = new FormData(editForm);
                    const response = await fetch('edit_transaction.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    alertBox.textContent = data.message;
                    alertBox.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                    alertBox.classList.remove('d-none');

                    if (data.success) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } catch (error) {
                    alertBox.textContent = 'An error occurred. Please try again.';
                    alertBox.className = 'alert alert-danger';
                    alertBox.classList.remove('d-none');
                }
            });
        });

        // Delete Transaction Button - populate modal field
        document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('deleteTransactionId').value = this.dataset.id;
                });
            });

            // Handle Delete Transaction Form Submission
            const deleteForm = document.querySelector('#deleteTransactionModal form');
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(deleteForm);
                console.log('Transaction ID Sent:', formData.get('transaction_id')); // Debugging
                const alertBox = deleteForm.querySelector('#transactionAlert');

                fetch('delete_transaction.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        alertBox.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                        alertBox.textContent = data.message;
                        alertBox.classList.remove('d-none');

                        if (data.success) {
                            setTimeout(() => {
                                bootstrap.Modal.getInstance(document.getElementById('deleteTransactionModal')).hide();
                                window.location.reload();
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        alertBox.className = 'alert alert-danger';
                        alertBox.textContent = 'An error occurred. Please try again.';
                        alertBox.classList.remove('d-none');
                    });
            });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>