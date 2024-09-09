<?php
// view_order.php
include '../config/database.php';
include '../includes/functions.php';

$orderId = $_GET['id'] ?? '';

if (!$orderId) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Decode JSON data for items_purchased
$itemsPurchased = json_decode($order['items_purchased'], true);
$itemsText = '';

if (is_array($itemsPurchased)) {
    foreach ($itemsPurchased as $item) {
        $itemsText .= htmlspecialchars($item['name']) . ' - $' . htmlspecialchars($item['price']) . ' x ' . htmlspecialchars($item['quantity']) . ' (Total: $' . htmlspecialchars($item['total']) . ')<br>';
    }
} else {
    $itemsText = 'No items';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .print-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Order Details</h1>

        <table class="table table-bordered">
            <tr>
                <th>Order ID</th>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td><?= htmlspecialchars($order['full_name']) ?></td>
            </tr>
            <tr>
                <th>Shipping Address</th>
                <td><?= htmlspecialchars($order['shipping_address']) ?></td>
            </tr>
            <tr>
                <th>Contact Information</th>
                <td><?= htmlspecialchars($order['contact_information']) ?></td>
            </tr>
            <tr>
                <th>Items Purchased</th>
                <td><?= $itemsText ?></td>
            </tr>
            <tr>
                <th>Price</th>
                <td>$<?= htmlspecialchars(number_format($order['price'], 2)) ?></td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td><?= htmlspecialchars($order['payment_method']) ?></td>
            </tr>
            <tr>
                <th>Payment Status</th>
                <td><?= htmlspecialchars($order['payment_status']) ?></td>
            </tr>
            <tr>
                <th>Paid Amount</th>
                <td>$<?= htmlspecialchars(number_format($order['paid_amount'], 2)) ?></td>
            </tr>
            <tr>
                <th>Remaining Amount</th>
                <td>$<?= htmlspecialchars(number_format($order['remaining_amount'], 2)) ?></td>
            </tr>
            <tr>
                <th>Special Instructions</th>
                <td><?= htmlspecialchars($order['special_instructions']) ?></td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
            </tr>
        </table>

        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
        <button class="btn btn-secondary print-button" onclick="window.print()">Print</button>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
