<?php
// Enable all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test database connection
$host = 'mysql-shehana.alwaysdata.net'; // Change 'shehana' to your username
$user = 'shehana'; // Your DB username
$pass = 'YOUR_PASSWORD'; // Your DB password
$db = 'shehana_drivingexperience'; // Your DB name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!<br>";
}

// Test if tables exist
$result = $conn->query("SHOW TABLES");
echo "<h3>Tables in database:</h3>";
while($row = $result->fetch_array()) {
    echo $row[0] . "<br>";
}

$conn->close();
?>
