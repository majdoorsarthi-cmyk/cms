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

// --- Fetch Latest Notice ---
$notice_q = mysqli_query($conn, "SELECT * FROM notifications ORDER BY id DESC LIMIT 1");
$latest_notice = ($notice_q && mysqli_num_rows($notice_q) > 0) ? mysqli_fetch_assoc($notice_q) : null;

// --- ATTENDANCE LOGIC ---
$month_start = date('Y-m-01');
$total_days_passed = (int)date('d'); 

$att_stats_q = mysqli_query($conn, "SELECT COUNT(*) as present_count FROM attendance 
                                    WHERE student_id = '$real_db_id' AND LOWER(status) = 'present' 
                                    AND date >= '$month_start'");
$att_stats = ($att_stats_q) ? mysqli_fetch_assoc($att_stats_q) : ['present_count' => 0];
$present_count = $att_stats['present_count'] ?? 0;
$attendance_percentage = ($total_days_passed > 0) ? round(($present_count / $total_days_passed) * 100) : 0;
$progress_color = ($attendance_percentage >= 75) ? "#2ecc71" : "#e74c3c"; 

// --- FEES LOGIC ---
$course_fee = $profile_data['course_fee'] ?? 0; 
$fee_q = mysqli_query($conn, "SELECT SUM(amount_paid) as paid FROM fees WHERE student_id = '$real_db_id'");
$total_paid = ($fee_q) ? (mysqli_fetch_assoc($fee_q)['paid'] ?? 0) : 0;
$due_amount = max(0, $course_fee - $total_paid);

// --- LISTS FOR DASHBOARD ---
$att_res = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = '$real_db_id' ORDER BY date DESC LIMIT 5");
$hw_res = mysqli_query($conn, "SELECT * FROM homework ORDER BY id DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Student Dashboard | CMS PRO</title>
    
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

        /* 🖥️ DESKTOP STYLES (ORIGINAL UNCHANGED) */
        .marquee-bar { background: var(--dark-sidebar); color: #fff; padding: 10px 0; font-size: 14px; font-weight: 500; position: sticky; top: 0; z-index: 1001; }
        .sidebar { width: 260px; background: var(--dark-sidebar); color: white; height: 100vh; position: fixed; padding: 25px 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-brand { font-size: 24px; font-weight: 800; margin-bottom: 35px; color: var(--primary-blue); }
        .sidebar-menu { list-style: none; flex: 1; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; padding: 14px 18px; display: flex; align-items: center; gap: 12px; border-radius: 12px; transition: 0.3s; font-size: 15px; margin-bottom: 5px; font-weight: 600; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #1e293b; color: var(--primary-blue); }
        
        .main-content { margin-left: 260px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 18px 40px; border-bottom: 1px solid #e4e6eb; }
        .user-photo { width: 48px; height: 48px; border-radius: 50%; border: 3px solid var(--primary-blue); object-fit: cover; }
        .container { padding: 30px 40px; }
        
        .welcome-box { background: linear-gradient(135deg, #3498db, #2980b9); padding: 30px; border-radius: 20px; color: white; margin-bottom: 30px; position: relative; overflow: hidden; }
        .welcome-box h1 { font-size: 28px; font-weight: 800; }
        .welcome-box p { font-size: 16px; margin-top: 5px; opacity: 0.9; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #e4e6eb; }
        .stat-card h3 { font-size: 16px; font-weight: 700; color: #4b5563; }
        .stat-card p { font-size: 24px; font-weight: 800; margin-top: 8px; }

        .progress-container { width: 100%; background: #eee; height: 8px; border-radius: 10px; margin-top: 12px; }
        .progress-bar { height: 100%; border-radius: 10px; }
        
        .main-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 30px; }
        .box { background: white; padding: 25px; border-radius: 20px; border: 1px solid #e4e6eb; }
        .box h2 { font-size: 18px; font-weight: 800; margin-bottom: 18px; color: #0f172a; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 14px; color: #65676b; font-size: 13px; font-weight: 700; border-bottom: 2px solid #f0f2f5; text-transform: uppercase; }
        td { padding: 16px 14px; border-bottom: 1px solid #f0f2f5; font-size: 15px; font-weight: 600; }
        .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .bg-success { background: #e7f3ff; color: #1877f2; }
        .bg-danger { background: #fdeaea; color: #e41e3f; }

        .hw-item { background: #f7f8fa; padding: 18px; border-radius: 15px; margin-bottom: 15px; border-left: 4px solid var(--primary-blue); }
        .btn-action { background: var(--primary-blue); color: white; text-decoration: none; padding: 14px; border-radius: 12px; font-size: 14px; display: block; text-align: center; margin-top: 12px; font-weight: 700; transition: 0.2s; }
        .btn-action:hover { opacity: 0.9; }

        .mobile-header, .mobile-app-grid, .mobile-bottom-nav { display: none; }


        /* 📱 PHONEPE REAL MOBILE APP INTERFACE (BIG & BOLD FONTS) */
        @media (max-width: 992px) {
            body { background: var(--phonepe-bg) !important; padding-top: 75px; padding-bottom: 95px; display: block !important; }

            .sidebar, .header { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; }
            .container { padding: 14px !important; }

            /* App Header Bar */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 72px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.4);
            }
            .mobile-header-user { display: flex; align-items: center; gap: 14px; }
            .mobile-avatar { width: 48px; height: 48px; border-radius: 50%; border: 2px solid #ffffff; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.9); font-size: 13px !important; font-weight: 600; display: block; }

            /* App Marquee */
            .marquee-bar { background: #3f1d70 !important; font-size: 14px !important; font-weight: 600 !important; border-bottom: 1px solid rgba(255,255,255,0.1); }

            /* PhonePe Welcome Card */
            .welcome-box {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, #7e22ce 100%) !important;
                border-radius: 24px !important; padding: 24px 20px !important; margin-bottom: 18px !important;
                box-shadow: 0 8px 25px rgba(95, 37, 159, 0.3) !important;
            }
            .welcome-box h1 { font-size: 24px !important; font-weight: 800 !important; }
            .welcome-box p { font-size: 16px !important; font-weight: 600 !important; opacity: 0.95; margin-top: 6px; }

            /* Stats Cards */
            .stats-grid { grid-template-columns: 1fr !important; gap: 14px !important; margin-bottom: 18px !important; }
            .stat-card { background: #ffffff !important; border-radius: 22px !important; padding: 20px !important; border: none !important; box-shadow: 0 4px 18px rgba(0,0,0,0.04) !important; }
            .stat-card h3 { font-size: 17px !important; font-weight: 800 !important; color: #1e293b !important; }
            .stat-card p { font-size: 28px !important; font-weight: 800 !important; }

            /* PhonePe Style Quick Service Grid */
            .mobile-app-grid { display: block !important; margin-bottom: 20px; }
            .app-grid-title { font-size: 19px !important; font-weight: 800; color: #0f172a; margin: 10px 4px 14px 4px; letter-spacing: -0.2px; }
            .app-grid-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; background: #ffffff; padding: 20px 12px; border-radius: 24px; box-shadow: 0 4px 18px rgba(0,0,0,0.04); }
            
            .app-grid-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; text-align: center; }
            .app-grid-icon { width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .app-grid-label { font-size: 13px !important; font-weight: 700; color: #334155; line-height: 1.2; }

            /* Tables & Content Box */
            .main-grid { grid-template-columns: 1fr !important; gap: 18px !important; }
            .box { background: #ffffff !important; border-radius: 24px !important; padding: 20px !important; border: none !important; box-shadow: 0 4px 18px rgba(0,0,0,0.04) !important; }
            .box h2 { font-size: 20px !important; font-weight: 800 !important; }

            table th { font-size: 13px !important; padding: 10px 8px !important; }
            table td { font-size: 16px !important; font-weight: 700 !important; padding: 14px 8px !important; }
            .status-badge { font-size: 12px !important; padding: 6px 12px !important; }

            .hw-item { padding: 16px !important; border-radius: 18px !important; }
            .hw-item b { font-size: 16px !important; font-weight: 800 !important; }
            .btn-action { font-size: 17px !important; font-weight: 800 !important; padding: 16px !important; border-radius: 16px !important; }

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

<!-- 📱 PhonePe Top Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-user">
        <a href="profile.php"><img src="<?php echo $stu_photo; ?>" class="mobile-avatar" alt="Profile"></a>
        <div>
            <h3><?php echo htmlspecialchars($profile_data['name'] ?? 'Student'); ?></h3>
            <small>Roll: #<?php echo $roll_no; ?></small>
        </div>
    </div>
    <div style="color:#fff; font-size:22px;">
        <a href="profile_settings.php" style="color:white;"><i class="fa-solid fa-gear"></i></a>
    </div>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">🎓 CMS PRO</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li><a href="online_exam.php"><i class="fas fa-file-signature"></i> Online Exam</a></li>
        <li><a href="my_results.php"><i class="fas fa-chart-line"></i> My Results</a></li>
        <li><a href="my_fees.php"><i class="fas fa-credit-card"></i> Fees History</a></li>
        <li><a href="homework.php"><i class="fas fa-book-reader"></i> Homework</a></li>
        <li><a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
        <li><a href="profile_settings.php"><i class="fas fa-user-circle"></i> Profile Setting</a></li>
    </ul>
    <div style="margin-top: auto;">
        <a href="logout.php" style="color: #e41e3f; display: flex; align-items: center; gap: 10px; text-decoration: none; font-size: 15px; font-weight: 700;">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    
    <!-- Notice Marquee Bar -->
    <div class="marquee-bar">
        <marquee behavior="scroll" direction="left" scrollamount="6">
            📢 <b>Notice:</b> <?php echo $latest_notice ? htmlspecialchars($latest_notice['title']) . " - " . htmlspecialchars($latest_notice['message']) : "Welcome to the Coaching Student Portal."; ?>
        </marquee>
    </div>

    <!-- 🖥️ DESKTOP HEADER -->
    <div class="header">
        <div><h2 style="font-size: 18px; font-weight: 800;">Dashboard Overview</h2></div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: right;">
                <p style="font-size: 14px; font-weight: 700;"><?php echo htmlspecialchars($profile_data['name'] ?? 'Student'); ?></p>
                <p style="font-size: 12px; color: #65676b; font-weight: 600;">Roll: #<?php echo $roll_no; ?></p>
            </div>
            <img src="<?php echo $stu_photo; ?>" class="user-photo" alt="Profile">
        </div>
    </div>

    <div class="container">
        
        <!-- Welcome Card -->
        <div class="welcome-box">
            <h1>Welcome back, <?php echo explode(' ', $profile_data['name'] ?? 'Student')[0]; ?>!</h1>
            <p>You have been present for <?php echo $present_count; ?> days this month.</p>
            <i class="fas fa-user-graduate" style="position: absolute; right: -10px; bottom: -10px; font-size: 130px; opacity: 0.12;"></i>
        </div>

        <!-- 📱 PhonePe Style Quick Action Grid -->
        <div class="mobile-app-grid">
            <div class="app-grid-title">Quick Services</div>
            <div class="app-grid-container">
                <a href="online_exam.php" class="app-grid-item">
                    <div class="app-grid-icon" style="background:#f3e8ff; color:#5f259f;"><i class="fa-solid fa-file-pen"></i></div>
                    <span class="app-grid-label">Exams</span>
                </a>
                <a href="homework.php" class="app-grid-item">
                    <div class="app-grid-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-book"></i></div>
                    <span class="app-grid-label">Homework</span>
                </a>
                <a href="my_results.php" class="app-grid-item">
                    <div class="app-grid-icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-solid fa-chart-line"></i></div>
                    <span class="app-grid-label">Results</span>
                </a>
                <a href="my_fees.php" class="app-grid-item">
                    <div class="app-grid-icon" style="background:#ffedd5; color:#ea580c;"><i class="fa-solid fa-wallet"></i></div>
                    <span class="app-grid-label">Pay Fees</span>
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><i class="fas fa-calendar-alt" style="color:#3498db"></i> Attendance</h3>
                <p><?php echo $attendance_percentage; ?>%</p>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?php echo $attendance_percentage; ?>%; background: <?php echo $progress_color; ?>;"></div>
                </div>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-wallet" style="color:#10b981"></i> Fees Paid</h3>
                <p style="color: #10b981;">₹<?php echo number_format($total_paid); ?></p>
                <span style="font-size: 13px; color: #e41e3f; font-weight: 700;">Due: ₹<?php echo number_format($due_amount); ?></span>
            </div>

            <div class="stat-card">
                <h3><i class="fas fa-book" style="color:#f39c12"></i> Enrolled Course</h3>
                <p style="font-size: 20px; color:#1c1e21; font-weight: 800;"><?php echo htmlspecialchars($student_course); ?></p>
                <p style="font-size: 13px; color: #65676b; font-weight: 600; margin-top: 4px;">Batch: <?php echo $profile_data['batch_time'] ?? 'Regular'; ?></p>
            </div>
        </div>

        <!-- Main Content Tables / Lists -->
        <div class="main-grid">
            <div class="box">
                <h2><i class="fas fa-history" style="color:#3498db"></i> Recent Attendance</h2>
                <table>
                    <thead>
                        <tr><th>Date</th><th>Day</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        if($att_res && mysqli_num_rows($att_res) > 0) {
                            while($row = mysqli_fetch_assoc($att_res)) {
                                $status_class = (strtolower($row['status']) == 'present') ? 'bg-success' : 'bg-danger';
                                $db_date = $row['date'];
                                echo "<tr>
                                        <td style='font-weight:700;'>".date('d M, Y', strtotime($db_date))."</td>
                                        <td style='color:#65676b;'>".date('l', strtotime($db_date))."</td>
                                        <td><span class='status-badge $status_class'>{$row['status']}</span></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; padding:30px; color:#94a3b8; font-weight:600;'>No records found in attendance table.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <h2><i class="fas fa-file-download" style="color:#f39c12"></i> Study Materials</h2>
                <?php
                if($hw_res && mysqli_num_rows($hw_res) > 0){
                    while($hw = mysqli_fetch_assoc($hw_res)) {
                        echo "<div class='hw-item'>
                                <b style='display:block; margin-bottom:6px;'>".htmlspecialchars($hw['title'])."</b>
                                <div style='display:flex; justify-content:space-between; align-items:center;'>
                                    <span style='font-size:12px; color:#65676b; font-weight:600;'>".date('d M Y', strtotime($hw['upload_date']))."</span>
                                    <a href='uploads/".$hw['file_path']."' style='color:#3498db; text-decoration:none; font-size:13px; font-weight:800;' download><i class='fas fa-download'></i> Download</a>
                                </div>
                              </div>";
                    }
                } else {
                    echo "<p style='text-align:center; color:#94a3b8; padding:20px; font-weight:600;'>No homework found.</p>";
                }
                ?>
                <a href="my_fees.php" class="btn-action">Pay Online Fees</a>
                <a href="homework.php" class="btn-action" style="background:#f0f2f5; color:#1c1e21; border:1px solid #e4e6eb">View All Homework</a>
            </div>
        </div>

    </div>
</div>

<!-- 📱 PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="dashboard.php" class="phonepe-nav-item active">
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
    <a href="profile_settings.php" class="phonepe-nav-item">
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