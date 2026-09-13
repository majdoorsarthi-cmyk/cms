<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

$message = "";
$type = ""; // success ya error dikhane ke liye

if(isset($_POST['reset_request'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check karein ki email database mein hai ya nahi
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($check_email) > 0){
        // Yahan aap Reset Link ya OTP bhejne ka logic daal sakte hain
        $message = "पासवर्ड रीसेट लिंक आपके ईमेल पर भेज दिया गया है!";
        $type = "success";
    } else { 
        $message = "यह ईमेल हमारे रिकॉर्ड में नहीं मिला!"; 
        $type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Official Coaching CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-color: #1a237e; 
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand { font-weight: 700; color: var(--brand-color) !important; }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .reset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            display: flex;
            overflow: hidden;
            max-width: 850px;
            width: 100%;
        }

        .side-info {
            background: var(--brand-color);
            color: white;
            padding: 40px;
            width: 40%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .form-area { padding: 50px; width: 60%; }

        @media (max-width: 768px) {
            .side-info { display: none; }
            .form-area { width: 100%; padding: 30px; }
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .btn-reset {
            background: var(--brand-color);
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-reset:hover { background: #0d144d; transform: translateY(-2px); }

        .status-msg {
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border-left: 5px solid;
        }
        .msg-error { background: #fff5f5; color: #c0392b; border-color: #c0392b; }
        .msg-success { background: #f0fff4; color: #27ae60; border-color: #27ae60; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-university"></i> ACADEMY<span>CMS</span></a>
    </div>
</nav>

<div class="main-container">
    <div class="reset-card">
        <div class="side-info">
            <i class="fas fa-key fa-4x mb-4"></i>
            <h3 class="fw-bold">पासवर्ड रिकवरी</h3>
            <p class="small opacity-75">चिंता न करें! अपना पंजीकृत ईमेल दर्ज करें और हम आपको पासवर्ड रीसेट करने के लिए निर्देश भेजेंगे।</p>
        </div>

        <div class="form-area">
            <h2 class="fw-bold text-dark mb-2">Forgot Password?</h2>
            <p class="text-muted mb-4 small">अपना ईमेल पता दर्ज करें</p>

            <?php if($message != "") { ?>
                <div class="status-msg <?php echo ($type == 'success') ? 'msg-success' : 'msg-error'; ?>">
                    <i class="fas <?php echo ($type == 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i> 
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small">REGISTERED EMAIL</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                    </div>
                </div>

                <button type="submit" name="reset_request" class="btn-reset">RESET PASSWORD</button>
            </form>

            <div class="text-center mt-4">
                <a href="login.php" class="text-decoration-none small fw-bold text-primary">
                    <i class="fas fa-arrow-left me-1"></i> वापस लॉगिन पर जाएं
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-3 text-center border-top mt-auto">
    <p class="mb-0 small text-muted">&copy; 2025 Professional Coaching CMS. Secure Access.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>