<?php
include 'db_config.php';

$roll = isset($_GET['roll_no']) ? mysqli_real_escape_string($conn, $_GET['roll_no']) : '';
$course = isset($_GET['course']) ? mysqli_real_escape_string($conn, $_GET['course']) : '';

// 1. डेटाबेस से चेक करें
$query = "SELECT id, is_verified FROM students WHERE TRIM(roll_no) = TRIM('$roll') AND course = '$course'";
$res = mysqli_query($conn, $query);

$error_msg = "";
$error_title = "रिकॉर्ड अनुपलब्ध";

if(mysqli_num_rows($res) > 0) {
    $data = mysqli_fetch_assoc($res);
    
    // 2. वेरिफिकेशन लॉजिक
    if($data['is_verified'] == 1) {
        header("Location: print_admit_card.php?id=".$data['id']."&course=".urlencode($course));
        exit();
    } else {
        $error_title = "सत्यापन लंबित (Pending Verification)";
        $error_msg = "आपका परीक्षा फॉर्म अभी यूनिवर्सिटी से वेरीफाई नहीं हुआ है। कृपया कार्यालय से संपर्क करें।";
    }
} else {
    $error_title = "रिकॉर्ड अनुपलब्ध (No Record Found)";
    $error_msg = "रोल नंबर <strong>\"".htmlspecialchars($roll)."\"</strong> और चयनित कोर्स के लिए कोई डेटा नहीं मिला। कृपया पुनः प्रयास करें।";
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>स्थिति जाँच | RAVINDRANATH TAGORE UNIVERSITY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --secondary: #0f172a; }
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { background: white !important; border-bottom: 2px solid #e2e8f0; }
        .glass-card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(15px); 
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 30px; padding: 50px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .error-icon { font-size: 70px; color: #f59e0b; margin-bottom: 20px; }
        .btn-retry { background: var(--primary); color: white; padding: 12px 30px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-retry:hover { background: #1d4ed8; color: white; transform: scale(1.02); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php"><i class="fas fa-graduation-cap"></i> RAVINDRANATH TAGORE UNIVERSITY</a>
    </div>
</nav>

<div class="container py-5 d-flex justify-content-center align-items-center flex-grow-1">
    <div class="col-md-6">
        <div class="glass-card text-center">
            <div class="error-icon"><i class="fas fa-user-clock"></i></div>
            <h3 class="fw-bold mb-3"><?php echo $error_title; ?></h3>
            <p class="text-secondary mb-4"><?php echo $error_msg; ?></p>
            
            <div class="d-grid gap-3">
                <a href="search_admit.php" class="btn btn-retry btn-lg">
                    <i class="fas fa-search me-2"></i> पुनः प्रयास करें
                </a>
                <a href="index.php" class="text-decoration-none text-muted fw-semibold">
                    <i class="fas fa-home me-2"></i> होम पेज
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-secondary border-top bg-white">
    &copy; 2026 RAVINDRANATH TAGORE UNIVERSITY. All Rights Reserved.
</footer>

</body>
</html>