<?php 
// 1. Output Buffering Start (Redirect Errors रोकने के लिए)
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Check Admin Login
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

$message = "";

// --- 2. DELETE STUDENT LOGIC (PRG Pattern) ---
if(isset($_GET['delete_id'])){
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Student रिकॉर्ड हटाने का क्वेरी
    $del_query = "DELETE FROM students WHERE id = '$del_id'";
    if(mysqli_query($conn, $del_query)){
        $_SESSION['msg'] = "<div class='success-toast toast-red'>
                               <i class='fa-solid fa-trash-can'></i> <span>Student Record Deleted Successfully!</span>
                           </div>";
    } else {
        $_SESSION['msg'] = "<div class='success-toast toast-red'>
                               <i class='fa-solid fa-circle-exclamation'></i> <span>Delete Failed: " . mysqli_error($conn) . "</span>
                           </div>";
    }
    // PRG Redirect: URL स्वच्छ रखने और रीलोड एरर रोकने के लिए
    header("Location: students_list.php");
    exit();
}

// Retrieve Session Message
if(isset($_SESSION['msg'])){
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// --- 3. SEARCH LOGIC ---
$search = "";
$search_condition = "";
if(isset($_GET['search']) && !empty(trim($_GET['search']))){
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $search_condition = " AND (s.name LIKE '%$search%' OR s.roll_no LIKE '%$search%' OR s.mobile LIKE '%$search%' OR u.name LIKE '%$search%' OR s.course LIKE '%$search%') ";
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>University ERP | Students & Fee Directory</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --bg-body: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.02);
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { margin: 0; background-color: var(--bg-body); color: #1e293b; overflow-x: hidden; min-height: 100vh; }

        /* --- TOAST NOTIFICATIONS --- */
        .success-toast {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .toast-green { background: #f0fdf4; border-left: 6px solid #22c55e; color: #166534; }
        .toast-red { background: #fef2f2; border-left: 6px solid #ef4444; color: #991b1b; }

        /* ---------------- 🖥️ DESKTOP STYLES ---------------- */
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; z-index: 1000; }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; color: var(--primary); font-size: 20px; font-weight: 800; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 18px; color: #64748b; text-decoration: none; border-radius: 12px; margin-bottom: 5px; font-weight: 600; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary-light); color: var(--primary); }

        .top-nav { height: 70px; position: fixed; left: var(--sidebar-width); right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; z-index: 999; }
        .profile-chip { display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 8px 15px; border-radius: 50px; font-size: 13px; font-weight: 700; border: 1px solid #e2e8f0; }

        .main-content { margin-left: var(--sidebar-width); margin-top: 70px; padding: 40px; width: calc(100% - var(--sidebar-width)); }
        .card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: var(--card-shadow); margin-bottom: 25px; }

        .search-box-desktop { display: flex; gap: 12px; margin-bottom: 20px; }
        .search-box-desktop input { flex: 1; padding: 14px 18px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 15px; font-weight: 600; outline: none; }
        .search-box-desktop button { background: var(--primary); color: #fff; border: none; padding: 0 25px; border-radius: 12px; font-weight: 700; cursor: pointer; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 15px; color: #64748b; font-size: 12px; font-weight: 800; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 600; }
        
        .badge-roll { background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-family: monospace; }
        .badge-course { background: #f0fdf4; color: #166534; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 12px; }

        .fee-badge { padding: 5px 12px; border-radius: 8px; font-weight: 800; font-size: 13px; display: inline-block; }
        .fee-paid { background: #dcfce7; color: #15803d; }
        .fee-due { background: #fef2f2; color: #b91c1c; }

        .actions { display: flex; gap: 8px; }
        .btn-act { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.2s; font-size: 14px; }
        .btn-act.edit { color: #d97706; background: #fffbeb; }
        .btn-act.del { color: #ef4444; background: #fef2f2; }
        .btn-act.pay { color: #22c55e; background: #f0fdf4; }
        .btn-act:hover { transform: scale(1.1); }

        .mobile-header, .mobile-bottom-nav, .mobile-student-list, .mobile-search-bar { display: none; }

        /* 📱 ---------------- PHONEPE MOBILE APP VIEW ---------------- */
        @media (max-width: 900px) {
            body { background: var(--phonepe-bg) !important; padding-top: 72px; padding-bottom: 90px; }

            .sidebar, .top-nav, table, .search-box-desktop { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 15px !important; margin-top: 0 !important; }

            /* 🟣 PhonePe Mobile Header */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 70px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { color: #ffffff; font-size: 22px; width: 44px; height: 44px; background: rgba(255,255,255,0.18); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 13px !important; font-weight: 600; display: block; }

            /* 🔍 Mobile Search Box */
            .mobile-search-bar { display: block !important; margin-bottom: 16px; }
            .mobile-search-bar form { display: flex; background: #ffffff; padding: 6px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 2px solid #e2e8f0; }
            .mobile-search-bar input { flex: 1; border: none; padding: 12px 14px; font-size: 16px !important; font-weight: 700; outline: none; background: transparent; }
            .mobile-search-bar button { background: var(--phonepe-purple); color: #fff; border: none; width: 48px; border-radius: 12px; font-size: 18px; display: flex; align-items: center; justify-content: center; cursor: pointer; }

            /* 📱 Student Mobile Cards (PhonePe Large Font Style) */
            .mobile-student-list { display: flex !important; flex-direction: column; gap: 16px; }

            .m-student-card {
                background: #ffffff; padding: 18px; border-radius: 22px;
                box-shadow: 0 4px 18px rgba(0,0,0,0.04); display: flex; flex-direction: column; gap: 14px;
                border: 1px solid #e2e8f0;
            }
            .m-card-top { display: flex; align-items: center; justify-content: space-between; }
            .m-avatar-info { display: flex; align-items: center; gap: 14px; }
            .m-avatar {
                width: 54px; height: 54px; border-radius: 18px;
                background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
                color: var(--primary); display: flex; align-items: center; justify-content: center;
                font-size: 24px; font-weight: 800; flex-shrink: 0;
            }
            .m-details h5 { font-size: 19px !important; font-weight: 800; color: #0f172a; margin: 0 0 3px 0; }
            .m-details p { font-size: 14px !important; font-weight: 700; color: #64748b; margin: 0; }
            
            /* 📊 Fees Dashboard Inside Mobile Card */
            .m-fee-box {
                background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px;
                padding: 12px 14px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; text-align: center;
            }
            .m-fee-item small { font-size: 11px !important; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px; }
            .m-fee-item span { font-size: 15px !important; font-weight: 800; }
            .m-fee-item.total span { color: #334155; }
            .m-fee-item.paid span { color: #16a34a; }
            .m-fee-item.due span { color: #dc2626; }

            /* 🔘 Mobile Quick Actions */
            .m-card-actions { display: flex; gap: 8px; margin-top: 2px; }
            .m-btn {
                flex: 1; padding: 13px; border-radius: 14px; font-size: 14px !important; font-weight: 800;
                text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            }
            .m-btn.pay { background: var(--phonepe-purple); color: #ffffff; }
            .m-btn.call { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
            .m-btn.edit { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
            .m-btn.delete { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; width: 48px; flex: none; }

            /* 📱 PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 74px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 13px !important; font-weight: 700; width: 25%; }
            .phonepe-nav-item i { font-size: 22px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body>

    <!-- 📱 PhonePe Mobile Header -->
    <div class="mobile-header">
        <div class="mobile-header-left">
            <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h3>Students Directory</h3>
                <small>Fees & Admission Portal</small>
            </div>
        </div>
        <div>
            <a href="add_student.php" style="color:#fff; font-size:22px; text-decoration:none;"><i class="fa-solid fa-user-plus"></i></a>
        </div>
    </div>

    <!-- 🖥️ DESKTOP SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-building-columns"></i> UNIVERSITY ERP
        </div>
        <div class="sidebar-menu">
            <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="add_student.php"><i class="fa-solid fa-user-plus"></i> Admission</a>
            <a href="collect_fees.php"><i class="fa-solid fa-receipt"></i> Collect Fees</a>
            <a href="students_list.php" class="active"><i class="fa-solid fa-users"></i> All Students</a>
            <a href="view_issued_docs.php"><i class="fa-solid fa-folder-open"></i> Archives</a>
            <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>

    <!-- 🖥️ DESKTOP TOP NAV -->
    <div class="top-nav">
        <div><h3 style="margin:0; font-size:18px; font-weight:800; color:#1e293b;">Students Directory & Fee Status</h3></div>
        <div class="profile-chip">
            <i class="fa-solid fa-circle-user" style="color:var(--primary); font-size:16px;"></i> Admin Account
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- Toast Notification Message -->
        <?php echo $message; ?>

        <!-- 📱 PhonePe Mobile Search Bar -->
        <div class="mobile-search-bar">
            <form method="GET" action="students_list.php">
                <input type="text" name="search" placeholder="Search name, roll no, DCA..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="card">
            <!-- 🖥️ Desktop Search Form -->
            <form method="GET" action="students_list.php" class="search-box-desktop">
                <input type="text" name="search" placeholder="🔍 Search student by name, roll no, course or mobile..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                <?php if(!empty($search)): ?>
                    <a href="students_list.php" style="background:#f1f5f9; color:#475569; padding:14px 20px; border-radius:12px; text-decoration:none; font-weight:700; display:flex; align-items:center;">Clear</a>
                <?php endif; ?>
            </form>

            <!-- 🖥️ Desktop Table View -->
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Total Fee</th>
                        <th>Paid Fee</th>
                        <th>Due (बाकी)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 📊 Dynamic Calculation Query (Total, Paid, Due)
                    $query = "SELECT s.*, 
                                     COALESCE(u.name, s.name, 'Student') as student_name, 
                                     COALESCE(s.mobile, u.mobile, '') as student_mobile,
                                     COALESCE(s.total_fee, 0) as total_fee,
                                     COALESCE(SUM(f.amount_paid), 0) as total_paid
                              FROM students s 
                              LEFT JOIN users u ON s.user_id = u.id 
                              LEFT JOIN fees f ON s.id = f.student_id
                              WHERE 1=1 {$search_condition} 
                              GROUP BY s.id
                              ORDER BY s.id DESC";
                    
                    $res = mysqli_query($conn, $query);

                    if(!$res) {
                        // Fallback query structure
                        $fallback_query = "SELECT s.*, 
                                                  s.name as student_name, 
                                                  s.mobile as student_mobile,
                                                  COALESCE(s.total_fee, 0) as total_fee,
                                                  COALESCE(SUM(f.amount_paid), 0) as total_paid
                                           FROM students s
                                           LEFT JOIN fees f ON s.id = f.student_id
                                           WHERE 1=1 {$search_condition}
                                           GROUP BY s.id
                                           ORDER BY s.id DESC";
                        $res = mysqli_query($conn, $fallback_query);
                    }

                    if($res && mysqli_num_rows($res) > 0){
                        while($s = mysqli_fetch_assoc($res)){
                            $roll = !empty($s['roll_no']) ? $s['roll_no'] : 'N/A';
                            $course = !empty($s['course']) ? $s['course'] : (!empty($s['class']) ? $s['class'] : 'General');
                            $total = (float)$s['total_fee'];
                            $paid = (float)$s['total_paid'];
                            $due = $total - $paid;
                            if($due < 0) $due = 0;
                            
                            echo "<tr>
                                <td><span class='badge-roll'>#{$roll}</span></td>
                                <td><b style='color:#0f172a; font-size:15px;'>".htmlspecialchars($s['student_name'])."</b></td>
                                <td><span class='badge-course'>".htmlspecialchars($course)."</span></td>
                                <td>₹" . number_format($total) . "</td>
                                <td><span style='color:#16a34a; font-weight:800;'>₹" . number_format($paid) . "</span></td>
                                <td>";
                                    if($due == 0 && $total > 0){
                                        echo "<span class='fee-badge fee-paid'><i class='fa-solid fa-circle-check'></i> Paid</span>";
                                    } else {
                                        echo "<span class='fee-badge fee-due'>₹" . number_format($due) . " Due</span>";
                                    }
                            echo "</td>
                                <td>
                                    <div class='actions'>
                                        <a href='collect_fees.php?student_id={$s['id']}' class='btn-act pay' title='Collect Fee'><i class='fa-solid fa-wallet'></i></a>
                                        <a href='edit_student.php?id={$s['id']}' class='btn-act edit' title='Edit Student'><i class='fa-solid fa-pen-to-square'></i></a>
                                        <a href='students_list.php?delete_id={$s['id']}' class='btn-act del' title='Delete' onclick=\"return confirm('Are you sure you want to delete this student record?')\"><i class='fa-solid fa-trash-can'></i></a>
                                    </div>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding:30px; color:#94a3b8; font-weight:700;'>No students record found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- 📱 PHONEPE MOBILE STUDENT LIST (Cards) -->
        <div class="mobile-student-list">
            <?php
            if($res && mysqli_num_rows($res) > 0){
                mysqli_data_seek($res, 0); // Reset pointer for mobile loop
                while($s = mysqli_fetch_assoc($res)){
                    $roll = !empty($s['roll_no']) ? $s['roll_no'] : 'N/A';
                    $course = !empty($s['course']) ? $s['course'] : (!empty($s['class']) ? $s['class'] : 'General');
                    $mobile = !empty($s['student_mobile']) ? $s['student_mobile'] : '';
                    $first_letter = strtoupper(substr($s['student_name'], 0, 1));
                    
                    $total = (float)$s['total_fee'];
                    $paid = (float)$s['total_paid'];
                    $due = $total - $paid;
                    if($due < 0) $due = 0;
                    ?>
                    <div class="m-student-card">
                        <!-- Top Header Info -->
                        <div class="m-card-top">
                            <div class="m-avatar-info">
                                <div class="m-avatar"><?php echo $first_letter; ?></div>
                                <div class="m-details">
                                    <h5><?php echo htmlspecialchars($s['student_name']); ?></h5>
                                    <p><i class="fa-solid fa-graduation-cap" style="color:var(--phonepe-purple);"></i> <?php echo htmlspecialchars($course); ?> | Roll: #<?php echo $roll; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- 📊 Fees Calculation Box (Total, Paid, Remaining) -->
                        <div class="m-fee-box">
                            <div class="m-fee-item total">
                                <small>Total Fee</small>
                                <span>₹<?php echo number_format($total); ?></span>
                            </div>
                            <div class="m-fee-item paid">
                                <small>Total Paid</small>
                                <span>₹<?php echo number_format($paid); ?></span>
                            </div>
                            <div class="m-fee-item due">
                                <small>Remaining</small>
                                <span>₹<?php echo number_format($due); ?></span>
                            </div>
                        </div>

                        <!-- 🔘 Mobile Quick Actions -->
                        <div class="m-card-actions">
                            <a href="collect_fees.php?student_id=<?php echo $s['id']; ?>" class="m-btn pay"><i class="fa-solid fa-wallet"></i> Pay Fee</a>
                            <?php if(!empty($mobile)): ?>
                                <a href="tel:<?php echo $mobile; ?>" class="m-btn call"><i class="fa-solid fa-phone"></i> Call</a>
                            <?php endif; ?>
                            <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="m-btn edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="students_list.php?delete_id=<?php echo $s['id']; ?>" class="m-btn delete" onclick="return confirm('Delete this student?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div style='text-align:center; padding:30px; background:#fff; border-radius:20px; color:#64748b; font-weight:700;'>No students record found</div>";
            }
            ?>
        </div>

    </div>

    <!-- 📱 PhonePe Bottom Navigation Bar -->
    <div class="mobile-bottom-nav">
        <a href="admin_dashboard.php" class="phonepe-nav-item">
            <i class="fa-solid fa-house"></i>
            <span>होम</span>
        </a>
        <a href="collect_fees.php" class="phonepe-nav-item">
            <i class="fa-solid fa-wallet"></i>
            <span>फीस</span>
        </a>
        <a href="students_list.php" class="phonepe-nav-item active">
            <i class="fa-solid fa-users"></i>
            <span>स्टूडेंट्स</span>
        </a>
        <a href="add_student.php" class="phonepe-nav-item">
            <i class="fa-solid fa-user-plus"></i>
            <span>एडमिशन</span>
        </a>
    </div>

    <!-- JS GUARD: Prevent Form Resubmission / Reload Prompts -->
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>

</body>
</html>
<?php 
ob_end_flush(); 
?>