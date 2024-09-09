<?php
// Include database configuration
require '../config/database.php';
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve and sanitize form data
        $fullName = htmlspecialchars($_POST['full_name']);
        $shippingAddress = htmlspecialchars($_POST['shipping_address']);
        $nearestLandmark = htmlspecialchars($_POST['nearest_landmark']);
        $contactInfo = htmlspecialchars($_POST['contact_information']);
        $itemsPurchased = isset($_POST['items_purchased']) ? $_POST['items_purchased'] : '[]'; // JSON string of items purchased
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
        $paymentMethod = htmlspecialchars($_POST['payment_method']);
        $paymentStatus = htmlspecialchars($_POST['payment_status']);
        $paidAmount = isset($_POST['paid_amount']) ? (float)$_POST['paid_amount'] : 0;
        $remainingAmount = ($paymentStatus === 'partial') ? $price - $paidAmount : 0;
        $specialInstructions = htmlspecialchars($_POST['special_instructions']);

        // Generate a unique order ID
        $orderId = date('ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO customers (order_id, full_name, shipping_address, nearest_landmark, contact_information, items_purchased, price, payment_method, payment_status, paid_amount, remaining_amount, special_instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Execute the statement with the form data
        $stmt->execute([$orderId, $fullName, $shippingAddress, $nearestLandmark, $contactInfo, $itemsPurchased, $price, $paymentMethod, $paymentStatus, $paidAmount, $remainingAmount, $specialInstructions]);

        // Redirect to the index page after order creation
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 8px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .product-item {
            margin-bottom: 10px;
        }
        .total-price {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Order</h2>
        <form id="orderForm" method="post" action="create_order.php">
            <!-- Add Custom Item Button at the Top -->
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="openModal()">Add Custom Item</button>
            </div>

            <!-- Products Section -->
            <div class="product-list" id="productList">
                <div>No items added.</div>
            </div>

            <!-- Customer Details Section -->
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="full_name" placeholder="e.g. Ram Bahadur" required>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="contact_information" placeholder="e.g. 9812345678" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="e.g. Kathmandu" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="shipping_address" placeholder="e.g. Ward No. 5" required>
            </div>
            <div class="form-group">
                <label for="nearestLandmark">Nearest Landmark</label>
                <input type="text" class="form-control" id="nearestLandmark" name="nearest_landmark" placeholder="e.g. Near Central Park" required>
            </div>
            <div class="form-group">
                <label for="paymentMethod">Payment Method</label>
                <select id="paymentMethod" name="payment_method" class="form-control" required>
                    <option value="COD">COD</option>
                    <option value="Online">Online</option>
                </select>
            </div>
            <div class="form-group">
                <label for="paymentStatus">Payment Status</label>
                <select id="paymentStatus" name="payment_status" class="form-control" onchange="togglePartialPaymentInput()" required>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                </select>
            </div>
            <div class="form-group" id="partialPaymentDiv" style="display: none;">
                <label for="paidAmount">Partial Payment</label>
                <input type="number" id="partialPayment" name="paid_amount" class="form-control" step="0.01" placeholder="e.g. 300">
            </div>
            <input type="hidden" id="totalAmount" name="price" value="0"> <!-- Placeholder for total amount -->
            <input type="hidden" id="itemsPurchased" name="items_purchased" value='[]'> <!-- Placeholder for items purchased -->
            <div class="form-group">
                <label for="specialInstructions">Special Instructions</label>
                <textarea id="specialInstructions" name="special_instructions" class="form-control" placeholder="Enter any special instructions here..."></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Submit Order</button>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Order Created Successfully!</h4>
                <span class="close" onclick="closeSuccessModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p class="text-center">Your order has been created successfully.</p>
            </div>
            <div class="modal-footer">
                <a href="index.php" class="btn btn-success">OK</a>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Custom Item -->
    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add Custom Item</h2>
            <form id="addItemForm" onsubmit="addItem(event)">
                <div class="form-group">
                    <label for="productName">Product Name</label>
                    <input type="text" id="productName" name="productName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Item</button>
            </form>
        </div>
    </div>

    <script>
        let products = [];
        let itemCount = 1;

        function openModal() {
            document.getElementById('addItemModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addItemModal').style.display = 'none';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        function addItem(event) {
            event.preventDefault(); // Prevent the form from submitting
            const productName = document.getElementById('productName').value;
            const price = parseFloat(document.getElementById('price').value);
            const quantity = parseInt(document.getElementById('quantity').value);
            const total = price * quantity;

            // Add item to the products array
            products.push({ name: productName, price: price, quantity: quantity, total: total });

            // Reset form
            document.getElementById('addItemForm').reset();
            closeModal();

            // Update product list
            updateProductList();
        }

        function updateProductList() {
            const productList = document.getElementById('productList');
            productList.innerHTML = ''; // Clear the current list

            if (products.length === 0) {
                productList.innerHTML = '<div>No items added.</div>';
                return;
            }

            // Display each product
            products.forEach((product, index) => {
                const productItem = document.createElement('div');
                productItem.classList.add('product-item');
                productItem.innerHTML = `
                    <span>Item${itemCount + index}: ${product.name} - Rs. ${product.price} x ${product.quantity} = Rs. ${product.total}</span>
                    <button class="btn btn-danger btn-sm" onclick="removeItem(${index})">❌</button>
                `;
                productList.appendChild(productItem);
            });

            // Update total price
            const totalPrice = products.reduce((acc, product) => acc + product.total, 0);
            const totalPriceDiv = document.createElement('div');
            totalPriceDiv.classList.add('total-price');
            totalPriceDiv.textContent = `Total: Rs. ${totalPrice.toFixed(2)}`;
            productList.appendChild(totalPriceDiv);

            // Update hidden totalAmount field
            document.getElementById('totalAmount').value = totalPrice.toFixed(2);

            // Update hidden input with JSON string of products array
            document.getElementById('itemsPurchased').value = JSON.stringify(products);
        }

        function removeItem(index) {
            products.splice(index, 1);
            updateProductList();
        }

        function togglePartialPaymentInput() {
            const paymentStatus = document.getElementById('paymentStatus').value;
            const partialPaymentDiv = document.getElementById('partialPaymentDiv');
            partialPaymentDiv.style.display = paymentStatus === 'partial' ? 'block' : 'none';
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
