<?php
session_name("STUDENT_SESSION");
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../../index.php"); exit(); }
// यहाँ क्विज़ शुरू होने का समय सेव करें
$_SESSION['quiz_start_time'] = date('Y-m-d H:i:s');

// 1 सेकंड का होल्ड देकर सीधे परीक्षा पेज पर रीडायरेक्ट करना
header("Refresh: 1; url=attempt.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Starting Attempt...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center">
        <div class="spinner-border text-purple" style="color: #8A4F8D; width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 text-secondary">Loading Quiz Environment... Please Wait.</h5>
    </div>
</body>
</html>