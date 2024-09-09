<?php
// Database connection
function getDbConnection() {
    $host = '127.0.0.1'; // Database host
    $dbname = 'nvc'; // Database name
    $username = 'root'; // Database username (default for XAMPP)
    $password = ''; // Database password (default for XAMPP)

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}

// Sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validate price
function validatePrice($price) {
    return is_numeric($price) && $price >= 0;
}

// Validate and calculate remaining amount
function calculateRemainingAmount($price, $paidAmount) {
    return $price - $paidAmount;
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}
?>
