<?php
// Aiven MySQL Cloud Credentials
$host = "mysql-9d9cc53-xxxx.aivencloud.com"; // Aiven का Host/URI नाम
$port = "25232";                              // Aiven का Service Port
$username = "avnadmin";                       // Aiven का User
$password = "your_aiven_password";           // Aiven का Password
$dbname = "coaching_cms";                     // आपका Database Name

// SSL Mode enable karke connect karein
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

// Connection check
if (!$conn) {
    echo "<h1>Database Connection Failed!</h1>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    exit;
}

// SQL Strict Mode aur Primary Key Constraint Override
mysqli_query($conn, "SET SESSION sql_require_primary_key = 0;");
mysqli_query($conn, "SET sql_mode=''");

// Character set & Timezone
mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Kolkata');
?>
