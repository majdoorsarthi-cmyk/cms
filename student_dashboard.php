<?php 
/**
 * CMS PRO - STUDENT DASHBOARD (WITH ATTENDANCE + BATCH TIMING & CLASS ALARM SYSTEM)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$user_id = $stu['user_id'] ?? ($stu['id'] ?? 0); 

// --- FETCH DETAILED PROFILE DATA ---
$profile_q = mysqli_query($conn, "SELECT * FROM students WHERE user_id = '$user_id'");
$profile_data = mysqli_fetch_assoc($profile_q);
$real_stu_id = $profile_data['id'] ?? 0;
$student_course = $profile_data['course'] ?? 'Not Enrolled';
$roll_no = $profile_data['roll_no'] ?? 'N/A';
$lms_status = $profile_data['status'] ?? 'Pending'; 
$expiry_date = $profile_data['lms_access_expiry'] ?? null;

// --- BATCH TIMING LOGIC ---
$batch_start = $profile_data['batch_start_time'] ?? '';
$batch_end = $profile_data['batch_end_time'] ?? '';

$formatted_batch = "Not Assigned";
if(!empty($batch_start) && !empty($batch_end)) {
    $formatted_batch = date('h:i A', strtotime($batch_start)) . " - " . date('h:i A', strtotime($batch_end));
}

// --- NAVIGATION LOGIC ---
$current_page = $_GET['page'] ?? 'dashboard';

// ==========================================
// 🚀 PURE AJAX DOCUMENT REQUEST (ZERO RELOAD)
// ==========================================
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'apply_doc') {
    header('Content-Type: application/json');
    $doc_type = mysqli_real_escape_string($conn, $_POST['doc_type']);
    $req_date = date('Y-m-d H:i:s');
    
    $check_req = mysqli_query($conn, "SELECT id FROM service_requests WHERE student_id = '$real_stu_id' AND doc_type = '$doc_type' AND status != 'Rejected'");
    
    if(mysqli_num_rows($check_req) > 0) {
        echo json_encode([
            'status' => 'error', 
            'message' => "⚠️ Application for $doc_type is already in process!"
        ]);
    } else {
        $q = "INSERT INTO service_requests (student_id, doc_type, status, request_date) VALUES ('$real_stu_id', '$doc_type', 'Pending', '$req_date')";
        if(mysqli_query($conn, $q)) {
            echo json_encode([
                'status' => 'success', 
                'message' => "🎉 Application for $doc_type submitted successfully!"
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => "❌ Database error. Please try again."
            ]);
        }
    }
    exit();
}

// --- DATA FETCHING ---
$doc_history_query = mysqli_query($conn, "SELECT * FROM service_requests WHERE student_id = '$real_stu_id' ORDER BY id DESC");
$video_res = mysqli_query($conn, "SELECT * FROM videos ORDER BY id DESC");
$hw_res = mysqli_query($conn, "SELECT * FROM homework ORDER BY id DESC LIMIT 5"); 

// Attendance Logic
$att_count_q = mysqli_query($conn, "SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present 
    FROM attendance WHERE student_id = '$real_stu_id'");
$att_stats = mysqli_fetch_assoc($att_count_q);
$att_percent = ($att_stats['total'] > 0) ? round(($att_stats['present'] / $att_stats['total']) * 100) : 0;
$att_res = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = '$real_stu_id' ORDER BY date DESC LIMIT 5");

// Fees Logic
$course_fee = $profile_data['course_fee'] ?? 0; 
$fee_q = mysqli_query($conn, "SELECT SUM(amount_paid) as paid FROM fees WHERE student_id = '$real_stu_id'");
$total_paid = mysqli_fetch_assoc($fee_q)['paid'] ?? 0;
$due_amount = $course_fee - $total_paid;

// Profile Photo Logic
$stu_photo = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
if(!empty($profile_data['photo']) && file_exists('uploads/profile/' . $profile_data['photo'])) {
    $stu_photo = 'uploads/profile/' . $profile_data['photo'];
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Student Portal | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --pp-purple: #5f259f;
            --pp-dark: #3d1668;
            --sidebar-bg: #0b132b;
            --nav-text: #94a3b8;
            --bg: #f3f4f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #d97706;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; }

        /* 🖼️ SUPER SMOOTH BANNER SLIDER */
        .hero-slider-container {
            position: relative;
            width: 100%;
            height: 210px;
            border-radius: 22px;
            overflow: hidden;
            margin-bottom: 22px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            background: #0f172a;
        }
        @media (min-width: 768px) {
            .hero-slider-container { height: 320px; }
        }
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(1.04);
            pointer-events: none;
        }
        .hero-slide.active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }
        .hero-slide img, .hero-slide video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .slider-dots-wrapper {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
            background: rgba(15, 23, 42, 0.45);
            padding: 6px 14px;
            border-radius: 30px;
            backdrop-filter: blur(8px);
        }
        .slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .slider-dot.active {
            width: 26px;
            background: #ffffff;
        }

        /* PHONEPE FLOATING TOAST NOTIFICATION */
        .pp-toast {
            position: fixed;
            top: -90px;
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: white;
            padding: 16px 26px;
            border-radius: 40px;
            font-weight: 800;
            font-size: 16px;
            z-index: 999999;
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            gap: 12px;
            width: 90%;
            max-width: 420px;
            justify-content: center;
        }
        .pp-toast.show { top: 25px; }

        /* 🔔 LIVE BATCH ALARM BANNER */
        .alarm-card {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 25px rgba(49, 46, 129, 0.25);
        }
        .alarm-status-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 800;
            backdrop-filter: blur(4px);
        }

        /* 🖥️ DESKTOP SIDEBAR & LAYOUT (>= 992px) */
        @media (min-width: 992px) {
            .mobile-only { display: none !important; }
            .sidebar {
                width: 280px; background: var(--sidebar-bg); height: 100vh; position: fixed;
                top: 0; left: 0; padding: 20px 16px; color: white; overflow-y: auto; z-index: 1050;
            }
            .sidebar-brand { font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
            .sidebar-brand i { color: var(--primary); }
            .menu-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 800; margin: 16px 0 8px 8px; letter-spacing: 0.8px; }
            .sidebar-menu a { color: var(--nav-text); text-decoration: none; padding: 12px 14px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 4px; font-weight: 600; }
            .sidebar-menu a i { width: 22px; font-size: 16px; text-align: center; }
            .sidebar-menu a.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }

            .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 30px; }
            .content-body { max-width: 1200px; margin: 0 auto; }

            .phonepe-card { background: #ffffff; border-radius: 20px; padding: 22px; border: 1px solid #e2e8f0; margin-bottom: 22px; }
            .phonepe-card-title { font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; }
            .phonepe-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
            .phonepe-btn { display: flex; flex-direction: column; align-items: center; text-decoration: none; gap: 8px; cursor: pointer; }
            .phonepe-icon-wrapper { width: 64px; height: 64px; background: #eff6ff; border-radius: 20px; display: flex; align-items: center; justify-content: center; border: 1px solid #dbeafe; }
            .phonepe-icon-wrapper i { font-size: 26px; color: var(--primary); }
            .phonepe-btn span { font-size: 13px; font-weight: 700; color: #1e293b; }

            .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 22px; }
            .stat-card { background: var(--card-bg); padding: 20px; border-radius: 18px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: space-between; }
            .stat-card h4 { color: var(--text-sub); font-size: 12px; text-transform: uppercase; font-weight: 800; margin-bottom: 6px; }
            .stat-card h2 { font-size: 20px; font-weight: 800; color: var(--text-main); }
            .app-card { background: var(--card-bg); border-radius: 18px; padding: 22px; border: 1px solid #e2e8f0; margin-bottom: 22px; }
            table { width: 100%; border-collapse: collapse; }
            th { text-align: left; font-size: 12px; color: var(--text-sub); padding: 12px 10px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; font-weight: 800; }
            td { padding: 14px 10px; font-size: 14px; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        }

        /* 📱 MOBILE PHONEPE NATIVE INTERFACE (< 992px) */
        @media (max-width: 991px) {
            .desktop-only { display: none !important; }

            .pp-header {
                position: fixed; top: 0; left: 0; right: 0; height: 72px;
                background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%);
                z-index: 9999; padding: 0 18px; display: flex; align-items: center;
                justify-content: space-between; box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }
            .pp-profile { display: flex; align-items: center; gap: 14px; }
            .pp-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.9); }
            .pp-title { color: white; margin: 0; font-size: 20px !important; font-weight: 900; }
            .pp-subtitle { color: rgba(255,255,255,0.85); font-size: 13px !important; font-weight: 700; }

            .main-content { padding: 88px 12px 85px 12px !important; width: 100% !important; margin-left: 0 !important; }

            .phonepe-card {
                background: #ffffff; border-radius: 24px; padding: 20px 14px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.06); border: none; margin-bottom: 18px;
            }
            .phonepe-card-title {
                font-size: 18px !important; font-weight: 900 !important;
                color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; padding-left: 4px;
            }
            .phonepe-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px 8px; }
            .phonepe-btn { display: flex; flex-direction: column; align-items: center; text-decoration: none; gap: 8px; cursor: pointer; }
            .phonepe-btn:active { transform: scale(0.92); }
            .phonepe-icon-wrapper {
                width: 62px; height: 62px; background: #f1f5f9; border-radius: 22px;
                display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;
            }
            .phonepe-icon-wrapper i { font-size: 28px !important; color: var(--pp-purple); }
            .phonepe-btn span { font-size: 14px !important; font-weight: 800 !important; text-align: center; color: #1e293b; }

            .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 18px; }
            .stat-card {
                background: #ffffff; padding: 18px 16px; border-radius: 20px;
                border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            }
            .stat-card h4 { font-size: 13px !important; font-weight: 800 !important; color: var(--text-sub); text-transform: uppercase; margin-bottom: 6px; }
            .stat-card h2 { font-size: 18px !important; font-weight: 900 !important; color: #0f172a; }

            .app-card { background: #ffffff; border-radius: 22px; padding: 20px; box-shadow: 0 6px 20px rgba(0,0,0,0.04); margin-bottom: 18px; border: none; }
            .responsive-table table, .responsive-table thead, .responsive-table tbody, .responsive-table th, .responsive-table td, .responsive-table tr { display: block; }
            .responsive-table thead { display: none; }
            .responsive-table tr { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px; margin-bottom: 12px; }
            .responsive-table td { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #cbd5e1; font-size: 16px !important; font-weight: 700; }
            .responsive-table td:last-child { border-bottom: none; }
            .responsive-table td::before { content: attr(data-label); font-size: 13px !important; font-weight: 800; color: var(--text-sub); text-transform: uppercase; }

            .sidebar-overlay {
                display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7);
                backdrop-filter: blur(5px); z-index: 99998; transition: 0.3s;
            }
            .sidebar-overlay.active { display: block !important; }
            .sidebar-drawer {
                position: fixed; top: 0; bottom: 0; left: -100%; width: 82vw; max-width: 320px;
                background: #0f172a; z-index: 99999; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                padding: 24px 18px; box-shadow: 10px 0 30px rgba(0,0,0,0.5); overflow-y: auto;
            }
            .sidebar-drawer.active { left: 0; }
            .drawer-link {
                display: flex; align-items: center; gap: 14px; color: #cbd5e1;
                padding: 16px; border-radius: 16px; font-size: 17px !important; font-weight: 800;
                text-decoration: none; margin-bottom: 8px;
            }
            .drawer-link.active { background: rgba(95, 37, 159, 0.45); color: #c084fc; }

            .mobile-bottom-nav {
                position: fixed; bottom: 0; left: 0; right: 0; height: 72px;
                background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                display: flex; justify-content: space-around; align-items: center;
                box-shadow: 0 -4px 25px rgba(0,0,0,0.08); padding-bottom: env(safe-area-inset-bottom);
            }
            .mobile-bottom-nav a {
                display: flex; flex-direction: column; align-items: center; text-decoration: none;
                color: #64748b; font-size: 12px !important; font-weight: 800; width: 20%;
            }
            .mobile-bottom-nav a i { font-size: 24px !important; margin-bottom: 4px; }
            .mobile-bottom-nav a.active { color: var(--pp-purple); }
        }

        .badge { padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 800; display: inline-block; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .btn-app { background: var(--primary); color: white; padding: 12px 18px; border-radius: 14px; text-decoration: none; font-size: 14px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; width: 100%; text-align: center; }
    </style>
</head>
<body>

<!-- PhonePe Floating Notification Toast -->
<div id="ppToast" class="pp-toast">
    <span id="ppToastMsg">Notification</span>
</div>

<!-- 📱 MOBILE PHONEPE HEADER -->
<div class="pp-header mobile-only">
    <div class="pp-profile">
        <img src="<?= $stu_photo ?>" class="pp-avatar" alt="Avatar">
        <div>
            <h3 class="pp-title"><?= htmlspecialchars($_SESSION['student']['name'] ?? 'Student Dashboard') ?></h3>
            <span class="pp-subtitle">Roll No: #<?= $roll_no ?></span>
        </div>
    </div>
    <button type="button" class="btn text-white p-0 fs-3" style="background:none; border:none;" onclick="toggleSidebar()"><i class="fa fa-bars text-white" style="font-size: 22px;"></i></button>
</div>

<!-- Drawer Overlay & Side Navigation (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar-drawer mobile-only" id="mobileDrawer">
    <div class="d-flex justify-content-between align-items-center mb-4 text-white">
        <h3 class="fw-bold m-0 text-white" style="font-size:20px;"><i class="fa fa-graduation-cap text-purple me-2"></i>CMS PORTAL</h3>
        <button class="btn text-white p-0 fs-4" style="background:none; border:none;" onclick="toggleSidebar()"><i class="fa fa-times text-white"></i></button>
    </div>
    <a href="?page=dashboard" class="drawer-link <?= ($current_page == 'dashboard') ? 'active' : '' ?>"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="lms_access.php" class="drawer-link <?= ($current_page == 'lms_portal') ? 'active' : '' ?>"><i class="fa fa-graduation-cap"></i> LMS Portal</a>
    <a href="?page=videos" class="drawer-link <?= ($current_page == 'videos') ? 'active' : '' ?>"><i class="fa fa-play-circle"></i> Video Lectures</a>
    <a href="online_exam.php" class="drawer-link"><i class="fa fa-file-signature"></i> Online Exams</a>
    <a href="lms_access.php" class="drawer-link"><i class="fa fa-book-reader"></i> Homework</a>
    <a href="attendance.php" class="drawer-link"><i class="fa fa-user-check"></i> Attendance</a>
    <a href="my_docs.php" class="drawer-link <?= ($current_page == 'my_docs') ? 'active' : '' ?>"><i class="fa fa-id-card"></i> Document Wallet</a>
    <a href="my_results.php" class="drawer-link"><i class="fa fa-poll-h"></i> Exam Results</a>
    <a href="my_fees.php" class="drawer-link"><i class="fa fa-receipt"></i> Fees & Receipts</a>
    <a href="profile_settings.php" class="drawer-link"><i class="fa fa-user-cog"></i> Profile Settings</a>
    <a href="logout.php" class="drawer-link" style="color:#f87171;"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<div class="sidebar desktop-only">
    <div class="sidebar-brand">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fas fa-university"></i> <span>CMS ACADEMY</span>
        </div>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        <a href="?page=dashboard" class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
        <div class="menu-label">Academics</div>
        <a href="lms_access.php" class="<?= ($current_page == 'lms_portal') ? 'active' : '' ?>"><i class="fas fa-graduation-cap"></i> <span>LMS Access</span></a>
        <a href="?page=videos" class="<?= ($current_page == 'videos') ? 'active' : '' ?>"><i class="fas fa-play-circle"></i> <span>Video Lectures</span></a>
        <a href="online_exam.php"><i class="fas fa-file-signature"></i> <span>Online Exams</span></a>
        <a href="homework.php"><i class="fas fa-book-reader"></i> <span>Homework Tasks</span></a>
        <a href="attendance.php"><i class="fas fa-user-check"></i> <span>My Attendance</span></a>
        <div class="menu-label">My Records</div>
        <a href="my_docs.php" class="<?= ($current_page == 'my_docs') ? 'active' : '' ?>"><i class="fas fa-id-card"></i> <span>Document Wallet</span></a>
        <a href="my_results.php"><i class="fas fa-poll-h"></i> <span>Exam Results</span></a>
        <a href="my_fees.php"><i class="fas fa-receipt"></i> <span>Fees & Receipts</span></a>
        <div class="menu-label">Account</div>
        <a href="profile_settings.php"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>
</div>

<!-- MAIN VIEW CONTAINER -->
<div class="main-content">
    <div class="content-body">

        <?php if($current_page == 'my_docs'): ?>
            <!-- DOCUMENT WALLET VIEW -->
            <div class="phonepe-card">
                <div class="phonepe-card-title">
                    <span><i class="fas fa-folder-plus text-primary me-2"></i> Apply Documents</span>
                </div>
                <div class="phonepe-grid">
                    <div onclick="applyDocAjax('Marksheet')" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-file-invoice"></i></div>
                        <span>Marksheet</span>
                    </div>
                    <div onclick="applyDocAjax('ID Card')" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-id-card"></i></div>
                        <span>ID Card</span>
                    </div>
                    <div onclick="applyDocAjax('Certificate')" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-award"></i></div>
                        <span>Certificate</span>
                    </div>
                    <div onclick="applyDocAjax('Admit Card')" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper" style="background:#fff7ed; border-color:#ffedd5;"><i class="fas fa-address-card" style="color:var(--warning);"></i></div>
                        <span>Admit Card</span>
                    </div>
                </div>
            </div>

            <div class="app-card">
                <h4 style="margin-bottom:14px; font-size:16px; font-weight:800;"><i class="fas fa-history me-2"></i> Request History</h4>
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="docHistoryBody">
                            <?php 
                            mysqli_data_seek($doc_history_query, 0); 
                            while($doc = mysqli_fetch_assoc($doc_history_query)): 
                                $is_ready = ($doc['status'] == 'Verified' || $doc['status'] == 'Approved');
                                
                                if($doc['doc_type'] == 'Certificate') {
                                    $download_url = "print_certificate.php?id=" . $doc['student_id'];
                                } elseif($doc['doc_type'] == 'ID Card') {
                                    $download_url = "print_id_card.php?id=" . $doc['student_id'];
                                } elseif($doc['doc_type'] == 'Report Card') {
                                    $download_url = "print_report_card.php?id=" . $doc['student_id'];
                                } else {
                                    $download_url = "print_marksheet.php?id=" . $doc['student_id'];
                                }
                            ?>
                            <tr>
                                <td data-label="Type"><b><?= $doc['doc_type'] ?></b></td>
                                <td data-label="Date"><?= date('d M, Y', strtotime($doc['request_date'])) ?></td>
                                <td data-label="Status"><span class="badge <?= $is_ready ? 'badge-success' : 'badge-warning' ?>"><?= $doc['status'] ?></span></td>
                                <td data-label="Action">
                                    <?php if($is_ready): ?>
                                        <a href="<?= $download_url ?>" target="_blank" class="btn-app" style="padding: 8px 14px; width: auto;"><i class="fas fa-download"></i> Get File</a>
                                    <?php else: ?>
                                        <span style="color:var(--text-sub); font-size:14px; font-weight:700;">In Verification</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif($current_page == 'videos'): ?>
            <!-- VIDEO LECTURES VIEW -->
            <div class="app-card">
                <h4 style="margin-bottom:14px; font-size:16px; font-weight:800;"><i class="fas fa-play-circle me-2"></i> Video Library</h4>
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:14px;">
                    <?php while($v = mysqli_fetch_assoc($video_res)): 
                        $v_url = str_replace("watch?v=", "embed/", $v['video_url']); ?>
                        <div style="border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; background: #fff;">
                            <iframe width="100%" height="160" src="<?= $v_url ?>" frameborder="0" allowfullscreen></iframe>
                            <div style="padding:12px;">
                                <h5 style="font-size:15px; font-weight:800; margin-bottom:4px;"><?= $v['title'] ?></h5>
                                <p style="font-size:13px; color:var(--primary); font-weight:800;"><?= $student_course ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        <?php elseif($current_page == 'lms_portal'): ?>
            <!-- LMS PORTAL VIEW -->
            <?php if($lms_status == 'Active'): ?>
                <div class="app-card" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
                    <span class="badge badge-success" style="margin-bottom:10px;">LMS Active</span>
                    <h2 style="font-weight:900; font-size:22px; margin-bottom:6px;"><?= $student_course ?></h2>
                    <p style="opacity:0.85; font-size:14px; font-weight:700;"><i class="fas fa-calendar-alt me-1"></i> Expiry: <b><?= ($expiry_date) ? date('d M Y', strtotime($expiry_date)) : 'Lifetime Access' ?></b></p>
                </div>

                <div class="app-card">
                    <h4 style="margin-bottom:14px; font-size:16px; font-weight:800;"><i class="fas fa-list-ol me-2"></i> Course Modules</h4>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:14px; background:#f8fafc; border-radius:14px; border-left:5px solid var(--success); font-size:15px; font-weight:800;">
                            <span>1. Course Overview & Fundamentals</span>
                            <i class="fas fa-check-circle text-success fs-5"></i>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:14px; background:#f8fafc; border-radius:14px; border-left:5px solid var(--warning); font-size:15px; font-weight:800;">
                            <span>2. Practical Lab Work & Tasks</span>
                            <span class="badge badge-warning">In Progress</span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="app-card" style="text-align:center; padding:40px 18px;">
                    <i class="fas fa-user-clock" style="font-size:48px; color:var(--warning); margin-bottom:14px;"></i>
                    <h3 style="font-weight:900; font-size:18px; margin-bottom:8px;">LMS Verification Pending</h3>
                    <p style="color:var(--text-sub); font-size:14px; margin-bottom:14px; font-weight:600;">Your course access request is currently under review by administrator.</p>
                    <span class="badge badge-warning">Status: <?= $lms_status ?></span>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- DASHBOARD HOME VIEW -->

            <!-- 🖼️ 🎥 SUPER SMOOTH CASAOS ASSETS BANNER SLIDER -->
            <div class="hero-slider-container">
                <div class="hero-slide active">
                    <img src="uploads/assets/center 1.jpeg" alt="Center Photo 1">
                </div>
                <div class="hero-slide">
                    <img src="uploads/assets/center 2.jpeg" alt="Center Photo 2">
                </div>
                <div class="hero-slide">
                    <img src="uploads/assets/center 3.jpeg" alt="Center Photo 3">
                </div>
                <div class="hero-slide">
                    <video id="heroVideo" muted playsinline preload="auto">
                        <source src="uploads/assets/video.mp4" type="video/mp4">
                    </video>
                </div>
                <!-- Dynamic Glassmorphism Dots -->
                <div class="slider-dots-wrapper">
                    <span class="slider-dot active" onclick="goToHeroSlide(0)"></span>
                    <span class="slider-dot" onclick="goToHeroSlide(1)"></span>
                    <span class="slider-dot" onclick="goToHeroSlide(2)"></span>
                    <span class="slider-dot" onclick="goToHeroSlide(3)"></span>
                </div>
            </div>

            <!-- 🔔 LIVE BATCH ALARM BANNER CARD -->
            <div class="alarm-card">
                <div>
                    <div style="font-size: 12px; font-weight: 800; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px;">Scheduled Batch Timing</div>
                    <div style="font-size: 20px; font-weight: 900; margin-top: 2px;" id="alarmTimeText">
                        <i class="fas fa-clock me-1 text-warning"></i> <?= $formatted_batch ?>
                    </div>
                </div>
                <div id="alarmBadgeContainer">
                    <span class="alarm-status-badge" id="batchLiveStatus">
                        <i class="fas fa-bell me-1"></i> Checking...
                    </span>
                </div>
            </div>

            <!-- PHONEPE STYLE STUDENT SERVICES CARD -->
            <div class="phonepe-card">
                <div class="phonepe-card-title">
                    <span>Student Quick Services</span>
                </div>
                <div class="phonepe-grid">
                    <a href="?page=lms_portal" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-graduation-cap"></i></div>
                        <span>LMS</span>
                    </a>
                    <a href="attendance.php" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-calendar-check"></i></div>
                        <span>Attendance</span>
                    </a>
                    <a href="lms_access.php" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-book-reader"></i></div>
                        <span>Homework</span>
                    </a>
                    <a href="online_exam.php" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper"><i class="fas fa-file-signature"></i></div>
                        <span>Exams</span>
                    </a>
                    <a href="?page=my_docs" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper" style="background:#f0fdf4; border-color:#bbf7d0;"><i class="fas fa-id-card" style="color:var(--success);"></i></div>
                        <span>Docs</span>
                    </a>
                    <a href="my_results.php" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper" style="background:#fef3c7; border-color:#fde68a;"><i class="fas fa-poll-h" style="color:var(--warning);"></i></div>
                        <span>Results</span>
                    </a>
                    <a href="my_fees.php" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper" style="background:#f1f5f9; border-color:#e2e8f0;"><i class="fas fa-receipt" style="color:#475569;"></i></div>
                        <span>Fees</span>
                    </a>
                    <a href="profile_settings.php" class="phonepe-btn">
                        <div class="phonepe-icon-wrapper" style="background:#faf5ff; border-color:#f3e8ff;"><i class="fas fa-user-cog" style="color:#9333ea;"></i></div>
                        <span>Profile</span>
                    </a>
                </div>
            </div>

            <!-- STATS OVERVIEW CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Batch Timing</h4>
                    <h2 style="color:var(--primary); font-size:16px;"><?= $formatted_batch ?></h2>
                </div>
                <a href="attendance.php" class="stat-card" style="text-decoration:none;">
                    <h4>Attendance</h4>
                    <h2 style="color:var(--primary);"><?= $att_percent ?>% <i class="fas fa-chevron-right" style="font-size:12px; color:var(--text-sub);"></i></h2>
                </a>
                <div class="stat-card">
                    <h4>LMS Status</h4>
                    <h2 style="color: <?= ($lms_status=='Active')?'var(--success)':'var(--danger)' ?>;"><?= ($lms_status=='Active')?'Enabled':'Locked' ?></h2>
                </div>
                <div class="stat-card">
                    <h4>Fees Paid</h4>
                    <h2 style="color:var(--success);">₹<?= number_format($total_paid) ?></h2>
                </div>
                <div class="stat-card">
                    <h4>My Course</h4>
                    <h2 style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= $student_course ?></h2>
                </div>
            </div>

            <!-- RECENT ATTENDANCE TABLE CARD -->
            <div class="app-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h4 style="font-size:16px; font-weight:800;"><i class="fas fa-calendar-check me-2"></i> Recent Attendance</h4>
                    <a href="attendance.php" style="font-size:14px; color:var(--primary); text-decoration:none; font-weight:800;">View All</a>
                </div>
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($r = mysqli_fetch_assoc($att_res)): ?>
                            <tr>
                                <td data-label="Date"><?= date('d M, Y', strtotime($r['date'])) ?></td>
                                <td data-label="Status" style="font-weight:900; color:<?= ($r['status']=='Present')? 'var(--success)':'var(--danger)' ?>"><?= $r['status'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- PHONEPE STYLE BOTTOM NAVIGATION BAR (MOBILE ONLY) -->
<div class="mobile-bottom-nav mobile-only">
    <a href="?page=dashboard" class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>">
        <i class="fas fa-home"></i><span>Home</span>
    </a>
    <a href="attendance.php">
        <i class="fas fa-user-check"></i><span>Attendance</span>
    </a>
    <a href="lms_access.php" class="<?= ($current_page == 'lms_portal') ? 'active' : '' ?>">
        <i class="fas fa-graduation-cap"></i><span>LMS</span>
    </a>
    <a href="?page=my_docs" class="<?= ($current_page == 'my_docs') ? 'active' : '' ?>">
        <i class="fas fa-id-card"></i><span>Docs</span>
    </a>
    <a href="javascript:void(0)" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i><span>Menu</span>
    </a>
</div>

<script>
// Show Notification Toast
function showToast(msg) {
    const toast = document.getElementById('ppToast');
    const msgElem = document.getElementById('ppToastMsg');
    if(msgElem) msgElem.innerText = msg;
    if(toast) {
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }
}

// Side Drawer Toggle
function toggleSidebar() {
    const drawer = document.getElementById('mobileDrawer');
    const overlay = document.getElementById('sidebarOverlay');
    if(drawer) drawer.classList.toggle('active');
    if(overlay) overlay.classList.toggle('active');
}

// 🚀 ZERO-RELOAD AJAX APPLY DOCUMENT
function applyDocAjax(docType) {
    const formData = new FormData();
    formData.append('ajax_action', 'apply_doc');
    formData.append('doc_type', docType);

    fetch('student_dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        showToast(data.message);
        if(data.status === 'success') {
            setTimeout(() => { window.location.reload(); }, 1200);
        }
    })
    .catch(() => {
        showToast("❌ Request failed! Network error.");
    });
}

// ==========================================
// 🎞️ SUPER SMOOTH BANNER SLIDER LOGIC
// ==========================================
let currentHeroSlide = 0;
const heroSlides = document.querySelectorAll('.hero-slide');
const heroDots = document.querySelectorAll('.slider-dot');
let heroSlideTimer = null;

function showHeroSlide(index) {
    if(!heroSlides || heroSlides.length === 0) return;
    
    heroSlides.forEach((slide) => {
        slide.classList.remove('active');
        const v = slide.querySelector('video');
        if(v) {
            v.pause();
            v.currentTime = 0;
        }
    });

    heroDots.forEach(dot => dot.classList.remove('active'));

    currentHeroSlide = (index + heroSlides.length) % heroSlides.length;
    heroSlides[currentHeroSlide].classList.add('active');
    if(heroDots[currentHeroSlide]) heroDots[currentHeroSlide].classList.add('active');

    const activeVideo = heroSlides[currentHeroSlide].querySelector('video');
    if(activeVideo) {
        activeVideo.play().catch(e => console.log("Autoplay check:", e));
    }
}

function nextHeroSlide() {
    showHeroSlide(currentHeroSlide + 1);
}

function startHeroSlider() {
    if(heroSlideTimer) clearInterval(heroSlideTimer);
    heroSlideTimer = setInterval(nextHeroSlide, 4000); // 4 Seconds Switch
}

function goToHeroSlide(index) {
    showHeroSlide(index);
    startHeroSlider();
}

// ==========================================
// ⏰ AUTOMATIC BATCH CLASS ALARM & STATUS SYSTEM
// ==========================================
const dbBatchStart = "<?= $batch_start ?>"; 
const dbBatchEnd   = "<?= $batch_end ?>";   
let hasAlarmRung = false;

const dcAlarmAudio = new Audio('Attendance_Lagao.mp3');
dcAlarmAudio.loop = true;

document.addEventListener('click', () => {
    dcAlarmAudio.load();
}, { once: true });

function playDCAlarm() {
    dcAlarmAudio.play().then(() => {
        setTimeout(() => {
            if (confirm("🔔 DC Academy: सेंटर आ गए हो, अपनी अटेंडेंस (Attendance) लगाओ!")) {
                stopDCAlarm();
            } else {
                stopDCAlarm();
            }
        }, 300);
    }).catch(e => {
        console.log("Autoplay restriction triggered:", e);
        showToast("🔔 अलार्म: सेंटर आ गए हो, अपनी अटेंडेंस (Attendance) लगाओ!");
    });
}

function stopDCAlarm() {
    dcAlarmAudio.pause();
    dcAlarmAudio.currentTime = 0;
}

function checkBatchAlarm() {
    if(!dbBatchStart || !dbBatchEnd) {
        const statusElem = document.getElementById('batchLiveStatus');
        if(statusElem) statusElem.innerHTML = `<i class="fas fa-minus-circle me-1"></i> No Timing Set`;
        return;
    }

    const now = new Date();
    function timeToMinutes(tStr) {
        if(!tStr) return 0;
        const parts = tStr.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    const currentMin = now.getHours() * 60 + now.getMinutes();
    const startMin = timeToMinutes(dbBatchStart);
    const endMin   = timeToMinutes(dbBatchEnd);

    const statusElem = document.getElementById('batchLiveStatus');

    if(statusElem) {
        if (currentMin >= startMin && currentMin < endMin) {
            statusElem.className = "alarm-status-badge text-white";
            statusElem.style.background = "#16a34a"; 
            statusElem.innerHTML = `<i class="fas fa-broadcast-tower me-1"></i> BATCH IS LIVE NOW`;
            
            if(!hasAlarmRung) {
                hasAlarmRung = true;
                playDCAlarm();
                showToast("🔔 अलार्म: सेंटर आ गए हो, अपनी अटेंडेंस लगाओ!");
            }
        } 
        else if (startMin - currentMin <= 5 && startMin - currentMin > 0) {
            const diff = startMin - currentMin;
            statusElem.className = "alarm-status-badge text-white";
            statusElem.style.background = "#d97706"; 
            statusElem.innerHTML = `<i class="fas fa-bell me-1"></i> Starting in ${diff} Min!`;

            if(!hasAlarmRung) {
                hasAlarmRung = true;
                playDCAlarm();
                showToast(`🔔 अलार्म: क्लास ${diff} मिनट में शुरू हो रही है, अपनी अटेंडेंस लगाओ!`);
            }
        } 
        else if (currentMin >= endMin) {
            statusElem.className = "alarm-status-badge";
            statusElem.style.background = "rgba(255,255,255,0.15)";
            statusElem.innerHTML = `<i class="fas fa-check-circle me-1"></i> Today's Class Over`;
        } 
        else {
            statusElem.className = "alarm-status-badge";
            statusElem.style.background = "rgba(255,255,255,0.2)";
            statusElem.innerHTML = `<i class="fas fa-calendar-clock me-1"></i> Scheduled Today`;
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    checkBatchAlarm();
    setInterval(checkBatchAlarm, 5000);
    startHeroSlider(); // 🚀 Start Smooth Banner Slider
});
</script>

</body>
</html>