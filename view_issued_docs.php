<?php
/**
 * SMART CMS PRO - MODERN ISSUED DOCUMENTS ARCHIVE
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
$query = "SELECT dr.*, u.name, s.roll_no, s.course 
          FROM service_requests dr
          JOIN students s ON dr.student_id = s.id
          JOIN users u ON s.user_id = u.id
          WHERE dr.status IN ('Verified', 'Approved', 'Issued')
          ORDER BY dr.id DESC";

$result = mysqli_query($conn, $query);
$total_issued = ($result) ? mysqli_num_rows($result) : 0;

// Fetch all rows into an array for both Desktop & Mobile rendering
$rows = [];
if($result && $total_issued > 0) {
    while($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Archive | SMART CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* 🖥️ DESKTOP SIDEBAR & LAYOUT (ORIGINAL UNTOUCHED) */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 20px; flex-shrink: 0; transition: 0.3s; height: 100vh; position: fixed; top: 0; left: 0; z-index: 100; }
        .logo-area { padding: 10px 10px 30px; border-bottom: 1px solid #1e293b; margin-bottom: 20px; font-weight: 800; font-size: 20px; color: #818cf8; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: #94a3b8; border-radius: 12px; margin-bottom: 8px; transition: 0.3s; font-size: 14px; }
        .nav-link:hover, .nav-link.active { background: var(--primary); color: white; }
        .nav-link i { width: 20px; }

        /* Main Content Desktop */
        .main-content { flex-grow: 1; padding: 30px; margin-left: 260px; width: calc(100% - 260px); }
        
        /* Stats Section */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--white); padding: 20px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; border: 1px solid #e2e8f0; }
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; background: #eef2ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px; }

        /* Table Design Desktop */
        .card { background: var(--white); border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        
        .search-wrapper { position: relative; }
        .search-wrapper input { padding: 12px 15px 12px 45px; border-radius: 14px; border: 1.5px solid #e2e8f0; width: 320px; outline: none; transition: 0.3s; font-size: 14px; }
        .search-wrapper i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-wrapper input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 16px 25px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 18px 25px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .student-flex { display: flex; align-items: center; gap: 12px; }
        .avatar-circle { width: 35px; height: 35px; background: #ddd6fe; color: #5b21b6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
        
        .btn-print { background: #1e293b; color: white; border: none; padding: 12px 24px; border-radius: 14px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-print:hover { background: #000; transform: translateY(-2px); }

        .badge-verified { background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 10px; font-weight: 700; font-size: 11px; display: inline-flex; align-items: center; gap: 6px; }

        /* Hide Mobile Elements on Desktop Screen */
        .mobile-header, .mobile-bottom-nav, .mobile-archive-cards, .mobile-search-card { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full Native UI + Bigger Fonts) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; }
            .sidebar, .page-header, .stats-grid, .card { display: none !important; }

            .main-content {
                margin-left: 0 !important;
                padding: 78px 12px 85px 12px !important;
                width: 100% !important;
            }

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
                font-size: 12.5px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                border: 1px solid rgba(255,255,255,0.3);
            }

            .mobile-print-icon {
                color: white;
                background: rgba(255,255,255,0.2);
                width: 38px; height: 38px;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 16px; border: none; cursor: pointer;
            }

            /* Live Mobile Search Box (No Reload) */
            .mobile-search-card {
                display: block !important;
                background: #ffffff;
                border-radius: 16px;
                padding: 12px 16px;
                margin-bottom: 14px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            }
            .mobile-search-inner {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .mobile-search-inner i { color: var(--phonepe-purple); font-size: 16px; }
            .mobile-search-inner input {
                border: none;
                outline: none;
                width: 100%;
                font-size: 15px;
                font-family: 'Poppins', sans-serif;
                color: #0f172a;
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

            .phonepe-card-top {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .phonepe-avatar {
                width: 52px; height: 52px;
                background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
                color: var(--phonepe-purple);
                border-radius: 16px;
                display: flex; align-items: center; justify-content: center;
                font-size: 22px;
                font-weight: 800;
                flex-shrink: 0;
            }

            .phonepe-info { flex-grow: 1; }

            /* Larger Readable Mobile Fonts */
            .phonepe-info h4 {
                font-size: 18.5px !important; /* Bold Large Name */
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2px;
            }

            .phonepe-info p {
                font-size: 14px !important; /* Larger Roll & Course */
                color: #64748b;
                margin: 0;
                font-weight: 500;
            }

            .phonepe-card-mid {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8fafc;
                padding: 10px 14px;
                border-radius: 12px;
            }

            .phonepe-doc-pill {
                background: #e0e7ff;
                color: #3730a3;
                font-weight: 700;
                font-size: 13.5px !important;
                padding: 5px 12px;
                border-radius: 8px;
            }

            .phonepe-status-verified {
                color: var(--success);
                font-weight: 700;
                font-size: 13.5px !important;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .phonepe-date-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px !important;
                color: #64748b;
                border-top: 1px dashed #e2e8f0;
                padding-top: 8px;
                margin-top: 2px;
            }

            /* Bottom PhonePe Nav Bar */
            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 62px;
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
                color: #64748b;
                font-size: 11px !important;
                font-weight: 500;
                width: 25%;
            }

            .phonepe-nav-item i { font-size: 19px; margin-bottom: 2px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 700; }
        }

        /* Print Media */
        @media print {
            .sidebar, .no-print, .search-wrapper, .btn-print, .mobile-header, .mobile-bottom-nav, .mobile-search-card { display: none !important; }
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
            <h3>दस्तावेज़ पुरालेख (Archive)</h3>
            <small>SMART CMS PRO Archive</small>
        </div>
    </div>
    <div class="mobile-header-right">
        <div class="mobile-count-pill"><?= $total_issued; ?> Docs</div>
        <button onclick="window.print()" class="mobile-print-icon" title="Print Report">
            <i class="fa-solid fa-print"></i>
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
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-file-export"></i> रिपोर्ट प्रिंट करें
        </button>
    </div>

    <!-- 🖥️ Desktop Stats Grid -->
    <div class="stats-grid no-print">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <div>
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 600;">कुल जारी दस्तावेज</p>
                <h3 style="font-size: 20px;"><?php echo $total_issued; ?></h3>
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

    <!-- 📱 Mobile Search Box (Live Search - No Page Reload) -->
    <div class="mobile-search-card no-print">
        <div class="mobile-search-inner">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="mobileSearchInput" onkeyup="searchTable()" placeholder="नाम, रोल नंबर या दस्तावेज़ खोजें...">
        </div>
    </div>

    <!-- 🖥️ DESKTOP CARD & TABLE VIEW -->
    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 16px; font-weight: 700;">इतिहास लॉग (History Log)</h3>
            <div class="search-wrapper no-print">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="नाम, रोल नंबर या कोर्स खोजें...">
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
                        $issueDate = (!empty($row['request_date'])) ? $row['request_date'] : date('Y-m-d');
                        $firstLetter = substr($row['name'], 0, 1);
                    ?>
                    <tr>
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
                        <td style="color: var(--text-muted);">
                            <?php echo date('d M, Y', strtotime($issueDate)); ?>
                        </td>
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
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.2; margin-bottom: 15px;">
                            <p style="color: var(--text-muted); font-weight: 500;">कोई डेटा उपलब्ध नहीं है</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 📱 MOBILE PHONEPE CARDS VIEW -->
    <div class="mobile-archive-cards no-print">
        <?php if(count($rows) > 0): ?>
            <?php foreach($rows as $row): 
                $issueDate = (!empty($row['request_date'])) ? $row['request_date'] : date('Y-m-d');
                $firstLetter = substr($row['name'], 0, 1);
            ?>
            <div class="phonepe-archive-card">
                <div class="phonepe-card-top">
                    <div class="phonepe-avatar">
                        <?php echo strtoupper($firstLetter); ?>
                    </div>
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

                <div class="phonepe-date-bar">
                    <span><i class="fa-regular fa-calendar-check me-1"></i> जारी तिथि:</span>
                    <strong style="color:#334155;"><?php echo date('d M, Y', strtotime($issueDate)); ?></strong>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center; padding:35px 15px; background:white; border-radius:18px; border:1px solid #e2e8f0;">
                <i class="fa-solid fa-box-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px; display:block;"></i>
                <p style="font-weight: 600; color: #64748b;">कोई आर्काइव रिकॉर्ड नहीं मिला।</p>
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

<!-- Live Search JavaScript (Works Instantaneously without Reloading) -->
<script>
function searchTable() {
    let desktopInput = document.getElementById("searchInput");
    let mobileInput = document.getElementById("mobileSearchInput");
    
    // Determine active query
    let filter = "";
    if (mobileInput && mobileInput.offsetParent !== null) {
        filter = mobileInput.value.toUpperCase();
    } else if (desktopInput) {
        filter = desktopInput.value.toUpperCase();
    }

    // 1. Search in Desktop Table
    let table = document.getElementById("archiveTable");
    if(table) {
        let tr = table.getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let textContent = tr[i].textContent || tr[i].innerText;
            tr[i].style.display = (textContent.toUpperCase().indexOf(filter) > -1) ? "" : "none";
        }
    }

    // 2. Search in Mobile PhonePe Cards
    let cards = document.getElementsByClassName("phonepe-archive-card");
    for (let i = 0; i < cards.length; i++) {
        let textContent = cards[i].textContent || cards[i].innerText;
        cards[i].style.display = (textContent.toUpperCase().indexOf(filter) > -1) ? "" : "none";
    }
}
</script>

</body>
</html>