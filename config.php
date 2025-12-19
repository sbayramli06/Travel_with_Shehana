<?php
// Database configuration
define('DB_HOST', 'mysql-shehana.alwaysdata.net');
define('DB_USER', 'shehana');
define('DB_PASS', 'ehjh3jhsuu1hs1qQu'); //
define('DB_NAME', 'shehana_drivingexperience');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
