<?php
// Database credentials - महेन्द्र जी, IP और Hostname यहाँ अपडेट कर दिए हैं
$host = "100.101.113.86";    // आपका असली IP (172.17.0.1 से बेहतर है)
$port = "3307";              // आपका MySQL पोर्ट
$username = "root";           
$password = "admin123";  
$dbname = "coaching_cms";

// कनेक्शन के लिए host.docker.internal भी एक अच्छा विकल्प होता है अगर IP काम न करे
$conn = mysqli_connect($host, $username, $password, $dbname, $port);

// Connection check karein
if (!$conn) {
    // अगर फिर भी कनेक्ट न हो, तो यह 'host.docker.internal' ट्राई करेगा
    $host_alt = "host.docker.internal";
    $conn = mysqli_connect($host_alt, $username, $password, $dbname, $port);
    
    if (!$conn) {
        echo "<h1>Database Connection Failed!</h1>";
        echo "Error: " . mysqli_connect_error() . "<br>";
        exit;
    }
}

// MySQL 8.0 की डेट एरर और स्ट्रिक्ट मोड को बंद करने के लिए
mysqli_query($conn, "SET sql_mode=''");

// हिंदी नाम के लिए
mysqli_set_charset($conn, "utf8mb4");

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone set
date_default_timezone_set('Asia/Kolkata');
?>