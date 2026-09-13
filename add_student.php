<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Check Admin Login
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

$message = "";

// --- Auto Generate Application Number ---
$year = date('Y');
$query = mysqli_query($conn, "SELECT id FROM students ORDER BY id DESC LIMIT 1");
$last_row = mysqli_fetch_assoc($query);
$next_id = ($last_row) ? $last_row['id'] + 1 : 1;
$app_no = "APP-" . $year . "-" . str_pad($next_id, 4, '0', STR_PAD_LEFT);

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $f_name = mysqli_real_escape_string($conn, $_POST['f_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $session = mysqli_real_escape_string($conn, $_POST['session']);
    $batch_start_time = mysqli_real_escape_string($conn, $_POST['batch_start_time']);
    $batch_end_time = mysqli_real_escape_string($conn, $_POST['batch_end_time']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Default password
    $application_no = mysqli_real_escape_string($conn, $_POST['application_no']);

    // Photo Upload
    $photo_name = "default.png";
    if(isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != ""){
        $target_dir = "uploads/profile/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $photo_name = "STU_" . time() . "." . $ext;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo_name);
    }

    // Step 1: Check Email/App No
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if(mysqli_num_rows($check) > 0) {
        $message = "<div class='alert error'>❌ यह ईमेल पहले से मौजूद है।</div>";
    } else {
        // Step 2: Insert into 'users' table (for login)
        $user_sql = "INSERT INTO users (name, email, mobile, password, role) VALUES ('$name', '$email', '$mobile', '$password', 'student')";
        
        if(mysqli_query($conn, $user_sql)){
            $user_id = mysqli_insert_id($conn);
            
            // Step 3: Insert into 'students' table with all fields including Batch Timings
            $stu_sql = "INSERT INTO students (user_id, roll_no, father_name, dob, gender, course, session, address, photo, batch_start_time, batch_end_time) 
                        VALUES ('$user_id', '$application_no', '$f_name', '$dob', '$gender', '$course', '$session', '$address', '$photo_name', '$batch_start_time', '$batch_end_time')";

            if(mysqli_query($conn, $stu_sql)){
                $message = "<div class='alert success'>✅ पंजीकरण सफल! एप्लीकेशन नंबर: <strong>$application_no</strong></div>";
                // Refresh App No
                $next_id++;
                $app_no = "APP-" . $year . "-" . str_pad($next_id, 4, '0', STR_PAD_LEFT);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>छात्र पंजीकरण | Smart CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #1e3a8a; 
            --bg: #f8fafc; 
            --text: #334155; 
            
            /* PhonePe UI Theme */
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); display: flex; min-height: 100vh; }
        
        /* 🖥️ DESKTOP SIDEBAR & LAYOUT (ORIGINAL) */
        .sidebar { width: 280px; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 20px; flex-shrink: 0; }
        .sidebar h2 { color: var(--primary); font-size: 22px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }
        .sidebar-info { background: var(--primary); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
        .sidebar-info p { font-size: 13px; margin-bottom: 10px; opacity: 0.9; }

        /* Main Form Area */
        .main-content { flex: 1; padding: 40px; display: flex; justify-content: center; }
        .form-card { background: white; width: 100%; max-width: 900px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; height: fit-content; }
        .form-header { padding: 25px 40px; border-bottom: 1px solid #f1f5f9; background: #fff; }
        .form-header h1 { font-size: 24px; color: #1e293b; }
        
        .registration-form { padding: 30px 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group label { font-size: 14px; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 6px; }
        .input-group label span { font-size: 12px; color: #94a3b8; font-weight: 400; }
        .input-group label i { color: var(--primary); }
        
        input, select, textarea { padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: 0.3s; background: #fcfdfe; color: #1e293b; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1); background: #fff; }
        
        .photo-section { text-align: center; margin-bottom: 10px; }
        #preview { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #e2e8f0; display: none; margin: 0 auto 10px; }
        .photo-placeholder { width: 100px; height: 100px; border-radius: 50%; background: #eef2ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 10px; border: 2px dashed #cbd5e1; }
        .upload-label { color: #2563eb; cursor: pointer; font-size: 14px; font-weight: 600; display: inline-block; }

        .btn-submit { grid-column: span 2; background: var(--primary); color: white; padding: 16px; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: 0.3s; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2); }
        .btn-submit:hover { background: #172554; transform: translateY(-1px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; grid-column: span 2; font-size: 14px; font-weight: 500; }
        .success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .mobile-header, .mobile-bottom-nav, .card-section-title { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Mobile App Interface) */
        @media (max-width: 991px) {
            body {
                background: var(--phonepe-bg) !important;
                flex-direction: column !important;
            }

            .sidebar { display: none !important; }

            /* PhonePe Style Header */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 62px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%) !important;
                z-index: 9999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.25);
            }

            .mobile-header-left {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .mobile-back-btn {
                color: #ffffff;
                font-size: 18px;
                text-decoration: none;
                width: 36px;
                height: 36px;
                background: rgba(255,255,255,0.15);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .mobile-header h3 {
                color: #ffffff !important;
                font-size: 16px;
                font-weight: 700;
                line-height: 1.2;
            }
            .mobile-header small {
                color: rgba(255,255,255,0.8);
                font-size: 11px;
                display: block;
            }

            .main-content {
                padding: 74px 12px 85px 12px !important;
                width: 100% !important;
            }

            .form-card {
                background: transparent !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .form-header { display: none !important; }

            .registration-form {
                padding: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 14px !important;
            }

            /* PhonePe Section Card Container */
            .form-section-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 16px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03);
                border: 1px solid #edf2f7;
            }

            .card-section-title {
                display: flex !important;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                font-weight: 700;
                color: var(--phonepe-purple);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 14px;
                padding-bottom: 8px;
                border-bottom: 1px solid #f1f5f9;
            }

            /* Profile Photo PhonePe Avatar Style */
            .photo-section {
                background: #ffffff;
                border-radius: 16px;
                padding: 18px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03);
                border: 1px solid #edf2f7;
                position: relative;
            }
            .avatar-wrapper {
                position: relative;
                width: 90px;
                height: 90px;
                margin: 0 auto 10px;
            }
            #preview {
                width: 90px !important;
                height: 90px !important;
                border: 3px solid var(--phonepe-purple) !important;
                margin: 0 !important;
            }
            .photo-placeholder {
                width: 90px !important;
                height: 90px !important;
                margin: 0 !important;
                border: 2px dashed var(--phonepe-purple) !important;
                background: #f3e8ff !important;
                color: var(--phonepe-purple) !important;
            }
            .camera-badge {
                position: absolute;
                bottom: 0;
                right: 0;
                background: var(--phonepe-purple);
                color: #fff;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                border: 2px solid #ffffff;
            }
            .upload-label span {
                color: var(--phonepe-purple) !important;
                font-size: 13px !important;
                font-weight: 600;
            }

            /* Input PhonePe Styling */
            .input-group label {
                font-size: 12.5px !important;
                color: #4a5568 !important;
            }
            .input-group label i {
                color: var(--phonepe-purple) !important;
            }

            input, select, textarea {
                padding: 12px 14px !important;
                border-radius: 12px !important;
                font-size: 13.5px !important;
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
            }
            input:focus, select:focus, textarea:focus {
                border-color: var(--phonepe-purple) !important;
                background: #ffffff !important;
                box-shadow: 0 0 0 3px rgba(95, 37, 159, 0.12) !important;
            }

            .btn-submit {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%) !important;
                padding: 14px !important;
                border-radius: 12px !important;
                font-size: 15px !important;
                font-weight: 700 !important;
                margin-top: 5px !important;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3) !important;
            }

            /* Bottom Navigation */
            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 60px;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                z-index: 9998;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
            }
            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #718096;
                font-size: 10px;
                font-weight: 500;
                width: 20%;
            }
            .phonepe-nav-item i {
                font-size: 18px;
                margin-bottom: 2px;
            }
            .phonepe-nav-item.active {
                color: var(--phonepe-purple) !important;
                font-weight: 700;
            }
        }
    </style>
</head>
<body>

<!-- Mobile Header Bar (PhonePe Style) -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>छात्र पंजीकरण</h3>
            <small>New Student Enrollment</small>
        </div>
    </div>
    <div style="color:#fff; font-size:18px;">
        <i class="fa fa-shield-alt"></i>
    </div>
</div>

<!-- Desktop Sidebar -->
<div class="sidebar">
    <h2>🏦 SMARTCMS</h2>
    <div class="sidebar-info">
        <h3>New Enrollment</h3>
        <p>नया छात्र खाता बनाने के लिए कृपया सही जानकारी भरें।</p>
        <p>📷 फोटो: अपनी स्पष्ट फोटो अपलोड करें।</p>
        <p>🔑 लॉगिन: छात्र Application No और DOB से लॉगिन कर सकेंगे।</p>
    </div>
    <a href="admin_dashboard.php" style="text-decoration:none; color:#64748b; font-size:14px; font-weight:600;"><i class="fa fa-arrow-left"></i> डैशबोर्ड पर वापस जाएं</a>
</div>

<!-- Main Content Form Area -->
<div class="main-content">
    <div class="form-card">
        <div class="form-header">
            <h1>छात्र पंजीकरण (Registration Form)</h1>
        </div>

        <form method="POST" enctype="multipart/form-data" class="registration-form">
            <?php echo $message; ?>

            <!-- Section 1: Profile Photo Picker -->
            <div class="photo-section">
                <div class="avatar-wrapper">
                    <img id="preview" src="#" alt="Student Photo">
                    <div id="photoPlaceholder" class="photo-placeholder"><i class="fa fa-user"></i></div>
                    <div class="camera-badge"><i class="fa fa-camera"></i></div>
                </div>
                <label class="upload-label">
                    <span>📷 छात्र फोटो चुनें (Upload Photo)</span>
                    <input type="file" name="photo" accept="image/*" style="display:none" onchange="previewImg(this)">
                </label>
            </div>

            <!-- Section 2: Personal Information Card -->
            <div class="form-section-card full-width">
                <div class="card-section-title"><i class="fa fa-user-circle"></i> व्यक्तिगत जानकारी (Personal Details)</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                    
                    <div class="input-group">
                        <label><i class="fa fa-user"></i> पूरा नाम <span>(Full Name)</span></label>
                        <input type="text" name="name" placeholder="उदा. Rahul Sharma" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-user-friends"></i> पिता का नाम <span>(Father's Name)</span></label>
                        <input type="text" name="f_name" placeholder="Father's Name" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-calendar-alt"></i> जन्म तिथि <span>(Date of Birth)</span></label>
                        <input type="date" name="dob" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-venus-mars"></i> लिंग <span>(Gender)</span></label>
                        <select name="gender" required>
                            <option value="">चुनें...</option>
                            <option value="Male">Male (पुरुष)</option>
                            <option value="Female">Female (महिला)</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Section 3: Course & Admission Details Card -->
            <div class="form-section-card full-width">
                <div class="card-section-title"><i class="fa fa-graduation-cap"></i> कोर्स एवं बैच समय (Academic & Batch Info)</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                    
                    <div class="input-group">
                        <label><i class="fa fa-book-open"></i> कोर्स चुनें <span>(Course)</span></label>
                        <select name="course" required>
                            <option value="">-- कोर्स चुनें --</option>
                            <option value="DCA">DCA</option>
                            <option value="PGDCA">PGDCA</option>
                            <option value="Tally">Tally Prime</option>
                            <option value="Olympiad">CSC Olympiad Computer</option>
                            <option value="Valantiyar">Valantiyar Computer</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-clock"></i> सत्र <span>(Session)</span></label>
                        <select name="session">
                            <option value="2025-2026">2025-2026</option>
                            <option value="2024-2025">2024-2025</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-clock"></i> बैच शुरू समय <span>(Start Time)</span></label>
                        <input type="time" name="batch_start_time" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-clock"></i> बैच समाप्त समय <span>(End Time)</span></label>
                        <input type="time" name="batch_end_time" required>
                    </div>

                    <div class="input-group full-width">
                        <label><i class="fa fa-id-badge"></i> एप्लीकेशन नंबर <span>(Auto-Generated)</span></label>
                        <input type="text" name="application_no" value="<?php echo $app_no; ?>" readonly style="background:#f1f5f9; font-weight:700; color:var(--phonepe-purple);">
                    </div>

                </div>
            </div>

            <!-- Section 4: Contact & Account Credentials Card -->
            <div class="form-section-card full-width">
                <div class="card-section-title"><i class="fa fa-address-card"></i> संपर्क एवं लॉगिन जानकारी (Contact & Login)</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                    
                    <div class="input-group">
                        <label><i class="fa fa-phone-alt"></i> मोबाइल / व्हाट्सएप नंबर</label>
                        <input type="text" name="mobile" maxlength="10" placeholder="10 अंकों का मोबाइल नंबर" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fa fa-envelope"></i> ईमेल आईडी <span>(Email)</span></label>
                        <input type="email" name="email" placeholder="example@gmail.com" required>
                    </div>

                    <div class="input-group full-width">
                        <label><i class="fa fa-map-marker-alt"></i> स्थायी पता <span>(Address)</span></label>
                        <textarea name="address" rows="2" placeholder="मकान नंबर, गांव/शहर, जिला, राज्य"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <label><i class="fa fa-lock"></i> लॉगिन पासवर्ड बनाएं <span>(Password)</span></label>
                        <input type="password" name="password" placeholder="मजबूत पासवर्ड दर्ज करें" required>
                    </div>

                </div>
            </div>

            <button type="submit" name="register" class="btn-submit">
                <i class="fa fa-check-circle"></i> पंजीकरण सुरक्षित करें (Submit Details)
            </button>
        </form>
    </div>
</div>

<!-- Mobile PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php?page=dashboard" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="add_student.php" class="phonepe-nav-item active">
        <i class="fa fa-user-plus"></i>
        <span>Add Student</span>
    </a>
    <a href="manage_students.php" class="phonepe-nav-item">
        <i class="fa fa-users"></i>
        <span>Students</span>
    </a>
    <a href="attendance_scanner.php" class="phonepe-nav-item">
        <i class="fa fa-qrcode"></i>
        <span>Scan QR</span>
    </a>
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-bars"></i>
        <span>Menu</span>
    </a>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview').style.display = 'block';
            var placeholder = document.getElementById('photoPlaceholder');
            if(placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>