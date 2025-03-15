<?php
include 'config.php';
session_start();

// Fetch all withdrawal requests
$withdrawalQuery = "SELECT * FROM withdrawal_requests WHERE status = 'pending'";
$withdrawalResult = mysqli_query($conn, $withdrawalQuery);

// Handle approval/rejection
if (isset($_GET['action']) && isset($_GET['request_id'])) {
    $requestId = $_GET['request_id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $updateQuery = "UPDATE withdrawal_requests SET status = 'approved' WHERE request_id = '$requestId'";
        mysqli_query($conn, $updateQuery);
    } elseif ($action == 'reject') {
        $updateQuery = "UPDATE withdrawal_requests SET status = 'rejected' WHERE request_id = '$requestId'";
        mysqli_query($conn, $updateQuery);
    }
    header("Location: withdrawal_requests.php"); // Refresh the page after action
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdrawal Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1>Withdrawal Requests</h1>
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($request = mysqli_fetch_assoc($withdrawalResult)) { ?>
                <tr>
                    <td><?php echo $request['user_id']; ?></td>
                    <td><?php echo number_format($request['amount'], 2); ?> USD</td>
                    <td><?php echo $request['request_date']; ?></td>
                    <td><?php echo $request['status']; ?></td>
                    <td>
                        <a href="withdrawal_requests.php?action=approve&request_id=<?php echo $request['request_id']; ?>" class="btn btn-success">Approve</a>
                        <a href="withdrawal_requests.php?action=reject&request_id=<?php echo $request['request_id']; ?>" class="btn btn-danger">Reject</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
