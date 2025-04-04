<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

// Handle message deletion
if (isset($_POST['delete_message']) && isset($_POST['message_id'])) {
    $message_id = filter_input(INPUT_POST, 'message_id', FILTER_SANITIZE_NUMBER_INT);
    $delete_sql = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

// Handle mark as read/unread
if (isset($_POST['toggle_read']) && isset($_POST['message_id'])) {
    $message_id = filter_input(INPUT_POST, 'message_id', FILTER_SANITIZE_NUMBER_INT);
    $sql = "UPDATE contacts SET is_read = NOT is_read WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

// Get filter parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
$where_clause = "";
$params = [];
$types = "";

if ($filter === 'unread') {
    $where_clause = "WHERE is_read = 0";
} elseif ($filter === 'read') {
    $where_clause = "WHERE is_read = 1";
}

if ($search) {
    $search = "%$search%";
    $where_clause = $where_clause ? "$where_clause AND" : "WHERE";
    $where_clause .= " (name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $params = array_merge($params, [$search, $search, $search]);
    $types .= "sss";
}

// Fetch messages with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$count_sql = "SELECT COUNT(*) as total FROM contacts $where_clause";
if ($params) {
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
} else {
    $total = $conn->query($count_sql)->fetch_assoc()['total'];
}

$total_pages = ceil($total / $per_page);

$sql = "SELECT * FROM contacts $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/upparac6.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <style>
        .message-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .message-card:hover {
            transform: translateY(-2px);
        }
        .message-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }
        .message-body {
            padding: 15px;
        }
        .message-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }
        .unread {
            border-left: 4px solid #007bff;
        }
        .filter-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Contact Messages</h2>

        <!-- Filters -->
        <div class="filter-card">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="btn-group">
                        <a href="?filter=all<?php echo $search ? '&search='.$search : ''; ?>"
                           class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">
                            All
                        </a>
                        <a href="?filter=unread<?php echo $search ? '&search='.$search : ''; ?>"
                           class="btn btn-outline-primary <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                            Unread
                        </a>
                        <a href="?filter=read<?php echo $search ? '&search='.$search : ''; ?>"
                           class="btn btn-outline-primary <?php echo $filter === 'read' ? 'active' : ''; ?>">
                            Read
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form class="d-flex">
                        <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                        <input type="search" name="search" class="form-control me-2"
                               placeholder="Search messages..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($result->num_rows > 0): ?>
            <?php while($message = $result->fetch_assoc()): ?>
                <div class="message-card <?php echo !$message['is_read'] ? 'unread' : ''; ?>">
                    <div class="message-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0"><?php echo htmlspecialchars($message['name']); ?></h5>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </small>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted">
                                    <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="message-body">
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    </div>
                    <div class="message-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" name="toggle_read" class="btn btn-sm btn-outline-primary">
                                    <?php if ($message['is_read']): ?>
                                        <i class="fas fa-envelope me-1"></i> Mark as Unread
                                    <?php else: ?>
                                        <i class="fas fa-envelope-open me-1"></i> Mark as Read
                                    <?php endif; ?>
                                </button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" name="delete_message" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this message?')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?><?php echo $search ? '&search='.$search : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>No messages found</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
