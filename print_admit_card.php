<?php
include 'db_config.php';

if (!isset($_GET['id'])) {
    die("Student ID missing!");
}

$student_id = mysqli_real_escape_string($conn, $_GET['id']);

$stu_q = mysqli_query($conn, "SELECT s.*, u.name as student_name, u.email, u.password 
                              FROM students s 
                              JOIN users u ON s.user_id = u.id 
                              WHERE s.id = '$student_id'");
$stu = mysqli_fetch_assoc($stu_q);

if (!$stu) { die("Student record not found!"); }

$photo_db = $stu['photo'];
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

// Google Lens संगत वेरिफिकेशन URL (जरूरत पड़ने पर डोमेन का नाम बदलें)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$domain = $_SERVER['HTTP_HOST'];
$verify_link = $protocol . "://" . $domain . "/verify.php?roll_no=" . urlencode($stu['roll_no']);

// QR डेटा एन्कोडिंग
$qr_data = urlencode($verify_link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admit Card - <?php echo $stu['student_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #525659; margin: 0; padding: 20px; }
        .report-card { width: 210mm; min-height: 297mm; background: white; margin: auto; padding: 15mm; position: relative; box-shadow: 0 0 15px rgba(0,0,0,0.3); box-sizing: border-box; }
        .report-card::after { content: ""; position: absolute; top: 5mm; left: 5mm; right: 5mm; bottom: 5mm; border: 2px solid #34495e; pointer-events: none; }
        
        .header-section { position: relative; height: 150px; border-bottom: 3px solid #3498db; margin-bottom: 20px; padding-bottom: 10px; }
        .logo-box { position: absolute; top: 50%; left: 35%; transform: translate(-50%, -50%); width: 500px; text-align: center; }
        .logo-box img { width: 100%; height: auto; object-fit: contain; }
        
        .student-photo-box { position: absolute; top: 5px; right: 0px; width: 110px; height: 130px; border: 2px solid #34495e; padding: 2px; background: #fff; z-index: 10; overflow: hidden; }
        .student-photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .report-title-strip { background: #34495e; color: white; text-align: center; padding: 8px; font-weight: bold; font-size: 18px; margin-bottom: 20px; letter-spacing: 2px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; font-size: 14px; }
        .info-item { border-bottom: 1px dashed #ccc; padding: 5px 0; }
        .info-label { font-weight: bold; color: #555; width: 130px; display: inline-block; }

        .login-box { border: 2px dashed #3498db; padding: 15px; background: #f9f9f9; margin-bottom: 25px; text-align: center; }
        .login-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .login-table th, .login-table td { border: 1px solid #ddd; padding: 10px; font-size: 14px; }

        /* Time Table CSS */
        .tt-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .tt-table th, .tt-table td { border: 1px solid #333; padding: 8px; text-align: left; }
        .tt-head { background: #e0e0e0; font-weight: bold; }

        .qr-section { position: absolute; bottom: 60px; left: 45%; text-align: center; }
        .footer { position: absolute; bottom: 40px; width: 88%; display: flex; justify-content: space-between; padding: 0 10px; }
        .sig-line { border-top: 1px solid #000; width: 160px; text-align: center; padding-top: 5px; font-size: 12px; font-weight: bold; }

        .no-print-btn { position: fixed; top: 20px; right: 20px; background: #27ae60; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; z-index: 999; }
        @media print { .no-print-btn { display: none; } body { background: white; padding: 0; } .report-card { border: none; box-shadow: none; margin: 0; width: 100%; } }
    </style>
</head>
<body>

<a href="javascript:void(0)" class="no-print-btn" onclick="window.print()">Download Admit Card</a>

<div class="report-card">
    <div class="header-section">
        <div class="logo-box"><img src="uploads/ll.png" alt="University Logo"></div>
        <div class="student-photo-box"><img src="<?php echo $photo_path; ?>?v=<?php echo time(); ?>" alt="Student Photo"></div>
    </div>

    <div class="report-title-strip">ADMIT CARD - <?php echo date('Y'); ?></div>

    <div class="info-grid">
        <div class="info-item"><span class="info-label">Student Name:</span> <strong><?php echo strtoupper($stu['student_name']); ?></strong></div>
        <div class="info-item"><span class="info-label">Roll Number:</span> <?php echo $stu['roll_no']; ?></div>
        <div class="info-item"><span class="info-label">Course:</span> <?php echo strtoupper($stu['course']); ?></div>
        <div class="info-item"><span class="info-label">Exam Center:</span> TC ACADAMY TENDUKHEDA</div>
    </div>

    <div class="login-box">
        <h4 style="margin: 0; color: #34495e;">PORTAL LOGIN CREDENTIALS</h4>
        <table class="login-table">
            <tr><th>USERNAME (EMAIL)</th><th>PASSWORD</th></tr>
            <tr>
                <td style="font-weight:bold; color:#c0392b;"><?php echo $stu['roll_no']; ?></td>
                <td style="font-weight:bold; color:#c0392b;"><?php echo $stu['password']; ?></td>
            </tr>
        </table>
    </div>

    <table class="tt-table">
        <?php
        $course_file = 'course_' . strtolower($stu['course']) . '.php';

        if (file_exists($course_file)) {
            include $course_file;
        } else {
            echo "<tr><td colspan='3' style='text-align:center;'>इस कोर्स का टाइम-टेबल उपलब्ध नहीं है।</td></tr>";
        }
        ?>
    </table>

    <!-- Google Lens Compatible QR Code -->
    <div class="qr-section">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $qr_data; ?>" width="80" alt="Verification QR">
        <p style="font-size: 8px; margin-top: 4px; font-weight: bold; letter-spacing: 0.5px;">SCAN TO VERIFY</p>
    </div>

    <div class="footer">
        <div class="sig-line">Class Coordinator</div>
        <div class="sig-line">Exam Controller</div>
        <div class="sig-line">Principal Signature</div>
    </div>
</div>
</body>
</html>