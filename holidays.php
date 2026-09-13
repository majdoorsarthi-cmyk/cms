<?php 
/**
 * CMS PRO - ADVANCED INTERACTIVE HOLIDAY CALENDAR ENGINE
 * Dynamic Month Grid & Table View with Red Highlighted Sundays & Holidays
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

// Auto Database Table & Schema Resolver for Holidays Table
function initializeHolidaysSchema($conn) {
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'holidays'");
    if (mysqli_num_rows($table_check) == 0) {
        $create_sql = "CREATE TABLE `holidays` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `holiday_date` DATE NOT NULL,
            `description` TEXT NULL,
            `type` ENUM('Festival', 'National', 'Academy', 'Sunday') DEFAULT 'Festival',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        @mysqli_query($conn, $create_sql);
    }
}
initializeHolidaysSchema($conn);

// Helper Function: Pure Dynamic 365 Days Holiday Data Engine
function fetchHolidaysData($conn, $selected_year) {
    $merged_holidays = [];

    // 1. पूरे 365 दिन के सभी 'रविवार (Sundays)' को ऑटोमैटिक अवकाश के रूप में जोड़ें
    $start_date = new DateTime("$selected_year-01-01");
    $end_date   = new DateTime("$selected_year-12-31");
    $interval   = new DateInterval('P1D');
    $period     = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));

    foreach ($period as $dt) {
        if ($dt->format('N') == 7) { // 7 = Sunday
            $date_str = $dt->format('Y-m-d');
            $merged_holidays[$date_str] = [
                'id' => 'sun_' . $date_str,
                'title' => 'रविवार (Sunday)',
                'type' => 'Sunday',
                'desc' => 'साप्ताहिक अवकाश'
            ];
        }
    }

    // 2. Database (holidays table) से सभी त्यौहार और स्पेशल छुट्टियां फैच करें
    $db_query = "SELECT * FROM holidays WHERE YEAR(holiday_date) = ? ORDER BY holiday_date ASC";
    $stmt = $conn->prepare($db_query);
    if ($stmt) {
        $stmt->bind_param("i", $selected_year);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $formatted_date = date('Y-m-d', strtotime($row['holiday_date']));
            // डेटाबेस की स्पेशल छुट्टी रविवार के ऊपर प्राथमिकता (Priority) लेगी
            $merged_holidays[$formatted_date] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'type' => !empty($row['type']) ? $row['type'] : 'Festival',
                'desc' => !empty($row['description']) ? $row['description'] : 'संस्थान अवकाश'
            ];
        }
    }

    // 3. सॉर्टिंग और फ़ॉर्मेटिंग
    ksort($merged_holidays);
    $final_list = [];
    $today = date('Y-m-d');
    $upcoming_count = 0;
    $passed_count = 0;

    $hindi_days = [
        'Sunday' => 'रविवार', 'Monday' => 'सोमवार', 'Tuesday' => 'मंगलवार',
        'Wednesday' => 'बुधवार', 'Thursday' => 'गुरुवार', 'Friday' => 'शुक्रवार', 'Saturday' => 'शनिवार'
    ];

    foreach ($merged_holidays as $date_str => $info) {
        $status = ($date_str >= $today) ? 'Upcoming' : 'Passed';
        if ($status === 'Upcoming') $upcoming_count++;
        else $passed_count++;

        $day_en = date('l', strtotime($date_str));

        $final_list[$date_str] = [
            'date' => $date_str,
            'formatted_date' => date('d-m-Y', strtotime($date_str)),
            'day_name' => $day_en,
            'day_hindi' => $hindi_days[$day_en] ?? $day_en,
            'title' => $info['title'],
            'type' => $info['type'],
            'desc' => $info['desc'],
            'status' => $status
        ];
    }

    return [
        'stats' => [
            'total' => count($final_list),
            'upcoming' => $upcoming_count,
            'passed' => $passed_count
        ],
        'holidays' => $final_list
    ];
}

// --- HANDLE AJAX REQUESTS ---
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    $y = isset($_GET['y']) ? intval($_GET['y']) : date('Y');
    $data = fetchHolidaysData($conn, $y);
    echo json_encode($data);
    exit();
}

// --- INITIAL SSR PAGE LOAD ---
$selected_year = isset($_GET['y']) ? intval($_GET['y']) : date('Y');
$initial_data  = fetchHolidaysData($conn, $selected_year);
$holidays_dict = $initial_data['holidays'];
$stats         = $initial_data['stats'];
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>छुट्टियों की सूची - TC Academy</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #dc2626;
            --primary-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --sidebar-bg: #0f172a;
            --bg: #fef2f2;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --success: #16a34a;
            --danger: #dc2626;
            --holiday-red: #ef4444;
            --holiday-bg: #ffe4e6;
            --app-header-height: 60px;
            --bottom-nav-height: 75px;
            --radius: 18px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Hind', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; padding-bottom: calc(var(--bottom-nav-height) + 20px); }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 280px; background: var(--sidebar-bg); height: 100vh; height: 100dvh;
            position: fixed; top: 0; left: 0; padding: 24px 16px; color: white;
            z-index: 10050; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .sidebar-brand { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 800; letter-spacing: 1px; margin: 20px 0 10px 10px; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; padding: 12px 16px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 15px; margin-bottom: 6px; font-weight: 600; transition: 0.2s; }
        .sidebar-menu a.active, .sidebar-menu a:hover { background: rgba(220, 38, 38, 0.15); color: #f87171; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 10040; backdrop-filter: blur(4px); }

        /* --- MOBILE HEADER --- */
        .app-header { display: none; position: fixed; top: 0; left: 0; right: 0; height: var(--app-header-height); background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(254, 202, 202, 0.8); align-items: center; justify-content: space-between; padding: 0 20px; z-index: 1000; }
        .app-header h3 { font-size: 18px; font-weight: 800; color: var(--danger); }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 28px; transition: all 0.3s ease; }
        .content-body { max-width: 1100px; margin: 0 auto; }

        /* BANNER */
        .big-home-banner {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            border-radius: 20px; padding: 16px 20px; color: #fff; display: flex;
            align-items: center; justify-content: space-between; text-decoration: none;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.2); margin-bottom: 24px; transition: transform 0.2s ease;
        }
        .big-home-left { display: flex; align-items: center; gap: 14px; }
        .big-home-icon { width: 48px; height: 48px; background: #dc2626; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; }

        /* FILTER CARD */
        .filter-card {
            background: var(--card-bg); border-radius: var(--radius); padding: 20px;
            box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.05); margin-bottom: 24px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
            border: 1px solid #fecaca;
        }
        .filter-card h2 { font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 12px; color: var(--danger); }
        .filter-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; width: 100%; max-width: 500px; justify-content: flex-end; }

        .select-custom { padding: 10px 14px; border-radius: 12px; border: 1px solid #fca5a5; font-size: 14px; font-weight: 700; background: #fff5f5; outline: none; cursor: pointer; color: var(--danger); }

        /* VIEW TOGGLE BUTTONS */
        .view-btn { padding: 10px 16px; border-radius: 12px; border: 1px solid #fca5a5; background: #fff; font-weight: 700; cursor: pointer; color: #dc2626; transition: 0.2s; }
        .view-btn.active { background: #dc2626; color: #fff; border-color: #dc2626; }

        /* STATS GRID */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--card-bg); padding: 20px; border-radius: var(--radius);
            border: 1px solid #fecaca; text-align: center; position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.04);
        }
        .stat-card::before { content:''; position: absolute; top:0; left:0; width: 100%; height: 4px; }
        .stat-card.total::before { background: var(--danger); }
        .stat-card.upcoming::before { background: #f97316; }
        .stat-card.passed::before { background: #94a3b8; }
        .stat-card h4 { font-size: 13px; color: var(--text-sub); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card h2 { font-size: 28px; font-weight: 800; margin-top: 4px; }

        /* --- ADVANCED CALENDAR GRID VIEW --- */
        .calendar-card {
            background: var(--card-bg); border-radius: var(--radius); padding: 24px;
            border: 1px solid #fecaca; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 24px;
        }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .calendar-month-title { font-size: 20px; font-weight: 800; color: #991b1b; }
        .month-nav-btn { background: #ffe4e6; border: none; width: 38px; height: 38px; border-radius: 10px; color: #dc2626; font-size: 16px; cursor: pointer; transition: 0.2s; }
        .month-nav-btn:hover { background: #dc2626; color: #fff; }

        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; }
        .cal-day-head { font-size: 13px; font-weight: 800; color: #991b1b; padding: 10px 0; background: #fff1f2; border-radius: 8px; }
        .cal-day-head.sun { color: #dc2626; background: #ffe4e6; }

        .cal-date-cell {
            aspect-ratio: 1; min-height: 60px; background: #fff; border: 1px solid #fee2e2; border-radius: 12px;
            padding: 6px; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-start;
            position: relative; transition: 0.2s; cursor: pointer;
        }
        .cal-date-cell.empty { background: transparent; border: none; cursor: default; }
        .cal-date-cell.today { border: 2px solid #dc2626; font-weight: bold; }
        
        /* RED HIGHLIGHT FOR HOLIDAYS & SUNDAYS */
        .cal-date-cell.holiday { background: #ffe4e6; border-color: #fca5a5; }
        .cal-date-cell .date-num { font-size: 14px; font-weight: 800; color: #1e293b; }
        .cal-date-cell.holiday .date-num { color: #dc2626; }
        .cal-date-cell .holiday-label {
            font-size: 9px; font-weight: 800; background: #dc2626; color: white;
            padding: 2px 4px; border-radius: 4px; width: 100%; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; text-align: left;
        }

        /* TABLE VIEW STYLES */
        .history-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid #fecaca; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .table-responsive { width: 100%; overflow-x: auto; }
        .history-table { width: 100%; border-collapse: collapse; text-align: left; }
        .history-table th { background: #ffe4e6; padding: 16px 20px; font-size: 13px; font-weight: 800; color: #991b1b; border-bottom: 1px solid #fecaca; }
        .history-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #fff1f2; font-weight: 600; vertical-align: middle; }
        .holiday-row { background-color: #fff5f5 !important; }
        .holiday-title { color: #dc2626 !important; font-weight: 800 !important; font-size: 15px; }

        /* BADGES */
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 800; }
        .badge-type-red { background: #ffe4e6; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-status-upcoming { background: #ffedd5; color: #c2410c; }
        .badge-status-passed { background: #f1f5f9; color: #64748b; }

        /* BOTTOM NAV */
        .mobile-bottom-nav { 
            display: none; position: fixed; bottom: 0; left: 0; right: 0; 
            height: var(--bottom-nav-height); background: #ffffff; 
            box-shadow: 0 -5px 25px rgba(0,0,0,0.08); z-index: 1000; 
            justify-content: space-around; align-items: center; border-top: 1px solid #fecaca; 
        }
        .mobile-bottom-nav a { color: #64748b; text-decoration: none; display: flex; flex-direction: column; align-items: center; font-size: 11px; font-weight: 800; gap: 4px; width: 25%; }
        .mobile-bottom-nav a.active { color: var(--danger); }
        .mobile-bottom-nav a i { font-size: 22px; }

        .big-home-nav-btn {
            background: #dc2626; color: #ffffff !important; border-radius: 16px;
            padding: 8px 16px; font-size: 12px !important; margin-top: -15px;
            box-shadow: 0 6px 15px rgba(220, 38, 38, 0.35); border: 3px solid #fff;
        }

        @media (max-width: 768px) {
            .app-header, .mobile-bottom-nav { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; padding: calc(var(--app-header-height) + 16px) 16px calc(var(--bottom-nav-height) + 20px) 16px; }
            .filter-card { flex-direction: column; align-items: stretch; gap: 12px; }
            .filter-actions { flex-direction: row; width: 100%; justify-content: space-between; }
            .cal-date-cell { min-height: 50px; padding: 4px; }
            .cal-date-cell .holiday-label { font-size: 8px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- APP HEADER (MOBILE) -->
<div class="app-header">
    <div style="display:flex; align-items:center; gap:12px;">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="font-size: 20px; cursor: pointer; color: var(--danger);"></i>
        <h3>365 दिन लाइव कैलेंडर</h3>
    </div>
    <a href="student_dashboard.php" style="font-size: 20px; color: var(--danger); text-decoration:none;"><i class="fas fa-home"></i></a>
</div>

<!-- SIDEBAR NAVIGATION -->
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-graduation-cap" style="color:#f87171;"></i> 
            <span>TC ACADEMY</span>
        </div>
        <i class="fas fa-times" onclick="toggleSidebar()" style="cursor:pointer; font-size:18px; color:#64748b;"></i>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">मुख्य मेनू</div>
        <a href="student_dashboard.php"><i class="fas fa-home"></i> <span>होम (Dashboard)</span></a>
        
        <div class="menu-label">हाजिरी सिस्टम</div>
        <a href="attendance.php"><i class="fas fa-fingerprint"></i> <span>आज की हाजिरी</span></a>
        <a href="attendance_history.php"><i class="fas fa-history"></i> <span>हाजिरी का इतिहास</span></a>
        <a href="holidays.php" class="active"><i class="fas fa-calendar-day"></i> <span>छुट्टियों की सूची (Red)</span></a>
        
        <div class="menu-label">अन्य</div>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>लॉगआउट करें</span></a>
    </div>
</div>

<div class="main-content">
    <div class="content-body">
        
        <!-- BANNER -->
        <a href="student_dashboard.php" class="big-home-banner">
            <div class="big-home-left">
                <div class="big-home-icon"><i class="fas fa-home"></i></div>
                <div>
                    <h4 style="font-size:16px; font-weight:800;">स्टूडेंट डैशबोर्ड</h4>
                    <p style="font-size:12px; color:#fca5a5;">मुख्य पृष्ठ पर जाने के लिए क्लिक करें</p>
                </div>
            </div>
            <i class="fas fa-chevron-right" style="color:#fca5a5;"></i>
        </a>

        <!-- FILTER & VIEW TOOLBAR -->
        <div class="filter-card">
            <h2><i class="fas fa-calendar-alt" style="color:var(--danger);"></i> लाइव एडवांस कैलेंडर View</h2>
            <div class="filter-actions">
                <div>
                    <button id="btnCalView" class="view-btn active" onclick="switchView('calendar')"><i class="fas fa-calendar-grid"></i> कैलेंडर</button>
                    <button id="btnTableView" class="view-btn" onclick="switchView('table')"><i class="fas fa-list"></i> सूची (List)</button>
                </div>
                
                <select id="yearSelect" class="select-custom" onchange="loadHolidaysAjax()">
                    <?php
                    for ($y = date('Y') + 1; $y >= date('Y') - 2; $y--) {
                        $sel = ($y == $selected_year) ? 'selected' : '';
                        echo "<option value='$y' $sel>वर्ष $y</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card total">
                <h4>कुल अवकाश (Red Days)</h4>
                <h2 id="statTotal" style="color:var(--danger);"><?= htmlspecialchars($stats['total']) ?></h2>
            </div>
            <div class="stat-card upcoming">
                <h4>आगामी छुट्टियां</h4>
                <h2 id="statUpcoming" style="color:#ea580c;"><?= htmlspecialchars($stats['upcoming']) ?></h2>
            </div>
            <div class="stat-card passed">
                <h4>बीती छुट्टियां</h4>
                <h2 id="statPassed" style="color:#64748b;"><?= htmlspecialchars($stats['passed']) ?></h2>
            </div>
        </div>

        <!-- CALENDAR MONTH VIEW (GRID) -->
        <div class="calendar-card" id="calendarViewSection">
            <div class="calendar-header">
                <button class="month-nav-btn" onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                <div class="calendar-month-title" id="currentMonthYearText">जनवरी 2026</div>
                <button class="month-nav-btn" onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="calendar-grid">
                <div class="cal-day-head sun">रवि</div>
                <div class="cal-day-head">सोम</div>
                <div class="cal-day-head">मंगल</div>
                <div class="cal-day-head">बुध</div>
                <div class="cal-day-head">गुरु</div>
                <div class="cal-day-head">शुक्र</div>
                <div class="cal-day-head">शनि</div>
            </div>
            <div class="calendar-grid" id="calendarDaysGrid" style="margin-top:8px;">
                <!-- Days rendered dynamically -->
            </div>
        </div>

        <!-- LIST TABLE VIEW -->
        <div class="history-card" id="tableViewSection" style="display:none;">
            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>दिनांक</th>
                            <th>दिन</th>
                            <th>त्योहार / अवकाश (Holiday)</th>
                            <th>प्रकार</th>
                            <th>विवरण</th>
                            <th>स्थिति</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Table rendered dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- BOTTOM MOBILE NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="attendance.php"><i class="fas fa-fingerprint"></i><span>हाजिरी</span></a>
    <a href="student_dashboard.php" class="big-home-nav-btn"><i class="fas fa-home" style="font-size:24px;"></i><span>HOME</span></a>
    <a href="attendance_history.php"><i class="fas fa-history"></i><span>इतिहास</span></a>
</div>

<script>
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    let holidaysDict = <?= json_encode($holidays_dict, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    let currentYear = <?= $selected_year ?>;
    let currentMonth = new Date().getMonth(); // 0 = Jan, 11 = Dec

    const monthNamesHindi = [
        "जनवरी", "फ़रवरी", "मार्च", "अप्रैल", "मई", "जून",
        "जुलाई", "अगस्त", "सितंबर", "अक्टूबर", "नवंबर", "दिसंबर"
    ];

    document.addEventListener("DOMContentLoaded", () => {
        renderCalendarGrid();
        renderTableList();
    });

    function toggleSidebar() {
        document.getElementById('sidebarNav').classList.toggle('active');
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    function switchView(view) {
        if (view === 'calendar') {
            document.getElementById('calendarViewSection').style.display = 'block';
            document.getElementById('tableViewSection').style.display = 'none';
            document.getElementById('btnCalView').classList.add('active');
            document.getElementById('btnTableView').classList.remove('active');
        } else {
            document.getElementById('calendarViewSection').style.display = 'none';
            document.getElementById('tableViewSection').style.display = 'block';
            document.getElementById('btnCalView').classList.remove('active');
            document.getElementById('btnTableView').classList.add('active');
        }
    }

    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth < 0) {
            currentMonth = 11;
        } else if (currentMonth > 11) {
            currentMonth = 0;
        }
        renderCalendarGrid();
    }

    // Dynamic AJAX Fetch Engine
    function loadHolidaysAjax() {
        currentYear = parseInt(document.getElementById('yearSelect').value);

        fetch(`holidays.php?ajax=1&y=${currentYear}`)
            .then(res => res.json())
            .then(response => {
                if(response.error) {
                    alert(response.error);
                    return;
                }
                document.getElementById('statTotal').innerText = response.stats.total;
                document.getElementById('statUpcoming').innerText = response.stats.upcoming;
                document.getElementById('statPassed').innerText = response.stats.passed;

                holidaysDict = response.holidays;
                renderCalendarGrid();
                renderTableList();
            })
            .catch(err => {
                console.error("AJAX Fetch Error:", err);
            });
    }

    // Render Real Interactive Calendar View
    function renderCalendarGrid() {
        document.getElementById('currentMonthYearText').innerText = `${monthNamesHindi[currentMonth]} ${currentYear}`;
        const grid = document.getElementById('calendarDaysGrid');
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1).getDay(); // 0 = Sun
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const todayStr = new Date().toISOString().split('T')[0];

        // Empty cells for alignment
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'cal-date-cell empty';
            grid.appendChild(emptyCell);
        }

        // Render month days
        for (let day = 1; day <= daysInMonth; day++) {
            const monthPadded = String(currentMonth + 1).padStart(2, '0');
            const dayPadded = String(day).padStart(2, '0');
            const dateKey = `${currentYear}-${monthPadded}-${dayPadded}`;

            const cell = document.createElement('div');
            cell.className = 'cal-date-cell';

            if (dateKey === todayStr) cell.classList.add('today');

            const isHoliday = holidaysDict.hasOwnProperty(dateKey);

            if (isHoliday) {
                cell.classList.add('holiday');
                const hol = holidaysDict[dateKey];
                cell.innerHTML = `
                    <span class="date-num">${day}</span>
                    <span class="holiday-label" title="${escapeHtml(hol.title)}">${escapeHtml(hol.title)}</span>
                `;
                cell.onclick = () => alert(`${hol.formatted_date} (${hol.day_hindi})\n${hol.title}\nविवरण: ${hol.desc}`);
            } else {
                cell.innerHTML = `<span class="date-num">${day}</span>`;
            }

            grid.appendChild(cell);
        }
    }

    // Render List Table View
    function renderTableList() {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        const keys = Object.keys(holidaysDict);
        if (keys.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px;">कोई छुट्टी दर्ज नहीं है।</td></tr>`;
            return;
        }

        keys.forEach(dateKey => {
            const row = holidaysDict[dateKey];
            const statusBadge = (row.status === 'Upcoming') 
                ? `<span class="badge badge-status-upcoming"><i class="fas fa-hourglass-half"></i> आने वाला</span>`
                : `<span class="badge badge-status-passed"><i class="fas fa-calendar-check"></i> बीता</span>`;

            const typeBadge = `<span class="badge badge-type-red"><i class="fas fa-umbrella-beach"></i> ${escapeHtml(row.type)}</span>`;

            const tr = document.createElement('tr');
            tr.className = 'holiday-row';
            tr.innerHTML = `
                <td><strong style="color:#dc2626;">${escapeHtml(row.formatted_date)}</strong></td>
                <td><span style="color:#b91c1c; font-weight:700;">${escapeHtml(row.day_hindi)}</span></td>
                <td><strong class="holiday-title"><i class="fas fa-star" style="font-size:11px; margin-right:4px;"></i> ${escapeHtml(row.title)}</strong></td>
                <td>${typeBadge}</td>
                <td><span style="color:#7f1d1d; font-size:13px;">${escapeHtml(row.desc)}</span></td>
                <td>${statusBadge}</td>
            `;
            tbody.appendChild(tr);
        });
    }
</script>

</body>
</html>