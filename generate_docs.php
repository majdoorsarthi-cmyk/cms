<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php';

if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// --- PAGINATION LOGIC (For 10 Lakh+ Data) ---[cite: 13]
$limit = 15; // Ek page par kitne record dikhane hain[cite: 13]
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- SEARCH & FILTER LOGIC ---[cite: 13]
$where_clauses = ["dr.status = 'Verified'"]; 

if(!empty($_GET['doc_type'])) {
    $doc_type = mysqli_real_escape_string($conn, $_GET['doc_type']);
    $where_clauses[] = "dr.doc_type = '$doc_type'";
}

if(!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clauses[] = "(u.name LIKE '%$search%' OR s.roll_no LIKE '%$search%')";
}

$where_sql = implode(' AND ', $where_clauses);

// --- TOTAL COUNT (For Pagination) ---[cite: 13]
$count_query = "SELECT COUNT(*) as total FROM service_requests dr 
                JOIN students s ON dr.student_id = s.id 
                JOIN users u ON s.user_id = u.id WHERE $where_sql";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// --- MAIN QUERY WITH LIMIT ---[cite: 13]
$query = "SELECT dr.*, u.name, s.roll_no, s.course 
          FROM service_requests dr
          JOIN students s ON dr.student_id = s.id
          JOIN users u ON s.user_id = u.id
          WHERE $where_sql
          ORDER BY dr.id DESC LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Fetch all rows into array for both Desktop & Mobile Rendering
$rows = [];
if($result && mysqli_num_rows($result) > 0) {
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
    <title>Admin Dashboard | Document Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --primary: #4361ee;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --bg: #f8fafc;
            --white: #ffffff;
            --text-main: #334155;

            /* PhonePe App Theme Variables */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); display: flex; color: var(--text-main); font-family: 'Poppins', sans-serif; min-height: 100vh; }

        /* 🖥️ DESKTOP STYLES (ORIGINAL UNTOUCHED) */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; color: white; padding: 20px; }
        .logo { font-size: 20px; font-weight: 700; margin-bottom: 40px; text-align: center; color: var(--primary); border-bottom: 1px solid #334155; padding-bottom: 15px; }
        .nav-links { list-style: none; }
        .nav-links li { margin: 8px 0; }
        .nav-links a { color: #cbd5e1; text-decoration: none; display: flex; align-items: center; padding: 12px; border-radius: 8px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: var(--primary); color: white; }
        .nav-links i { margin-right: 12px; width: 20px; }

        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }

        .filter-section { background: var(--white); padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 12px; font-weight: 600; color: #64748b; }
        .filter-section input, .filter-section select { padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; min-width: 200px; outline: none; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: 500; }

        .table-container { background: var(--white); border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f1f5f9; }
        th { padding: 15px; font-size: 13px; font-weight: 600; color: #475569; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        tr:hover { background: #f8fafc; }

        .actions { display: flex; gap: 10px; }
        .btn-icon { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.2s; border: none; cursor: pointer; }
        .btn-p { background: #e0e7ff; color: var(--primary); }
        .btn-e { background: #fef3c7; color: var(--warning); }
        .btn-d { background: #fee2e2; color: var(--danger); }
        .btn-icon:hover { transform: translateY(-2px); filter: brightness(0.9); }

        .pagination { margin-top: 20px; display: flex; justify-content: center; gap: 5px; }
        .pagination a { padding: 8px 16px; border: 1px solid #e2e8f0; background: white; text-decoration: none; border-radius: 6px; color: var(--text-main); transition: 0.3s; }
        .pagination a.active { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination a:hover:not(.active) { background: #f1f5f9; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-doc { background: #dcfce7; color: #166534; }

        /* Hide Mobile Elements on Desktop */
        .mobile-header, .mobile-bottom-nav, .mobile-doc-list, .mobile-search-card { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full Native App UI + Larger Fonts) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; }
            .sidebar, .top-bar, .filter-section, .table-container { display: none !important; }

            /* PhonePe Top App Header */
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

            .mobile-count-pill {
                background: rgba(255,255,255,0.2);
                color: #ffffff;
                font-size: 12.5px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                border: 1px solid rgba(255,255,255,0.3);
            }

            .main-content {
                margin-left: 0 !important;
                padding: 78px 12px 85px 12px !important;
                width: 100% !important;
            }

            /* Mobile Filter Search Card */
            .mobile-search-card {
                display: block !important;
                background: #ffffff;
                border-radius: 18px;
                padding: 14px;
                margin-bottom: 15px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            }

            .mobile-search-card form {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .mobile-input-group {
                display: flex;
                gap: 8px;
            }

            .mobile-input-group input, .mobile-input-group select {
                padding: 10px 14px;
                font-size: 14px;
                border: 1.5px solid #cbd5e1;
                border-radius: 12px;
                outline: none;
                width: 100%;
                font-family: 'Poppins', sans-serif;
            }

            .mobile-input-group input:focus, .mobile-input-group select:focus {
                border-color: var(--phonepe-purple);
            }

            .mobile-search-btn {
                background: var(--phonepe-purple);
                color: #ffffff;
                border: none;
                padding: 11px;
                border-radius: 12px;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            /* PhonePe Transaction Cards List */
            .mobile-doc-list {
                display: flex !important;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-card {
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
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
            }

            .phonepe-icon-box {
                width: 44px; height: 44px;
                background: #f3e8ff;
                color: var(--phonepe-purple);
                border-radius: 14px;
                display: flex; align-items: center; justify-content: center;
                font-size: 20px;
                flex-shrink: 0;
            }

            .phonepe-details { flex-grow: 1; }

            /* Larger Mobile Typography */
            .phonepe-details h4 {
                font-size: 17.5px !important; /* Larger Student Name */
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2px;
            }

            .phonepe-details p {
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

            /* Action Buttons Bar */
            .phonepe-card-actions {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr;
                gap: 8px;
                margin-top: 4px;
            }

            .phonepe-act-btn {
                padding: 10px 12px !important;
                border-radius: 12px !important;
                font-size: 14.5px !important; /* Larger Font */
                font-weight: 700 !important;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                border: none;
            }

            .phonepe-act-print {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                color: #ffffff !important;
                box-shadow: 0 3px 10px rgba(95, 37, 159, 0.25) !important;
            }

            .phonepe-act-edit {
                background: #fef3c7 !important;
                color: #d97706 !important;
                border: 1px solid #fde68a !important;
            }

            .phonepe-act-delete {
                background: #fee2e2 !important;
                color: #dc2626 !important;
                border: 1px solid #fecaca !important;
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
    </style>
</head>
<body>

<!-- 📱 PhonePe App Header (Mobile Only) -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>दस्तावेज़ जेनेरेटर (Docs)</h3>
            <small>Document Manager</small>
        </div>
    </div>
    <div class="mobile-count-pill">
        <?= number_format($total_rows); ?> Records
    </div>
</div>

<!-- 🖥️ Sidebar (Desktop Only) -->
<aside class="sidebar">
    <div class="logo">CMS PRO PANEL</div>
    <ul class="nav-links">
        <li><a href="admin_dashboard.php"><i class="fa fa-gauge"></i> Dashboard</a></li>
        <li><a href="students.php"><i class="fa fa-user-graduate"></i> Manage Students</a></li>
        <li><a href="generate_docs.php" class="active"><i class="fa fa-file-invoice"></i> Documents</a></li>
        <li><a href="verify.php"><i class="fa fa-check-double"></i> Verification</a></li>
        <li><a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</aside>

<main class="main-content">
    
    <!-- 🖥️ Desktop Top Bar -->
    <div class="top-bar">
        <h2>Document Generator</h2>
        <span>Total Records: <b><?php echo number_format($total_rows); ?></b></span>
    </div>

    <!-- 🖥️ Desktop Filter Section -->
    <form class="filter-section" method="GET">
        <div class="filter-group">
            <label>Search Data</label>
            <input type="text" name="search" placeholder="Name or Roll No..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>
        <div class="filter-group">
            <label>Document Type</label>
            <select name="doc_type">
                <option value="">All Documents</option>
                <option value="Marksheet" <?php if(@$_GET['doc_type']=='Marksheet') echo 'selected'; ?>>Marksheet</option>
                <option value="Certificate" <?php if(@$_GET['doc_type']=='Certificate') echo 'selected'; ?>>Certificate</option>
                <option value="ID Card" <?php if(@$_GET['doc_type']=='ID Card') echo 'selected'; ?>>ID Card</option>
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa fa-filter"></i> Apply</button>
        <a href="generate_docs.php" style="font-size: 13px; color: #64748b; text-decoration: none;">Reset</a>
    </form>

    <!-- 📱 Mobile Filter Search Card -->
    <div class="mobile-search-card">
        <form method="GET">
            <div class="mobile-input-group">
                <input type="text" name="search" placeholder="नाम या रोल नंबर खोजें..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <select name="doc_type">
                    <option value="">सभी दस्तावेज़</option>
                    <option value="Marksheet" <?php if(@$_GET['doc_type']=='Marksheet') echo 'selected'; ?>>Marksheet</option>
                    <option value="Certificate" <?php if(@$_GET['doc_type']=='Certificate') echo 'selected'; ?>>Certificate</option>
                    <option value="ID Card" <?php if(@$_GET['doc_type']=='ID Card') echo 'selected'; ?>>ID Card</option>
                </select>
            </div>
            <button type="submit" class="mobile-search-btn">
                <i class="fa fa-magnifying-glass"></i> खोजें (Apply Filter)
            </button>
        </form>
    </div>

    <!-- 🖥️ DESKTOP TABLE VIEW -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Information</th>
                    <th>Document</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($rows) > 0): ?>
                    <?php foreach($rows as $row): ?>
                    <tr>
                        <td style="color: #94a3b8;">#<?php echo $row['id']; ?></td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div style="font-size: 12px; color: #64748b;">Roll: <?php echo $row['roll_no']; ?> | <?php echo $row['course']; ?></div>
                        </td>
                        <td><span class="badge badge-doc"><?php echo $row['doc_type']; ?></span></td>
                        <td><span style="color: var(--success); font-weight: 500;"><i class="fa fa-circle-check"></i> Verified</span></td>
                        <td class="actions">
                            <a href="print_<?php echo strtolower(str_replace(' ', '_', $row['doc_type'])); ?>.php?id=<?php echo $row['student_id']; ?>" target="_blank" class="btn-icon btn-p" title="Print Document">
                                <i class="fa fa-print"></i>
                            </a>
                            <a href="edit_request.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-e" title="Edit Details">
                                <i class="fa fa-pen-to-square"></i>
                            </a>
                            <form action="delete_process.php" method="POST" onsubmit="return confirm('Lifetime ke liye delete ho jayega. Confirm?');" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-icon btn-d" title="Delete Permanent">
                                    <i class="fa fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:40px;">No Verified Records Found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 📱 MOBILE PHONEPE CARDS VIEW -->
    <div class="mobile-doc-list">
        <?php if(count($rows) > 0): ?>
            <?php foreach($rows as $row): ?>
            <div class="phonepe-card">
                <div class="phonepe-card-top">
                    <div class="phonepe-icon-box">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div class="phonepe-details">
                        <h4><?= htmlspecialchars($row['name']); ?></h4>
                        <p>Roll: #<?= $row['roll_no']; ?> • <?= $row['course']; ?></p>
                    </div>
                </div>

                <div class="phonepe-card-mid">
                    <span class="phonepe-doc-pill"><i class="fa-solid fa-certificate me-1"></i> <?= $row['doc_type']; ?></span>
                    <span class="phonepe-status-verified"><i class="fa-solid fa-circle-check"></i> Verified</span>
                </div>

                <div class="phonepe-card-actions">
                    <a href="print_<?php echo strtolower(str_replace(' ', '_', $row['doc_type'])); ?>.php?id=<?php echo $row['student_id']; ?>" target="_blank" class="phonepe-act-btn phonepe-act-print">
                        <i class="fa fa-print"></i> Print
                    </a>
                    <a href="edit_request.php?id=<?php echo $row['id']; ?>" class="phonepe-act-btn phonepe-act-edit">
                        <i class="fa fa-pen-to-square"></i> Edit
                    </a>
                    <form action="delete_process.php" method="POST" onsubmit="return confirm('Lifetime ke liye delete ho jayega. Confirm?');" style="display:inline; width:100%;">
                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="phonepe-act-btn phonepe-act-delete" style="width:100%;">
                            <i class="fa fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align:center; padding:30px; background:white; border-radius:16px; border:1px solid #e2e8f0;">
                <i class="fa-solid fa-folder-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px;"></i>
                <p style="font-weight: 600; color: #64748b;">कोई सत्यापित रिकॉर्ड नहीं मिला।</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&search=<?php echo @$_GET['search']; ?>&doc_type=<?php echo @$_GET['doc_type']; ?>">Prev</a>
        <?php endif; ?>

        <?php 
        for($i = max(1, $page-2); $i <= min($page+2, $total_pages); $i++): 
        ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo @$_GET['search']; ?>&doc_type=<?php echo @$_GET['doc_type']; ?>" class="<?php if($i==$page) echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&search=<?php echo @$_GET['search']; ?>&doc_type=<?php echo @$_GET['doc_type']; ?>">Next</a>
        <?php endif; ?>
    </div>
</main>

<!-- 📱 PhonePe Bottom Navigation Bar (Mobile Only) -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>होम</span>
    </a>
    <a href="students.php" class="phonepe-nav-item">
        <i class="fa fa-user-graduate"></i>
        <span>विद्यार्थी</span>
    </a>
    <a href="generate_docs.php" class="phonepe-nav-item active">
        <i class="fa fa-file-invoice"></i>
        <span>दस्तावेज़</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

</body>
</html>