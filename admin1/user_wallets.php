<?php
include 'config.php';
session_start();

// Fetch all user wallet transactions
$walletQuery = "SELECT * FROM user_wallets";
$walletResult = mysqli_query($conn, $walletQuery);

// Handle deposit/withdrawal transactions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $amount = $_POST['amount'];
    $transactionType = $_POST['transaction_type'];

    $insertQuery = "INSERT INTO user_wallets (user_id, amount, transaction_type)
                    VALUES ('$userId', '$amount', '$transactionType')";
    mysqli_query($conn, $insertQuery);
    header("Location: user_wallets.php"); // Refresh after insertion
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Wallets</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>User Wallets</h1>
    <form action="user_wallets.php" method="POST">
        <div class="form-group">
            <label for="user_id">User</label>
            <select class="form-control" name="user_id" id="user_id">
                <!-- Fetch and display users -->
                <?php
                $usersQuery = "SELECT * FROM users";
                $usersResult = mysqli_query($conn, $usersQuery);
                while ($user = mysqli_fetch_assoc($usersResult)) {
                    echo "<option value='".$user['user_id']."'>".$user['name']."</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" name="amount" id="amount" required>
        </div>
        <div class="form-group">
            <label for="transaction_type">Transaction Type</label>
            <select class="form-control" name="transaction_type" id="transaction_type">
                <option value="deposit">Deposit</option>
                <option value="withdrawal">Withdrawal</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Transaction</button>
    </form>

    <h3>All Wallet Transactions</h3>
    <table class="table">
        <thead>
            <tr>
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
                    <td><?php echo number_format($wallet['amount'], 2); ?> USD</td>
                    <td><?php echo $wallet['transaction_type']; ?></td>
                    <td><?php echo $wallet['date']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
