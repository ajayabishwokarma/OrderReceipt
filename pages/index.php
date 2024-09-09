<?php
include '../includes/functions.php';

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && !empty($_POST['order_ids'])) {
    $orderIds = $_POST['order_ids'];

    try {
        $pdo = getDbConnection();
        
        // Prepare and execute the delete query
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id IN ($placeholders)");
        $stmt->execute($orderIds);

        // Redirect back to the index page after deletion
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        echo "Error deleting orders: " . htmlspecialchars($e->getMessage());
    }
}

// Handle individual delete
if (isset($_GET['delete_id'])) {
    $orderId = $_GET['delete_id'] ?? '';

    if ($orderId) {
        try {
            $pdo = getDbConnection();
            
            // Prepare and execute the delete query
            $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
            $stmt->execute([$orderId]);

            // Redirect back to the index page after deletion
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            echo "Error deleting order: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Pagination logic
$itemsPerPage = isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Filtering logic
$filterStatus = $_GET['status'] ?? '';
$dateRange = $_GET['date_range'] ?? '';

$today = date('Y-m-d');
$startDate = $endDate = '';

switch ($dateRange) {
    case 'today':
        $startDate = $endDate = $today;
        break;
    case 'yesterday':
        $startDate = $endDate = date('Y-m-d', strtotime('-1 day'));
        break;
    case '7days':
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = $today;
        break;
    case '30days':
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = $today;
        break;
    case '90days':
        $startDate = date('Y-m-d', strtotime('-90 days'));
        $endDate = $today;
        break;
    case '365days':
        $startDate = date('Y-m-d', strtotime('-365 days'));
        $endDate = $today;
        break;
    default:
        break;
}

$query = "SELECT * FROM customers WHERE 1";

if ($filterStatus) {
    $query .= " AND payment_status = '$filterStatus'";
}

if ($startDate && $endDate) {
    $query .= " AND created_at BETWEEN '$startDate' AND '$endDate'";
}

$query .= " ORDER BY created_at DESC LIMIT $itemsPerPage OFFSET $offset";

$pdo = getDbConnection();
$result = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM customers WHERE 1";
if ($filterStatus) {
    $countQuery .= " AND payment_status = '$filterStatus'";
}
if ($startDate && $endDate) {
    $countQuery .= " AND created_at BETWEEN '$startDate' AND '$endDate'";
}
$totalRecords = $pdo->query($countQuery)->fetchColumn();
$totalPages = ceil($totalRecords / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dropbtn {
            background-color: #04AA6D;
            color: white;
            padding: 16px;
            font-size: 16px;
            border: none;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            margin-left: auto;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0; 
            background-color: #f1f1f1;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #ddd;}

        .dropdown:hover .dropdown-content {display: block;}

        .dropdown:hover .dropbtn {background-color: #3e8e41;}
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Order Dashboard</h1>

        <!-- Filter Form -->
        <form method="GET" action="index.php" class="mb-4">
            <div class="form-row align-items-end">
                <div class="col-md-3 mb-3">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Select Status</option>
                        <option value="Paid" <?= $filterStatus === 'Paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="Partial Paid" <?= $filterStatus === 'Partial Paid' ? 'selected' : '' ?>>Partial Paid</option>
                        <option value="Unpaid" <?= $filterStatus === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_range">Date Range</label>
                    <select class="form-control" id="date_range" name="date_range">
                        <option value="">Select Date Range</option>
                        <option value="today" <?= $dateRange === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="yesterday" <?= $dateRange === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                        <option value="7days" <?= $dateRange === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                        <option value="30days" <?= $dateRange === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
                        <option value="90days" <?= $dateRange === '90days' ? 'selected' : '' ?>>Last 90 Days</option>
                        <option value="365days" <?= $dateRange === '365days' ? 'selected' : '' ?>>Last 365 Days</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                </div>
            </div>
        </form>

        <!-- Bulk Actions and Dropdown Menu -->
        <form method="POST" action="index.php">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="bulk-actions" style="display: none;">
                        <button type="submit" name="bulk_delete" class="btn btn-danger">Delete Selected</button>
                        <button type="submit" name="print_selected" class="btn btn-info">Print Selected</button>
                    </div>
                    <div class="dropdown">
    <button class="dropbtn">Dropdown</button>
    <div class="dropdown-content">
        <a href="create_order.php">Create Order</a>
        <!-- Update Export to Excel link to include a query parameter -->
        <a href="../includes/export_to_excel.php?export=1" class="dropdown-item">Export to Excel</a>
    </div>
</div>

                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Shipping Address</th>
                            <th>Nearest Landmark</th>
                            <th>Contact Info</th>
                            <th>Items Purchased</th>
                            <th>Price</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Paid Amount</th>
                            <th>Remaining Amount</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $order): ?>
                            <?php
                            // Decode JSON data for items_purchased
                            $itemsPurchased = json_decode($order['items_purchased'], true);
                            $itemsText = '';

                            if (is_array($itemsPurchased)) {
                                foreach ($itemsPurchased as $item) {
                                    $itemsText .= htmlspecialchars($item['name']) . ' - Rs. ' . htmlspecialchars($item['price']) . ' x ' . htmlspecialchars($item['quantity']) . '<br>';
                                }
                            } else {
                                $itemsText = 'No items';
                            }
                            ?>
                            <tr>
                                <td><input type="checkbox" name="order_ids[]" value="<?= htmlspecialchars($order['id']) ?>"></td>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                                <td><?= htmlspecialchars($order['nearest_landmark']) ?></td>
                                <td><?= htmlspecialchars($order['contact_information']) ?></td>
                                <td><?= $itemsText ?></td>
                                <td><?= htmlspecialchars($order['price']) ?></td>
                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td><?= htmlspecialchars($order['payment_status']) ?></td>
                                <td><?= htmlspecialchars($order['paid_amount']) ?></td>
                                <td><?= htmlspecialchars($order['remaining_amount']) ?></td>
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                                <td>
                                    <a href="view_order.php?id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="?delete_id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Select/Deselect all checkboxes
    document.getElementById('select-all').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        // Update bulk actions visibility
        updateBulkActions();
    });

    // Show/hide bulk actions based on selection
    document.querySelectorAll('input[name="order_ids[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        var selectedCount = document.querySelectorAll('input[name="order_ids[]"]:checked').length;
        var bulkActions = document.querySelector('.bulk-actions');
        bulkActions.style.display = selectedCount > 0 ? 'block' : 'none';
    }
</script>

</body>
</html>
