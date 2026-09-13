<?php 
// 1. Output Buffering Start (रीलोड और Header Sent एरर रोकने के लिए)
ob_start();

// 2. Session and Database Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// 3. Student Auth Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$session_id = mysqli_real_escape_string($conn, $stu['user_id'] ?? ($stu['id'] ?? 0)); 

// --- Fetch Students Data ---
$profile_q = mysqli_query($conn, "SELECT * FROM students WHERE user_id = '$session_id' OR id = '$session_id' LIMIT 1");
$profile_data = ($profile_q && mysqli_num_rows($profile_q) > 0) ? mysqli_fetch_assoc($profile_q) : $stu;

$real_db_id = $profile_data['id'] ?? $session_id; 
$student_course = $profile_data['course'] ?? 'General';
$roll_no = $profile_data['roll_no'] ?? 'N/A';
$phone = $profile_data['phone'] ?? $profile_data['mobile'] ?? 'Not Updated';
$email = $profile_data['email'] ?? 'Not Updated';
$father_name = $profile_data['father_name'] ?? $profile_data['guardian_name'] ?? 'Not Updated';
$address = $profile_data['address'] ?? 'Not Updated';
$batch_time = $profile_data['batch_time'] ?? 'Regular Batch';

// Profile Photo Logic Fix
$raw_photo = $profile_data['photo'] ?? '';
$stu_photo = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

if (!empty($raw_photo)) {
    if (file_exists('uploads/profile/' . $raw_photo)) {
        $stu_photo = 'uploads/profile/' . $raw_photo . '?v=' . time();
    } elseif (file_exists('uploads/' . $raw_photo)) {
        $stu_photo = 'uploads/' . $raw_photo . '?v=' . time();
    }
}

// --- FEES SUMMARY FOR PROFILE ---
$course_fee = $profile_data['course_fee'] ?? 0; 
$fee_q = mysqli_query($conn, "SELECT SUM(amount_paid) as paid FROM fees WHERE student_id = '$real_db_id'");
$total_paid = ($fee_q) ? (mysqli_fetch_assoc($fee_q)['paid'] ?? 0) : 0;
$due_amount = max(0, $course_fee - $total_paid);

// --- ATTENDANCE SUMMARY ---
$month_start = date('Y-m-01');
$total_days_passed = (int)date('d'); 
$att_stats_q = mysqli_query($conn, "SELECT COUNT(*) as present_count FROM attendance 
                                    WHERE student_id = '$real_db_id' AND LOWER(status) = 'present' 
                                    AND date >= '$month_start'");
$present_count = ($att_stats_q) ? (mysqli_fetch_assoc($att_stats_q)['present_count'] ?? 0) : 0;
$attendance_percentage = ($total_days_passed > 0) ? round(($present_count / $total_days_passed) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>My Profile | CMS PRO</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #3498db;
            --dark-sidebar: #0f172a;
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: #f0f2f5; display: flex; color: #1c1e21; min-height: 100vh; overflow-x: hidden; }

        /* 🖥️ DESKTOP STYLES (MATCHES DASHBOARD) */
        .sidebar { width: 260px; background: var(--dark-sidebar); color: white; height: 100vh; position: fixed; padding: 25px 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-brand { font-size: 24px; font-weight: 800; margin-bottom: 35px; color: var(--primary-blue); }
        .sidebar-menu { list-style: none; flex: 1; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; padding: 14px 18px; display: flex; align-items: center; gap: 12px; border-radius: 12px; transition: 0.3s; font-size: 15px; margin-bottom: 5px; font-weight: 600; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #1e293b; color: var(--primary-blue); }
        
        .main-content { margin-left: 260px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 18px 40px; border-bottom: 1px solid #e4e6eb; }
        .user-photo { width: 48px; height: 48px; border-radius: 50%; border: 3px solid var(--primary-blue); object-fit: cover; }
        .container { padding: 30px 40px; }

        .profile-card-desktop { background: white; border-radius: 20px; padding: 30px; border: 1px solid #e4e6eb; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; gap: 30px; align-items: center; margin-bottom: 30px; }
        .profile-card-desktop img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-blue); }

        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; background: white; padding: 30px; border-radius: 20px; border: 1px solid #e4e6eb; }
        .info-item { display: flex; flex-direction: column; gap: 6px; }
        .info-item label { font-size: 13px; color: #64748b; font-weight: 700; text-transform: uppercase; }
        .info-item p { font-size: 16px; color: #0f172a; font-weight: 700; background: #f8fafc; padding: 12px 16px; border-radius: 10px; border: 1px solid #f1f5f9; }

        .btn-edit { display: inline-flex; align-items: center; gap: 8px; background: var(--primary-blue); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none; font-size: 14px; margin-top: 15px; }

        .mobile-header, .mobile-profile-hero, .mobile-bottom-nav { display: none; }


        /* 📱 PHONEPE REAL MOBILE APP INTERFACE */
        @media (max-width: 992px) {
            body { background: var(--phonepe-bg) !important; padding-top: 60px; padding-bottom: 95px; display: block !important; }

            .sidebar, .header, .profile-card-desktop { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; }
            .container { padding: 14px !important; }

            /* App Header Bar */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 65px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3);
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 800; margin: 0; }
            .mobile-header a { color: #ffffff; font-size: 20px; text-decoration: none; }

            /* PhonePe Style Profile Hero Card */
            .mobile-profile-hero {
                display: flex !important; flex-direction: column; align-items: center;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, #7e22ce 100%);
                padding: 28px 20px 24px 20px; border-radius: 28px; color: white; text-align: center;
                margin-bottom: 18px; box-shadow: 0 8px 25px rgba(95, 37, 159, 0.3); position: relative;
            }
            .mobile-avatar-lg { width: 95px; height: 95px; border-radius: 50%; border: 4px solid #ffffff; object-fit: cover; box-shadow: 0 6px 16px rgba(0,0,0,0.25); margin-bottom: 12px; }
            .mobile-profile-hero h2 { font-size: 23px !important; font-weight: 800 !important; margin-bottom: 4px; }
            .mobile-badge { background: rgba(255,255,255,0.2); padding: 5px 14px; border-radius: 20px; font-size: 13px !important; font-weight: 700; display: inline-block; }

            /* Mobile Info List (PhonePe Settings Style) */
            .info-grid { grid-template-columns: 1fr !important; gap: 12px !important; padding: 0 !important; background: transparent !important; border: none !important; }
            .info-item { background: #ffffff; padding: 16px 18px; border-radius: 20px; box-shadow: 0 3px 12px rgba(0,0,0,0.03); display: flex; flex-direction: row; align-items: center; justify-content: space-between; }
            .info-item-left { display: flex; align-items: center; gap: 14px; }
            .info-icon { width: 44px; height: 44px; border-radius: 14px; background: #f3e8ff; color: var(--phonepe-purple); display: flex; align-items: center; justify-content: center; font-size: 18px; }
            .info-item label { font-size: 12px !important; color: #64748b !important; font-weight: 700 !important; margin: 0; }
            .info-item p { font-size: 16px !important; color: #0f172a !important; font-weight: 800 !important; background: none !important; padding: 0 !important; border: none !important; margin-top: 2px; }

            /* Action Buttons Mobile */
            .mobile-action-box { display: flex; flex-direction: column; gap: 12px; margin-top: 18px; }
            .btn-phonepe-primary { background: var(--phonepe-purple); color: white; padding: 16px; border-radius: 18px; font-size: 17px !important; font-weight: 800; text-align: center; text-decoration: none; display: block; box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3); }
            .btn-phonepe-danger { background: #fee2e2; color: #dc2626; padding: 16px; border-radius: 18px; font-size: 17px !important; font-weight: 800; text-align: center; text-decoration: none; display: block; }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 76px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 13px !important; font-weight: 700; width: 25%; }
            .phonepe-nav-item i { font-size: 24px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe Mobile Header Bar -->
<div class="mobile-header">
    <a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i></a>
    <h3>माय प्रोफाइल</h3>
    <a href="profile_settings.php"><i class="fa-solid fa-pen-to-square"></i></a>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">🎓 CMS PRO</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li><a href="online_exam.php"><i class="fas fa-file-signature"></i> Online Exam</a></li>
        <li><a href="my_results.php"><i class="fas fa-chart-line"></i> My Results</a></li>
        <li><a href="my_fees.php"><i class="fas fa-credit-card"></i> Fees History</a></li>
        <li><a href="homework.php"><i class="fas fa-book-reader"></i> Homework</a></li>
        <li><a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
        <li><a href="profile.php" class="active"><i class="fas fa-user-circle"></i> Profile Setting</a></li>
    </ul>
    <div style="margin-top: auto;">
        <a href="logout.php" style="color: #e41e3f; display: flex; align-items: center; gap: 10px; text-decoration: none; font-size: 15px; font-weight: 700;">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    
    <!-- 🖥️ DESKTOP HEADER -->
    <div class="header">
        <div><h2 style="font-size: 18px; font-weight: 800;">Student Profile</h2></div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: right;">
                <p style="font-size: 14px; font-weight: 700;"><?php echo htmlspecialchars($profile_data['name'] ?? 'Student'); ?></p>
                <p style="font-size: 12px; color: #65676b; font-weight: 600;">Roll: #<?php echo $roll_no; ?></p>
            </div>
            <img src="<?php echo $stu_photo; ?>" class="user-photo" alt="Profile">
        </div>
    </div>

    <div class="container">
        
        <!-- 📱 PhonePe Style Hero Card (Mobile) -->
        <div class="mobile-profile-hero">
            <img src="<?php echo $stu_photo; ?>" class="mobile-avatar-lg" alt="Profile">
            <h2><?php echo htmlspecialchars($profile_data['name'] ?? 'Student'); ?></h2>
            <div class="mobile-badge">Roll No: #<?php echo $roll_no; ?></div>
        </div>

        <!-- 🖥️ DESKTOP PROFILE CARD -->
        <div class="profile-card-desktop">
            <img src="<?php echo $stu_photo; ?>" alt="Profile">
            <div>
                <h1 style="font-size: 26px; font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($profile_data['name'] ?? 'Student'); ?></h1>
                <p style="font-size: 15px; color: #64748b; font-weight: 600; margin-top: 4px;">Course: <b style="color:var(--primary-blue);"><?php echo htmlspecialchars($student_course); ?></b> | Roll No: <b>#<?php echo $roll_no; ?></b></p>
                <a href="profile_settings.php" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit Profile Settings</a>
            </div>
        </div>

        <!-- Student Details Grid (Mobile & Desktop) -->
        <div class="info-grid">
            
            <div class="info-item">
                <div class="info-item-left">
                    <div class="info-icon"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <label>पूरा नाम (Full Name)</label>
                        <p><?php echo htmlspecialchars($profile_data['name'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-item-left">
                    <div class="info-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div>
                        <label>कोर्स (Course)</label>
                        <p><?php echo htmlspecialchars($student_course); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-item-left">
                    <div class="info-icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <label>मोबाइल नंबर (Mobile)</label>
                        <p><?php echo htmlspecialchars($phone); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-item-left">
                    <div class="info-icon" style="background:#ffedd5; color:#ea580c;"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <label>ईमेल (Email ID)</label>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-item-left">
                    <div class="info-icon" style="background:#fce7f3; color:#db2777;"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <label>पिता का नाम (Father's Name)</label>
                        <p><?php echo htmlspecialchars($father_name); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-item-left">
                    <div class="info-icon" style="background:#f1f5f9; color:#475569;"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <label>बैच टाइम (Batch Time)</label>
                        <p><?php echo htmlspecialchars($batch_time); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-item" style="grid-column: span 2;">
                <div class="info-item-left">
                    <div class="info-icon" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <label>पता (Address)</label>
                        <p><?php echo htmlspecialchars($address); ?></p>
                    </div>
                </div>
            </div>

        </div>

        <!-- 📱 Mobile Action Buttons -->
        <div class="mobile-action-box">
            <a href="profile_settings.php" class="btn-phonepe-primary"><i class="fa-solid fa-gear"></i> प्रोफाइल अपडेट करें</a>
            <a href="logout.php" class="btn-phonepe-danger"><i class="fa-solid fa-power-off"></i> लॉगआउट (Logout)</a>
        </div>

    </div>
</div>

<!-- 📱 PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="dashboard.php" class="phonepe-nav-item">
        <i class="fa-solid fa-house"></i>
        <span>होम</span>
    </a>
    <a href="my_fees.php" class="phonepe-nav-item">
        <i class="fa-solid fa-wallet"></i>
        <span>फीस</span>
    </a>
    <a href="my_results.php" class="phonepe-nav-item">
        <i class="fa-solid fa-file-invoice"></i>
        <span>रिजल्ट</span>
    </a>
    <a href="profile.php" class="phonepe-nav-item active">
        <i class="fa-solid fa-user"></i>
        <span>प्रोफाइल</span>
    </a>
</div>

</body>
</html>
<?php 
// End Output Buffering
ob_end_flush(); 
?>