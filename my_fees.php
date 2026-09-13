<?php 
// 1. Output Buffering Start (Page Reload & Header Sent Error रोकने के लिए)
ob_start();

// 2. Session and Database Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// 3. Student Login Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$stu_id = $stu['id'] ?? $stu['user_id'] ?? 0;

// --- HELPER: Dynamic Column Checker ---
function getSafeColumn($conn, $table, $possible_names) {
    foreach($possible_names as $name) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$name'");
        if($check && mysqli_num_rows($check) > 0) return $name;
    }
    return $possible_names[0]; 
}

$fee_col = getSafeColumn($conn, 'fees', ['student_id', 'user_id']);
$pay_date_col = getSafeColumn($conn, 'fees', ['payment_date', 'date', 'created_at']);
$u_id_col = getSafeColumn($conn, 'students', ['user_id', 'id']);
$total_fee_col = getSafeColumn($conn, 'students', ['total_fee', 'course_fee']); // Auto Detect Admin Fee Field

// --- REAL-TIME ADMIN SYNCED DATA FETCHING ---
$profile_q = mysqli_query($conn, "SELECT `$total_fee_col` as total_fee, roll_no, course, name FROM students WHERE $u_id_col = '$stu_id'");
$profile_data = ($profile_q && mysqli_num_rows($profile_q) > 0) ? mysqli_fetch_assoc($profile_q) : $stu;

$total_fee = (float)($profile_data['total_fee'] ?? 0); 
$roll_no = $profile_data['roll_no'] ?? 'N/A';
$course_name = $profile_data['course'] ?? 'General';
$student_name = $profile_data['name'] ?? 'Student';

// Calculate Paid Amount from Database
$fee_sum_q = mysqli_query($conn, "SELECT SUM(amount_paid) as total_paid FROM fees WHERE $fee_col = '$stu_id'");
$fee_sum_data = ($fee_sum_q) ? mysqli_fetch_assoc($fee_sum_q) : [];
$total_paid = (float)($fee_sum_data['total_paid'] ?? 0);

// Dynamic Math: Total - Paid = Remaining Due
$balance_due = max(0, $total_fee - $total_paid);

// Fetch Transaction History
$query = "SELECT * FROM fees WHERE $fee_col = '$stu_id' ORDER BY $pay_date_col DESC";
$res = mysqli_query($conn, $query);

$transactions = [];
if($res && mysqli_num_rows($res) > 0) {
    while($r = mysqli_fetch_assoc($res)) {
        $transactions[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Fees & Transactions | Student Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --top-nav-height: 70px;
            --bg-body: #f1f5f9;
            --primary: #1e293b;
            --accent: #4f46e5;
            --white: #ffffff;
            
            /* PhonePe UI Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg-body); color: #334155; overflow-x: hidden; min-height: 100vh; }

        /* ---------------- 🖥️ DESKTOP STYLES (UNCHANGED & CLEAN) ---------------- */
        .sidebar {
            width: var(--sidebar-width); height: 100vh;
            background: var(--primary); position: fixed; left: 0; top: 0;
            color: white; padding: 20px; z-index: 100; transition: 0.3s;
        }

        .logo-area { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .logo-area h2 { font-size: 18px; color: #818cf8; letter-spacing: 1px; }

        .nav-menu { list-style: none; }
        .nav-item { margin: 10px 0; }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 15px; color: #94a3b8; text-decoration: none;
            border-radius: 10px; transition: 0.3s; font-size: 15px; font-weight: 600;
        }
        .nav-link:hover, .nav-link.active { background: var(--accent); color: white; }

        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); min-height: 100vh; }

        .top-nav {
            height: var(--top-nav-height); background: var(--white);
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 99;
        }
        .page-title h3 { font-size: 18px; font-weight: 700; color: var(--primary); }

        .user-profile {
            display: flex; align-items: center; gap: 12px;
            background: #f8fafc; padding: 6px 16px; border-radius: 50px; border: 1px solid #e2e8f0;
        }

        .dashboard-wrapper { padding: 30px; max-width: 1200px; margin: auto; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }

        .stat-card {
            background: var(--white); padding: 25px; border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;
            border-left: 5px solid #e2e8f0;
        }
        .stat-card.blue { border-left-color: #3b82f6; }
        .stat-card.green { border-left-color: #22c55e; }
        .stat-card.red { border-left-color: #ef4444; }

        .stat-info h4 { font-size: 13px; color: #64748b; text-transform: uppercase; font-weight: 700; }
        .stat-info p { font-size: 26px; font-weight: 800; color: var(--primary); margin-top: 4px; }

        .table-card { background: var(--white); border-radius: 20px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 800; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; font-weight: 600; }

        .badge-paid { background: #dcfce7; color: #15803d; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 800; }

        .btn-receipt {
            background: #f8fafc; color: var(--accent); border: 1px solid var(--accent);
            padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700;
            text-decoration: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-receipt:hover { background: var(--accent); color: white; }

        .mobile-header, .mobile-txn-list, .mobile-bottom-nav { display: none; }


        /* 📱 ---------------- PHONEPE MOBILE APP VIEW (BIG FONTS & MODERN APP UI) ---------------- */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; padding-top: 72px; padding-bottom: 90px; }

            .sidebar, .top-nav, .table-card { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; }
            .dashboard-wrapper { padding: 14px !important; }

            /* PhonePe Mobile Top Bar */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 70px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 16px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.3);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { color: #ffffff; font-size: 20px; width: 42px; height: 42px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 700; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.9); font-size: 13.5px !important; font-weight: 500; display: block; }

            /* PhonePe Wallet Style Cards (Big & Clear Fonts) */
            .stats-grid { display: flex !important; flex-direction: column; gap: 14px; margin-bottom: 22px; }
            .stat-card {
                padding: 22px !important; border-radius: 22px !important; border-left: none !important;
                box-shadow: 0 4px 18px rgba(0,0,0,0.04) !important;
            }
            
            /* Total Fee Card */
            .stat-card.blue { background: linear-gradient(135deg, #2a085c 0%, var(--phonepe-purple) 100%) !important; color: white !important; }
            .stat-card.blue .stat-info h4 { color: rgba(255,255,255,0.85) !important; font-size: 14px !important; letter-spacing: 0.5px; }
            .stat-card.blue .stat-info p { color: #ffffff !important; font-size: 32px !important; font-weight: 800 !important; }

            /* Paid Fee Card */
            .stat-card.green { background: #ffffff !important; border: 2px solid #22c55e !important; }
            .stat-card.green .stat-info h4 { color: #15803d !important; font-size: 14px !important; font-weight: 700 !important; }
            .stat-card.green .stat-info p { color: #16a34a !important; font-size: 30px !important; font-weight: 800 !important; }

            /* Outstanding Due Card */
            .stat-card.red { background: #ffffff !important; border: 2px solid #ef4444 !important; }
            .stat-card.red .stat-info h4 { color: #b91c1c !important; font-size: 14px !important; font-weight: 700 !important; }
            .stat-card.red .stat-info p { color: #dc2626 !important; font-size: 30px !important; font-weight: 800 !important; }

            /* PhonePe Transaction List UI */
            .mobile-txn-section-title { font-size: 18px !important; font-weight: 800; color: #0f172a; margin: 22px 6px 14px 6px; display: flex; justify-content: space-between; align-items: center; }
            .mobile-txn-list { display: flex !important; flex-direction: column; gap: 12px; }
            
            .m-txn-card {
                background: #ffffff; padding: 18px; border-radius: 20px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between;
                border: 1px solid #f1f5f9;
            }
            .m-txn-left { display: flex; align-items: center; gap: 14px; }
            .m-txn-icon {
                width: 52px; height: 52px; border-radius: 18px;
                background: #f3e8ff; color: var(--phonepe-purple);
                display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
            }
            .m-txn-details h5 { font-size: 17px !important; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
            .m-txn-details p { font-size: 13.5px !important; font-weight: 600; color: #64748b; }
            .m-txn-details small { font-size: 12px !important; font-weight: 500; color: #94a3b8; }

            .m-txn-right { text-align: right; }
            .m-txn-amount { font-size: 20px !important; font-weight: 800; color: #16a34a; margin-bottom: 6px; }
            .m-btn-download {
                background: #f8fafc; color: var(--phonepe-purple); border: 1.5px solid #e9d5ff;
                padding: 7px 14px; border-radius: 12px; font-size: 13px !important; font-weight: 700;
                text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
            }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 68px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 12px !important; font-weight: 600; width: 25%; }
            .phonepe-nav-item i { font-size: 22px; margin-bottom: 3px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 700; }
        }
    </style>
</head>
<body>

    <!-- 📱 PhonePe Mobile App Header -->
    <div class="mobile-header">
        <div class="mobile-header-left">
            <a href="dashboard.php" class="mobile-back-btn"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h3>फीस व विवरण</h3>
                <small><?php echo htmlspecialchars($student_name); ?> (रोल: <?php echo htmlspecialchars($roll_no); ?>)</small>
            </div>
        </div>
        <div style="color:#fff; font-size:24px;">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>

    <!-- 🖥️ DESKTOP SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="logo-area">
            <h2>CMS ACADEMY</h2>
            <p style="font-size: 10px; opacity: 0.6;">Student Management System</p>
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li class="nav-item"><a href="profile.php" class="nav-link"><i class="fas fa-user-graduate"></i> My Profile</a></li>
            <li class="nav-item"><a href="my_fees.php" class="nav-link active"><i class="fas fa-wallet"></i> Fees History</a></li>
            <li class="nav-item"><a href="courses.php" class="nav-link"><i class="fas fa-book"></i> Courses</a></li>
            <li class="nav-item"><a href="id_card.php" class="nav-link"><i class="fas fa-id-card"></i> ID Card</a></li>
            <li class="nav-item" style="margin-top: 50px;">
                <a href="logout.php" class="nav-link" style="color: #fca5a5;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-content">
        
        <!-- 🖥️ DESKTOP TOP NAV -->
        <nav class="top-nav">
            <div class="page-title">
                <h3>Fees & Transactions Statement</h3>
            </div>
            <div class="user-profile">
                <div style="text-align: right; line-height: 1.2;">
                    <p style="font-size: 14px; font-weight: 700; color:#0f172a;"><?php echo htmlspecialchars($student_name); ?></p>
                    <small style="font-size: 11px; color: #64748b; font-weight:600;">Roll: <?php echo htmlspecialchars($roll_no); ?></small>
                </div>
                <i class="fas fa-user-circle" style="font-size: 32px; color: #cbd5e1;"></i>
            </div>
        </nav>

        <div class="dashboard-wrapper">
            
            <!-- STATS / WALLET SUMMARY (LIVE ADMIN SYNCED) -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-info">
                        <h4>Total Course Fee (कुल फ़ीस)</h4>
                        <p>₹<?php echo number_format($total_fee); ?></p>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="stat-info">
                        <h4>Fees Paid (कुल जमा)</h4>
                        <p>₹<?php echo number_format($total_paid); ?></p>
                    </div>
                </div>
                <div class="stat-card red">
                    <div class="stat-info">
                        <h4>Outstanding Due (बाकी फ़ीस)</h4>
                        <p>₹<?php echo number_format($balance_due); ?></p>
                    </div>
                </div>
            </div>

            <!-- 🖥️ DESKTOP TABLE VIEW -->
            <div class="table-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 18px; color: var(--primary); font-weight:700;">Payment History</h2>
                    <span style="font-size: 13px; color: #64748b; font-weight:600;">Course: <b><?php echo htmlspecialchars($course_name); ?></b></span>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Receipt No</th>
                            <th>Course</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($transactions)): ?>
                            <?php foreach($transactions as $row): ?>
                            <tr>
                                <td><b><?php echo date('d M, Y', strtotime($row[$pay_date_col])); ?></b></td>
                                <td style="font-family: monospace; font-weight:700; color:#4f46e5;"><?php echo $row['receipt_no'] ?? 'REC'.$row['id']; ?></td>
                                <td><?php echo htmlspecialchars($course_name); ?></td>
                                <td style="color: #16a34a; font-weight: 800; font-size:15px;">₹<?php echo number_format($row['amount_paid']); ?></td>
                                <td><span class="badge-paid">SUCCESS</span></td>
                                <td>
                                    <a href="generate_receipt.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-receipt">
                                        <i class="fas fa-download"></i> Print Receipt
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5;">No payment history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 📱 PHONEPE MOBILE TRANSACTION LIST -->
            <div class="mobile-txn-section-title">
                <span>लेन-देन का इतिहास (Transactions)</span>
                <span style="font-size: 13px; color: var(--phonepe-purple); font-weight: 700;"><?php echo count($transactions); ?> Records</span>
            </div>

            <div class="mobile-txn-list">
                <?php if(!empty($transactions)): ?>
                    <?php foreach($transactions as $row): ?>
                    <div class="m-txn-card">
                        <div class="m-txn-left">
                            <div class="m-txn-icon">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="m-txn-details">
                                <h5><?php echo htmlspecialchars($course_name); ?> Fee</h5>
                                <p><?php echo $row['receipt_no'] ?? 'REC'.$row['id']; ?></p>
                                <small><?php echo date('d M Y, h:i A', strtotime($row[$pay_date_col])); ?></small>
                            </div>
                        </div>
                        <div class="m-txn-right">
                            <div class="m-txn-amount">+ ₹<?php echo number_format($row['amount_paid']); ?></div>
                            <a href="generate_receipt.php?id=<?php echo $row['id']; ?>" target="_blank" class="m-btn-download">
                                <i class="fa-solid fa-file-pdf"></i> रसीद (PDF)
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align:center; padding:40px; background:#fff; border-radius:20px; color:#64748b; font-weight:600;">
                        कोई लेन-देन नहीं मिला।
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- 📱 PhonePe Bottom Navigation Bar -->
    <div class="mobile-bottom-nav">
        <a href="dashboard.php" class="phonepe-nav-item">
            <i class="fa-solid fa-house"></i>
            <span>होम</span>
        </a>
        <a href="my_fees.php" class="phonepe-nav-item active">
            <i class="fa-solid fa-wallet"></i>
            <span>फीस</span>
        </a>
        <a href="my_results.php" class="phonepe-nav-item">
            <i class="fa-solid fa-file-invoice"></i>
            <span>रिजल्ट</span>
        </a>
        <a href="profile.php" class="phonepe-nav-item">
            <i class="fa-solid fa-user"></i>
            <span>प्रोफाइल</span>
        </a>
    </div>

    <!-- PREVENT AUTOMATIC PAGE RELOADS & RESUBMISSIONS -->
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