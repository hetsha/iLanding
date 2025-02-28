<?php
require 'config/db.php'; // Include your database connection

// Handle form actions (add, edit, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $project_id = $_POST['project_id'] ?: NULL;
        $amount = $_POST['amount'];
        $type = $_POST['transaction_type'];
        $desc = $_POST['description'];
        $date = $_POST['transaction_date'];
        $recorded_by = $_POST['recorded_by']; // Get selected user from dropdown

        $stmt = $conn->prepare("INSERT INTO transactions (project_id, amount, transaction_type, description, transaction_date, recorded_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$project_id, $amount, $type, $desc, $date, $recorded_by]);
    } elseif ($action === 'edit') {
        $transaction_id = $_POST['transaction_id'];
        $project_id = $_POST['project_id'] ?: NULL;
        $amount = $_POST['amount'];
        $type = $_POST['transaction_type'];
        $desc = $_POST['description'];
        $date = $_POST['transaction_date'];

        $stmt = $conn->prepare("UPDATE transactions SET project_id=?, amount=?, transaction_type=?, description=?, transaction_date=? WHERE transaction_id=?");
        $stmt->execute([$project_id, $amount, $type, $desc, $date, $transaction_id]);
    } elseif ($action === 'delete') {
        $transaction_id = $_POST['transaction_id'];
        $stmt = $conn->prepare("DELETE FROM transactions WHERE transaction_id=?");
        $stmt->execute([$transaction_id]);
    }
}

// Fetch transactions
$result = $conn->query("SELECT t.*, p.name AS project_name FROM transactions t LEFT JOIN projects p ON t.project_id = p.id ORDER BY t.transaction_date DESC");
$transactions = $result->fetch_all(MYSQLI_ASSOC);

// Fetch projects
$result = $conn->query("SELECT id, name FROM projects");
$projects = $result->fetch_all(MYSQLI_ASSOC);

// Fetch users
$result = $conn->query("SELECT * FROM users");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content container mt-4 finance-card">
        <h2>Transactions</h2>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTransactionModal">Add Transaction</button>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Project</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= $transaction['transaction_id'] ?></td>
                        <td><?= $transaction['project_name'] ?: 'General' ?></td>
                        <td><?= ucfirst($transaction['transaction_type']) ?></td>
                        <td>₹<?= number_format($transaction['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($transaction['description']) ?></td>
                        <td><?= $transaction['transaction_date'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn"
                                data-id="<?= $transaction['transaction_id'] ?>"
                                data-project-id="<?= $transaction['project_id'] ?>"
                                data-amount="<?= $transaction['amount'] ?>"
                                data-type="<?= $transaction['transaction_type'] ?>"
                                data-description="<?= htmlspecialchars($transaction['description']) ?>"
                                data-date="<?= $transaction['transaction_date'] ?>"
                                data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                                Edit
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete transaction?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Transaction Modal -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Project (Optional)</label>
                            <select class="form-select" name="project_id">
                                <option value="">General</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select" name="transaction_type" required>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" class="form-control" name="transaction_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="recorded_by">Recorded By:</label>
                            <select name="recorded_by" required>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Transaction Modal -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="transaction_id" id="edit-transaction-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Project (Optional)</label>
                            <select class="form-select" name="project_id" id="edit-project-id">
                                <option value="">General</option>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="edit-amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select" id="edit-type" name="transaction_type" required>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="edit-description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" class="form-control" id="edit-date" name="transaction_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit-transaction-id').value = this.dataset.id;
                document.getElementById('edit-project-id').value = this.dataset.projectId;
                document.getElementById('edit-amount').value = this.dataset.amount;
                document.getElementById('edit-type').value = this.dataset.type;
                document.getElementById('edit-description').value = this.dataset.description;
                document.getElementById('edit-date').value = this.dataset.date;
            });
        });
    </script>
</body>
</html>
