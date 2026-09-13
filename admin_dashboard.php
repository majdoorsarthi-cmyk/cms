<?php 
// 1. Session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// 2. Admin login check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// --- Verification Logic ---
if(isset($_GET['action']) && isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $page_target = isset($_GET['page']) ? mysqli_real_escape_string($conn, $_GET['page']) : 'dashboard';
    
    if($_GET['action'] == 'verify'){
        $expiry_date = date('Y-m-d', strtotime('+1 year'));
        mysqli_query($conn, "UPDATE students SET status='Active', lms_access_expiry='$expiry_date' WHERE id='$id'");
    } elseif($_GET['action'] == 'suspend'){
        mysqli_query($conn, "UPDATE students SET status='Suspended' WHERE id='$id'");
    }
    
    header("Location: admin_dashboard.php?page=" . $page_target);
    exit();
}

// --- Manual Quiz Access Logic ---
if(isset($_GET['action']) && $_GET['action'] == 'toggle_quiz' && isset($_GET['subject_id'])){
    $sub_id = (int)$_GET['subject_id'];
    
    $check = mysqli_query($conn, "SELECT is_manual_active FROM subjects WHERE id = '$sub_id'");
    $row = mysqli_fetch_assoc($check);
    $new_status = ($row['is_manual_active'] == 1) ? 0 : 1;
    
    mysqli_query($conn, "UPDATE subjects SET is_manual_active = '$new_status' WHERE id = '$sub_id'");
    
    header("Location: admin_dashboard.php?page=lms_master");
    exit();
}

// --- Notice Logic ---
$msg_status = "";
if(isset($_POST['send_notice'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $date = date('Y-m-d H:i:s');
    
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS notifications (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), message TEXT, created_at DATETIME)");
    $query = "INSERT INTO notifications (title, message, created_at) VALUES ('$title', '$msg', '$date')";
    if(mysqli_query($conn, $query)){
        $msg_status = "<div class='alert-success'><i class='fa-solid fa-circle-check'></i> Notice Broadcasted Successfully!</div>";
    }
}

// 3. Database Stats
$total_students = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM students"));
$total_teachers = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='teacher'"));
$total_questions = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM questions"));
$lms_active_users = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM students WHERE status='Active'"));

$seven_days = date('Y-m-d', strtotime('+7 days'));
$expiring_soon_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM students WHERE status='Active' AND lms_access_expiry <= '$seven_days' AND lms_access_expiry >= CURDATE()"));

$pending_requests = 0;
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'service_requests'");
if(mysqli_num_rows($check_table) > 0) {
    $pending_requests = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM service_requests WHERE status='Pending'"));
}

$current_month = date('m');
$current_year = date('Y');
$monthly_res = mysqli_query($conn, "SELECT SUM(amount_paid) as m_total FROM fees WHERE MONTH(payment_date) = '$current_month' AND YEAR(payment_date) = '$current_year'");
$monthly_data = mysqli_fetch_assoc($monthly_res);
$monthly_fees = $monthly_data['m_total'] ?? 0;

$current_page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Dashboard | CMS PRO Ultra</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #a2a3b7;
            --sidebar-active-bg: #2b2b40;
            --main-bg: #f8fafc;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --white: #ffffff;
            --card-border: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            
            --accent: #f59e0b; 
            --danger: #ef4444;
            --success: #10b981;
            --purple: #8b5cf6;
            
            --phonepe-purple: #5f259f;
            --phonepe-gradient: linear-gradient(135deg, #4d1c87 0%, #290d4c 100%);
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            -webkit-tap-highlight-color: transparent; 
        }

        body {
            background: var(--main-bg); 
            color: var(--text-dark); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- SIDEBAR BASE --- */
        .sidebar {
            background: var(--sidebar-bg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-brand-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar-brand-box h3 { 
            font-size: 20px; 
            font-weight: 800; 
            color: #fff; 
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-brand-box h3 i { color: var(--primary); }

        .nav-label { 
            font-size: 11px; 
            text-transform: uppercase; 
            color: #565674; 
            padding: 20px 20px 8px 20px; 
            font-weight: 800; 
            letter-spacing: 1px; 
        }

        .menu-item { margin-bottom: 4px; padding: 0 12px; }
        .dropdown-btn, .nav-link { 
            width: 100%; 
            padding: 12px 14px; 
            text-decoration: none; 
            font-size: 14px; 
            color: var(--sidebar-text); 
            display: flex; 
            align-items: center; 
            background: none; 
            border: none; 
            cursor: pointer; 
            border-radius: 10px; 
            transition: all 0.2s ease; 
            font-weight: 600; 
        }
        .dropdown-btn i.main-icon, .nav-link i.main-icon { 
            margin-right: 12px; 
            font-size: 16px; 
            width: 20px; 
            text-align: center; 
        }
        .dropdown-btn:hover, .nav-link:hover, .active-nav { 
            background: var(--sidebar-active-bg); 
            color: #ffffff; 
        }
        .active-nav {
            background: var(--primary) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }
        
        .dropdown-container { 
            display: none; 
            padding-left: 12px; 
            margin-top: 4px; 
        }
        .dropdown-container a { 
            color: var(--sidebar-text); 
            padding: 10px 14px; 
            font-size: 13px; 
            display: block; 
            text-decoration: none; 
            border-radius: 8px; 
            transition: 0.2s; 
            font-weight: 500;
        }
        .dropdown-container a:hover { 
            color: #ffffff; 
            background: rgba(255,255,255,0.05); 
        }
        .logout-btn { 
            color: #f87171 !important; 
            margin-top: 20px !important; 
            border-top: 1px solid rgba(255,255,255,0.05); 
            padding-top: 16px !important; 
        }

        /* --- DASHBOARD HEADERS & CARDS --- */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-header h2 { 
            font-size: 22px; 
            font-weight: 800; 
            color: var(--text-dark); 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }

        .dashboard-cards { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 20px; 
            margin-bottom: 28px; 
        }
        .card { 
            padding: 20px; 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            gap: 16px; 
            color: #fff; 
            transition: transform 0.25s ease, box-shadow 0.25s ease; 
            box-shadow: var(--shadow-md); 
            position: relative;
            overflow: hidden;
        }
        .card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
        
        .card-blue { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .card-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .card-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .card-default { color: var(--text-dark); background: var(--white); border: 1px solid var(--card-border); }

        .icon-box { 
            width: 52px; 
            height: 52px; 
            border-radius: 14px; 
            background: rgba(255,255,255,0.2); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 22px; 
            color: #fff; 
            flex-shrink: 0; 
        }
        .card-default .icon-box { background: #f1f5f9; color: var(--primary); }
        .card .text { display: flex; flex-direction: column; }
        .card span { font-size: 13px; opacity: 0.9; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-default span { color: var(--text-muted); }
        .card h2 { margin-top: 4px; font-size: 26px; font-weight: 800; }

        /* --- CONTENT CONTAINERS & TABLES --- */
        .content-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 25px; }
        .action-box { background: var(--white); padding: 22px; border-radius: 16px; border: 1px solid var(--card-border); box-shadow: var(--shadow-sm); }
        .action-box h3 { margin-bottom: 16px; font-size: 16px; font-weight: 700; color: var(--text-dark); }
        
        input, textarea { width: 100%; padding: 12px 16px; margin-bottom: 14px; background: #f8fafc; border: 1px solid var(--card-border); border-radius: 10px; outline: none; font-size: 14px; color: var(--text-dark); transition: 0.2s; }
        input:focus, textarea:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 700; width: 100%; transition: 0.2s; font-size: 14px; }
        .btn-submit:hover { background: var(--primary-dark); }

        .alert-success { background: #ecfdf5; color: #047857; padding: 12px; border-radius: 10px; border: 1px solid #a7f3d0; font-size: 13px; font-weight: 600; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }

        .notice-item { background: #f8fafc; padding: 12px 14px; border-radius: 10px; margin-bottom: 10px; border-left: 4px solid var(--primary); border-top: 1px solid var(--card-border); border-right: 1px solid var(--card-border); border-bottom: 1px solid var(--card-border); }
        .notice-item p { font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .notice-item small { color: var(--text-muted); font-size: 11px; }

        .table-container { background: var(--white); border-radius: 16px; padding: 22px; border: 1px solid var(--card-border); box-shadow: var(--shadow-sm); }
        .table-container h3 { font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--text-dark); }
        
        .table-responsive-box { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 650px; }
        th { text-align: left; padding: 12px 14px; color: var(--text-muted); font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; font-weight: 700; letter-spacing: 0.5px; }
        td { padding: 14px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: var(--text-dark); vertical-align: middle; }
        
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .badge-active, .badge.active { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-suspended, .badge.suspended { background: #fee2e2; color: #b91c1c; }
        .badge-purple { background: #f3e8ff; color: #6b21a8; }
        
        .verify-btn, .verify-link { color: #fff; background: var(--success); padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 12px; display: inline-block; }
        .suspend-btn { color: #fff; background: var(--danger); padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 12px; display: inline-block; }
        .edit-link { color: var(--primary); font-size: 16px; text-decoration: none; }
        
        .rotate { transform: rotate(180deg); }
        .arrow { margin-left: auto; transition: 0.3s; font-size: 12px; }

        .mobile-bottom-nav { display: none; }
        .mobile-header { display: none; }

        /* 🖥️ DESKTOP VIEW (Screen width >= 992px) - STABLE & CLEAN */
        @media (min-width: 992px) {
            .sidebar { 
                position: fixed;
                top: 0; left: 0;
                width: 260px; 
                height: 100vh; 
                padding: 0 0 20px 0; 
                overflow-y: auto; 
                z-index: 1000; 
                box-shadow: var(--shadow-md); 
            }

            .main { 
                margin-left: 260px; 
                padding: 30px; 
                width: calc(100% - 260px); 
                min-height: 100vh;
            }

            .sidebar-close-btn { display: none; }
        }

        /* 📱 MOBILE VIEW - PhonePe Native UI Integration */
        @media (max-width: 991px) {
            body { background: #f1f5f9; }

            /* PhonePe Dynamic Top Bar */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 62px;
                background: var(--phonepe-gradient);
                z-index: 999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            }
            
            .mobile-profile-box { display: flex; align-items: center; gap: 12px; }
            .mobile-avatar {
                width: 38px; height: 38px;
                border-radius: 50%;
                background: #ffffff;
                color: var(--phonepe-purple);
                display: flex; align-items: center; justify-content: center;
                font-size: 16px; font-weight: 800;
            }
            .mobile-header h3 { font-size: 15px; font-weight: 800; color: #ffffff; margin: 0; }
            .mobile-header small { font-size: 11px; color: rgba(255,255,255,0.8); display: block; }

            .menu-toggle { 
                font-size: 16px; color: #ffffff; 
                cursor: pointer; border: none; 
                background: rgba(255,255,255,0.15); 
                width: 38px; height: 38px; 
                border-radius: 50%; 
                display: flex; align-items: center; justify-content: center; 
            }

            .main { 
                margin-left: 0 !important; 
                width: 100% !important; 
                padding: 76px 12px 80px 12px !important; 
            }

            .page-header h2 { font-size: 18px; }

            .content-grid { grid-template-columns: 1fr; gap: 16px; }

            /* Grid Layout for Mobile Dashboard */
            .dashboard-cards { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 12px; 
                margin-bottom: 20px;
            }
            .card { 
                padding: 14px 12px; 
                border-radius: 14px; 
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .icon-box { width: 38px; height: 38px; border-radius: 10px; font-size: 16px; }
            .card span { font-size: 11px; }
            .card h2 { font-size: 20px; margin-top: 0; }

            /* Action boxes & Tables on Mobile */
            .action-box, .table-container {
                border-radius: 14px;
                padding: 16px;
                margin-bottom: 16px;
            }

            table { min-width: 550px; }
            th, td { padding: 10px; font-size: 12px; }

            /* Inline Date form inside LMS table fix */
            .table-responsive-box form {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                align-items: center;
                margin-top: 6px;
            }
            .table-responsive-box form input[type="date"] {
                width: 100px;
                padding: 4px 6px;
                font-size: 11px;
                margin-bottom: 0;
            }

            /* Off-Canvas Navigation Drawer */
            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 9998;
            }
            .sidebar-overlay.active { display: block; }

            .sidebar { 
                position: fixed;
                top: 0; bottom: 0;
                left: -100%;
                width: 80vw; max-width: 300px;
                height: 100%;
                z-index: 9999;
                box-shadow: var(--shadow-lg);
                padding: 0 0 20px 0;
                overflow-y: auto;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .sidebar.active { left: 0; }

            .sidebar-brand-box { background: rgba(0, 0, 0, 0.2); }
            .sidebar-close-btn {
                background: rgba(255,255,255,0.1);
                border: none;
                width: 32px; height: 32px;
                border-radius: 50%;
                color: #ffffff;
                font-size: 14px;
                cursor: pointer;
            }

            /* Bottom App Bar (PhonePe Style) */
            .mobile-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 60px;
                background: #ffffff;
                border-top: 1px solid var(--card-border);
                z-index: 998;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 10px rgba(0,0,0,0.03);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: var(--text-muted);
                font-size: 10px;
                font-weight: 700;
                width: 20%;
            }

            .phonepe-nav-item i {
                font-size: 18px;
                margin-bottom: 2px;
            }

            .phonepe-nav-item.active { color: var(--phonepe-purple); }
        }
    </style>
</head>
<body>

<!-- Mobile Dynamic Header -->
<div class="mobile-header">
    <div class="mobile-profile-box">
        <div class="mobile-avatar"><i class="fa-solid fa-user-shield"></i></div>
        <div>
            <h3>TC ACADEMY CONTROL</h3>
            <small>Tendukheda Branch</small>
        </div>
    </div>
    <button type="button" class="menu-toggle" onclick="toggleMobileSidebar()">
        <i class="fa-solid fa-bars-staggered"></i>
    </button>
</div>

<!-- Backdrop Overlay -->
<div class="sidebar-overlay" id="overlay" onclick="toggleMobileSidebar()"></div>

<!-- Navigation Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand-box">
        <h3><i class="fa-solid fa-cube"></i> CMS PRO</h3>
        <button type="button" class="sidebar-close-btn" onclick="toggleMobileSidebar()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div>
        <div class="nav-label">Main Menu</div>
        <div class="menu-item">
            <a href="admin_dashboard.php?page=dashboard" class="nav-link <?= ($current_page == 'dashboard') ? 'active-nav' : '' ?>">
                <i class="fa-solid fa-chart-pie main-icon"></i> Dashboard
            </a>
        </div>

        <div class="nav-label">Academic & Admin</div>
        <div class="menu-item">
            <button type="button" class="dropdown-btn">
                <span><i class="fa-solid fa-user-graduate main-icon"></i> Academic</span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </button>
            <div class="dropdown-container">
                <a href="add_student.php"><i class="fa-solid fa-plus"></i> New Admission</a>
                <a href="manage_students.php"><i class="fa-solid fa-users-gear"></i> Manage Students</a>
                <a href="attendance_scanner.php"><i class="fa-solid fa-qrcode"></i> QR Attendance</a>
                <a href="homework.php"><i class="fa-solid fa-book-open"></i> Homework</a>
            </div>
        </div>

        <div class="menu-item">
            <button type="button" class="dropdown-btn" style="color: var(--accent);">
                <span><i class="fa-solid fa-id-card main-icon"></i> Document Center</span>
                <?php if($pending_requests > 0) echo "<span style='background:var(--danger);color:white;padding:2px 6px;border-radius:10px;font-size:10px;margin-left:auto;'>$pending_requests</span>"; ?>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </button>
            <div class="dropdown-container">
                <a href="verify_requests.php">Verify Requests</a>
                <a href="generate_docs.php">Generate/Issue Docs</a>
                <a href="view_issued_docs.php">Issued History</a>
            </div>
        </div>

        <div class="menu-item">
            <button type="button" class="dropdown-btn <?= ($current_page == 'lms_master') ? 'active-nav' : '' ?>">
                <span><i class="fa-solid fa-laptop-code main-icon"></i> E-Learning</span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </button>
            <div class="dropdown-container" style="<?= ($current_page == 'lms_master') ? 'display:block;' : '' ?>">
                <a href="admin_dashboard.php?page=lms_master" style="color:#a78bfa; font-weight:700;">LMS Master Control</a>
                <a href="admin_upload_video.php">Upload Videos</a>
                <a href="admin_ebook.php">Manage E-Books</a>
                <a href="manage_courses.php">Course Structure</a>
            </div>
        </div>

        <div class="menu-item">
    <button type="button" class="dropdown-btn">
        <span><i class="fa-solid fa-lock-open main-icon"></i> Manual Quiz Access</span>
        <i class="fa-solid fa-chevron-down arrow"></i>
    </button>
    <div class="dropdown-container">
        <?php 
        // DCA / PGDCA और BA दोनों के विषयों को शामिल किया गया है
        // Note: ID अपनी Database `subjects` table के अनुसार मैच कर लें
        $subjects = [
            // --- DCA / PGDCA Subjects ---
            1  => "IT Tools (DCA)", 
            2  => "MS Office (DCA)", 
            3  => "MS Access (DCA)", 
            4  => "C++ (PGDCA)", 
            5  => "Communication", 
            6  => "Web Tech (PGDCA)", 
            7  => "Tally (DCA)", 
            8  => "C Language", 
            9  => "Cyber Security",

            // --- BA Subjects ---
            10 => "BA Hindi Literature",
            11 => "BA English Literature",
            12 => "BA History",
            13 => "BA Political Science",
            14 => "BA Sociology",
            15 => "BA Economics"
        ];
        
        foreach($subjects as $id => $name) { 
            $status_q = mysqli_query($conn, "SELECT is_manual_active FROM subjects WHERE id = '$id'");
            $sub = mysqli_fetch_assoc($status_q);
            $is_active = ($sub && $sub['is_manual_active'] == 1);
            
            $btn_text = $is_active ? "बंद करें" : "चालू करें";
            $color = $is_active ? "var(--danger)" : "var(--success)";
        ?>
            <a href="admin_dashboard.php?page=lms_master&action=toggle_quiz&subject_id=<?php echo $id; ?>" 
               style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; font-size: 12px;">
                <span><?php echo $name; ?></span>
                <span style="background: <?php echo $is_active ? 'rgba(239, 68, 68, 0.2)' : 'rgba(16, 185, 129, 0.2)'; ?>; color: <?php echo $color; ?>; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px;">
                    <?php echo $btn_text; ?>
                </span>
            </a>
        <?php } ?>
    </div>
</div>

        <div class="menu-item">
            <a href="admin_dashboard.php?page=lms_master&action=enable_all" class="nav-link" style="color:var(--success);">
                <i class="fa-solid fa-key main-icon"></i> Unlock Attempt
            </a>
        </div>

        <div class="menu-item">
            <button type="button" class="dropdown-btn">
                <span><i class="fa-solid fa-file-pen main-icon"></i> Examination</span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </button>
            <div class="dropdown-container">
                <a href="add_marks.php">Add Marks</a>
                <a href="bulk_upload_marks.php">Bulk Upload</a>
                <a href="admin_add_question.php">Add Questions</a>
                <a href="manage_exams.php">Manage Exams</a>
                <a href="view_results.php">View Results</a>
            </div>
        </div>

        <div class="menu-item">
            <a href="all_activity.php" class="nav-link"><i class="fa-solid fa-clock-rotate-left main-icon"></i> Activity Log</a>
        </div>
        <div class="menu-item">
            <a href="logout.php" class="nav-link logout-btn"><i class="fa-solid fa-right-from-bracket main-icon"></i> Logout</a>
        </div>
    </div>
</div>

<!-- Main Area -->
<div class="main">
    
    <?php if($current_page == 'lms_master'): ?>
        <!-- LMS PAGE VIEW -->
        <div class="page-header">
            <h2>LMS Control Panel <span class="badge badge-purple"><i class="fa-solid fa-bolt"></i> LIVE</span></h2>
        </div>

        <div class="dashboard-cards">
            <div class="card card-default">
                <div class="icon-box"><i class="fa-solid fa-circle-play"></i></div>
                <div class="text"><span>Active Learners</span><h2><?= $lms_active_users ?></h2></div>
            </div>
            <div class="card card-default">
                <div class="icon-box"><i class="fa-solid fa-hourglass-half" style="color: var(--danger);"></i></div>
                <div class="text"><span>Expiring Soon</span><h2><?= $expiring_soon_count ?></h2></div>
            </div>
            <div class="card card-default">
                <div class="icon-box"><i class="fa-solid fa-user-clock" style="color: var(--accent);"></i></div>
                <div class="text"><span>Pending Review</span><h2><?= $pending_requests ?></h2></div>
            </div>
        </div>

        <div class="table-container">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
                <h3>Student LMS Access & Support</h3>
                <div>
                    <a href="admin_dashboard.php?page=lms_master" style="font-size:12px; text-decoration:none; color:<?= !isset($_GET['filter'])?'var(--primary)':'var(--text-muted)' ?>; margin-right:10px; font-weight:700;">ALL</a>
                    <a href="admin_dashboard.php?page=lms_master&filter=expiring" style="font-size:12px; text-decoration:none; color:<?= (isset($_GET['filter']) && $_GET['filter']=='expiring')?'var(--danger)':'var(--text-muted)' ?>; font-weight:700;">EXPIRING SOON</a>
                </div>
            </div>
            
            <div class="table-responsive-box">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Expiry Date</th>
                            <th>LMS Access</th>
                            <th>Mentor Support</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT s.id, u.name, s.course, s.status, s.lms_access_expiry, u.mobile FROM students s JOIN users u ON s.user_id = u.id ";
                        if(isset($_GET['filter']) && $_GET['filter'] == 'expiring'){
                            $sql .= "WHERE s.status='Active' AND s.lms_access_expiry <= '$seven_days' AND s.lms_access_expiry >= CURDATE() ";
                        }
                        $sql .= "ORDER BY s.id DESC";

                        $lms_q = mysqli_query($conn, $sql);
                        while($lms = mysqli_fetch_assoc($lms_q)):
                            $expiry_display = ($lms['lms_access_expiry']) ? date('d M Y', strtotime($lms['lms_access_expiry'])) : 'No Date';
                            $wa_msg = urlencode("Namaste " . $lms['name'] . ", Aapka " . $lms['course'] . " course access " . $expiry_display . " ko khatam ho raha hai. Isse jari rakhne ke liye humein message karein.");
                        ?>
                        <tr>
                            <td><b><?= $lms['name'] ?></b></td>
                            <td><?= $lms['course'] ?></td>
                            <td><small><?= $expiry_display ?></small></td>
                            <td>
                                <span class="badge <?= ($lms['status']=='Active')?'badge-active':'badge-warning' ?>">
                                    <?= ($lms['status']=='Active')?'Lifetime Enabled':'Access Locked' ?>
                                </span>
                            </td>
                            <td><a href="https://wa.me/91<?= $lms['mobile'] ?>?text=<?= $wa_msg ?>" target="_blank" style="color:var(--success); text-decoration:none; font-weight: 700;"><i class="fa-brands fa-whatsapp"></i> Notify</a></td>
                            <td>
                                <?php if($lms['status'] != 'Active'): ?>
                                    <a href="?action=verify&id=<?= $lms['id'] ?>&page=lms_master" class="verify-link">Enable</a>
                                <?php else: ?>
                                    <a href="?action=suspend&id=<?= $lms['id'] ?>&page=lms_master" style="color:var(--danger); text-decoration:none; font-size:12px; font-weight:700;">Revoke</a>
                                <?php endif; ?>

                                <form action="update_date.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="redirect_page" value="lms_master">
                                    <input type="hidden" name="student_id" value="<?= $lms['id'] ?>"> 
                                    <input type="date" name="start_date" required>
                                    <input type="date" name="end_date" required>
                                    <button type="submit" style="padding:4px 8px; font-size:11px; cursor:pointer; background: var(--primary); color:#fff; border:none; border-radius:6px; font-weight:700;">Set</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- MAIN DASHBOARD VIEW -->
        <div class="page-header">
            <h2>Dashboard Overview</h2>
        </div>

        <div class="dashboard-cards">
            <div class="card card-blue">
                <div class="icon-box"><i class="fa-solid fa-users"></i></div>
                <div class="text"><span>Total Students</span><h2><?php echo $total_students; ?></h2></div>
            </div>
            <div class="card card-green">
                <div class="icon-box"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                <div class="text"><span>Total Revenue</span><h2>₹<?php echo number_format($monthly_fees); ?></h2></div>
            </div>
            <div class="card card-orange">
                <div class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="text"><span>Pending Requests</span><h2><?php echo $pending_requests; ?></h2></div>
            </div>
            <div class="card card-purple">
                <div class="icon-box"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="text"><span>Present Today</span><h2>
                    <?php 
                        $today = date('Y-m-d');
                        $att_q = mysqli_query($conn, "SELECT id FROM attendance WHERE date='$today' AND status='Present'");
                        echo mysqli_num_rows($att_q);
                    ?></h2>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="action-box">
                <h3><i class="fa-solid fa-bullhorn" style="color:var(--primary);"></i> Broadcast Notice</h3>
                <?php echo $msg_status; ?>
                <form action="" method="POST">
                    <input type="text" name="title" placeholder="Notice Title" required>
                    <textarea name="message" rows="3" placeholder="Write message..." required></textarea>
                    <button type="submit" name="send_notice" class="btn-submit">Send Notification</button>
                </form>
            </div>

            <div class="action-box">
                <h3>Recent Broadcasts</h3>
                <?php 
                $notices = mysqli_query($conn, "SELECT * FROM notifications ORDER BY id DESC LIMIT 3");
                while($n = mysqli_fetch_assoc($notices)){
                    echo "<div class='notice-item'>
                            <p>{$n['title']}</p>
                            <small><i class='fa-solid fa-clock'></i> ".date('d M, Y', strtotime($n['created_at']))."</small>
                          </div>";
                }
                ?>
            </div>
        </div>

        <div class="table-container">
            <h3>Student Management</h3>
            <div class="table-responsive-box">
                <table>
                    <thead>
                        <tr><th>Roll No</th><th>Name</th><th>Status</th><th>Action</th><th>Edit</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = mysqli_query($conn, "SELECT s.id, s.roll_no, s.status, u.name FROM students s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC LIMIT 10");
                        while($row = mysqli_fetch_assoc($recent)) {
                            $badge = strtolower($row['status']);
                            echo "<tr>
                                    <td><b>#{$row['roll_no']}</b></td>
                                    <td>{$row['name']}</td>
                                    <td><span class='badge $badge'>{$row['status']}</span></td>
                                    <td>".($row['status'] != 'Active' ? "<a href='?action=verify&id={$row['id']}&page=dashboard' class='verify-btn'>Verify</a>" : "<a href='?action=suspend&id={$row['id']}&page=dashboard' class='suspend-btn'>Suspend</a>")."</td>
                                    <td><a href='manage_students.php?id={$row['id']}' class='edit-link'><i class='fa-solid fa-pen-to-square'></i></a></td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Mobile PhonePe Bottom Navbar -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php?page=dashboard" class="phonepe-nav-item <?= ($current_page == 'dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </a>
    <a href="manage_students.php" class="phonepe-nav-item">
        <i class="fa-solid fa-user-graduate"></i>
        <span>Students</span>
    </a>
    <a href="admin_dashboard.php?page=lms_master" class="phonepe-nav-item <?= ($current_page == 'lms_master') ? 'active' : '' ?>">
        <i class="fa-solid fa-circle-play"></i>
        <span>LMS</span>
    </a>
    <a href="attendance_scanner.php" class="phonepe-nav-item">
        <i class="fa-solid fa-qrcode"></i>
        <span>QR Scan</span>
    </a>
    <a href="javascript:void(0)" class="phonepe-nav-item" onclick="toggleMobileSidebar()">
        <i class="fa-solid fa-bars"></i>
        <span>Menu</span>
    </a>
</div>

<script>
    // Toggle Accordion Dropdown
    var dropdown = document.getElementsByClassName("dropdown-btn");
    for (var i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function(e) {
            e.preventDefault();
            this.classList.toggle("active-nav");
            var arrow = this.querySelector(".arrow");
            if(arrow) arrow.classList.toggle("rotate");
            var dropdownContent = this.nextElementSibling;
            if(dropdownContent) {
                if(dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            }
        });
    }

    // Toggle Mobile Drawer
    function toggleMobileSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('overlay');
        
        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = ''; 
        } else {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; 
        }
    }
</script>
</body>
</html>