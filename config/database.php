<?php
// Define the database connection parameters
$host = '127.0.0.1'; // Database host
$dbname = 'nvc'; // Database name
$username = 'root'; // Database username (default for XAMPP)
$password = ''; // Database password (default for XAMPP)

// Build the DSN string for PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Handle connection errors
    echo "Database connection failed: " . $e->getMessage();
}
?>

