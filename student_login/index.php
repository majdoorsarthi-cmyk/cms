<?php
// सेशन का नाम अलग रखें ताकि एडमिन के साथ टकराव न हो
session_name("STUDENT_SESSION");

// Parent Directory से db_config.php include करें
include '../db_config.php';

// अगर पहले से लॉगिन है, तो सीधा डैशबोर्ड पर भेजें
if (isset($_SESSION['user'])) {
    header("Location: my.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // इनपुट को सैनिटाइज़ और TRIM करें
    $u = mysqli_real_escape_string($conn, trim($_POST['username']));
    $p = mysqli_real_escape_string($conn, trim($_POST['password']));
    $p_md5 = md5($p); // MD5 Hash Password

    // Roll No, User ID या Email किसी से भी मैच करने की फ्लेक्सिबल क्वेरी
    $query = "SELECT * FROM students WHERE 
              (TRIM(roll_no) = '$u' OR TRIM(roll_no) LIKE '%$u%' OR user_id = '$u' OR TRIM(email) = '$u') 
              AND 
              (password = '$p' OR password = '$p_md5') 
              AND 
              (is_deleted = 0 OR is_deleted IS NULL)";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $student_data = mysqli_fetch_assoc($result);
        
        // सेशन में डेटा स्टोर करें
        $_SESSION['user'] = !empty($student_data['roll_no']) ? $student_data['roll_no'] : $student_data['user_id'];
        $_SESSION['student_id'] = $student_data['id'];
        $_SESSION['user_id'] = $student_data['user_id'];
        
        header("Location: my.php");
        exit();
    } else {
        $error = "Invalid User ID/Roll No or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to AISECT Group of Universities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-family: sans-serif;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            backdrop-filter: blur(10px);
        }
        .btn-custom {
            background-color: #8e44ad;
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
        }
        .btn-custom:hover { background-color: #732d91; }
        .form-control { background: rgba(255,255,255,0.9); border: none; }
    </style>
</head>
<body>

<div class="login-box">
    <h3 class="mb-3">Welcome back</h3>
    <p>Log in to AISECT Group of Universities</p>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger py-2 text-center" style="font-size: 14px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label>User ID / Roll No</label>
            <input type="text" name="username" class="form-control" placeholder="Enter Roll No or User ID" required>
        </div>
        <div class="mb-4">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-custom">Log in</button>
        <div class="text-center mt-3">
            <a href="#" class="text-white text-decoration-none">Forgot password?</a>
        </div>
    </form>
</div>

</body>
</html>