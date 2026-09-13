<?php
/**
 * SMART CMS PRO - VERIFY REQUESTS (MODERN ADMIN)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php';

// Admin login check[cite: 12]
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// --- Action Logic: Approve or Reject (PRG Pattern for Smooth Reloads) ---[cite: 12]
if(isset($_GET['action']) && isset($_GET['id'])) {
    $req_id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    
    // स्टूडेंट की ID निकालें[cite: 12]
    $get_stu_id = mysqli_query($conn, "SELECT student_id FROM service_requests WHERE id = '$req_id'");
    $stu_data = mysqli_fetch_assoc($get_stu_id);
    $s_id = isset($stu_data['student_id']) ? $stu_data['student_id'] : 0;
    
    if($action == 'approve') {
        // 1. रिक्वेस्ट स्टेटस बदलें[cite: 12]
        $update_req = "UPDATE service_requests SET status='Verified' WHERE id='$req_id'";
        mysqli_query($conn, $update_req);
        
        // 2. स्टूडेंट को वेरिफाई करें[cite: 12]
        $update_stu = "UPDATE students SET is_verified = 1 WHERE id = '$s_id'";
        if(mysqli_query($conn, $update_stu)) {
            header("Location: verify_requests.php?msg=approved");
            exit();
        }
    } elseif($action == 'reject') {
        $update = "UPDATE service_requests SET status='Rejected' WHERE id='$req_id'";
        if(mysqli_query($conn, $update)) {
            header("Location: verify_requests.php?msg=rejected");
            exit();
        }
    }
}

// System Alert Messages
$msg = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'approved') {
        $msg = "<div class='alert success'><i class='fa-solid fa-circle-check me-2'></i> Student Verified and Request Approved!</div>";
    } elseif($_GET['msg'] == 'rejected') {
        $msg = "<div class='alert danger'><i class='fa-solid fa-circle-xmark me-2'></i> Request has been rejected.</div>";
    }
}

// Fetch Pending Requests[cite: 12]
$query = "SELECT dr.*, s.roll_no, s.course, u.name 
          FROM service_requests dr
          JOIN students s ON dr.student_id = s.id
          JOIN users u ON s.user_id = u.id
          WHERE dr.status = 'Pending'
          ORDER BY dr.request_date ASC";

$result = mysqli_query($conn, $query);
$pending_count = ($result) ? mysqli_num_rows($result) : 0;
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Verify Documents | SMART CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            --danger: #ef4444;

            /* PhonePe Mobile App Theme */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); font-family: 'Plus Jakarta Sans', sans-serif; display: flex; min-height: 100vh; }

        /* 🖥️ DESKTOP STYLES (ORIGINAL UNTOUCHED) */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 20px; flex-shrink: 0; position: sticky; top: 0; height: 100vh; }
        .logo-area { padding: 10px 10px 30px; border-bottom: 1px solid #1e293b; margin-bottom: 20px; font-weight: 800; font-size: 20px; color: #818cf8; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; text-decoration: none; color: #94a3b8; border-radius: 12px; margin-bottom: 8px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: var(--primary); color: white; }
        
        .main-content { flex-grow: 1; padding: 30px; overflow-y: auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        .alert { padding: 15px 20px; border-radius: 16px; margin-bottom: 25px; font-weight: 600; display: flex; align-items: center; gap: 10px; animation: slideIn 0.5s ease; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .card { background: var(--white); border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 16px 25px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 18px 25px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .student-info { display: flex; flex-direction: column; }
        .stu-name { font-weight: 700; color: var(--text-main); }
        .stu-meta { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        
        .badge-doc { background: #eef2ff; color: #4f46e5; padding: 6px 12px; border-radius: 10px; font-weight: 700; font-size: 11px; border: 1px solid #e0e7ff; }

        .action-group { display: flex; gap: 8px; }
        .btn { padding: 8px 16px; border-radius: 12px; text-decoration: none; font-size: 13px; font-weight: 700; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; }
        .btn-approve { background: var(--success); color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
        .btn-reject { background: var(--danger); color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }
        .btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .empty-box { text-align: center; padding: 60px; color: var(--text-muted); }

        /* Hide Mobile UI on Desktop */
        .mobile-header, .mobile-bottom-nav, .mobile-requests-list { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full Native App Layout + Larger Fonts) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; }
            .sidebar, .page-header, table, .card-header { display: none !important; }

            /* PhonePe Top App Header Bar */
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
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.25);
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

            .mobile-badge-count {
                background: #ef4444;
                color: white;
                font-size: 13px;
                font-weight: 700;
                padding: 3px 10px;
                border-radius: 20px;
            }

            .main-content {
                padding: 78px 12px 85px 12px !important;
                width: 100% !important;
                overflow-x: hidden;
            }

            .card {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            /* PhonePe Pending Request Cards List */
            .mobile-requests-list {
                display: flex !important;
                flex-direction: column;
                gap: 14px;
            }

            .phonepe-req-card {
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
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .phonepe-user-icon {
                width: 46px; height: 46px;
                background: #f3e8ff;
                color: var(--phonepe-purple);
                border-radius: 14px;
                display: flex; align-items: center; justify-content: center;
                font-size: 20px;
                flex-shrink: 0;
            }

            .phonepe-stu-details { flex-grow: 1; }
            
            /* Larger Mobile Typography */
            .phonepe-stu-details h4 {
                font-size: 17.5px !important; /* Larger Student Name */
                font-weight: 700;
                color: #0f172a;
                margin: 0 0 2px 0;
            }

            .phonepe-stu-details p {
                font-size: 13.5px !important; /* Larger Roll / Course */
                color: #64748b;
                margin: 0;
                font-weight: 500;
            }

            .phonepe-doc-pill {
                background: #f1f5f9;
                color: var(--phonepe-purple);
                padding: 8px 12px;
                border-radius: 10px;
                font-size: 13.5px !important;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .phonepe-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-top: 4px;
            }

            .phonepe-btn {
                padding: 12px 16px !important;
                border-radius: 12px !important;
                font-size: 15px !important; /* Larger Button Text */
                font-weight: 700 !important;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: none;
            }

            .phonepe-btn-approve {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
                color: #ffffff !important;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important;
            }

            .phonepe-btn-reject {
                background: #fef2f2 !important;
                color: #dc2626 !important;
                border: 1px solid #fecaca !important;
            }

            /* PhonePe Bottom Nav */
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
            <h3>दस्तावेज़ सत्यापन (Verify)</h3>
            <small>Review Document Applications</small>
        </div>
    </div>
    <div class="mobile-badge-count">
        <?= $pending_count; ?> Pending
    </div>
</div>

<!-- 🖥️ Sidebar (Desktop Only) -->
<div class="sidebar">
    <div class="logo-area">
        <i class="fa-solid fa-graduation-cap"></i> CMS PRO
    </div>
    <a href="admin_dashboard.php" class="nav-link"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="manage_students.php" class="nav-link"><i class="fa-solid fa-users"></i> Students</a>
    <a href="verify_requests.php" class="nav-link active"><i class="fa-solid fa-circle-check"></i> Verify Requests</a>
    <a href="view_issued_docs.php" class="nav-link"><i class="fa-solid fa-box-archive"></i> Archive</a>
    <a href="logout.php" class="nav-link" style="margin-top: 50px; color: #f87171;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    
    <!-- 🖥️ Desktop Page Header -->
    <div class="page-header">
        <div>
            <h2 style="font-size: 24px; font-weight: 800;">Verification Requests</h2>
            <p style="color: var(--text-muted); font-size: 14px;">Review and approve student document applications</p>
        </div>
        <div style="text-align: right;">
            <span style="display: block; font-size: 12px; color: var(--text-muted); font-weight: 600;">PENDING NOW</span>
            <span style="font-size: 20px; font-weight: 800; color: var(--primary);"><?php echo $pending_count; ?> Requests</span>
        </div>
    </div>

    <?php echo $msg; ?>

    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 16px; font-weight: 700;">Pending Applications</h3>
        </div>

        <?php if($pending_count > 0): ?>
        
        <!-- 🖥️ DESKTOP TABLE VIEW -->
        <table>
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Document Type</th>
                    <th>Applied Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td>
                        <div class="student-info">
                            <span class="stu-name"><?php echo htmlspecialchars($row['name']); ?></span>
                            <span class="stu-meta">Roll: #<?php echo $row['roll_no']; ?> | <?php echo $row['course']; ?></span>
                        </div>
                    </td>
                    <td><span class="badge-doc"><i class="fa-solid fa-file-pdf"></i> <?php echo $row['doc_type']; ?></span></td>
                    <td style="color: var(--text-muted); font-weight: 500;">
                        <?php echo date('d M, Y', strtotime($row['request_date'])); ?>
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="verify_requests.php?action=approve&id=<?php echo $row['id']; ?>" 
                               class="btn btn-approve" 
                               onclick="return confirm('क्या आप इस आवेदन को अप्रूव करना चाहते हैं?')">
                               <i class="fa-solid fa-check"></i> Approve
                            </a>
                            <a href="verify_requests.php?action=reject&id=<?php echo $row['id']; ?>" 
                               class="btn btn-reject" 
                               onclick="return confirm('क्या आप इस आवेदन को निरस्त करना चाहते हैं?')">
                               <i class="fa-solid fa-xmark"></i> Reject
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- 📱 MOBILE PHONEPE CARDS VIEW -->
        <div class="mobile-requests-list">
            <?php 
            mysqli_data_seek($result, 0); // Pointer reset
            while($row = mysqli_fetch_assoc($result)): 
            ?>
            <div class="phonepe-req-card">
                <div class="phonepe-card-top">
                    <div class="phonepe-user-icon">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div class="phonepe-stu-details">
                        <h4><?= htmlspecialchars($row['name']); ?></h4>
                        <p>Roll: #<?= $row['roll_no']; ?> • <?= $row['course']; ?></p>
                    </div>
                </div>

                <div class="phonepe-doc-pill">
                    <span><i class="fa-solid fa-file-pdf me-1"></i> <?= $row['doc_type']; ?></span>
                    <span style="font-size:12px; color:#64748b; font-weight:500;">
                        <?= date('d M, Y', strtotime($row['request_date'])); ?>
                    </span>
                </div>

                <div class="phonepe-actions">
                    <a href="verify_requests.php?action=approve&id=<?= $row['id']; ?>" 
                       class="phonepe-btn phonepe-btn-approve" 
                       onclick="return confirm('क्या आप इस आवेदन को अप्रूव करना चाहते हैं?')">
                        <i class="fa-solid fa-check"></i> Approve
                    </a>
                    <a href="verify_requests.php?action=reject&id=<?= $row['id']; ?>" 
                       class="phonepe-btn phonepe-btn-reject" 
                       onclick="return confirm('क्या आप इस आवेदन को निरस्त करना चाहते हैं?')">
                        <i class="fa-solid fa-xmark"></i> Reject
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
            <div class="empty-box">
                <i class="fa-solid fa-circle-check" style="font-size: 50px; color: var(--success); opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                <p style="font-weight: 700; font-size: 16px;">कोई लंबित अनुरोध नहीं है!</p>
                <p style="font-size: 13.5px;">सभी दस्तावेज़ आवेदनों का निपटारा हो चुका है।</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 📱 PhonePe Bottom Navigation Bar (Mobile Only) -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>होम</span>
    </a>
    <a href="manage_students.php" class="phonepe-nav-item">
        <i class="fa fa-users"></i>
        <span>विद्यार्थी</span>
    </a>
    <a href="verify_requests.php" class="phonepe-nav-item active">
        <i class="fa fa-circle-check"></i>
        <span>सत्यापन</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

</body>
</html>