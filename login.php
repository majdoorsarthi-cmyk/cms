<?php
include 'db_config.php';

if(isset($_POST['login'])){
    $role = $_POST['role'];

    if($role == 'admin'){
        // --- एडमिन के लिए अलग सेशन नाम ---
        session_name("ADMIN_SESSION");
        session_start();
        
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $pass = mysqli_real_escape_string($conn, $_POST['password']);
        
        $admin_res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass' AND role='admin'");
        if(mysqli_num_rows($admin_res) > 0){
            $_SESSION['admin'] = $email;
            header("Location: admin_dashboard.php");
            exit();
        } else { 
            $error = "गलत एडमिन ईमेल या पासवर्ड!"; 
        }
    } else {
        // --- स्टूडेंट के लिए अलग सेशन नाम ---
        session_name("STUDENT_SESSION");
        session_start();
        
        $input = mysqli_real_escape_string($conn, $_POST['app_no']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);

        $query = "SELECT s.*, u.email FROM students s
                  JOIN users u ON s.user_id = u.id 
                  WHERE TRIM(s.roll_no) = TRIM('$input') AND s.dob = '$dob'";
        
        $res = mysqli_query($conn, $query);
        
        if($res && mysqli_num_rows($res) > 0){
            $student_data = mysqli_fetch_assoc($res);
            $_SESSION['student'] = $student_data; 
            header("Location: student_dashboard.php"); 
            exit();
        } else { 
            $error = "गलत रोल नंबर या जन्म तिथि!"; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --main-bg: #f4f7fe;
            --text-dark: #2b3674;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background: var(--main-bg);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-card p {
            color: #a3aed0;
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group { margin-bottom: 20px; position: relative; }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a3aed0;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: #f4f7fe;
            border: 1px solid #e0e5f2;
            border-radius: 12px;
            outline: none;
            transition: 0.3s;
        }

        input:focus { border-color: var(--primary); background: #fff; }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover { opacity: 0.9; transform: translateY(-2px); }

        .error-msg {
            background: #fff5f5;
            color: #ff5b5b;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffebeb;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>CMS PRO</h2>
    <p>Enter your admin credentials</p>

    <?php if($error != "") { ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php } ?>

    <form action="" method="POST">
        <div class="form-group">
            <i class="fa fa-envelope"></i>
            <input type="email" name="email" placeholder="Admin Email" required>
        </div>
        <div class="form-group">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn-login">Sign In</button>
    </form>
</div>

</body>
</html>