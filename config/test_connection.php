<?php
// test_connection.php

// Include the database configuration file
include 'config/database.php';

try {
    // Try to execute a simple query
    $stmt = $db->query("SELECT 1");
    if ($stmt) {
        echo "Database connection is successful!";
    } else {
        echo "Database connection failed!";
    }
} catch (PDOException $e) {
    // Catch and display any errors
    echo "Database connection error: " . $e->getMessage();
}
?>
