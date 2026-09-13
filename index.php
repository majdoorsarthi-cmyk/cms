<?php 
/**
 * UNIVERSITY CMS - HYBRID RESPONSIVE & MOBILE APP PORTAL
 * Biometric Fingerprint Auto-Login, Native Sound Alarm & Auto-Update Enabled
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

$error = "";
$biometric_register_script = "";

// 🎵 ऑडियो फ़ाइल का रियल वेब पाथ (Attendance Tune)
// Linux path (/DATA/AppData/coaching_cms/...) को वेब रूट के सापेक्ष 'uploads/' पाथ में मैप किया गया है
$attendance_audio_path = "uploads/Attendance_Lagao.mp3";

if(isset($_POST['login'])){
    $role = $_POST['role'];

    if($role == 'admin'){
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $pass = mysqli_real_escape_string($conn, $_POST['password']);
        
        $admin_res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass' AND role='admin'");
        if(mysqli_num_rows($admin_res) > 0){
            $_SESSION['admin'] = $email;
            
            // First time login success -> Register Biometric Credentials
            $biometric_register_script = "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    saveBiometricCredentials('admin', '$email', '$pass', 'admin_dashboard.php');
                });
            </script>";
        } else { 
            $error = "गलत एडमिन ईमेल या पासवर्ड!"; 
        }
    } else {
        // 🔥 स्टूडेंट लॉगिन लॉजिक
        $input = mysqli_real_escape_string($conn, $_POST['app_no']);
        $pass = mysqli_real_escape_string($conn, $_POST['dob']);

        $query = "SELECT s.*, u.email 
                  FROM students s
                  JOIN users u ON s.user_id = u.id 
                  WHERE TRIM(s.roll_no) = TRIM('$input') 
                  AND u.password = '$pass'";
        
        $res = mysqli_query($conn, $query);
        
        if($res && mysqli_num_rows($res) > 0){
            $student_data = mysqli_fetch_assoc($res);
            $_SESSION['student'] = $student_data; 
            
            $batch_time = $student_data['batch_start_time'] ?? '08:00';
            $stu_name   = addslashes($student_data['name'] ?? 'Student');

            // Save Biometric Credentials & Sync Native App Alarm + Music
            $biometric_register_script = "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    saveBiometricCredentials('student', '$input', '$pass', 'student_dashboard.php', '$batch_time');
                    
                    // 🔔 एंड्रॉइड ऐप में ऑडियो का Full Web URL भेजें
                    if (typeof AndroidNative !== 'undefined' && AndroidNative.setNativeClassAlarm) {
                        var fullAudioUrl = window.location.origin + '/' + '$attendance_audio_path';
                        AndroidNative.setNativeClassAlarm('$batch_time', '$stu_name', fullAudioUrl);
                    }
                });
            </script>";
        } else { 
            $error = "गलत रोल नंबर या पासवर्ड!"; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TC ACADEMY - Official Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-primary: #1a237e;
            --brand-accent: #3d5af1;
            --accent-yellow: #ffd600;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #eef2f6;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #ffffff !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 14px 0;
        }
        .navbar-brand { 
            font-weight: 800; 
            color: var(--brand-primary) !important; 
            font-size: 24px; 
        }
        .navbar-brand span { color: #e67e22; }
        
        .nav-link { 
            font-weight: 600; 
            color: #475569 !important; 
            margin: 0 8px;
            font-size: 16px;
            transition: 0.3s;
        }
        .nav-link:hover { color: var(--brand-accent) !important; }
        .nav-link i { margin-right: 6px; color: var(--brand-accent); }

        .portal-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-card-main {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.1);
            width: 100%;
            overflow: hidden;
            display: flex;
            border: 1px solid #cbd5e1;
        }

        .desktop-side-banner {
            background: linear-gradient(135deg, var(--brand-primary) 0%, #0d144d 100%);
            color: #ffffff;
            padding: 80px 70px;
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .desktop-side-banner h3 { font-size: 32px; font-weight: 800; margin-bottom: 24px; }
        .info-list { list-style: none; padding: 0; }
        .info-list li { font-size: 17px; margin-bottom: 22px; display: flex; align-items: flex-start; gap: 14px; line-height: 1.6; }
        .info-list i { color: var(--accent-yellow); font-size: 22px; margin-top: 3px; }

        .app-qr-banner-box {
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 16px;
            text-align: center;
        }
        .app-qr-banner-box img {
            width: 130px;
            height: auto;
            border-radius: 10px;
            background: #fff;
            padding: 6px;
            border: 2px solid #cbd5e1;
            margin-bottom: 12px;
        }
        .btn-app-download {
            background: var(--accent-yellow);
            color: #000;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            transition: 0.2s;
        }
        .btn-app-download:hover { opacity: 0.9; color: #000; }

        .login-form-wrapper {
            padding: 50px 45px;
            width: 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header-title {
            font-size: 30px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-header-sub {
            font-size: 16px;
            color: var(--text-muted);
            margin-bottom: 25px;
            font-weight: 500;
        }

        .role-switch-box {
            background: #f1f5f9;
            padding: 6px;
            border-radius: 16px;
            display: flex;
            margin-bottom: 28px;
            border: 1px solid #e2e8f0;
        }

        .role-tab-btn {
            flex: 1;
            padding: 15px;
            border: none;
            background: transparent;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-muted);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .role-tab-btn.active {
            background: #ffffff;
            color: var(--brand-accent);
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .form-group-custom { margin-bottom: 22px; }
        .form-group-custom label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom i {
            position: absolute;
            left: 18px;
            color: #94a3b8;
            font-size: 18px;
        }

        .input-group-custom input {
            width: 100%;
            padding: 16px 18px 16px 52px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            outline: none;
            transition: 0.2s;
        }

        .input-group-custom input:focus {
            border-color: var(--brand-accent);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(61, 90, 241, 0.12);
        }

        .btn-submit-login {
            background: linear-gradient(135deg, var(--brand-accent) 0%, var(--brand-primary) 100%);
            color: #ffffff;
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(61, 90, 241, 0.25);
            margin-top: 15px;
            transition: 0.2s;
        }

        .btn-submit-login:hover { transform: translateY(-2px); opacity: 0.96; }

        /* 🔥 Quick Fingerprint Button Style */
        .btn-fingerprint-login {
            background: #10b981;
            color: #ffffff;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
            margin-bottom: 22px;
            transition: 0.2s;
        }
        .btn-fingerprint-login:hover { background: #059669; }

        .error-alert-box {
            background: #fef2f2;
            color: #dc2626;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 22px;
            border-left: 5px solid #ef4444;
        }

        @media (max-width: 991px) {
            .desktop-side-banner { display: none; }
            .login-form-wrapper { width: 100%; padding: 30px 20px; }
            .portal-wrapper { padding: 15px; }
            .login-card-main { max-width: 100%; border-radius: 18px; }
            
            .form-header-title { font-size: 24px; }
            .form-header-sub { font-size: 14px; }
            .role-tab-btn { font-size: 15px; padding: 12px 8px; }
            .input-group-custom input { font-size: 15px; padding: 14px 14px 14px 46px; }
            .btn-submit-login { font-size: 16px; padding: 16px; }
        }

        @media (min-width: 992px) {
            .login-card-main { max-width: 1250px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-university me-2"></i>TC<span>ACADEMY</span></a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> होम</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search_admit.php"><i class="fas fa-download"></i> एडमिट कार्ड</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="courses.php"><i class="fas fa-book"></i> कोर्सेज</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lms_master.php"><i class="fas fa-graduation-cap"></i> LMS पोर्टल</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://agu.aisectexams.com/login/index.php"><i class="fas fa-file-signature"></i> परीक्षा</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php"><i class="fas fa-phone-alt"></i> सहायता</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="portal-wrapper">
    <div class="login-card-main">
        
        <div class="desktop-side-banner">
            <h3>SMART CMS Portal</h3>
            <ul class="info-list">
                <li><i class="fas fa-user-graduate"></i> <div><b>छात्र (Students):</b> अपने रोल नंबर और पासवर्ड का उपयोग करके लॉगिन करें।</div></li>
                <li><i class="fas fa-user-shield"></i> <div><b>एडमिन (Admin):</b> आधिकारिक ईमेल और पासवर्ड द्वारा एक्सेस प्राप्त करें।</div></li>
                <li><i class="fas fa-fingerprint"></i> <div><b>बायोमीट्रिक लॉगिन:</b> एक बार लॉगिन करने के बाद अगली बार फिंगरप्रिंट से डायरेक्ट खोलें।</div></li>
            </ul>

            <div class="app-qr-banner-box">
                <p style="font-size: 14px; font-weight: 700; margin-bottom: 10px; color: #fff;"><i class="fas fa-mobile-alt me-1"></i> TC Academy Mobile App</p>
                <img src="uploads/app_qr.jpg" alt="App QR Code">
                <div>
                    <a href="download.php" class="btn-app-download"><i class="fas fa-download me-1"></i> App डाउनलोड करें</a>
                </div>
            </div>
        </div>

        <div class="login-form-wrapper">
            
            <div class="form-header-title">लॉगिन करें</div>
            <div class="form-header-sub">टी सी एकाडमी पोर्टल में आपका स्वागत है</div>

            <?php if($error != "") { ?>
                <div class="error-alert-box">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                </div>
            <?php } ?>

            <!-- 🔥 Quick Biometric Scan Button -->
            <button type="button" id="btnFingerprint" class="btn-fingerprint-login" onclick="triggerAppBiometric()">
                <i class="fas fa-fingerprint fa-lg"></i> <span>फिंगरप्रिंट (Fingerprint) से क्विक लॉगिन करें</span>
            </button>

            <form method="POST" id="mainLoginForm">
                <input type="hidden" name="role" id="roleInput" value="student">
                <input type="hidden" name="login" value="1">
                
                <div class="role-switch-box">
                    <button type="button" class="role-tab-btn active" id="btnStudent" onclick="switchRole('student')">
                        <i class="fas fa-user-graduate"></i> छात्र (Student)
                    </button>
                    <button type="button" class="role-tab-btn" id="btnAdmin" onclick="switchRole('admin')">
                        <i class="fas fa-user-shield"></i> एडमिन (Admin)
                    </button>
                </div>

                <div id="studentFields">
                    <div class="form-group-custom">
                        <label>Application Number</label>
                        <div class="input-group-custom">
                            <i class="fas fa-id-card"></i>
                            <input type="text" name="app_no" id="app_no" placeholder="APP2025XXX" required>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>पासवर्ड (Password)</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="dob" id="dob" placeholder="अपना पासवर्ड दर्ज करें" required>
                        </div>
                    </div>
                </div>

                <div id="adminFields" style="display: none;">
                    <div class="form-group-custom">
                        <label>आधिकारिक ईमेल (Official Email)</label>
                        <div class="input-group-custom">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" placeholder="admin@university.com">
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>पासवर्ड (Password)</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-login">
                    <span>सुरक्षित लॉगिन करें</span> <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <span class="text-muted fs-6">नया एडमिशन? </span>
                <a href="register.php" class="fw-bold text-decoration-none text-primary fs-6">यहाँ रजिस्ट्रेशन करें</a>
            </div>

        </div>
    </div>
</div>

<!-- 🚀 IN-APP AUTO UPDATE MODAL -->
<div id="updateModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:999999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#ffffff; border-radius:24px; padding:30px 24px; text-align:center; max-width:380px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,0.3);">
        <div style="width:70px; height:70px; background:#eff6ff; color:#2563eb; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto; font-size:32px;">
            <i class="fas fa-arrow-alt-circle-down"></i>
        </div>
        <h3 style="font-weight:800; color:#0f172a; margin-bottom:8px; font-size:20px;">नया ऐप अपडेट उपलब्ध है!</h3>
        <p style="color:#64748b; font-size:14px; line-height:1.5; margin-bottom:20px;">
            ऑटो-अटेंडेंस म्यूजिक और फिंगरप्रिंट क्विक लॉगिन का उपयोग करने के लिए ऐप को अभी अपडेट करें।
        </p>
        <a href="uploads/assets/TCCMS.apk" class="btn-submit-login" style="text-decoration:none; display:inline-flex; justify-content:center; width:100%; padding:14px;">
            <i class="fas fa-download me-2"></i> अभी अपडेट करें (Update Now)
        </a>
    </div>
</div>

<!-- 🎵 HTML5 WEB AUDIO PLAYER ELEMENT -->
<audio id="attendanceAlarmSound" src="<?php echo $attendance_audio_path; ?>" preload="auto"></audio>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function switchRole(role) {
    const roleInput = document.getElementById('roleInput');
    const btnStudent = document.getElementById('btnStudent');
    const btnAdmin = document.getElementById('btnAdmin');
    const studentFields = document.getElementById('studentFields');
    const adminFields = document.getElementById('adminFields');

    const appNo = document.getElementById('app_no');
    const dob = document.getElementById('dob');
    const email = document.getElementById('email');
    const password = document.getElementById('password');

    roleInput.value = role;

    if (role === 'admin') {
        btnAdmin.classList.add('active');
        btnStudent.classList.remove('active');
        
        studentFields.style.display = 'none';
        adminFields.style.display = 'block';

        appNo.required = false; appNo.disabled = true;
        dob.required = false; dob.disabled = true;

        email.required = true; email.disabled = false;
        password.required = true; password.disabled = false;
    } else {
        btnStudent.classList.add('active');
        btnAdmin.classList.remove('active');

        studentFields.style.display = 'block';
        adminFields.style.display = 'none';

        appNo.required = true; appNo.disabled = false;
        dob.required = true; dob.disabled = false;

        email.required = false; email.disabled = true;
        password.required = false; password.disabled = true;
    }
}

// ----------------------------------------------------
// 🔥 REAL FINGERPRINT, ALARM & AUTO-LOGIN INTEGRATION
// ----------------------------------------------------

// 1. पहला लॉगिन सफल होने पर फिंगरप्रिंट और बैच टाइम क्रेडेंशियल्स सेव करना
function saveBiometricCredentials(role, val1, val2, redirectTarget, batchTime) {
    localStorage.setItem('bio_role', role);
    localStorage.setItem('bio_val1', val1);
    localStorage.setItem('bio_val2', val2);
    localStorage.setItem('biometric_saved', 'true');

    if (batchTime) {
        localStorage.setItem('student_batch_time', batchTime);
    }

    if (typeof AndroidNative !== "undefined" && AndroidNative.registerBiometricData) {
        AndroidNative.registerBiometricData(role, val1, val2);
    }
    window.location.href = redirectTarget;
}

// 2. ऑटो-लॉगिन ट्रिगर करना
function autoFillAndLogin(role, val1, val2) {
    switchRole(role);
    if (role === 'admin') {
        document.getElementById('email').value = val1;
        document.getElementById('password').value = val2;
    } else {
        document.getElementById('app_no').value = val1;
        document.getElementById('dob').value = val2;
    }
    document.getElementById('mainLoginForm').submit();
}

// 3. 'फिंगरप्रिंट से क्विक लॉगिन' बटन पर क्लिक करने पर
function triggerAppBiometric() {
    if (typeof AndroidNative !== "undefined" && AndroidNative.triggerBiometricManually) {
        AndroidNative.triggerBiometricManually();
    } else if (localStorage.getItem('biometric_saved') === 'true') {
        let role = localStorage.getItem('bio_role');
        let val1 = localStorage.getItem('bio_val1');
        let val2 = localStorage.getItem('bio_val2');
        if (role && val1 && val2) {
            autoFillAndLogin(role, val1, val2);
        }
    }
}

// 4. 🎵 अटेंडेंस अलार्म चेकर (वेब ब्राउज़र एवं PWA सपोर्ट के लिए)
function startAttendanceTimerCheck() {
    setInterval(() => {
        let savedBatchTime = localStorage.getItem('student_batch_time');
        if (!savedBatchTime) return;

        let now = new Date();
        let currentHours = String(now.getHours()).padStart(2, '0');
        let currentMinutes = String(now.getMinutes()).padStart(2, '0');
        let currentTimeStr = `${currentHours}:${currentMinutes}`;

        let todayKey = 'alarm_triggered_' + new Date().toISOString().slice(0, 10);
        
        if (currentTimeStr === savedBatchTime && !localStorage.getItem(todayKey)) {
            let alarmSound = document.getElementById('attendanceAlarmSound');
            if (alarmSound) {
                alarmSound.play().catch(e => {
                    console.log("Autoplay restrictions handled: ", e);
                });
            }
            alert("⏰ रिमाइंडर: आपका क्लास टाइम हो गया है! कृपया अपनी अटेंडेंस दर्ज करें।");
            localStorage.setItem(todayKey, 'true');
        }
    }, 10000);
}

// 5. पेज लोड इनिशियलाइजेशन
window.addEventListener('load', function() {
    var btnFingerprint = document.getElementById('btnFingerprint');
    
    var isAppRegistered = (typeof AndroidNative !== "undefined" && AndroidNative.isBiometricRegistered && AndroidNative.isBiometricRegistered());
    var isWebRegistered = (localStorage.getItem('biometric_saved') === 'true');

    if (isAppRegistered || isWebRegistered) {
        btnFingerprint.style.display = 'flex';
    }

    // 🚀 ऐप ऑटो-अपडेट चेक
    var isApp = typeof AndroidNative !== "undefined";
    var appVersion = isApp && AndroidNative.getAppVersion ? AndroidNative.getAppVersion() : 1;
    if (isApp && appVersion < 2) {
        document.getElementById('updateModal').style.display = 'flex';
    }

    startAttendanceTimerCheck();
});

switchRole('student');
</script>

<?php 
if(!empty($biometric_register_script)) {
    echo $biometric_register_script;
}
?>

</body>
</html>