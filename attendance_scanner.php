<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Admin check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// Filter Logic for Date
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Live Attendance Portal | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #f1f5f9;
            --sidebar-bg: #1e293b;
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #0f172a;

            /* PhonePe & Modern App Theme */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg-color); font-family: 'Poppins', sans-serif; display: flex; min-height: 100vh; color: #334155; }
        
        /* 🖥️ DESKTOP STYLES */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: #fff; position: fixed; height: 100vh; padding: 20px; z-index: 100; box-shadow: 4px 0 10px rgba(0,0,0,0.05); }
        .sidebar h3 { margin-bottom: 30px; font-size: 20px; color: #f8fafc; text-align: center; border-bottom: 1px solid #334155; padding-bottom: 15px; font-weight: 700; }
        .sidebar a { color: #94a3b8; text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin: 8px 0; border-radius: 10px; font-weight: 500; transition: all 0.3s ease; }
        .sidebar a:hover, .sidebar a.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #fff; padding: 18px 25px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }

        /* Stats & Controls Grid */
        .top-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
        
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 18px; border: 1px solid #e2e8f0; }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; flex-shrink: 0; }
        .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        
        .stat-info h3 { font-size: 22px; font-weight: 700; color: var(--dark); line-height: 1.2; }
        .stat-info span { font-size: 13px; color: #64748b; font-weight: 500; }

        /* Search & Filter Bar */
        .filter-bar { background: white; padding: 16px 20px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; gap: 15px; border: 1px solid #e2e8f0; }
        .search-box { display: flex; align-items: center; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; width: 320px; gap: 10px; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 14px; font-weight: 500; }

        /* Table Styling */
        .log-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; }
        th { background: #f8fafc; padding: 14px 16px; color: #475569; font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid #e2e8f0; letter-spacing: 0.5px; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        tr:hover td { background: #f8fafc; }
        
        /* Badges & Media */
        .img-preview { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; border: 2px solid #e2e8f0; cursor: pointer; transition: 0.2s; }
        .img-preview:hover { transform: scale(1.1); border-color: var(--primary); }
        .sig-preview { width: 60px; height: 35px; object-fit: contain; background: #fafafa; border: 1px solid #e2e8f0; padding: 2px; border-radius: 6px; cursor: pointer; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-present { background: #dcfce7; color: #15803d; }
        
        .map-btn { color: var(--primary); text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 5px; background: #eff6ff; padding: 6px 10px; border-radius: 8px; transition: 0.2s; }
        .map-btn:hover { background: #dbeafe; }
        .device-info { font-size: 11px; color: #94a3b8; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Modal Image Viewer */
        .modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); justify-content: center; align-items: center; }
        .modal img { max-width: 85%; max-height: 85%; border-radius: 16px; border: 3px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }

        /* Hide Mobile Elements on Desktop */
        .mobile-header, .mobile-bottom-nav, .mobile-attendance-cards, .mobile-search-bar { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full Native App Experience) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; }
            .sidebar, .header-title, table, .filter-bar { display: none !important; }

            /* PhonePe App Header Bar */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 65px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 12px; }
            .mobile-back-btn {
                color: #ffffff;
                font-size: 18px;
                text-decoration: none;
                width: 36px; height: 36px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 17px !important; font-weight: 700; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 11px !important; display: block; }

            .main-content {
                margin-left: 0 !important;
                padding: 75px 12px 85px 12px !important;
                width: 100% !important;
            }

            /* Layout Grid Adjustments */
            .top-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; margin-bottom: 15px !important; }
            .stat-card { padding: 12px 14px !important; border-radius: 14px !important; gap: 10px !important; }
            .stat-icon { width: 40px !important; height: 40px !important; font-size: 18px !important; border-radius: 10px !important; }
            .stat-info h3 { font-size: 16px !important; }
            .stat-info span { font-size: 11px !important; }

            /* Mobile Search Bar */
            .mobile-search-bar {
                display: flex !important;
                background: #fff;
                padding: 10px 14px;
                border-radius: 14px;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            }
            .mobile-search-bar input {
                border: none;
                outline: none;
                width: 100%;
                font-size: 14px;
                font-family: inherit;
            }

            /* Date Picker Bar in Mobile */
            .mobile-date-card {
                background: white;
                border-radius: 14px;
                padding: 10px 14px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 15px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            }
            .mobile-date-card label { font-size: 13.5px; font-weight: 600; color: #334155; }
            .mobile-date-card input[type="date"] {
                padding: 6px 10px;
                font-size: 13.5px;
                font-weight: 600;
                border: 1.5px solid var(--phonepe-purple);
                border-radius: 8px;
                color: var(--phonepe-dark);
                outline: none;
            }

            .log-container { background: transparent !important; padding: 0 !important; box-shadow: none !important; border: none !important; }
            .log-header-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 10px 4px 10px 4px; display: flex; justify-content: space-between; align-items: center; }

            /* PhonePe Transaction Cards Style List */
            .mobile-attendance-cards { display: flex !important; flex-direction: column; gap: 12px; }
            
            .phonepe-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 14px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03);
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .phonepe-card-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                border-bottom: 1px dashed #e2e8f0;
                padding-bottom: 8px;
            }

            .student-meta h4 {
                font-size: 15px !important;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2px;
            }
            .student-meta span {
                font-size: 12px !important;
                color: #64748b;
                font-weight: 500;
            }

            .phonepe-card-body {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .time-box {
                background: #f8fafc;
                padding: 6px 10px;
                border-radius: 8px;
                font-size: 12px !important;
                color: #334155;
            }
            .time-box strong { font-size: 13px !important; }

            .media-box {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .phonepe-card-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8fafc;
                padding: 8px 10px;
                border-radius: 8px;
                font-size: 12px !important;
            }

            /* Bottom PhonePe Nav Bar */
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
                box-shadow: 0 -4px 15px rgba(0,0,0,0.04);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #64748b;
                font-size: 10.5px !important;
                font-weight: 500;
                width: 25%;
            }

            .phonepe-nav-item i { font-size: 18px; margin-bottom: 3px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 700; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe App Header (Mobile Only) -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>लाइव अटेंडेंस रिकॉर्ड्स</h3>
            <small id="liveClockMobile">Live Monitoring</small>
        </div>
    </div>
    <div style="color:#fff; font-size:20px;">
        <i class="fa-solid fa-user-check"></i>
    </div>
</div>

<!-- 🖥️ Sidebar (Desktop Only) -->
<div class="sidebar">
    <h3>Coaching Admin</h3>
    <a href="admin_dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a>
    <a href="attendance_scanner.php" class="active"><i class="fa fa-clipboard-user"></i> Live Attendance</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    
    <!-- 🖥️ Desktop Header -->
    <div class="header-title">
        <div>
            <h2 style="font-weight: 700; color: var(--dark); font-size: 22px;"><i class="fa-solid fa-clipboard-user text-primary me-2"></i> Attendance Live Monitoring Panel</h2>
            <small style="color: #64748b;">Real-time student check-in verification & tracking</small>
        </div>
        <div style="text-align: right;">
            <div id="liveClockDesktop" style="font-weight: 700; color: var(--dark); font-size: 16px;"></div>
            <small style="color: #64748b;"><?= date('l, d M Y') ?></small>
        </div>
    </div>

    <!-- Live Statistics Cards Grid -->
    <?php
    $p_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM attendance WHERE date='$selected_date' AND status='present'"));
    $photo_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM attendance WHERE date='$selected_date' AND photo_path IS NOT NULL AND photo_path != ''"));
    $sig_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM attendance WHERE date='$selected_date' AND signature_path IS NOT NULL AND signature_path != ''"));
    ?>
    
    <div class="top-grid">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa fa-user-check"></i></div>
            <div class="stat-info">
                <h3><?= $p_count ?></h3>
                <span>Present Today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa fa-camera"></i></div>
            <div class="stat-info">
                <h3><?= $photo_count ?></h3>
                <span>Selfie Verified</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple"><i class="fa fa-signature"></i></div>
            <div class="stat-info">
                <h3><?= $sig_count ?></h3>
                <span>Digital Signed</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa fa-calendar-day"></i></div>
            <div class="stat-info">
                <h3><?= date('d M', strtotime($selected_date)) ?></h3>
                <span>Selected Date</span>
            </div>
        </div>
    </div>

    <!-- 🖥️ Search & Filter Controls (Desktop) -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fa fa-search" style="color: #94a3b8;"></i>
            <input type="text" id="desktopSearch" placeholder="Search Student Name or ID..." onkeyup="filterRecords()">
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                <label style="font-weight: 600; font-size: 13.5px; color: #475569;">Date Filter:</label>
                <input type="date" name="date" value="<?= $selected_date ?>" style="padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 500; outline: none;" onchange="this.form.submit()">
            </form>
            <button onclick="window.print()" style="padding: 8px 14px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; cursor: pointer; color: #334155;">
                <i class="fa fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- 📱 Mobile Live Search Bar -->
    <div class="mobile-search-bar">
        <i class="fa fa-search" style="color: #94a3b8;"></i>
        <input type="text" id="mobileSearch" placeholder="छात्र का नाम या ID खोजें..." onkeyup="filterMobileRecords()">
    </div>

    <!-- 📱 Mobile Date Filter Card -->
    <div class="mobile-date-card">
        <label><i class="fa-solid fa-calendar-days text-primary me-1"></i> दिनांक (Date):</label>
        <form method="GET" action="" id="mobileDateForm">
            <input type="date" name="date" value="<?= $selected_date ?>" onchange="document.getElementById('mobileDateForm').submit()">
        </form>
    </div>

    <!-- Attendance Records Section -->
    <div class="log-container">
        
        <div class="table-header d-none d-lg-flex">
            <h3 style="font-size: 17px; font-weight: 700; color: #0f172a;"><i class="fa fa-list-check me-2" style="color: var(--primary);"></i> Attendance Logs (<?= date('d-M-Y', strtotime($selected_date)) ?>)</h3>
        </div>

        <!-- 🖥️ DESKTOP TABLE VIEW -->
        <table id="desktopTable">
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Selfie</th>
                    <th>Status & Time</th>
                    <th>Location Map</th>
                    <th>Digital Sig</th>
                    <th>IP & Device Info</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM attendance WHERE date='$selected_date' ORDER BY id DESC";
                $res = mysqli_query($conn, $sql);

                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $lat = $row['latitude'];
                        $long = $row['longitude'];
                        $map_url = "https://www.google.com/maps?q={$lat},{$long}";
                ?>
                <tr class="attendance-row" data-search="<?= strtolower($row['student_name'] . ' ' . $row['student_id']) ?>">
                    <td>
                        <strong style="color: #0f172a; font-size: 14.5px;"><?= htmlspecialchars($row['student_name']) ?></strong><br>
                        <small style="color: #64748b; font-weight: 500;">ID: #<?= $row['student_id'] ?></small>
                    </td>

                    <td>
                        <?php if(!empty($row['photo_path']) && file_exists($row['photo_path'])): ?>
                            <img src="<?= $row['photo_path'] ?>" class="img-preview" onclick="openModal(this.src)" title="Click to view">
                        <?php else: ?>
                            <small style="color:#cbd5e1;"><i class="fa fa-image me-1"></i> No Photo</small>
                        <?php endif; ?>
                    </td>

                    <td>
                        <span class="badge badge-present"><i class="fa fa-check-circle"></i> PRESENT</span><br>
                        <div style="margin-top: 4px; font-size: 12px;">
                            <span style="color:var(--success); font-weight: 600;"><i class="fa fa-arrow-down-left me-1"></i> In: <?= $row['in_time'] ?></span><br>
                            <span style="color:var(--danger); font-weight: 600;"><i class="fa fa-arrow-up-right me-1"></i> Out: <?= $row['out_time'] ? $row['out_time'] : 'N/A' ?></span>
                        </div>
                    </td>

                    <td>
                        <?php if(!empty($lat) && !empty($long)): ?>
                            <a href="<?= $map_url ?>" target="_blank" class="map-btn">
                                <i class="fa-solid fa-location-dot"></i> View Location
                            </a><br>
                            <small style="font-size: 10px; color:#94a3b8;"><?= number_format((float)$lat, 4) ?>, <?= number_format((float)$long, 4) ?></small>
                        <?php else: ?>
                            <small style="color:#cbd5e1;"><i class="fa fa-location-slash me-1"></i> Disabled</small>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if(!empty($row['signature_path']) && file_exists($row['signature_path'])): ?>
                            <img src="<?= $row['signature_path'] ?>" class="sig-preview" onclick="openModal(this.src)">
                        <?php else: ?>
                            <small style="color:#cbd5e1;">No Sig</small>
                        <?php endif; ?>
                    </td>

                    <td>
                        <small style="font-weight: 600; color:#475569;"><?= $row['ip_address'] ?></small>
                        <div class="device-info" title="<?= htmlspecialchars($row['device_info']) ?>">
                            <?= htmlspecialchars($row['device_info']) ?>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr id='noDataRow'><td colspan='6' style='text-align:center; color:#94a3b8; padding: 30px;'>इस तिथि के लिए कोई उपस्थिति रिकॉर्ड नहीं मिला।</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- 📱 MOBILE PHONEPE CARDS VIEW -->
        <div class="log-header-title d-lg-none">
            <span>उपस्थिति विवरण (Attendance Logs)</span>
            <small style="color: var(--phonepe-purple); font-weight: 600;"><?= $p_count ?> छात्र</small>
        </div>

        <div class="mobile-attendance-cards" id="mobileCardsContainer">
            <?php
            if(mysqli_num_rows($res) > 0) {
                mysqli_data_seek($res, 0); // Reset query pointer
                while($row = mysqli_fetch_assoc($res)) {
                    $lat = $row['latitude'];
                    $long = $row['longitude'];
                    $map_url = "https://www.google.com/maps?q={$lat},{$long}";
            ?>
            <div class="phonepe-card mobile-card-item" data-search="<?= strtolower($row['student_name'] . ' ' . $row['student_id']) ?>">
                <div class="phonepe-card-top">
                    <div class="student-meta">
                        <h4><?= htmlspecialchars($row['student_name']) ?></h4>
                        <span>Roll/ID: #<?= $row['student_id'] ?></span>
                    </div>
                    <span class="badge badge-present"><i class="fa fa-check-circle"></i> PRESENT</span>
                </div>

                <div class="phonepe-card-body">
                    <div class="time-box">
                        <div>In: <strong style="color: var(--success);"><?= $row['in_time'] ?></strong></div>
                        <div>Out: <strong style="color: var(--danger);"><?= $row['out_time'] ? $row['out_time'] : 'N/A' ?></strong></div>
                    </div>

                    <div class="media-box">
                        <?php if(!empty($row['photo_path']) && file_exists($row['photo_path'])): ?>
                            <img src="<?= $row['photo_path'] ?>" class="img-preview" onclick="openModal(this.src)" title="Selfie">
                        <?php endif; ?>

                        <?php if(!empty($row['signature_path']) && file_exists($row['signature_path'])): ?>
                            <img src="<?= $row['signature_path'] ?>" class="sig-preview" onclick="openModal(this.src)" title="Signature">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="phonepe-card-footer">
                    <div>
                        <?php if(!empty($lat) && !empty($long)): ?>
                            <a href="<?= $map_url ?>" target="_blank" class="map-btn">
                                <i class="fa-solid fa-location-dot"></i> मैप देखें
                            </a>
                        <?php else: ?>
                            <span style="color:#cbd5e1;"><i class="fa-solid fa-location-crosshairs"></i> Location Off</span>
                        <?php endif; ?>
                    </div>
                    <div style="color: #64748b; font-size: 11px; font-weight: 500;">
                        IP: <?= $row['ip_address'] ?>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<div class='text-center py-4 bg-white rounded-3 border'><p class='text-muted mb-0' style='font-size:13px;'>इस तारीख के लिए कोई रिकॉर्ड उपलब्ध नहीं है।</p></div>";
            }
            ?>
        </div>

    </div>
</div>

<!-- 📱 PhonePe Bottom Navigation Bar (Mobile Only) -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>होम</span>
    </a>
    <a href="attendance_scanner.php" class="phonepe-nav-item active">
        <i class="fa fa-clipboard-user"></i>
        <span>अटेंडेंस</span>
    </a>
    <a href="homework.php" class="phonepe-nav-item">
        <i class="fa fa-book-open"></i>
        <span>होमवर्क</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

<!-- Image View Modal -->
<div id="imageModal" class="modal" onclick="closeModal()">
    <img id="modalImg">
</div>

<script>
    // Live Clock Functionality
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        
        const desktopClock = document.getElementById('liveClockDesktop');
        const mobileClock = document.getElementById('liveClockMobile');
        
        if(desktopClock) desktopClock.innerText = timeString;
        if(mobileClock) mobileClock.innerText = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Desktop Live Search Filter
    function filterRecords() {
        let input = document.getElementById('desktopSearch').value.toLowerCase();
        let rows = document.querySelectorAll('.attendance-row');

        rows.forEach(row => {
            let searchData = row.getAttribute('data-search');
            if (searchData.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Mobile Live Search Filter
    function filterMobileRecords() {
        let input = document.getElementById('mobileSearch').value.toLowerCase();
        let cards = document.querySelectorAll('.mobile-card-item');

        cards.forEach(card => {
            let searchData = card.getAttribute('data-search');
            if (searchData.includes(input)) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Image Popup Modal Handler
    function openModal(src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
</script>

</body>
</html>