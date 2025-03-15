<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Calculate total income from transactions
$incomeQuery = "SELECT SUM(amount) AS total_income FROM transactions WHERE transaction_type = 'income'";
$incomeResult = $conn->query($incomeQuery);
$total_income = $incomeResult->fetch_assoc()['total_income'] ?? 0;

// Calculate total expenses from transactions
$expenseQuery = "SELECT SUM(amount) AS total_expenses FROM transactions WHERE transaction_type = 'expense'";
$expenseResult = $conn->query($expenseQuery);
$total_expenses = $expenseResult->fetch_assoc()['total_expenses'] ?? 0;

// Calculate total profit
$total_profit = $total_income - $total_expenses;

// Fetch all transactions
$transactionQuery = "SELECT * FROM transactions ORDER BY transaction_id DESC";
$transactionResult = mysqli_query($conn, $transactionQuery);

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $transactionQuery = "SELECT * FROM transactions WHERE transaction_date BETWEEN '$startDate' AND '$endDate' ORDER BY transaction_date DESC";
    $transactionResult = mysqli_query($conn, $transactionQuery);
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transactions Log</title>
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
    <?php if (isset($_SESSION['success_message'])) { ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message'];
                                            unset($_SESSION['success_message']); ?></div>
    <?php } ?>

    <?php if (isset($_SESSION['error_message'])) { ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message'];
                                        unset($_SESSION['error_message']); ?></div>
    <?php } ?>
    <div class="main-content">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="stat-number">Total Income</div>
                    <div class="card-body">
                        <h5 class="card-title text-success">₹ <?php echo number_format($total_income, 2); ?> </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="stat-number ">Total Expenses</div>
                    <div class="card-body">
                        <h5 class="card-title text-danger">₹ <?php echo number_format($total_expenses, 2); ?> </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="stat-number">Total Profit</div>
                    <div class="card-body">
                        <h5 class="card-title text-primary">₹ <?php echo number_format($total_profit, 2); ?> </h5>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <!-- Button to open the modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#transactionModal">
            View All Transactions
        </button>

        <h2>Add New Transaction</h2>
        <form action="add_transaction.php" method="POST">
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" name="amount" id="amount" required>
            </div>
            <div class="form-group">
                <label for="transaction_type">Transaction Type</label>
                <select class="form-control" name="transaction_type" id="transaction_type" required>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" class="form-control" name="description" id="description" required>
            </div>
            <button type="submit" class="btn btn-success">Add Transaction</button>
        </form>
    </div>

    <div class="container">

        <!-- Transactions Modal -->
        <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transactionModalLabel">All Transactions</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body finance-card">
                        <table class="table no-border">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($transaction = mysqli_fetch_assoc($transactionResult)) { ?>
                                    <tr>
                                        <td><?php echo $transaction['transaction_id']; ?></td>
                                        <td>₹ <?php echo number_format($transaction['amount'], 2); ?> </td>
                                        <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                                        <td><?php echo $transaction['description']; ?></td>
                                        <td><?php echo $transaction['transaction_date']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


</body>

</html>