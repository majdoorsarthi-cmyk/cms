<?php
// Session start karein taaki hum use destroy kar sakein
session_start();

// Sabhi session variables ko clear karein
$_SESSION = array();

// Agar session cookie use ho rahi hai, to use bhi expire karein
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Poora session destroy karein
session_destroy();

// Logout hone ke baad user ko wapas Login Page (index.php) par bhej dein
header("location: index.php");
exit;
?>