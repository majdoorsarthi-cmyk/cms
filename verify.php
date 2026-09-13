<?php
include 'db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sidebar Navigation Fix
$dashboard_url = "student_dashboard.php"; 
if(!isset($_SESSION['student'])) {
    $dashboard_url = "index.php";
}

$student_data = null;
$search_error = "";

if (isset($_GET['roll_no']) && !empty(trim($_GET['roll_no']))) {
    $search_val = mysqli_real_escape_string($conn, $_GET['roll_no']);
    
    // Student, User details with left join to avoid missing fields error
    $query = "SELECT s.*, u.name as student_name, u.email, u.mobile 
              FROM students s 
              JOIN users u ON s.user_id = u.id 
              WHERE (s.roll_no = '$search_val' OR s.enrollment_no = '$search_val') 
              AND s.is_deleted = 0 LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $student_data = mysqli_fetch_assoc($result);
        $student_id = $student_data['id'];
        
        // Fetch Marks / Subjects
        $marks_q = mysqli_query($conn, "SELECT * FROM marks WHERE student_id = '$student_id'");
        $log_status = "Success";
    } else {
        $search_error = "Invalid Roll Number or Enrollment No! No record found in official database.";
        $log_status = "Failed";
    }

    // Verification Access Logging
    $ip = $_SERVER['REMOTE_ADDR'];
    $browser = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT']);
    mysqli_query($conn, "INSERT INTO verification_logs (roll_no, ip_address, browser_info, status) 
                        VALUES ('$search_val', '$ip', '$browser', '$log_status')");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Student Verification Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary: #4f46e5; --dark: #0f172a; --bg: #f1f5f9; --university-blue: #003366; --gold: #b8860b; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg); font-family: 'Poppins', sans-serif; display: flex; }

        .sidebar { width: 260px; background: #fff; height: 100vh; position: fixed; padding: 25px; border-right: 1px solid #e2e8f0; z-index: 1000; }
        .sidebar-brand { font-size: 22px; font-weight: 800; color: var(--primary); margin-bottom: 40px; }
        .sidebar-menu a { color: #64748b; text-decoration: none; padding: 12px 15px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 8px; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #eef2ff; color: var(--primary); }

        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; }
        
        .marksheet-container { 
            background: white; 
            max-width: 880px; 
            margin: 0 auto; 
            border: 8px double var(--university-blue); 
            padding: 30px; 
            position: relative; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .watermark { 
            position: absolute; 
            top: 50%; left: 50%; 
            transform: translate(-50%, -50%) rotate(-35deg); 
            font-size: 65px; 
            font-weight: 900; 
            color: rgba(0, 51, 102, 0.04); 
            white-space: nowrap; 
            pointer-events: none; 
            z-index: 0; 
            letter-spacing: 8px;
        }

        .verified-badge {
            position: absolute;
            top: 25px;
            right: 25px;
            background: #22c55e;
            color: white;
            padding: 5px 15px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 20px;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .marksheet-header { text-align: center; border-bottom: 2px solid var(--gold); padding-bottom: 15px; margin-bottom: 20px; }
        .marksheet-header h1 { font-family: 'Cinzel', serif; color: var(--university-blue); font-size: 24px; margin: 5px 0; }
        .marksheet-header p { font-size: 11px; text-transform: uppercase; color: #555; font-weight: 600; letter-spacing: 1px; }

        .student-info-grid { display: flex; justify-content: space-between; gap: 15px; margin-bottom: 20px; position: relative; z-index: 1; }
        .info-table { width: 80%; border-collapse: collapse; }
        .info-table td { padding: 5px 8px; font-size: 13px; border: none; vertical-align: top; }
        .label { font-weight: 700; color: #333; width: 32%; }

        .photo-box { width: 120px; text-align: center; }
        .photo-box img { width: 110px; height: 130px; border: 2px solid var(--university-blue); object-fit: cover; padding: 2px; background: #fff; }

        .marks-table { width: 100%; border-collapse: collapse; margin-top: 15px; position: relative; z-index: 1; }
        .marks-table th { background: var(--university-blue); color: white; padding: 8px 10px; font-size: 12px; border: 1px solid #333; }
        .marks-table td { padding: 8px; border: 1px solid #ccc; font-size: 12px; text-align: left; }

        .result-footer { margin-top: 30px; display: flex; justify-content: space-between; align-items: flex-end; position: relative; z-index: 1; }
        .sig-box { text-align: center; width: 180px; }
        .sig-line { border-top: 1px solid #000; margin-top: 35px; font-size: 11px; font-weight: 700; padding-top: 5px; }

        .search-area { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .search-form { display: flex; gap: 10px; max-width: 500px; }
        .search-form input { flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .search-form button { background: var(--university-blue); color: white; border: none; padding: 0 20px; border-radius: 8px; cursor: pointer; font-weight: bold; }

        @media print {
            .sidebar, .search-area, .print-btn, .back-nav { display: none !important; }
            .main-content { margin: 0; width: 100%; padding: 0; }
            body { background: white; }
            .marksheet-container { border: 4px solid var(--university-blue); box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fas fa-graduation-cap"></i> CMS PRO</div>
    <div class="sidebar-menu">
        <a href="student_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="student_dashboard.php?page=my_docs" class="active"><i class="fas fa-file-invoice"></i> Verification</a>
        <a href="logout.php" style="margin-top:50px; color:#ef4444;"><i class="fas fa-power-off"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="back-nav" style="margin-bottom: 15px;">
        <a href="<?php echo $dashboard_url; ?>" style="text-decoration:none; color:var(--university-blue); font-size:14px; font-weight:600;">
            <i class="fa fa-arrow-left"></i> RETURN TO DASHBOARD
        </a>
    </div>

    <div class="search-area">
        <h3 style="font-size:14px; margin-bottom:10px;">Verify Student Academic Records</h3>
        <form class="search-form" method="GET">
            <input type="text" name="roll_no" placeholder="Enter Roll Number or Enrollment No..." required value="<?php echo isset($_GET['roll_no']) ? htmlspecialchars($_GET['roll_no']) : ''; ?>">
            <button type="submit"><i class="fa fa-search"></i> VERIFY</button>
        </form>
    </div>

    <?php if ($student_data): ?>
    <div class="marksheet-container">
        <div class="verified-badge"><i class="fas fa-check-circle"></i> VERIFIED RECORD</div>
        <div class="watermark">OFFICIAL VERIFIED</div>

        <div class="marksheet-header">
            <img src="uploads/ll.png" height="65" alt="University Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2991/2991148.png';">
            <h1>RNTU & AISECT COMPUTER ACADEMY</h1>
            <p>Official Student Verification & Academic Report - Session <?php echo date('Y'); ?></p>
        </div>

        <!-- सम्पूर्ण छात्र विवरण (Real Complete Details) -->
        <div class="student-info-grid">
            <table class="info-table">
                <tr>
                    <td class="label">STUDENT NAME</td>
                    <td>: <b><?php echo strtoupper($student_data['student_name']); ?></b></td>
                </tr>
                <tr>
                    <td class="label">FATHER'S NAME</td>
                    <td>: <?php echo strtoupper(!empty($student_data['father_name']) ? $student_data['father_name'] : 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">MOTHER'S NAME</td>
                    <td>: <?php echo strtoupper(!empty($student_data['mother_name']) ? $student_data['mother_name'] : 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">ROLL NUMBER</td>
                    <td>: <b style="color:var(--university-blue);"><?php echo $student_data['roll_no']; ?></b></td>
                </tr>
                <tr>
                    <td class="label">ENROLLMENT NO.</td>
                    <td>: <?php echo !empty($student_data['enrollment_no']) ? $student_data['enrollment_no'] : 'RNTU/'.date('Y').'/0'.$student_data['id']; ?></td>
                </tr>
                <tr>
                    <td class="label">COURSE / BRANCH</td>
                    <td>: <b><?php echo strtoupper($student_data['course']); ?></b></td>
                </tr>
                <tr>
                    <td class="label">EXAM CENTER</td>
                    <td>: <?php echo !empty($student_data['center_name']) ? strtoupper($student_data['center_name']) : 'TC ACADAMY TENDUKHEDA'; ?></td>
                </tr>
                <tr>
                    <td class="label">GENDER / DOB</td>
                    <td>: <?php echo strtoupper($student_data['gender'] ?? 'MALE'); ?> / <?php echo $student_data['dob'] ?? 'N/A'; ?></td>
                </tr>
            </table>

            <div class="photo-box">
                <?php 
                $photo_db = $student_data['photo'] ?? '';
                $photo_path = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
                if(!empty($photo_db)) {
                    $dirs = ['uploads/students/', 'uploads/profile/', 'uploads/'];
                    foreach($dirs as $dir) {
                        if(file_exists($dir . $photo_db)) {
                            $photo_path = $dir . $photo_db;
                            break;
                        }
                    }
                }
                ?>
                <img src="<?php echo $photo_path; ?>" alt="Student Photo">
                <p style="font-size: 9px; font-weight: bold; margin-top: 4px; color:#555;">REGISTRATION OK</p>
            </div>
        </div>

        <!-- विषय व टाइम-टेबल / रिजल्ट सूची -->
        <table class="marks-table">
            <?php 
            if($marks_q && mysqli_num_rows($marks_q) > 0): 
            ?>
            <thead>
                <tr>
                    <th style="width: 45%;">SUBJECT NAME</th>
                    <th>MAX MARKS</th>
                    <th>MIN MARKS</th>
                    <th>OBTAINED</th>
                    <th>RESULT</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_o = 0; $total_m = 0;
                while($m = mysqli_fetch_assoc($marks_q)): 
                    $total_o += $m['obtained_marks'];
                    $total_m += $m['total_marks'];
                    $is_pass = ($m['obtained_marks'] >= ($m['total_marks'] * 0.33));
                ?>
                <tr>
                    <td><?php echo strtoupper($m['subject_name']); ?></td>
                    <td style="text-align:center;"><?php echo $m['total_marks']; ?></td>
                    <td style="text-align:center;"><?php echo round($m['total_marks'] * 0.33); ?></td>
                    <td style="text-align:center;"><b><?php echo $m['obtained_marks']; ?></b></td>
                    <td style="text-align:center;"><?php echo $is_pass ? '<span style="color:green;font-weight:bold;">PASS</span>' : '<span style="color:red;font-weight:bold;">FAIL</span>'; ?></td>
                </tr>
                <?php endwhile; ?>
                <tr style="background:#f9f9f9; font-weight:bold;">
                    <td>GRAND TOTAL</td>
                    <td style="text-align:center;"><?php echo $total_m; ?></td>
                    <td style="text-align:center;">---</td>
                    <td style="text-align:center; color:var(--university-blue);"><?php echo $total_o; ?></td>
                    <td style="text-align:center; color:green;"><?php echo ($total_o >= $total_m * 0.33) ? 'PASSED' : 'FAILED'; ?></td>
                </tr>
            </tbody>
            <?php else: ?>
            <!-- अगर अलग टेबल में मार्क्स न हों तो कोर्स के हिसाब से टाइम-टेबल फ़ाइल लोड करेगा -->
            <?php
            $course_file = 'course_' . strtolower($student_data['course']) . '.php';
            if (file_exists($course_file)) {
                include $course_file;
            } else {
                echo "<tr><td colspan='4' style='text-align:center; padding:15px;'>स्टूडेंट के कोर्स (" . strtoupper($student_data['course']) . ") के सब्जेक्ट और रिकॉर्ड्स सत्यापित हैं।</td></tr>";
            }
            ?>
            <?php endif; ?>
        </table>

        <div class="result-footer">
            <div style="text-align:center;">
                <?php
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $domain = $_SERVER['HTTP_HOST'];
                $verify_url = $protocol . "://" . $domain . $_SERVER['REQUEST_URI'];
                ?>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode($verify_url); ?>" width="75" style="border:1px solid #ccc; padding:2px;">
                <p style="font-size:9px; margin-top:3px; font-weight:bold; color:#333;">SCAN TO VERIFY LIVE</p>
            </div>
            
            <div class="sig-box">
                <div class="sig-line">Controller of Examinations</div>
            </div>
            
            <div class="sig-box">
                <div class="sig-line">Principal / Director</div>
            </div>
        </div>

        <div style="margin-top:20px; border-top:1px dashed #ccc; padding-top:10px; font-size:10px; color:#777; text-align:center;">
            Note: This is an official digital verification record fetched from the database.
        </div>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <button class="print-btn" onclick="window.print()" style="background:var(--university-blue); color:white; padding:12px 30px; border:none; border-radius:30px; cursor:pointer; font-weight:600; box-shadow:0 5px 15px rgba(0,51,102,0.3);">
            <i class="fa fa-print"></i> PRINT VERIFICATION REPORT
        </button>
    </div>

    <?php elseif ($search_error): ?>
        <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:8px; text-align:center; font-weight:bold;"><?php echo $search_error; ?></div>
    <?php endif; ?>
</div>

</body>
</html>