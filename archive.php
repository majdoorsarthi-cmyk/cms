<?php
/**
 * SMART CMS PRO - ADVANCED ISSUED DOCUMENTS ARCHIVE
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php';

// Admin login check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// Data Fetching Logic
$query = "SELECT dr.*, u.name, u.phone, s.roll_no, s.course 
          FROM service_requests dr
          JOIN students s ON dr.student_id = s.id
          JOIN users u ON s.user_id = u.id
          WHERE dr.status IN ('Verified', 'Approved', 'Issued')
          ORDER BY dr.id DESC";

$result = mysqli_query($conn, $query);
$total_issued = ($result) ? mysqli_num_rows($result) : 0;

// Fetch unique doc types for dynamic filter chips
$doc_types = [];
$rows = [];
if($result && $total_issued > 0) {
    while($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
        if(!in_array($r['doc_type'], $doc_types) && !empty($r['doc_type'])) {
            $doc_types[] = $r['doc_type'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Archive | SMART CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --sidebar-bg: #0f172a;
            --bg: #f1f5f9;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --success: #10b981;

            /* PhonePe App Theme Variables */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); font-family: 'Plus Jakarta Sans', sans-serif; display: flex; min-height: 100vh; }

        /* 🖥️ DESKTOP SIDEBAR & LAYOUT */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 20px; flex-shrink: 0; transition: 0.3s; height: 100vh; position: fixed; top: 0; left: 0; z-index: 100; }
        .logo-area { padding: 10px 10px 30px; border-bottom: 1px solid #1e293b; margin-bottom: 20px; font-weight: 800; font-size: 20px; color: #818cf8; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: #94a3b8; border-radius: 12px; margin-bottom: 8px; transition: 0.3s; font-size: 14px; }
        .nav-link:hover, .nav-link.active { background: var(--primary); color: white; }
        .nav-link i { width: 20px; }

        .main-content { flex-grow: 1; padding: 30px; margin-left: 260px; width: calc(100% - 260px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .action-header-btns { display: flex; gap: 10px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: var(--white); padding: 20px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; border: 1px solid #e2e8f0; }
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; background: #eef2ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px; }

        /* Table Design Desktop */
        .card { background: var(--white); border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        
        .search-wrapper { position: relative; }
        .search-wrapper input { padding: 12px 15px 12px 45px; border-radius: 14px; border: 1.5px solid #e2e8f0; width: 320px; outline: none; transition: 0.3s; font-size: 14px; }
        .search-wrapper i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 16px 25px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 18px 25px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .student-flex { display: flex; align-items: center; gap: 12px; }
        .avatar-circle { width: 35px; height: 35px; background: #ddd6fe; color: #5b21b6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
        
        .btn-print { background: #1e293b; color: white; border: none; padding: 12px 20px; border-radius: 14px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-excel { background: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 14px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-excel:hover { background: #059669; }

        .badge-verified { background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 10px; font-weight: 700; font-size: 11px; display: inline-flex; align-items: center; gap: 6px; }

        /* Hide Mobile UI on Desktop */
        .mobile-header, .mobile-bottom-nav, .mobile-archive-cards, .mobile-search-card, .chips-container, .phonepe-modal-overlay { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full Native UI + Advanced Features) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; }
            .sidebar, .page-header, .stats-grid, .card { display: none !important; }

            .main-content {
                margin-left: 0 !important;
                padding: 75px 12px 85px 12px !important;
                width: 100% !important;
            }

            /* PhonePe Purple App Header Bar */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 65px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 12px; }
            .mobile-back-btn {
                color: #ffffff;
                font-size: 20px;
                text-decoration: none;
                width: 38px; height: 38px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 18px !important; font-weight: 700; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 12px !important; display: block; }

            .mobile-header-right { display: flex; align-items: center; gap: 8px; }
            .mobile-count-pill {
                background: rgba(255,255,255,0.2);
                color: #ffffff;
                font-size: 12px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                border: 1px solid rgba(255,255,255,0.3);
            }

            /* Live Mobile Search Box */
            .mobile-search-card {
                display: block !important;
                background: #ffffff;
                border-radius: 16px;
                padding: 12px 16px;
                margin-bottom: 10px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            }
            .mobile-search-inner { display: flex; align-items: center; gap: 10px; }
            .mobile-search-inner i { color: var(--phonepe-purple); font-size: 16px; }
            .mobile-search-inner input { border: none; outline: none; width: 100%; font-size: 15px; font-family: 'Poppins', sans-serif; color: #0f172a; }

            /* NEW FEATURE 1: Category Filter Chips Bar */
            .chips-container {
                display: flex !important;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 10px;
                margin-bottom: 8px;
                scrollbar-width: none;
            }
            .chips-container::-webkit-scrollbar { display: none; }
            .chip-btn {
                background: #ffffff;
                border: 1px solid #cbd5e1;
                color: #475569;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 12.5px;
                font-weight: 600;
                white-space: nowrap;
                cursor: pointer;
                transition: 0.2s;
            }
            .chip-btn.active {
                background: var(--phonepe-purple);
                color: #ffffff;
                border-color: var(--phonepe-purple);
                box-shadow: 0 3px 8px rgba(95, 37, 159, 0.25);
            }

            /* PhonePe Transaction Cards List */
            .mobile-archive-cards {
                display: flex !important;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-archive-card {
                background: #ffffff;
                border-radius: 18px;
                padding: 16px;
                box-shadow: 0 3px 12px rgba(0,0,0,0.03);
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-card-top { display: flex; align-items: center; gap: 14px; }
            .phonepe-avatar {
                width: 52px; height: 52px;
                background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
                color: var(--phonepe-purple);
                border-radius: 16px;
                display: flex; align-items: center; justify-content: center;
                font-size: 22px; font-weight: 800; flex-shrink: 0;
            }

            .phonepe-info { flex-grow: 1; }
            .phonepe-info h4 { font-size: 18px !important; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
            .phonepe-info p { font-size: 13.5px !important; color: #64748b; margin: 0; font-weight: 500; }

            .phonepe-card-mid {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8fafc;
                padding: 10px 14px;
                border-radius: 12px;
            }

            .phonepe-doc-pill { background: #e0e7ff; color: #3730a3; font-weight: 700; font-size: 13px !important; padding: 4px 10px; border-radius: 8px; }
            .phonepe-status-verified { color: var(--success); font-weight: 700; font-size: 13px !important; display: flex; align-items: center; gap: 5px; }

            /* Card Actions Bar */
            .phonepe-card-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-top: 1px dashed #e2e8f0;
                padding-top: 10px;
            }
            .btn-quick-view {
                background: #f1f5f9;
                color: var(--phonepe-purple);
                border: none;
                padding: 6px 12px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
            }
            .btn-wa-share {
                background: #dcfce7;
                color: #15803d;
                border: none;
                padding: 6px 12px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 700;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 62px;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                z-index: 998;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 11px !important; font-weight: 500; width: 25%; }
            .phonepe-nav-item i { font-size: 19px; margin-bottom: 2px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 700; }

            /* NEW FEATURE 2: PhonePe Bottom Sheet Modal */
            .phonepe-modal-overlay {
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(15, 23, 42, 0.6);
                z-index: 10000;
                display: none;
                align-items: flex-end;
            }
            .phonepe-modal-sheet {
                background: #ffffff;
                width: 100%;
                border-radius: 24px 24px 0 0;
                padding: 24px 20px 30px;
                animation: slideUp 0.3s ease-out;
            }
            @keyframes slideUp {
                from { transform: translateY(100%); }
                to { transform: translateY(0); }
            }
            .sheet-handle { width: 40px; height: 5px; background: #cbd5e1; border-radius: 10px; margin: 0 auto 15px; }
            .modal-detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
            .modal-detail-row span { color: #64748b; }
            .modal-detail-row strong { color: #0f172a; font-weight: 600; }
        }

        @media print {
            .sidebar, .no-print, .search-wrapper, .btn-print, .btn-excel, .mobile-header, .mobile-bottom-nav, .mobile-search-card, .chips-container, .phonepe-card-actions { display: none !important; }
            .main-content { padding: 0 !important; margin-left: 0 !important; width: 100% !important; }
            .card { border: none; box-shadow: none; display: block !important; }
            body { background: white; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe Top App Header (Mobile Only) -->
<div class="mobile-header no-print">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>दस्तावेज़ पुरालेख</h3>
            <small>SMART CMS PRO Archive</small>
        </div>
    </div>
    <div class="mobile-header-right">
        <div class="mobile-count-pill" id="mobileCounterPill"><?= $total_issued; ?> Docs</div>
        <button onclick="exportCSV()" class="mobile-back-btn" title="Export Excel" style="background: rgba(255,255,255,0.2); border:none; cursor:pointer;">
            <i class="fa-solid fa-file-excel"></i>
        </button>
    </div>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<div class="sidebar no-print">
    <div class="logo-area">
        <i class="fa-solid fa-graduation-cap"></i> CMS PRO
    </div>
    <a href="admin_dashboard.php" class="nav-link"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="students.php" class="nav-link"><i class="fa-solid fa-users"></i> Students</a>
    <a href="verify.php" class="nav-link"><i class="fa-solid fa-id-card"></i> Verify Requests</a>
    <a href="archive.php" class="nav-link active"><i class="fa-solid fa-box-archive"></i> Archive</a>
    <a href="logout.php" class="nav-link" style="margin-top: 50px; color: #f87171;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN CONTENT WRAPPER -->
<div class="main-content">

    <!-- 🖥️ Desktop Header -->
    <div class="page-header no-print">
        <div>
            <h2 style="font-size: 24px; font-weight: 800;">दस्तावेज़ पुरालेख (Archive)</h2>
            <p style="color: var(--text-muted); font-size: 14px;">सभी सत्यापित और जारी किए गए दस्तावेजों की सूची</p>
        </div>
        <div class="action-header-btns">
            <button onclick="exportCSV()" class="btn-excel">
                <i class="fa-solid fa-file-excel"></i> Excel डाउनलोड करें
            </button>
            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-print"></i> रिपोर्ट प्रिंट करें
            </button>
        </div>
    </div>

    <!-- 🖥️ Desktop Stats Grid -->
    <div class="stats-grid no-print">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <div>
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 600;">कुल जारी दस्तावेज</p>
                <h3 style="font-size: 20px;" id="desktopTotalCount"><?php echo $total_issued; ?></h3>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #ecfdf5; color: var(--success);"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div>
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 600;">अंतिम अपडेट</p>
                <h3 style="font-size: 16px;">आज, <?php echo date('d M, Y'); ?></h3>
            </div>
        </div>
    </div>

    <!-- 📱 Mobile Live Search Box -->
    <div class="mobile-search-card no-print">
        <div class="mobile-search-inner">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="mobileSearchInput" onkeyup="filterData()" placeholder="नाम, रोल नंबर या दस्तावेज़ खोजें...">
        </div>
    </div>

    <!-- 📱 FEATURE 1: Category Filter Chips (Mobile Only) -->
    <div class="chips-container no-print">
        <button class="chip-btn active" onclick="filterByChip('ALL', this)">सभी (All)</button>
        <?php foreach($doc_types as $type): ?>
            <button class="chip-btn" onclick="filterByChip('<?php echo htmlspecialchars($type); ?>', this)">
                <?php echo htmlspecialchars($type); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- 🖥️ DESKTOP CARD & TABLE VIEW -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 16px; font-weight: 700;">इतिहास लॉग (History Log)</h3>
            <div class="search-wrapper no-print">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="filterData()" placeholder="नाम, रोल नंबर या कोर्स खोजें...">
            </div>
        </div>

        <table id="archiveTable">
            <thead>
                <tr>
                    <th>छात्र का विवरण</th>
                    <th>कोर्स / रोल नंबर</th>
                    <th>दस्तावेज़ प्रकार</th>
                    <th>जारी तिथि</th>
                    <th>स्थिति</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($rows) > 0): ?>
                    <?php foreach($rows as $row): 
                        $issueDate = (!empty($row['request_date'])) ? date('d M, Y', strtotime($row['request_date'])) : date('d M, Y');
                        $firstLetter = substr($row['name'], 0, 1);
                    ?>
                    <tr data-doctype="<?php echo htmlspecialchars($row['doc_type']); ?>">
                        <td>
                            <div class="student-flex">
                                <div class="avatar-circle"><?php echo strtoupper($firstLetter); ?></div>
                                <div class="stu-name" style="font-weight: 700;"><?php echo htmlspecialchars($row['name']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($row['course']); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);">ID: <?php echo htmlspecialchars($row['roll_no']); ?></div>
                        </td>
                        <td>
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-file-lines" style="color: var(--primary);"></i>
                                <?php echo htmlspecialchars($row['doc_type']); ?>
                            </span>
                        </td>
                        <td style="color: var(--text-muted);"><?php echo $issueDate; ?></td>
                        <td>
                            <span class="badge-verified">
                                <i class="fa-solid fa-check-double"></i> <?php echo strtoupper($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px;">
                            <p style="color: var(--text-muted); font-weight: 500;">कोई डेटा उपलब्ध नहीं है</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 📱 MOBILE PHONEPE CARDS VIEW -->
    <div class="mobile-archive-cards no-print" id="mobileCardsList">
        <?php if(count($rows) > 0): ?>
            <?php foreach($rows as $row): 
                $issueDate = (!empty($row['request_date'])) ? date('d M, Y', strtotime($row['request_date'])) : date('d M, Y');
                $firstLetter = substr($row['name'], 0, 1);
                $phone = !empty($row['phone']) ? $row['phone'] : '';
                $waMessage = rawurlencode("नमस्ते " . $row['name'] . ", आपका " . $row['doc_type'] . " सफलतापूर्वक जारी (Issued) कर दिया गया है।");
            ?>
            <div class="phonepe-archive-card" data-doctype="<?php echo htmlspecialchars($row['doc_type']); ?>">
                <div class="phonepe-card-top">
                    <div class="phonepe-avatar"><?php echo strtoupper($firstLetter); ?></div>
                    <div class="phonepe-info">
                        <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                        <p>Roll: #<?php echo htmlspecialchars($row['roll_no']); ?> • <?php echo htmlspecialchars($row['course']); ?></p>
                    </div>
                </div>

                <div class="phonepe-card-mid">
                    <span class="phonepe-doc-pill">
                        <i class="fa-solid fa-file-lines me-1"></i> <?php echo htmlspecialchars($row['doc_type']); ?>
                    </span>
                    <span class="phonepe-status-verified">
                        <i class="fa-solid fa-circle-check"></i> <?php echo strtoupper($row['status']); ?>
                    </span>
                </div>

                <!-- Action Bar Inside Card -->
                <div class="phonepe-card-actions">
                    <button class="btn-quick-view" onclick="openBottomSheet('<?php echo htmlspecialchars($row['name']); ?>', '<?php echo htmlspecialchars($row['roll_no']); ?>', '<?php echo htmlspecialchars($row['course']); ?>', '<?php echo htmlspecialchars($row['doc_type']); ?>', '<?php echo $issueDate; ?>', '<?php echo htmlspecialchars($row['status']); ?>')">
                        <i class="fa-solid fa-eye me-1"></i> विवरण देखें
                    </button>
                    
                    <?php if(!empty($phone)): ?>
                        <a href="https://wa.me/91<?php echo $phone; ?>?text=<?php echo $waMessage; ?>" target="_blank" class="btn-wa-share">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center; padding:35px 15px; background:white; border-radius:18px;">
                <p style="font-weight: 600; color: #64748b;">कोई रिकॉर्ड नहीं मिला।</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- 📱 PhonePe Bottom Navigation Bar (Mobile Only) -->
<div class="mobile-bottom-nav no-print">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>होम</span>
    </a>
    <a href="students.php" class="phonepe-nav-item">
        <i class="fa fa-user-graduate"></i>
        <span>विद्यार्थी</span>
    </a>
    <a href="archive.php" class="phonepe-nav-item active">
        <i class="fa-solid fa-box-archive"></i>
        <span>पुरालेख</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

<!-- 📱 FEATURE 2: PhonePe Quick Bottom Sheet Modal -->
<div class="phonepe-modal-overlay" id="phonepeModal" onclick="closeBottomSheet(event)">
    <div class="phonepe-modal-sheet" onclick="event.stopPropagation()">
        <div class="sheet-handle"></div>
        <h3 style="font-size: 18px; font-weight: 700; color: var(--phonepe-purple); margin-bottom: 15px;">
            <i class="fa-solid fa-circle-info me-2"></i> दस्तावेज़ विवरण
        </h3>

        <div class="modal-detail-row"><span>छात्र का नाम:</span> <strong id="mName">-</strong></div>
        <div class="modal-detail-row"><span>रोल नंबर:</span> <strong id="mRoll">-</strong></div>
        <div class="modal-detail-row"><span>कोर्स:</span> <strong id="mCourse">-</strong></div>
        <div class="modal-detail-row"><span>दस्तावेज़ का प्रकार:</span> <strong id="mDocType" style="color: var(--primary);">-</strong></div>
        <div class="modal-detail-row"><span>जारी तिथि:</span> <strong id="mDate">-</strong></div>
        <div class="modal-detail-row"><span>स्थिति (Status):</span> <strong id="mStatus" style="color: var(--success);">-</strong></div>

        <button onclick="closeBottomSheet()" style="width: 100%; margin-top: 20px; background: var(--phonepe-purple); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer;">
            बंद करें (Close)
        </button>
    </div>
</div>

<!-- JavaScript Logic -->
<script>
let selectedCategory = "ALL";

// Search & Filter Combined Logic
function filterData() {
    let desktopInput = document.getElementById("searchInput");
    let mobileInput = document.getElementById("mobileSearchInput");
    
    let query = "";
    if (mobileInput && mobileInput.offsetParent !== null) {
        query = mobileInput.value.toUpperCase();
    } else if (desktopInput) {
        query = desktopInput.value.toUpperCase();
    }

    let visibleCount = 0;

    // Filter Desktop Table Rows
    let rows = document.querySelectorAll("#archiveTable tbody tr");
    rows.forEach(tr => {
        let text = tr.textContent || tr.innerText;
        let docType = tr.getAttribute("data-doctype") || "";
        
        let matchesQuery = text.toUpperCase().indexOf(query) > -1;
        let matchesCategory = (selectedCategory === "ALL" || docType === selectedCategory);

        if (matchesQuery && matchesCategory) {
            tr.style.display = "";
            visibleCount++;
        } else {
            tr.style.display = "none";
        }
    });

    // Filter Mobile Cards
    let cards = document.querySelectorAll(".phonepe-archive-card");
    let mobileVisibleCount = 0;
    cards.forEach(card => {
        let text = card.textContent || card.innerText;
        let docType = card.getAttribute("data-doctype") || "";

        let matchesQuery = text.toUpperCase().indexOf(query) > -1;
        let matchesCategory = (selectedCategory === "ALL" || docType === selectedCategory);

        if (matchesQuery && matchesCategory) {
            card.style.display = "flex";
            mobileVisibleCount++;
        } else {
            card.style.display = "none";
        }
    });

    // Update Counter Badges
    let finalCount = (cards.length > 0) ? mobileVisibleCount : visibleCount;
    document.getElementById("mobileCounterPill").innerText = finalCount + " Docs";
    document.getElementById("desktopTotalCount").innerText = finalCount;
}

// Category Chip Click Logic
function filterByChip(category, btnElement) {
    selectedCategory = category;
    
    // Update Active Chip Style
    let chips = document.querySelectorAll(".chip-btn");
    chips.forEach(c => c.classList.remove("active"));
    btnElement.classList.add("active");

    filterData();
}

// Bottom Sheet Open/Close
function openBottomSheet(name, roll, course, docType, date, status) {
    document.getElementById("mName").innerText = name;
    document.getElementById("mRoll").innerText = roll;
    document.getElementById("mCourse").innerText = course;
    document.getElementById("mDocType").innerText = docType;
    document.getElementById("mDate").innerText = date;
    document.getElementById("mStatus").innerText = status;

    document.getElementById("phonepeModal").style.display = "flex";
}

function closeBottomSheet(e) {
    document.getElementById("phonepeModal").style.display = "none";
}

// Export Table Data to Excel/CSV (Zero Library Needed)
function exportCSV() {
    let table = document.getElementById("archiveTable");
    let rows = table.querySelectorAll("tr");
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        if (rows[i].style.display === "none") continue; // Skip filtered out rows
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) {
            let cleanText = cols[j].innerText.replace(/\n/g, ' ').replace(/,/g, '');
            row.push('"' + cleanText + '"');
        }
        csv.push(row.join(","));
    }

    let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    let downloadLink = document.createElement("a");
    downloadLink.download = "issued_documents_archive.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

</body>
</html>