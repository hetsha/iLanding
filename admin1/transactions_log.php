<?php
include 'config.php';
session_start();

// Fetch all transactions
$transactionQuery = "SELECT * FROM transactions ORDER BY transaction_date DESC";
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>Transactions Log</h1>

    <form action="transactions_log.php" method="GET">
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" name="start_date" id="start_date">
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" name="end_date" id="end_date">
        </div>
        <button type="submit" class="btn btn-primary">Filter Transactions</button>
    </form>

    <h3>All Transactions</h3>
    <table class="table">
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
                    <td><?php echo number_format($transaction['amount'], 2); ?> USD</td>
                    <td><?php echo $transaction['transaction_type']; ?></td>
                    <td><?php echo $transaction['description']; ?></td>
                    <td><?php echo $transaction['transaction_date']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
