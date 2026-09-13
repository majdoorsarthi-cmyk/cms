<?php
session_name("STUDENT_SESSION");
session_start();

if (!isset($_SESSION['user'])) { 
    header("Location: ../../index.php"); 
    exit(); 
}

// URL या सेशन से subject_id प्राप्त करें
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Quiz | AISECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: url('../../../book_bg.jpg') no-repeat center center fixed; background-size: cover; font-family: -apple-system, sans-serif; }
        .center-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .alert-white-card { background: white; padding: 25px 40px; border-radius: 8px; width: 100%; max-width: 780px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #dee2e6; }
        .alert-text { font-size: 15px; color: #212529; margin-bottom: 8px; }
        .sub-text { font-size: 13.5px; color: #495057; }
    </style>
</head>
<body>
    <div class="center-wrapper">
        <div class="alert-white-card shadow-sm">
            <div class="alert-text">You are not allowed to review this quiz</div>
            <div class="sub-text text-muted">This window will close shortly.</div>
        </div>
    </div>

    <script>
    // 3 सेकंड (3000ms) के बाद छात्र के संबंधित DCA या BA सब्जेक्ट के view.php पेज पर रीडायरेक्ट करेगा
    setTimeout(function() {
        var subjectId = "<?php echo $subject_id; ?>";
        if (subjectId > 0) {
            window.location.href = 'view.php?subject_id=' + subjectId;
        } else {
            window.location.href = 'view.php';
        }
    }, 3000);
    </script>
</body>
</html>