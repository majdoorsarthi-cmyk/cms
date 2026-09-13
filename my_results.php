<?php 
// 1. Session and Header
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
include 'db_config.php'; 

// Auth Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

// 2. Fetch REAL Data
$session_stu_id = $_SESSION['student']['id'];
$query_student = mysqli_query($conn, "SELECT * FROM students WHERE id = '$session_stu_id'");
$stu_data = mysqli_fetch_assoc($query_student);

if(!$stu_data) {
    die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
            <h2 style='color:#ef4444;'>❌ Error: Student profile not found in database.</h2>
            <br><a href='student_dashboard.php'>← Back to Dashboard</a>
         </div>");
}

$student_name    = $stu_data['name']; 
$correct_roll    = trim($stu_data['roll_no']); 
$correct_course  = trim($stu_data['course']); 
$correct_session = trim($stu_data['session'] ?? '2024-2025'); 
$enroll_no       = isset($stu_data['enrollment_no']) ? $stu_data['enrollment_no'] : "CMS/".date('Y')."/".$session_stu_id;

$institute_name  = "CMS PRO UNIVERSITY ACADEMY"; 
$tagline         = "An ISO 9001:2015 Certified Educational Institution";

$show_result = false;
$error_msg = "";

if(!isset($_SESSION['captcha_code']) || empty($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = rand(1111, 9999);
}

// 3. Form Submission
if(isset($_POST['verify_roll'])) {
    $input_roll    = mysqli_real_escape_string($conn, trim($_POST['roll_no']));
    $input_captcha = $_POST['captcha'];

    if($input_captcha != $_SESSION['captcha_code']) {
        $error_msg = "❌ Invalid Captcha Code!";
    } elseif($input_roll !== $correct_roll) {
        $error_msg = "❌ Roll Number does not match our records!";
    } else {
        $show_result = true;
    }
    $_SESSION['captcha_code'] = rand(1111, 9999);
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Academic Transcript | <?php echo htmlspecialchars($student_name); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root { 
            --univ-blue: #1e3a8a; 
            --gold: #854d0e; 
            --border-color: #000;
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; min-height: 100vh; }

        /* Desktop UI Controls */
        .no-print { display: flex; justify-content: center; gap: 20px; padding: 20px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 12px 25px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; font-size: 15px; }
        .btn-blue { background: var(--univ-blue); color: white; }
        .btn-green { background: #059669; color: white; }

        /* Desktop Portal Search Card */
        .search-container { max-width: 450px; margin: 60px auto; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); border-top: 8px solid var(--univ-blue); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 8px; }
        .input-field { width: 100%; padding: 14px 16px; border: 2px solid #cbd5e1; border-radius: 12px; font-size: 16px; font-weight: 600; outline: none; transition: all 0.2s; }
        .input-field:focus { border-color: var(--univ-blue); box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1); }

        /* Marksheet Desktop Design */
        .marksheet-wrapper { width: 210mm; min-height: 297mm; margin: 30px auto; background: #fff; padding: 10px; box-shadow: 0 0 50px rgba(0,0,0,0.15); position: relative; overflow: hidden; border-radius: 4px; }
        .marksheet-wrapper::before { content: "CMS PRO ACADEMY"; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; font-weight: 900; color: rgba(0,0,0,0.03); z-index: 0; white-space: nowrap; pointer-events: none; }

        .outer-frame { border: 15px solid transparent; border-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png') 30 round; padding: 5px; height: 100%; }
        .inner-frame { border: 2px solid var(--border-color); padding: 30px; height: 100%; position: relative; z-index: 1; }

        .univ-header { text-align: center; border-bottom: 2px solid var(--univ-blue); padding-bottom: 20px; margin-bottom: 30px; position: relative; }
        .univ-header h1 { font-family: 'Cinzel', serif; font-size: 36px; color: var(--univ-blue); letter-spacing: 1px; }
        .univ-header p { font-size: 12px; font-weight: 700; color: var(--gold); text-transform: uppercase; }

        .doc-title { background: var(--univ-blue); color: white; padding: 8px 30px; display: inline-block; font-weight: 800; margin: 20px 0; letter-spacing: 2px; border-radius: 50px; font-size: 14px; }

        .student-info { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .student-info td { padding: 8px 5px; font-size: 14px; }
        .field-label { font-weight: 700; color: #334155; width: 150px; }
        .field-value { border-bottom: 1px dashed #94a3b8; font-weight: 800; color: #000; }

        .marks-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .marks-table th { border: 2px solid var(--border-color); padding: 12px; background: #f1f5f9; font-size: 13px; color: var(--univ-blue); font-weight: 800; }
        .marks-table td { border: 1px solid var(--border-color); padding: 12px; text-align: center; font-weight: 700; }

        .summary-box { margin-top: 30px; display: grid; grid-template-columns: repeat(4, 1fr); border: 2px solid var(--border-color); text-align: center; }
        .sum-col { padding: 15px; border-right: 1px solid var(--border-color); }
        .sum-col:last-child { border-right: none; }
        .sum-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; }
        .sum-data { font-size: 20px; font-weight: 800; color: var(--univ-blue); }

        .footer-sig { margin-top: 80px; display: flex; justify-content: space-between; align-items: flex-end; }

        /* Hidden Mobile Components for Desktop */
        .mobile-header, .mobile-bottom-nav { display: none; }

        /* 📱 MOBILE VIEW (PhonePe App UI & Big Fonts) */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; padding-top: 70px; padding-bottom: 85px; }

            .no-print { display: none !important; }

            /* PhonePe Mobile Top Bar */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 68px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { color: #ffffff; font-size: 20px; width: 42px; height: 42px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 13.5px !important; font-weight: 500; display: block; }

            /* Mobile Big Font Card Form */
            .search-container {
                margin: 20px 12px !important; padding: 28px 20px !important;
                border-radius: 24px !important; border-top: none !important;
                box-shadow: 0 8px 30px rgba(0,0,0,0.06) !important;
            }
            .search-container h2 { font-size: 24px !important; font-weight: 800 !important; color: #0f172a !important; margin-bottom: 24px !important; }
            
            .form-group label { font-size: 16px !important; font-weight: 800 !important; color: #1e293b !important; margin-bottom: 10px !important; }
            .input-field { padding: 18px 18px !important; font-size: 18px !important; font-weight: 700 !important; border-radius: 16px !important; border: 2px solid #cbd5e1 !important; }

            .btn-phonepe {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                padding: 18px !important; font-size: 19px !important; font-weight: 800 !important;
                border-radius: 16px !important; width: 100% !important; justify-content: center !important;
                box-shadow: 0 6px 20px rgba(95, 37, 159, 0.3) !important; color: #fff !important;
            }

            /* Responsive Marksheet Container on Mobile */
            .marksheet-wrapper {
                width: 100% !important; min-height: auto !important; margin: 10px 0 !important;
                padding: 10px !important; box-shadow: none !important; border-radius: 16px !important;
            }
            .inner-frame { padding: 15px !important; }
            .univ-header h1 { font-size: 24px !important; }
            .univ-header img { width: 50px !important; position: static !important; display: block; margin: 0 auto 10px auto; }
            .student-info td { font-size: 13px !important; display: block; width: 100% !important; padding: 4px 0 !important; }
            .summary-box { grid-template-columns: repeat(2, 1fr) !important; gap: 10px; border: none !important; }
            .sum-col { border: 1px solid #e2e8f0 !important; border-radius: 12px; }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 72px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 13px !important; font-weight: 700; width: 33%; }
            .phonepe-nav-item i { font-size: 23px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }

        @media print {
            body { background: none; padding: 0; }
            .no-print, .search-container, .mobile-header, .mobile-bottom-nav { display: none !important; }
            .marksheet-wrapper { margin: 0; box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe Top App Header -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="student_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>Academic Transcript</h3>
            <small><?php echo htmlspecialchars($student_name); ?></small>
        </div>
    </div>
    <div style="color:#fff; font-size:22px;">
        <i class="fa-solid fa-graduation-cap"></i>
    </div>
</div>

<!-- 🖥️ Desktop Action Bar -->
<div class="no-print">
    <a href="student_dashboard.php" class="btn btn-blue"><i class="fas fa-th-large"></i> DASHBOARD</a>
    <?php if($show_result): ?>
        <button onclick="window.print()" class="btn btn-green"><i class="fas fa-print"></i> PRINT MARKSHEET</button>
    <?php endif; ?>
</div>

<?php if(!$show_result): ?>
    <!-- ROLL NUMBER & CAPTCHA SEARCH CARD -->
    <div class="search-container">
        <h2>Academic Portal</h2>
        
        <?php if($error_msg): ?>
            <div style="background:#fef2f2; color:#dc2626; padding:14px; border-radius:12px; font-weight:700; text-align:center; font-size:15px; margin-bottom:20px; border:1px solid #fecaca;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ROLL NUMBER</label>
                <input type="text" name="roll_no" class="input-field" placeholder="Example: 2024101" value="<?php echo htmlspecialchars($correct_roll); ?>" required>
            </div>
            
            <div class="form-group">
                <label>SECURITY CAPTCHA: <b style="color:var(--phonepe-purple); font-size:22px; letter-spacing:3px; margin-left:8px;"><?php echo $_SESSION['captcha_code']; ?></b></label>
                <input type="number" name="captcha" class="input-field" placeholder="Enter code above" required>
            </div>
            
            <button type="submit" name="verify_roll" class="btn btn-phonepe">
                <i class="fa-solid fa-file-invoice me-2"></i> GENERATE TRANSCRIPT
            </button>
        </form>
    </div>

<?php else: ?>

    <!-- MARKSHEET / TRANSCRIPT RESULT VIEW -->
    <div class="marksheet-wrapper" id="printArea">
        <div class="outer-frame">
            <div class="inner-frame">
                
                <div class="univ-header">
                    <img src="https://cdn-icons-png.flaticon.com/512/2991/2991148.png" style="width: 80px; position: absolute; left: 40px; top: 10px;">
                    <h1><?php echo $institute_name; ?></h1>
                    <p><?php echo $tagline; ?></p>
                    <p style="color:#000; margin-top:5px; font-weight:700;">Academic Session: <?php echo $correct_session; ?></p>
                </div>

                <div style="text-align: center;">
                    <div class="doc-title">STATEMENT OF MARKS</div>
                </div>

                <table class="student-info">
                    <tr>
                        <td class="field-label">Student Name</td>
                        <td class="field-value">: <?php echo strtoupper($student_name); ?></td>
                        <td class="field-label" style="padding-left: 20px;">Roll Number</td>
                        <td class="field-value">: <?php echo $correct_roll; ?></td>
                    </tr>
                    <tr>
                        <td class="field-label">Course / Program</td>
                        <td class="field-value">: <?php echo strtoupper($correct_course); ?></td>
                        <td class="field-label" style="padding-left: 20px;">Enrollment No.</td>
                        <td class="field-value">: <?php echo $enroll_no; ?></td>
                    </tr>
                </table>

                <table class="marks-table">
                    <thead>
                        <tr>
                            <th width="15%">Subject Code</th>
                            <th width="50%" style="text-align: left;">Subject Title</th>
                            <th width="10%">Max</th>
                            <th width="15%">Obtained</th>
                            <th width="10%">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $t_max = 0; $t_obt = 0; $is_failed = false;
                        $res_marks = mysqli_query($conn, "SELECT * FROM marks WHERE student_id = '$session_stu_id'");
                        $code_counter = 1001;

                        if(mysqli_num_rows($res_marks) > 0){
                            while($m = mysqli_fetch_assoc($res_marks)):
                                $obt = (int)$m['obtained_marks'];
                                $max = (int)$m['total_marks'];
                                $t_max += $max; $t_obt += $obt;
                                $status = ($obt >= ($max * 0.33)) ? "P" : "F";
                                if($status == "F") $is_failed = true;
                        ?>
                        <tr>
                            <td>CS-<?php echo $code_counter++; ?></td>
                            <td style="text-align: left; font-weight:700;"><?php echo strtoupper($m['subject_name']); ?></td>
                            <td><?php echo $max; ?></td>
                            <td style="font-size: 18px; color: var(--univ-blue); font-weight:800;"><?php echo $obt; ?></td>
                            <td style="color: <?php echo ($status=='F')?'#dc2626':'#16a34a'; ?>; font-weight:800;"><?php echo $status; ?></td>
                        </tr>
                        <?php endwhile; } else { ?>
                            <tr>
                                <td colspan="5" style="padding:20px; color:#64748b;">कोई अंक (Marks) उपलब्ध नहीं हैं।</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php 
                    $perc = ($t_max > 0) ? ($t_obt / $t_max) * 100 : 0;
                    $final_status = ($is_failed || $perc < 33) ? "FAILED" : "PASSED";
                    $division = "FAIL";
                    if($final_status == "PASSED") {
                        if($perc >= 60) $division = "First Division";
                        elseif($perc >= 45) $division = "Second Division";
                        else $division = "Third Division";
                    }
                ?>

                <div class="summary-box">
                    <div class="sum-col">
                        <div class="sum-title">Total Marks</div>
                        <div class="sum-data"><?php echo $t_obt; ?>/<?php echo $t_max; ?></div>
                    </div>
                    <div class="sum-col">
                        <div class="sum-title">Percentage</div>
                        <div class="sum-data"><?php echo round($perc, 2); ?>%</div>
                    </div>
                    <div class="sum-col">
                        <div class="sum-title">Division</div>
                        <div class="sum-data"><?php echo $division; ?></div>
                    </div>
                    <div class="sum-col">
                        <div class="sum-title">Status</div>
                        <div class="sum-data" style="color: <?php echo ($final_status=='PASSED')?'#16a34a':'#dc2626'; ?>">
                            <?php echo $final_status; ?>
                        </div>
                    </div>
                </div>

                <div class="footer-sig">
                    <div style="text-align: center;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=VERIFIED_CMS_<?php echo $correct_roll; ?>_RESULT_<?php echo $final_status; ?>" style="border: 1px solid #000; padding: 2px;">
                        <p style="font-size: 9px; font-weight: 700; margin-top: 5px;">SCAN TO VERIFY</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="font-weight: 800; border-top: 2px solid #000; padding-top: 10px; width: 200px;">Controller of Examinations</p>
                        <p style="font-size: 11px; color: #64748b; font-weight:600;">Date: <?php echo date('d-m-Y'); ?></p>
                    </div>
                </div>

                <div style="margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    * Computer generated official transcript document.
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>

<!-- 📱 PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="student_dashboard.php" class="phonepe-nav-item">
        <i class="fa-solid fa-house"></i>
        <span>होम</span>
    </a>
    <a href="academic_transcript.php" class="phonepe-nav-item active">
        <i class="fa-solid fa-file-invoice"></i>
        <span>मार्कशीट</span>
    </a>
    <a href="online_exam.php" class="phonepe-nav-item">
        <i class="fa-solid fa-laptop-code"></i>
        <span>एग्जाम</span>
    </a>
</div>

</body>
</html>