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

// --- 2. DELETE LOGIC (PRG Pattern - Prevent Reload Resubmission) ---
if(isset($_GET['delete_id'])){
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    if(mysqli_query($conn, "DELETE FROM fees WHERE id = '$del_id'")){
        $_SESSION['msg'] = "<div class='success-toast toast-red'>
                               <i class='fa-solid fa-trash-can'></i> <span>Record Deleted Successfully!</span>
                           </div>";
    } else {
        $_SESSION['msg'] = "<div class='success-toast toast-red'>
                               <i class='fa-solid fa-circle-exclamation'></i> <span>Delete Failed: " . mysqli_error($conn) . "</span>
                           </div>";
    }
    // Redirect to clear GET parameters from URL
    header("Location: collect_fees.php");
    exit();
}

// --- 3. PAYMENT LOGIC (PRG Pattern - Prevent Reload Resubmission) ---
if(isset($_POST['pay_fee'])){
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $payment_date = date('Y-m-d H:i:s');
    $receipt_no = "REC" . rand(100000, 999999);

    $sql = "INSERT INTO fees (student_id, amount_paid, payment_date, receipt_no, status) 
            VALUES ('$student_id', '$amount', '$payment_date', '$receipt_no', 'paid')";

    if(mysqli_query($conn, $sql)){
        $last_id = mysqli_insert_id($conn);
        $_SESSION['msg'] = "<div class='success-toast toast-green'>
                    <div><i class='fa-solid fa-circle-check'></i> <strong>Payment Successful!</strong></div>
                    <a href='generate_receipt.php?id=$last_id' target='_blank' class='toast-link'>
                       🖨️ Print Receipt #$receipt_no
                    </a>
                    </div>";
    } else {
        $_SESSION['msg'] = "<div class='success-toast toast-red'>
                    <i class='fa-solid fa-circle-exclamation'></i> <strong>Payment Failed:</strong> " . mysqli_error($conn) . "
                    </div>";
    }
    // Redirect to prevent duplicate submission on F5 / Refresh
    header("Location: collect_fees.php");
    exit();
}

// Retrieve session toast message
if(isset($_SESSION['msg'])){
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>University ERP | Fees Management</title>
    
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

        /* --- SUCCESS & ERROR TOAST NOTIFICATIONS --- */
        .success-toast {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .toast-green { background: #f0fdf4; border-left: 6px solid #22c55e; color: #166534; }
        .toast-red { background: #fef2f2; border-left: 6px solid #ef4444; color: #991b1b; }
        .toast-link {
            background: #22c55e;
            color: #ffffff !important;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(34,197,94,0.3);
        }

        /* ---------------- 🖥️ DESKTOP STYLES ---------------- */
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 30px 20px; z-index: 1000; }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; color: var(--primary); font-size: 20px; font-weight: 800; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 18px; color: #64748b; text-decoration: none; border-radius: 12px; margin-bottom: 5px; font-weight: 600; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary-light); color: var(--primary); }

        .top-nav { height: 70px; position: fixed; left: var(--sidebar-width); right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; z-index: 999; }
        .profile-chip { display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 8px 15px; border-radius: 50px; font-size: 13px; font-weight: 700; border: 1px solid #e2e8f0; }

        .main-content { margin-left: var(--sidebar-width); margin-top: 70px; padding: 40px; width: calc(100% - var(--sidebar-width)); }
        .grid-container { display: grid; grid-template-columns: 1fr 1.8fr; gap: 30px; }
        .card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: var(--card-shadow); }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 15px; color: #64748b; font-size: 12px; font-weight: 800; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .receipt-tag { background: #f5f3ff; color: #7c3aed; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-family: monospace; }

        label { display: block; font-size: 12px; font-weight: 800; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        select, input { width: 100%; padding: 14px; border: 1.5px solid #cbd5e1; border-radius: 12px; margin-bottom: 20px; outline: none; font-size: 15px; font-weight: 600; background: #fff; transition: 0.2s; }
        select:focus, input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .btn-pay { width: 100%; background: var(--primary); color: white; border: none; padding: 16px; border-radius: 12px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 12px rgba(79,70,229,0.2); }
        .btn-pay:hover { background: #4338ca; transform: translateY(-1px); }

        .actions { display: flex; gap: 8px; }
        .btn-act { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.2s; font-size: 14px; }
        .btn-act.print { color: #4f46e5; background: #eef2ff; }
        .btn-act.edit { color: #d97706; background: #fffbeb; }
        .btn-act.del { color: #ef4444; background: #fef2f2; }
        .btn-act:hover { transform: scale(1.1); }

        .student-item { display: flex; justify-content: space-between; align-items: center; padding: 14px; border-radius: 12px; background: #f8fafc; margin-bottom: 10px; border: 1px solid #e2e8f0; font-size: 15px; font-weight: 700; }
        .total-badge { background: #22c55e; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 13px; }

        .mobile-header, .mobile-bottom-nav, .mobile-txn-section-title, .mobile-txn-list { display: none; }

        /* 📱 ---------------- PHONEPE MOBILE APP VIEW ---------------- */
        @media (max-width: 900px) {
            body { background: var(--phonepe-bg) !important; padding-top: 72px; padding-bottom: 90px; }

            .sidebar, .top-nav, table { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 15px !important; margin-top: 0 !important; }
            .grid-container { grid-template-columns: 1fr !important; gap: 16px !important; }

            /* 🟣 PhonePe Mobile Header */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 70px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { color: #ffffff; font-size: 22px; width: 44px; height: 44px; background: rgba(255,255,255,0.18); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; }
            .mobile-header h3 { color: #ffffff !important; font-size: 21px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 13px !important; font-weight: 600; display: block; }

            /* 🎴 Mobile Cards Styling */
            .card {
                padding: 20px !important;
                border-radius: 22px !important;
                border: none !important;
                box-shadow: 0 4px 18px rgba(0,0,0,0.04) !important;
                margin-bottom: 10px !important;
            }

            .card h3 { font-size: 18px !important; font-weight: 800 !important; color: #0f172a !important; margin-bottom: 18px !important; }

            /* 🔤 Mobile Inputs (Bade Font) */
            label { font-size: 13px !important; font-weight: 800 !important; color: #475569 !important; margin-bottom: 8px !important; }
            select, input {
                padding: 16px !important;
                font-size: 17px !important;
                font-weight: 700 !important;
                border-radius: 14px !important;
                border: 2px solid #e2e8f0 !important;
                margin-bottom: 18px !important;
                background: #f8fafc !important;
            }
            select:focus, input:focus { border-color: var(--phonepe-purple) !important; background: #fff !important; }

            /* 🟣 PhonePe Action Button */
            .btn-pay {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                padding: 18px !important;
                font-size: 18px !important;
                font-weight: 800 !important;
                border-radius: 16px !important;
                box-shadow: 0 6px 20px rgba(95, 37, 159, 0.3) !important;
            }

            /* 📱 Mobile Transactions List (PhonePe History Style) */
            .mobile-txn-section-title { font-size: 19px !important; font-weight: 800; color: #0f172a; margin: 22px 6px 14px 6px; display: flex; justify-content: space-between; align-items: center; }
            .mobile-txn-list { display: flex !important; flex-direction: column; gap: 12px; }

            .m-txn-card {
                background: #ffffff; padding: 18px; border-radius: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between;
            }
            .m-txn-left { display: flex; align-items: center; gap: 14px; }
            .m-txn-icon {
                width: 52px; height: 52px; border-radius: 18px;
                background: #f3e8ff; color: var(--phonepe-purple);
                display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
            }
            .m-txn-details h5 { font-size: 17px !important; font-weight: 800; color: #0f172a; margin: 0 0 3px 0; }
            .m-txn-details p { font-size: 14px !important; font-weight: 700; color: #64748b; margin: 0; }
            .m-txn-details small { font-size: 12px !important; font-weight: 600; color: #94a3b8; display: block; margin-top: 2px; }

            .m-txn-right { text-align: right; }
            .m-txn-amount { font-size: 20px !important; font-weight: 800; color: #16a34a; margin-bottom: 6px; }
            
            .m-actions { display: flex; gap: 6px; justify-content: flex-end; }
            .m-btn-act {
                padding: 6px 12px; border-radius: 10px; font-size: 12px !important; font-weight: 800;
                text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
            }
            .m-btn-act.print { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
            .m-btn-act.del { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

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
                <h3>Collect Fees</h3>
                <small>Admin Fee Portal</small>
            </div>
        </div>
        <div style="color:#fff; font-size:24px;">
            <i class="fa-solid fa-wallet"></i>
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
            <a href="collect_fees.php" class="active"><i class="fa-solid fa-receipt"></i> Collect Fees</a>
            <a href="student_list.php"><i class="fa-solid fa-users"></i> All Students</a>
            <a href="view_issued_docs.php"><i class="fa-solid fa-folder-open"></i> Archives</a>
            <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>

    <!-- 🖥️ DESKTOP TOP NAV -->
    <div class="top-nav">
        <div><h3 style="margin:0; font-size:18px; font-weight:800; color:#1e293b;">Fees Management Counter</h3></div>
        <div class="profile-chip">
            <i class="fa-solid fa-circle-user" style="color:var(--primary); font-size:16px;"></i> Admin Account
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- Display Toast Notification -->
        <?php echo $message; ?>
        
        <div class="grid-container">
            
            <!-- LEFT COLUMN: PAYMENT FORM & TOTALS -->
            <div>
                <!-- New Payment Card -->
                <div class="card">
                    <h3><i class="fa-solid fa-money-bill-transfer" style="color:var(--primary)"></i> Collect New Fee</h3>
                    <form method="POST" action="collect_fees.php">
                        <label>Select Student</label>
                        <select name="student_id" required>
                            <option value="">-- Search & Select Student --</option>
                            <?php
                            // Safe student fetch with fallback query
                            $students_query = "SELECT s.id, COALESCE(u.name, s.name, 'Student') as name, s.roll_no 
                                              FROM students s 
                                              LEFT JOIN users u ON s.user_id = u.id 
                                              ORDER BY s.id DESC";
                            $res = mysqli_query($conn, $students_query);
                            if(!$res) {
                                $res = mysqli_query($conn, "SELECT id, name, roll_no FROM students ORDER BY id DESC");
                            }
                            
                            if($res && mysqli_num_rows($res) > 0){
                                while($row = mysqli_fetch_assoc($res)){
                                    $roll = !empty($row['roll_no']) ? " (#".$row['roll_no'].")" : "";
                                    echo "<option value='{$row['id']}'>".htmlspecialchars($row['name']).$roll."</option>";
                                }
                            }
                            ?>
                        </select>

                        <label>Payment Amount (₹)</label>
                        <input type="number" name="amount" placeholder="Enter Amount (e.g. 5000)" min="1" step="any" required>

                        <button type="submit" name="pay_fee" class="btn-pay">
                            <i class="fa-solid fa-bolt"></i> Generate & Save Payment
                        </button>
                    </form>
                </div>

                <!-- Student Totals Summary Card -->
                <div class="card" style="margin-top: 25px;">
                    <h3><i class="fa-solid fa-calculator" style="color:#059669;"></i> Student Total Paid Summary</h3>
                    <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                        <?php
                        $totals_query = "SELECT COALESCE(u.name, s.name, 'Student') as name, SUM(f.amount_paid) as total 
                                        FROM fees f 
                                        JOIN students s ON f.student_id = s.id 
                                        LEFT JOIN users u ON s.user_id = u.id 
                                        GROUP BY f.student_id 
                                        ORDER BY total DESC";
                        $totals = mysqli_query($conn, $totals_query);
                        
                        if(!$totals) {
                            $totals = mysqli_query($conn, "SELECT s.name, SUM(f.amount_paid) as total FROM fees f JOIN students s ON f.student_id = s.id GROUP BY f.student_id");
                        }

                        if($totals && mysqli_num_rows($totals) > 0){
                            while($t = mysqli_fetch_assoc($totals)){
                                echo "<div class='student-item'>
                                        <span><i class='fa-solid fa-user-graduate' style='color:#94a3b8; margin-right:8px;'></i> ".htmlspecialchars($t['name'])."</span>
                                        <span class='total-badge'>₹".number_format($t['total'])."</span>
                                      </div>";
                            }
                        } else {
                            echo "<div style='text-align:center; color:#94a3b8; padding:15px; font-weight:600;'>No records found</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: DESKTOP TRANSACTION TABLE -->
            <div class="card">
                <h3><i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);"></i> Recent Transaction History</h3>
                
                <!-- 🖥️ Desktop Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $history_query = "SELECT f.*, COALESCE(u.name, s.name, 'Student') as name 
                                         FROM fees f 
                                         JOIN students s ON f.student_id = s.id 
                                         LEFT JOIN users u ON s.user_id = u.id 
                                         ORDER BY f.id DESC LIMIT 15";
                        $history = mysqli_query($conn, $history_query);
                        
                        if(!$history) {
                            $history = mysqli_query($conn, "SELECT f.*, s.name FROM fees f JOIN students s ON f.student_id = s.id ORDER BY f.id DESC LIMIT 15");
                        }

                        if($history && mysqli_num_rows($history) > 0){
                            while($f = mysqli_fetch_assoc($history)){
                                $date_str = isset($f['payment_date']) ? date('d M, Y', strtotime($f['payment_date'])) : date('d M, Y');
                                echo "<tr>
                                    <td><span class='receipt-tag'>#{$f['receipt_no']}</span></td>
                                    <td><b style='color:#334155;'>".htmlspecialchars($f['name'])."</b></td>
                                    <td><b style='color:#22c55e; font-size:15px;'>₹" . number_format($f['amount_paid']) . "</b></td>
                                    <td style='font-size:12px; color:#64748b; font-weight:600;'>{$date_str}</td>
                                    <td>
                                        <div class='actions'>
                                            <a href='generate_receipt.php?id={$f['id']}' target='_blank' class='btn-act print' title='Print Receipt'><i class='fa-solid fa-print'></i></a>
                                            <a href='edit_fee.php?id={$f['id']}' class='btn-act edit' title='Edit'><i class='fa-solid fa-pen-to-square'></i></a>
                                            <a href='collect_fees.php?delete_id={$f['id']}' class='btn-act del' onclick=\"return confirm('Are you sure you want to delete this payment record?')\"><i class='fa-solid fa-trash-can'></i></a>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:30px; color:#94a3b8; font-weight:700;'>No transaction history found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- 📱 PHONEPE MOBILE TRANSACTION HISTORY LIST -->
        <div class="mobile-txn-section-title">
            <span>Recent Transactions</span>
            <span style="font-size:13px; color:var(--phonepe-purple); font-weight:800;">History</span>
        </div>

        <div class="mobile-txn-list">
            <?php
            // Re-fetch for mobile view list
            if($history && mysqli_num_rows($history) > 0){
                mysqli_data_seek($history, 0); // Reset pointer
                while($f = mysqli_fetch_assoc($history)){
                    $date_str = isset($f['payment_date']) ? date('d M Y, h:i A', strtotime($f['payment_date'])) : date('d M Y');
                    ?>
                    <div class="m-txn-card">
                        <div class="m-txn-left">
                            <div class="m-txn-icon">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="m-txn-details">
                                <h5><?php echo htmlspecialchars($f['name']); ?></h5>
                                <p>#<?php echo $f['receipt_no']; ?></p>
                                <small><?php echo $date_str; ?></small>
                            </div>
                        </div>
                        <div class="m-txn-right">
                            <div class="m-txn-amount">+ ₹<?php echo number_format($f['amount_paid']); ?></div>
                            <div class="m-actions">
                                <a href="generate_receipt.php?id=<?php echo $f['id']; ?>" target="_blank" class="m-btn-act print">
                                    <i class="fa-solid fa-print"></i> Receipt
                                </a>
                                <a href="collect_fees.php?delete_id=<?php echo $f['id']; ?>" class="m-btn-act del" onclick="return confirm('Delete this fee record?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div style='text-align:center; padding:30px; background:#fff; border-radius:18px; color:#64748b; font-weight:700;'>No payment transactions found</div>";
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
        <a href="collect_fees.php" class="phonepe-nav-item active">
            <i class="fa-solid fa-wallet"></i>
            <span>फीस</span>
        </a>
        <a href="student_list.php" class="phonepe-nav-item">
            <i class="fa-solid fa-users"></i>
            <span>स्टूडेंट्स</span>
        </a>
        <a href="add_student.php" class="phonepe-nav-item">
            <i class="fa-solid fa-user-plus"></i>
            <span>एडमिशन</span>
        </a>
    </div>

    <!-- JS GUARD: Prevent Form Resubmission Prompt on Browser Refresh (F5) -->
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