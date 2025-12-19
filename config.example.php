<?php
// Database configuration template
// Copy this file to config.php and fill in your actual credentials

define('DB_HOST', 'mysql-yourusername.alwaysdata.net');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'YOUR_PASSWORD_HERE');  // ← Replace with your actual password
define('DB_NAME', 'your_database_name');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
