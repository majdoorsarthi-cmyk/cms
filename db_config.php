<?php
// Aiven MySQL Cloud Credentials
$host = "mysql-9d9cc53-majdoorsarthi-d1a8.k.aivencloud.com";
$port = 13848;
$username = "avnadmin";
$password = "AVNS_CXh977fYw0GUSdyTCUU";
$dbname = "coaching_cms";

// SSL Mode ke saath connection establish karein
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

// Connection check
if (!$conn) {
    echo "<h1>Database Connection Failed!</h1>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    exit;
}

// Primary Key requirement aur Strict mode ko disable karein
mysqli_query($conn, "SET SESSION sql_require_primary_key = 0;");
mysqli_query($conn, "SET sql_mode=''");

// Character encoding aur Timezone
mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Kolkata');
?>
