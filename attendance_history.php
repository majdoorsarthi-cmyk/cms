<?php 
/**
 * CMS PRO - ULTRA HIGH PERFORMANCE ATTENDANCE HISTORY (FULL DYNAMIC & AJAX EDITION)
 * PhonePe Style Mobile Interface & Zero-Reload Architecture Update
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// Auth Check
if (!isset($_SESSION['student'])) { 
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$user_id = $stu['user_id'] ?? ($stu['id'] ?? null); 

// Dynamic Safe Column Lookup Engine
function getSafeColumn($conn, $table, $possible_names) {
    foreach($possible_names as $name) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$name'");
        if($check && mysqli_num_rows($check) > 0) return $name;
    }
    return $possible_names[0]; 
}

$att_col = getSafeColumn($conn, 'attendance', ['student_id', 'user_id']);
$att_date_col = getSafeColumn($conn, 'attendance', ['attendance_date', 'date']);
$stu_name_col = getSafeColumn($conn, 'students', ['name', 'student_name', 'full_name', 'username']);

// Auto Database Alter Execution (Schema Safety)
function addColumnIfNotExists($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check && mysqli_num_rows($check) == 0) {
        @mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

addColumnIfNotExists($conn, 'attendance', 'user_id', "INT NULL");
addColumnIfNotExists($conn, 'attendance', 'student_id', "INT NULL");
addColumnIfNotExists($conn, 'attendance', 'out_time', "TIME NULL");
addColumnIfNotExists($conn, 'attendance', 'signature_path', "VARCHAR(500) NULL");

// FAST STUDENT LOOKUP
$profile_stmt = $conn->prepare("SELECT id, `$stu_name_col` as fetched_name FROM students WHERE user_id = ? OR id = ? LIMIT 1");
$profile_stmt->bind_param("ss", $user_id, $user_id);
$profile_stmt->execute();
$profile_res = $profile_stmt->get_result();
$profile_data = $profile_res->fetch_assoc();
$real_stu_id = $profile_data['id'] ?? $user_id;

// Absolute & Relative Path Resolver
function resolveProofData($raw_path, $default_folder) {
    $path = trim($raw_path ?? '');
    if (empty($path)) return '';
    
    if (strpos($path, 'data:image') === 0) return $path;
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;

    $clean_path = ltrim($path, '/');

    if (file_exists($clean_path)) return $clean_path;
    
    $casaos_base = '/DATA/AppData/coaching_cms/';
    if (file_exists($casaos_base . $clean_path)) {
        return $clean_path; 
    }

    $folder_path = $default_folder . basename($clean_path);
    if (file_exists($folder_path)) return $folder_path;

    return $clean_path;
}

// Helper Function: Data Processing Engine
function fetchAttendanceData($conn, $real_stu_id, $att_col, $att_date_col, $selected_month, $selected_year) {
    $festivals = [
        '01-01' => 'नव वर्ष', '01-14' => 'मकर संक्रांति', '01-26' => 'गणतंत्र दिवस',
        '03-08' => 'महाशिवरात्रि', '03-25' => 'होली', '04-14' => 'अम्बेडकर जयंती',
        '08-15' => 'स्वतंत्रता दिवस', '08-26' => 'कृष्ण जन्माष्टमी', '10-02' => 'गांधी जयंती',
        '10-12' => 'दशहरा', '10-31' => 'दीपावली', '12-25' => 'क्रिसमस'
    ];

    $att_records = [];
    $query = "SELECT *, `$att_date_col` as att_date FROM attendance WHERE (`$att_col` = ? OR user_id = ?) AND MONTH(`$att_date_col`) = ? AND YEAR(`$att_date_col`) = ? ORDER BY `$att_date_col` DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $real_stu_id, $real_stu_id, $selected_month, $selected_year);
    $stmt->execute();
    $att_q = $stmt->get_result();

    if ($att_q) {
        while ($row = $att_q->fetch_assoc()) {
            $formatted_date = date('Y-m-d', strtotime($row['att_date']));
            $att_records[$formatted_date] = $row;
        }
    }

    $timestamp = mktime(0, 0, 0, $selected_month, 1, $selected_year);
    $days_in_month = date('t', $timestamp);
    $today = date('Y-m-d');

    $history_list = [];
    $count_present = 0; $count_absent = 0; $count_holiday = 0;

    for ($day = $days_in_month; $day >= 1; $day--) {
        $date_str = sprintf('%04d-%02d-%02d', $selected_year, $selected_month, $day);
        $mm_dd = sprintf('%02d-%02d', $selected_month, $day);
        $day_of_week = date('w', strtotime($date_str));
        
        $status = 'Not Marked'; $remark = '-'; $photo = ''; $signature = ''; $lat = ''; $lng = ''; $in_time = '--:--'; $out_time = '--:--';

        if (isset($festivals[$mm_dd])) {
            $status = 'Holiday'; $remark = $festivals[$mm_dd] . " (अवकाश)";
        } elseif ($day_of_week == 0) {
            $status = 'Holiday'; $remark = "रविवार (छुट्टी)";
        }

        if (isset($att_records[$date_str])) {
            $rec = $att_records[$date_str];
            $status = !empty($rec['status']) ? ucfirst($rec['status']) : 'Present';
            
            $raw_photo = !empty($rec['photo_path']) ? $rec['photo_path'] : ($rec['photo'] ?? '');
            $raw_sig   = !empty($rec['signature_path']) ? $rec['signature_path'] : ($rec['signature'] ?? '');

            $photo     = resolveProofData($raw_photo, 'uploads/attendance/');
            $signature = resolveProofData($raw_sig, 'uploads/signatures/');
            
            $lat  = $rec['latitude'] ?? '';
            $lng  = $rec['longitude'] ?? '';
            $in_time  = !empty($rec['in_time']) ? date('h:i A', strtotime($rec['in_time'])) : '--:--';
            $out_time = !empty($rec['out_time']) ? date('h:i A', strtotime($rec['out_time'])) : '--:--';
            $remark = ($status == 'Present') ? 'उपस्थित दर्ज' : 'अनुपस्थित';
        } elseif ($date_str < $today && $status != 'Holiday') {
            $status = 'Absent'; $remark = 'अटेंडेंस दर्ज नहीं की गई';
        }

        if ($date_str <= $today) {
            if ($status == 'Present') $count_present++;
            elseif ($status == 'Absent') $count_absent++;
            elseif ($status == 'Holiday') $count_holiday++;

            $history_list[] = [
                'date' => $date_str,
                'formatted_date' => date('d-m-Y', strtotime($date_str)),
                'day_name' => date('D', strtotime($date_str)),
                'status' => $status,
                'remark' => $remark,
                'photo' => $photo,
                'signature' => $signature,
                'lat' => $lat,
                'lng' => $lng,
                'in_time' => $in_time,
                'out_time' => $out_time
            ];
        }
    }

    return [
        'stats' => ['present' => $count_present, 'absent' => $count_absent, 'holiday' => $count_holiday],
        'list'  => $history_list
    ];
}

// --- HANDLE AJAX REQUESTS ---
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    $m = isset($_GET['m']) ? sprintf('%02d', max(1, min(12, intval($_GET['m'])))) : date('m');
    $y = isset($_GET['y']) ? intval($_GET['y']) : date('Y');
    
    $data = fetchAttendanceData($conn, $real_stu_id, $att_col, $att_date_col, $m, $y);
    echo json_encode($data);
    exit();
}

// --- INITIAL SSR PAGE LOAD ---
$selected_month = isset($_GET['m']) ? sprintf('%02d', max(1, min(12, intval($_GET['m'])))) : date('m');
$selected_year  = isset($_GET['y']) ? intval($_GET['y']) : date('Y');
$initial_data   = fetchAttendanceData($conn, $real_stu_id, $att_col, $att_date_col, $selected_month, $selected_year);
$history_list   = $initial_data['list'];
$stats          = $initial_data['stats'];
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>TC Academy-हाजिरी इतिहास</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Hind:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3f1d70;
            --primary: #5f259f;
            --sidebar-bg: #0f172a;
            --bg: #f4f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --success: #16a34a;
            --danger: #dc2626;
            --holiday: #4338ca;
            --app-header-height: 64px;
            --bottom-nav-height: 72px;
            --radius: 22px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Hind', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; padding-bottom: calc(var(--bottom-nav-height) + 20px); }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 280px; background: var(--sidebar-bg); height: 100vh;
            position: fixed; top: 0; left: 0; padding: 24px 16px; color: white;
            z-index: 10050; transition: transform 0.3s ease;
            box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .sidebar-brand { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 800; letter-spacing: 1px; margin: 20px 0 10px 10px; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; padding: 12px 16px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 15px; margin-bottom: 6px; font-weight: 600; transition: 0.2s; }
        .sidebar-menu a.active, .sidebar-menu a:hover { background: rgba(95, 37, 159, 0.25); color: #a78bfa; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 10040; backdrop-filter: blur(4px); }

        /* --- PHONEPE MOBILE HEADER --- */
        .app-header { 
            display: none; position: fixed; top: 0; left: 0; right: 0; 
            height: var(--app-header-height); 
            background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%); 
            align-items: center; justify-content: space-between; padding: 0 18px; 
            box-shadow: 0 4px 15px rgba(95, 37, 159, 0.25); z-index: 1000; color: #ffffff;
        }
        .app-header-title { font-weight: 800; font-size: 22px; display: flex; align-items: center; gap: 14px; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 28px; transition: all 0.3s ease; }
        .content-body { max-width: 1100px; margin: 0 auto; }

        /* --- BIG HOME CARD BANNER --- */
        .big-home-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 20px; padding: 18px 22px; color: #fff; display: flex;
            align-items: center; justify-content: space-between; text-decoration: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12); margin-bottom: 24px; transition: transform 0.2s ease;
        }
        .big-home-banner:active { transform: scale(0.98); }
        .big-home-left { display: flex; align-items: center; gap: 14px; }
        .big-home-icon { width: 50px; height: 50px; background: var(--phonepe-purple); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; }

        /* --- TOOLBAR & FILTER CARD --- */
        .filter-card {
            background: var(--card-bg); border-radius: var(--radius); padding: 22px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03); margin-bottom: 24px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
            border: 1px solid #e2e8f0;
        }
        .filter-card h2 { font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 12px; color: var(--text-main); }
        .filter-actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; width: 100%; max-width: 620px; justify-content: flex-end; }
        
        .search-box { position: relative; flex: 1; min-width: 180px; }
        .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-sub); font-size: 18px; }
        .search-box input { width: 100%; padding: 12px 14px 12px 46px; border-radius: 14px; border: 1px solid #cbd5e1; font-size: 15px; font-weight: 700; outline: none; transition: 0.2s; background: #f8fafc; color: var(--text-main); }
        .search-box input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(95, 37, 159, 0.15); }

        .select-custom { padding: 12px 16px; border-radius: 14px; border: 1px solid #cbd5e1; font-size: 15px; font-weight: 700; background: #f8fafc; outline: none; cursor: pointer; color: var(--text-main); }

        .btn-export { background: #16a34a; color: #fff; border: none; padding: 12px 20px; border-radius: 14px; font-size: 15px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2); }
        .btn-export:hover { background: #15803d; transform: translateY(-1px); }

        /* --- STATS DASHBOARD GRID --- */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--card-bg); padding: 22px; border-radius: var(--radius);
            border: 1px solid #e2e8f0; text-align: center; position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .stat-card::before { content:''; position: absolute; top:0; left:0; width: 100%; height: 5px; }
        .stat-card.present::before { background: var(--success); }
        .stat-card.absent::before { background: var(--danger); }
        .stat-card.holiday::before { background: var(--holiday); }

        .stat-card h4 { font-size: 14px; color: var(--text-sub); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card h2 { font-size: 32px; font-weight: 800; margin-top: 6px; }

        /* --- DESKTOP TABLE VIEW --- */
        .history-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .table-responsive { width: 100%; overflow-x: auto; }
        .history-table { width: 100%; border-collapse: collapse; text-align: left; }
        .history-table th { background: #f8fafc; padding: 18px 20px; font-size: 14px; font-weight: 800; color: var(--text-sub); border-bottom: 1px solid #e2e8f0; }
        .history-table td { padding: 18px 20px; font-size: 15px; border-bottom: 1px solid #f1f5f9; font-weight: 600; vertical-align: middle; }

        /* --- PHONEPE STYLE MOBILE CARDS VIEW --- */
        .app-cards-container { display: none; flex-direction: column; gap: 16px; }
        .app-card {
            background: var(--card-bg); border-radius: 20px; padding: 20px; border: 1px solid #e2e8f0;
            display: flex; flex-direction: column; gap: 16px; box-shadow: 0 8px 22px rgba(0,0,0,0.04);
            transition: transform 0.2s ease;
        }
        .app-card-header { display: flex; justify-content: space-between; align-items: center; }
        .app-card-date { font-size: 20px; font-weight: 800; color: var(--text-main); letter-spacing: 0.2px; }
        .app-card-day { font-size: 15px; color: var(--text-sub); font-weight: 700; margin-top: 2px; }
        .app-card-body { display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 14px 18px; border-radius: 16px; gap: 10px; flex-wrap: wrap; }

        /* BADGES - BIGGER MOBILE FONTS */
        .badge { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 30px; font-size: 15px; font-weight: 800; }
        .badge-present { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-absent { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-holiday { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }

        /* PROOF THUMBNAILS & ACTIONS */
        .proof-action-group { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .thumb-img { width: 48px; height: 48px; border-radius: 14px; object-fit: cover; border: 2px solid #e2e8f0; cursor: pointer; transition: 0.2s; background: #fff; }
        .thumb-img:hover { transform: scale(1.08); border-color: var(--primary); }
        .btn-view-proof { background: rgba(95, 37, 159, 0.1); color: var(--phonepe-purple); border: none; padding: 12px 18px; border-radius: 14px; font-size: 15px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-view-proof:hover { background: var(--phonepe-purple); color: #fff; }
        .btn-map { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; padding: 12px 18px; border-radius: 14px; font-size: 15px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }

        /* MODAL POPUP */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.85); z-index: 30000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(8px); }
        .modal-content { background: #fff; width: 100%; max-width: 450px; border-radius: 26px; padding: 26px; text-align: center; position: relative; }
        .proof-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 18px; }
        .proof-box { border: 1px solid #e2e8f0; border-radius: 18px; padding: 12px; background: #f8fafc; }
        .proof-box h5 { font-size: 14px; font-weight: 700; color: var(--text-sub); margin-bottom: 8px; }
        .proof-box img { width: 100%; height: 130px; object-fit: contain; border-radius: 12px; background: #fff; }
        .close-modal { position: absolute; top: 18px; right: 18px; font-size: 18px; cursor: pointer; color: #64748b; background: #f1f5f9; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

        /* PHONEPE STYLE BOTTOM MOBILE NAVIGATION BAR */
        .mobile-bottom-nav { 
            display: none; position: fixed; bottom: 0; left: 0; right: 0; 
            height: var(--bottom-nav-height); background: #ffffff; 
            box-shadow: 0 -6px 25px rgba(0,0,0,0.1); z-index: 1000; 
            justify-content: space-around; align-items: center; border-top: 1px solid #e2e8f0; 
        }
        .mobile-bottom-nav a { 
            color: #64748b; text-decoration: none; display: flex; 
            flex-direction: column; align-items: center; font-size: 14px; 
            font-weight: 800; gap: 4px; width: 25%; 
        }
        .mobile-bottom-nav a.active { color: var(--phonepe-purple); }
        .mobile-bottom-nav a i { font-size: 26px; }

        .big-home-nav-btn {
            background: var(--phonepe-purple); color: #ffffff !important; border-radius: 20px;
            padding: 12px 20px; font-size: 14px !important; margin-top: -22px;
            box-shadow: 0 8px 20px rgba(95, 37, 159, 0.4); border: 3px solid #ffffff;
        }

        /* LOADING SKELETON ANIMATION */
        .loading-shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 16px;
        }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

        /* PHONEPE MOBILE SPECIFIC ENHANCEMENTS & BIGGER FONTS */
        @media (max-width: 768px) {
            .app-header, .mobile-bottom-nav { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; padding: calc(var(--app-header-height) + 16px) 16px calc(var(--bottom-nav-height) + 20px) 16px; }
            .filter-card { flex-direction: column; align-items: stretch; gap: 16px; padding: 20px; }
            .filter-card h2 { font-size: 22px; }
            .filter-actions { flex-direction: column; max-width: 100%; gap: 12px; }
            .search-box input, .select-custom, .btn-export { width: 100%; font-size: 17px !important; padding: 14px 18px !important; }
            .history-card { display: none; }
            .app-cards-container { display: flex; }
            
            .stat-card { padding: 18px 14px; }
            .stat-card h4 { font-size: 14px; }
            .stat-card h2 { font-size: 34px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- PHONEPE MOBILE HEADER -->
<div class="app-header">
    <div class="app-header-title">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="font-size: 26px; cursor: pointer;"></i>
        <span>हाजिरी इतिहास</span>
    </div>
    <a href="student_dashboard.php" style="font-size: 24px; color: #ffffff; text-decoration:none;"><i class="fas fa-home"></i></a>
</div>

<!-- SIDEBAR NAVIGATION -->
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-graduation-cap" style="color:#a78bfa;"></i> 
            <span>TC ACADEMY</span>
        </div>
        <i class="fas fa-times" onclick="toggleSidebar()" style="cursor:pointer; font-size:22px; color:#64748b;"></i>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">मुख्य मेनू</div>
        <a href="student_dashboard.php"><i class="fas fa-home" style="color:#a78bfa;"></i> <span>होम (Dashboard)</span></a>
        
        <div class="menu-label">हाजिरी सिस्टम</div>
        <a href="attendance.php"><i class="fas fa-fingerprint" style="color:#38bdf8;"></i> <span>आज की हाजिरी</span></a>
        <a href="attendance_history.php" class="active"><i class="fas fa-history" style="color:#a78bfa;"></i> <span>हाजिरी का इतिहास</span></a>
        <a href="holidays.php"><i class="fas fa-calendar-day" style="color:#fbbf24;"></i> <span>छुट्टियों की सूची</span></a>
        
        <div class="menu-label">अकाउंट</div>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>लॉगआउट करें</span></a>
    </div>
</div>

<!-- PROOF DUAL MODAL -->
<div id="proofModal" class="modal-overlay" onclick="if(event.target===this) closeProofModal()">
    <div class="modal-content">
        <div class="close-modal" onclick="closeProofModal()"><i class="fas fa-times"></i></div>
        <h3 style="font-size:22px; font-weight:800;"><i class="fas fa-shield-alt" style="color:var(--phonepe-purple);"></i> प्रमाणीकरण विवरण</h3>
        <p id="modalDate" style="font-size:16px; font-weight:700; color:var(--text-sub); margin-top:6px;"></p>
        
        <div class="proof-modal-grid">
            <div class="proof-box">
                <h5><i class="fas fa-camera"></i> लाइव फोटो</h5>
                <img id="modalImg" src="" alt="Live Photo" onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Photo';">
            </div>
            <div class="proof-box">
                <h5><i class="fas fa-pen-nib"></i> सिग्नेचर</h5>
                <img id="modalSig" src="" alt="Live Signature" onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Signature';">
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="content-body">
        
        <!-- BIG HOME BUTTON BANNER -->
        <a href="student_dashboard.php" class="big-home-banner">
            <div class="big-home-left">
                <div class="big-home-icon"><i class="fas fa-home"></i></div>
                <div>
                    <h4 style="font-size:19px; font-weight:800;">स्टूडेंट डैशबोर्ड</h4>
                    <p style="font-size:14px; color:#94a3b8;">मुख्य पृष्ठ (Home Page) पर जाने के लिए यहाँ क्लिक करें</p>
                </div>
            </div>
            <i class="fas fa-chevron-right" style="color:#a78bfa; font-size:22px;"></i>
        </a>

        <!-- TOOLBAR & FILTER CARD -->
        <div class="filter-card">
            <h2><i class="fas fa-calendar-alt" style="color:var(--phonepe-purple);"></i> रिकॉर्ड्स फ़िल्टर</h2>
            <div class="filter-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="tableSearch" onkeyup="filterData()" placeholder="खोजें (तारीख, दिन)...">
                </div>
                
                <!-- Pure AJAX Dropdowns -->
                <select id="monthSelect" class="select-custom" onchange="loadAttendanceAjax(event)">
                    <?php
                    $hindi_months = ['01'=>'जनवरी', '02'=>'फरवरी', '03'=>'मार्च', '04'=>'अप्रैल', '05'=>'मई', '06'=>'जून', '07'=>'जुलाई', '08'=>'अगस्त', '09'=>'सितंबर', '10'=>'अक्टूबर', '11'=>'नवंबर', '12'=>'दिसंबर'];
                    foreach ($hindi_months as $key => $name) {
                        $sel = ($key == $selected_month) ? 'selected' : '';
                        echo "<option value='$key' $sel>$name</option>";
                    }
                    ?>
                </select>
                
                <select id="yearSelect" class="select-custom" onchange="loadAttendanceAjax(event)">
                    <?php
                    for ($y = date('Y'); $y >= date('Y') - 2; $y--) {
                        $sel = ($y == $selected_year) ? 'selected' : '';
                        echo "<option value='$y' $sel>$y</option>";
                    }
                    ?>
                </select>

                <button type="button" class="btn-export" onclick="exportCSV()"><i class="fas fa-file-excel"></i> Export</button>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card present">
                <h4>उपस्थित</h4>
                <h2 id="statPresent" style="color:var(--success);"><?= htmlspecialchars($stats['present']) ?></h2>
            </div>
            <div class="stat-card absent">
                <h4>अनुपस्थित</h4>
                <h2 id="statAbsent" style="color:var(--danger);"><?= htmlspecialchars($stats['absent']) ?></h2>
            </div>
            <div class="stat-card holiday">
                <h4>छुट्टियां</h4>
                <h2 id="statHoliday" style="color:var(--holiday);"><?= htmlspecialchars($stats['holiday']) ?></h2>
            </div>
        </div>

        <!-- DESKTOP TABLE VIEW -->
        <div class="history-card">
            <div class="table-responsive">
                <table class="history-table" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>दिनांक</th>
                            <th>दिन</th>
                            <th>स्थिति</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>विवरण</th>
                            <th>प्रमाण एवं मैप</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Rendered dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE APP CARDS VIEW (PHONEPE STYLE & BIGGER FONTS) -->
        <div class="app-cards-container" id="appCardList">
            <!-- Rendered dynamically via JavaScript -->
        </div>

    </div>
</div>

<!-- PHONEPE STYLE BOTTOM MOBILE NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="attendance.php"><i class="fas fa-fingerprint"></i><span>हाजिरी</span></a>
    <a href="student_dashboard.php" class="big-home-nav-btn"><i class="fas fa-home" style="font-size:28px;"></i><span>HOME</span></a>
    <a href="attendance_history.php" class="active"><i class="fas fa-history"></i><span>इतिहास</span></a>
</div>

<script>
    // Safe Helper for HTML Escaping
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Dynamic Mobile & Web Map Opener (Fixes Reloading Issue Completely)
    function openMapLocation(e, lat, lng) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (!lat || !lng) return false;
        
        // Open safely in new tab/window without page redirect
        window.open(`https://maps.google.com/?q=${lat},${lng}`, '_blank');
        return false;
    }

    // Initial Render Data
    let currentData = <?= json_encode($history_list, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    document.addEventListener("DOMContentLoaded", () => {
        renderUI(currentData);
    });

    // Sidebar Toggle
    function toggleSidebar() {
        document.getElementById('sidebarNav').classList.toggle('active');
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    // Dynamic Modal Visualizer (Prevents Any Reload)
    function viewProof(e, photoUrl, sigUrl, dateStr) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        document.getElementById('modalImg').src = photoUrl || 'https://via.placeholder.com/150?text=No+Photo';
        document.getElementById('modalSig').src = sigUrl || 'https://via.placeholder.com/150?text=No+Signature';
        document.getElementById('modalDate').innerText = "दिनांक: " + dateStr;
        document.getElementById('proofModal').style.display = 'flex';
    }

    function closeProofModal() {
        document.getElementById('proofModal').style.display = 'none';
    }

    // PURE AJAX ENGINE (FIXED: Zero Reloading)
    function loadAttendanceAjax(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        let m = document.getElementById('monthSelect').value;
        let y = document.getElementById('yearSelect').value;

        // Display Loading Skeleton
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="7" class="loading-shimmer" style="height: 120px;"></td></tr>`;
        document.getElementById('appCardList').innerHTML = `<div class="app-card loading-shimmer" style="height: 120px;"></div>`;

        fetch(`attendance_history.php?ajax=1&m=${m}&y=${y}`)
            .then(res => res.json())
            .then(response => {
                if(response.error) {
                    console.error("AJAX error:", response.error);
                    document.getElementById('tableBody').innerHTML = `<tr><td colspan="7" style="text-align:center; color:red; padding:20px;">त्रुटि: ${response.error}</td></tr>`;
                    document.getElementById('appCardList').innerHTML = `<div style="text-align:center; color:red; padding:20px;">त्रुटि: ${response.error}</div>`;
                    return;
                }
                
                // Dynamic Stat Counter Update
                document.getElementById('statPresent').innerText = response.stats.present;
                document.getElementById('statAbsent').innerText = response.stats.absent;
                document.getElementById('statHoliday').innerText = response.stats.holiday;

                // Render List Data Without Reload
                currentData = response.list;
                renderUI(currentData);
            })
            .catch(err => {
                console.error("AJAX Fetch Error:", err);
                document.getElementById('tableBody').innerHTML = `<tr><td colspan="7" style="text-align:center; color:red; padding:20px;">डेटा लोड करने में असमर्थ।</td></tr>`;
            });
    }

    // Dynamic Render Engine
    function renderUI(list) {
        let tbody = document.getElementById('tableBody');
        let cardList = document.getElementById('appCardList');

        tbody.innerHTML = '';
        cardList.innerHTML = '';

        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:var(--text-sub); padding:30px; font-size:16px;">कोई रिकॉर्ड नहीं मिला।</td></tr>`;
            cardList.innerHTML = `<div style="text-align:center; color:var(--text-sub); padding:30px; font-size:16px; font-weight:700;">कोई रिकॉर्ड नहीं मिला।</div>`;
            return;
        }

        list.forEach(row => {
            let badgeHTML = '';
            if (row.status === 'Present') {
                badgeHTML = `<span class="badge badge-present"><i class="fas fa-check-circle"></i> उपस्थित</span>`;
            } else if (row.status === 'Absent') {
                badgeHTML = `<span class="badge badge-absent"><i class="fas fa-times-circle"></i> अनुपस्थित</span>`;
            } else {
                badgeHTML = `<span class="badge badge-holiday"><i class="fas fa-calendar-day"></i> अवकाश</span>`;
            }

            let proofHTML = '';
            if (row.photo || row.signature || (row.lat && row.lng)) {
                let safePhoto = escapeHtml(row.photo);
                let safeSig   = escapeHtml(row.signature);
                let safeDate  = escapeHtml(row.formatted_date);

                let imgThumb = row.photo ? `<img src="${safePhoto}" class="thumb-img" title="फोटो देखें" onclick="viewProof(event, '${safePhoto}', '${safeSig}', '${safeDate}')">` : '';
                let sigThumb = row.signature ? `<img src="${safeSig}" class="thumb-img" title="हस्ताक्षर देखें" onclick="viewProof(event, '${safePhoto}', '${safeSig}', '${safeDate}')">` : '';
                
                let mapBtn = (row.lat && row.lng) 
                    ? `<button type="button" onclick="openMapLocation(event, '${escapeHtml(row.lat)}', '${escapeHtml(row.lng)}')" class="btn-map"><i class="fas fa-location-dot"></i> मैप</button>` 
                    : '';

                proofHTML = `
                    <div class="proof-action-group">
                        ${imgThumb}
                        ${sigThumb}
                        <button type="button" class="btn-view-proof" onclick="viewProof(event, '${safePhoto}', '${safeSig}', '${safeDate}')">
                            <i class="fas fa-eye"></i> प्रमाण
                        </button>
                        ${mapBtn}
                    </div>
                `;
            } else {
                proofHTML = `<span style="color:#cbd5e1; font-size:14px; font-weight:700;">कोई प्रमाण नहीं</span>`;
            }

            // Render Desktop Table Row
            let tr = document.createElement('tr');
            tr.className = 'data-row';
            tr.innerHTML = `
                <td><strong>${escapeHtml(row.formatted_date)}</strong></td>
                <td>${escapeHtml(row.day_name)}</td>
                <td>${badgeHTML}</td>
                <td><span style="color:#16a34a; font-weight:700;"><i class="far fa-clock"></i> ${escapeHtml(row.in_time)}</span></td>
                <td><span style="color:#ea580c; font-weight:700;"><i class="far fa-clock"></i> ${escapeHtml(row.out_time)}</span></td>
                <td>${escapeHtml(row.remark)}</td>
                <td>${proofHTML}</td>
            `;
            tbody.appendChild(tr);

            // Render Mobile Card (PhonePe Style)
            let card = document.createElement('div');
            card.className = 'app-card data-row-mobile';
            card.innerHTML = `
                <div class="app-card-header">
                    <div>
                        <div class="app-card-date">${escapeHtml(row.formatted_date)}</div>
                        <div class="app-card-day">${escapeHtml(row.day_name)}</div>
                    </div>
                    <div>${badgeHTML}</div>
                </div>
                <div style="font-size:16px; color:var(--text-sub); display:flex; gap:18px; font-weight:700; background:#f8fafc; padding:12px 16px; border-radius:14px;">
                    <span><i class="far fa-clock" style="color:#16a34a; font-size:18px;"></i> In: ${escapeHtml(row.in_time)}</span>
                    <span><i class="far fa-clock" style="color:#ea580c; font-size:18px;"></i> Out: ${escapeHtml(row.out_time)}</span>
                </div>
                <div class="app-card-body">
                    <div style="font-size:15px; font-weight:700; color:var(--text-main);">${escapeHtml(row.remark)}</div>
                    ${proofHTML}
                </div>
            `;
            cardList.appendChild(card);
        });

        filterData();
    }

    // Instant Filter/Search
    function filterData() {
        let input = document.getElementById("tableSearch").value.toLowerCase();
        
        document.querySelectorAll("#attendanceTable tbody tr.data-row").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
        });

        document.querySelectorAll("#appCardList .data-row-mobile").forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(input) ? "flex" : "none";
        });
    }

    // Export CSV
    function exportCSV() {
        let rows = document.querySelectorAll("#attendanceTable tr");
        let csv = [];
        rows.forEach(row => {
            let cols = row.querySelectorAll("td, th");
            let rowData = [];
            cols.forEach((col, idx) => {
                if(idx !== 6) { 
                    rowData.push('"' + col.innerText.replace(/"/g, '""').trim() + '"');
                }
            });
            if(rowData.length) csv.push(rowData.join(","));
        });

        let csvFile = new Blob(["\ufeff" + csv.join("\n")], { type: "text/csv;charset=utf-8;" });
        let downloadLink = document.createElement("a");
        let m = document.getElementById('monthSelect').value;
        let y = document.getElementById('yearSelect').value;
        downloadLink.download = `Attendance_History_${m}_${y}.csv`;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.click();
    }
</script>

</body>
</html>