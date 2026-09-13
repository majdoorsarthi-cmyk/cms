<?php
// 1. Output Buffering Start (Page Reload & Header Error रोकने के लिए)
ob_start();

include 'db_config.php';
$msg = "";
$msg_class = "";

if(isset($_POST['register'])){
    // Sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $session = mysqli_real_escape_string($conn, $_POST['session']);

    // Image Upload Logic
    $target_dir = "uploads/";
    
    // Check if uploads folder exists, if not create it
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = time() . "_" . basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . $image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if email already exists
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($check_email) > 0){
        $msg = "यह ईमेल पहले से पंजीकृत है!";
        $msg_class = "error-box";
    } else {
        // Upload file to server
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            
            // Step 1: Insert into users table
            $user_query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
            
            if(mysqli_query($conn, $user_query)){
                $user_id = mysqli_insert_id($conn); 
                
                // Step 2: Generate Unique Application Number
                $app_no = "APP-" . substr($session, 0, 4) . "-" . rand(1000, 9999);

                // Step 3: Insert into students table
                $student_query = "INSERT INTO students (user_id, name, father_name, roll_no, course, mobile, dob, gender, address, session, photo, admission_date, status) 
                                  VALUES ('$user_id', '$name', '$father_name', '$app_no', '$course', '$mobile', '$dob', '$gender', '$address', '$session', '$image_name', NOW(), 'Not Verified')";
                
                if(mysqli_query($conn, $student_query)){
                    $msg = "<i class='fas fa-check-circle me-1'></i> <b>पंजीकरण सफल!</b> <br> आपका Application No: <b style='font-size:18px;'>$app_no</b> <br> स्टेटस: <b style='color:#ef4444;'>NOT VERIFIED</b>. <br> एडमिन जल्द ही आपका रिकॉर्ड चेक करेगा।";
                    $msg_class = "success-box";
                } else {
                    $msg = "डेटाबेस त्रुटि: " . mysqli_error($conn);
                    $msg_class = "error-box";
                }
            } else {
                $msg = "खाता बनाने में विफल!";
                $msg_class = "error-box";
            }
        } else {
            $msg = "फोटो अपलोड करने में विफल! कृपया दोबारा प्रयास करें।";
            $msg_class = "error-box";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Student Registration - CMS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --brand-color: #1a237e;
            --accent-color: #ffd600;
            --text-dark: #2c3e50;
            --success-color: #27ae60;
            
            /* PhonePe Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 🖥️ ---------------- DESKTOP STYLES (UNCHANGED & CLEAN) ---------------- */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            z-index: 1000;
        }
        .navbar-brand { font-weight: 700; color: var(--brand-color) !important; font-size: 24px; }
        .navbar-brand span { color: #e67e22; }
        .nav-link { font-weight: 600; color: var(--text-dark) !important; }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .reg-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            display: flex;
            overflow: hidden;
            max-width: 1100px;
            width: 100%;
        }

        .reg-side-info {
            background: var(--brand-color);
            color: white;
            padding: 50px 40px;
            width: 35%;
            display: flex;
            flex-direction: column;
        }

        .instruction-list {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }

        .instruction-list li {
            font-size: 14px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            opacity: 0.9;
            line-height: 1.5;
        }

        .instruction-list li i {
            margin-right: 12px;
            color: var(--accent-color);
            margin-top: 4px;
        }

        .reg-form-area {
            padding: 45px;
            width: 65%;
            background: #fff;
        }

        .form-label { font-weight: 600; font-size: 13px; color: var(--text-dark); margin-bottom: 5px; }
        .form-control, .form-select { 
            padding: 10px; 
            border-radius: 8px; 
            border: 1px solid #ddd; 
            font-size: 14px; 
            margin-bottom: 15px;
        }

        .btn-register {
            background: var(--success-color);
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
            margin-top: 10px;
            cursor: pointer;
        }
        .btn-register:hover { background: #219150; transform: translateY(-2px); }

        .success-box { background: #e8f6ef; color: #15803d; padding: 15px; border-radius: 10px; border-left: 5px solid #27ae60; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
        .error-box { background: #fef2f2; color: #dc2626; padding: 15px; border-radius: 10px; border-left: 5px solid #dc2626; margin-bottom: 20px; font-size: 14px; font-weight: 600; }

        .mobile-app-header { display: none; }


        /* 📱 ---------------- PHONEPE MOBILE APP VIEW (BIG FONTS & MODERN APP UI) ---------------- */
        @media (max-width: 768px) {
            body { 
                background-color: var(--phonepe-bg) !important; 
                font-family: 'Poppins', sans-serif !important; 
                padding-bottom: 30px;
            }

            .navbar, footer, .reg-side-info { display: none !important; }

            /* PhonePe Mobile App Header */
            .mobile-app-header {
                display: flex !important;
                position: sticky;
                top: 0;
                z-index: 9999;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%);
                color: #ffffff;
                padding: 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3);
            }
            .mobile-app-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { 
                color: #ffffff; 
                font-size: 20px; 
                width: 42px; 
                height: 42px; 
                background: rgba(255,255,255,0.2); 
                border-radius: 50%; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                text-decoration: none; 
            }
            .mobile-app-header h3 { font-size: 20px !important; font-weight: 700; margin: 0; color: #fff; }
            .mobile-app-header small { font-size: 13px !important; color: rgba(255,255,255,0.85); display: block; font-weight: 500; }

            .main-container { padding: 12px !important; background: transparent !important; }

            .reg-card {
                box-shadow: none !important;
                background: transparent !important;
                border-radius: 0 !important;
            }

            .reg-form-area {
                width: 100% !important;
                padding: 18px !important;
                background: #ffffff !important;
                border-radius: 24px !important;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
            }

            .reg-form-area h2 { font-size: 22px !important; font-weight: 800 !important; color: #0f172a !important; margin-bottom: 20px !important; }

            /* Big & Bold Form Labels */
            .form-label { 
                font-size: 15px !important; 
                font-weight: 700 !important; 
                color: #1e293b !important; 
                margin-bottom: 8px !important; 
                display: block;
            }

            /* Big Inputs & Dropdowns */
            .form-control, .form-select {
                padding: 14px 16px !important;
                font-size: 16px !important;
                font-weight: 600 !important;
                border-radius: 14px !important;
                border: 2px solid #e2e8f0 !important;
                margin-bottom: 18px !important;
                background-color: #f8fafc !important;
                color: #0f172a !important;
            }
            .form-control:focus, .form-select:focus {
                border-color: var(--phonepe-purple) !important;
                background-color: #ffffff !important;
                box-shadow: 0 0 0 4px rgba(95, 37, 159, 0.1) !important;
            }

            /* Big PhonePe Action Button */
            .btn-register {
                padding: 18px !important;
                font-size: 18px !important;
                font-weight: 800 !important;
                border-radius: 16px !important;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                box-shadow: 0 8px 20px rgba(95, 37, 159, 0.3) !important;
                margin-top: 15px !important;
            }

            .success-box { font-size: 16px !important; padding: 18px !important; border-radius: 16px !important; }
            .error-box { font-size: 16px !important; padding: 18px !important; border-radius: 16px !important; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe Mobile Top App Bar -->
<div class="mobile-app-header">
    <div class="mobile-app-header-left">
        <a href="index.php" class="mobile-back-btn"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h3>छात्र पंजीकरण</h3>
            <small>New Student Registration</small>
        </div>
    </div>
    <div style="font-size:24px; color:#fff;">
        <i class="fa-solid fa-user-plus"></i>
    </div>
</div>

<!-- 🖥️ DESKTOP NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-university"></i> TC ACADAMY<span>CMS</span></a>
        <div class="ms-auto">
            <a href="index.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> लॉगिन पेज पर जाएँ</a>
        </div>
    </div>
</nav>

<div class="main-container">
    <div class="reg-card">
        
        <!-- 🖥️ DESKTOP SIDEBAR INFO -->
        <div class="reg-side-info">
            <h3 class="fw-bold mb-3"><i class="fas fa-user-plus me-2"></i>New Enrollment</h3>
            <p class="small opacity-75">नया छात्र खाता बनाने के लिए कृपया सही जानकारी भरें:</p>
            
            <ul class="instruction-list">
                <li><i class="fas fa-camera"></i><b>फोटो:</b> अपनी स्पष्ट पासपोर्ट साइज फोटो अपलोड करें।</li>
                <li><i class="fas fa-id-card"></i><b>दस्तावेज़:</b> अपना नाम और पिता का नाम दर्ज करें।</li>
                <li><i class="fas fa-key"></i><b>लॉगिन:</b> पंजीकरण के बाद Application Number नोट कर लें।</li>
                <li><i class="fas fa-lock"></i><b>वेरिफिकेशन:</b> एडमिन द्वारा वेरिफिकेशन होने तक इंतज़ार करें।</li>
            </ul>

            <div class="mt-auto pt-4 border-top border-secondary opacity-75 small text-center">
                Registration Portal &copy; 2026
            </div>
        </div>

        <!-- FORM REGISTRATION AREA -->
        <div class="reg-form-area">
            <h2 class="fw-bold text-dark mb-4">छात्र पंजीकरण (Registration)</h2>

            <?php if($msg != "") { echo "<div class='$msg_class'>$msg</div>"; } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label text-primary"><i class="fas fa-upload me-1"></i> छात्र की फोटो अपलोड करें (Upload Photo)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">पूरा नाम (Full Name)</label>
                        <input type="text" name="name" class="form-control" placeholder="पूरा नाम दर्ज करें" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">पिता का नाम (Father's Name)</label>
                        <input type="text" name="father_name" class="form-control" placeholder="पिता का नाम दर्ज करें" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">जन्म तिथि (Date of Birth)</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">लिंग (Gender)</label>
                        <select name="gender" class="form-select" required>
                            <option value="">चुनें...</option>
                            <option value="Male">पुरुष (Male)</option>
                            <option value="Female">महिला (Female)</option>
                            <option value="Other">अन्य (Other)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">कोर्स चुनें (Select Course)</label>
                        <select name="course" class="form-select" required>
                            <option value="">-- कोर्स चुनें --</option>
                          <option value="BASIC">BASIC</option>
                          <option value="BA">BA</option>
                          <option value="CYBER SECURTY">CYBER SECURTY</option>
                            <option value="DCA">DCA</option>
                          <option value="TALLY">TALLY</option>
                          <option value="CPCT TYPING">CPCT TYPING</option>
                            <option value="PGDCA">PGDCA</option>
                            <option value="Web Development">Full Stack Web Development</option>
                            <option value="Tally ERP">Tally Prime with GST</option>
                            <option value="OLYMPAID">Olympaid</option>
                            <option value="TC ACADAMY">TC ACADAMY VALANTIYAR</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">सत्र (Session)</label>
                        <select name="session" class="form-select" required>
                            <option value="2026-27">2026-2027</option>
                            <option value="2025-26">2025-2026</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">मोबाइल / व्हाट्सएप नंबर</label>
                        <input type="text" name="mobile" class="form-control" placeholder="10 अंकों का नंबर" pattern="[0-9]{10}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ईमेल आईडी (Email)</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">स्थायी पता (Full Address)</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="मकान नंबर, गांव/शहर, जिला, राज्य" required></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">एक मजबूत पासवर्ड बनाएं</label>
                        <input type="password" name="password" class="form-control" placeholder="कम से कम 6 अक्षर दर्ज करें" minlength="6" required>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-register">पंजीकरण पूर्ण करें (Complete Registration) <i class="fas fa-arrow-right ms-1"></i></button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted mb-0" style="font-size:14px;">क्या आप पहले से पंजीकृत हैं? <a href="index.php" class="fw-bold text-decoration-none" style="color:var(--phonepe-purple);">यहाँ क्लिक कर लॉगिन करें</a></p>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-3 text-center border-top">
    <p class="mb-0 small text-muted">&copy; 2026 Smart Coaching CMS | TC ACADAMY Center</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- PREVENT AUTOMATIC PAGE RELOADS & RESUBMISSIONS -->
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</body>
</html>
<?php 
ob_end_flush(); 
?>