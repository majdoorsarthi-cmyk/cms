<?php
include 'db_config.php';

if (!isset($_GET['id'])) {
    die("Student ID missing!");
}

$student_id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. Fetch Student, User & Profile Data (Join Fix - 's.*' includes the 'photo' column)
$stu_q = mysqli_query($conn, "SELECT s.*, u.name as student_name, u.email 
                              FROM students s 
                              JOIN users u ON s.user_id = u.id 
                              WHERE s.id = '$student_id'");
$stu = mysqli_fetch_assoc($stu_q);

if (!$stu) { die("Student record not found!"); }

// --- FIXED PHOTO PATH LOGIC (LIVE & SMART CHECK) ---
$photo_db = $stu['photo']; // Database column for photo name
$photo_path = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png"; // Default image if none found

if(!empty($photo_db)) {
    // List of directories where photos are usually stored in your CMS
    $dirs = ['uploads/students/', 'uploads/profile/', 'uploads/'];
    
    foreach($dirs as $dir) {
        if(file_exists($dir . $photo_db)) {
            $photo_path = $dir . $photo_db;
            break; // Stop searching once file is found
        }
    }
}

// 2. Fetch Marks (Ordered by Subject)
$marks_q = mysqli_query($conn, "SELECT * FROM marks WHERE student_id = '$student_id' ORDER BY id ASC");

// 3. Fetch Attendance (Real Count)
$att_q = mysqli_query($conn, "SELECT COUNT(*) as total_present FROM attendance WHERE student_id = '$student_id' AND status = 'Present'");
$att_data = mysqli_fetch_assoc($att_q);
$total_present = $att_data['total_present'] ?? 0;

// Function to calculate Grade
function getGrade($percentage) {
    if ($percentage >= 90) return "A+";
    if ($percentage >= 75) return "A";
    if ($percentage >= 60) return "B";
    if ($percentage >= 45) return "C";
    if ($percentage >= 33) return "D";
    return "E (Needs Improvement)";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Report - <?php echo $stu['student_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #525659; margin: 0; padding: 20px; }
        
        .report-card {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: auto;
            padding: 15mm;
            position: relative;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            box-sizing: border-box;
        }

        .report-card::after {
            content: "";
            position: absolute;
            top: 5mm; left: 5mm; right: 5mm; bottom: 5mm;
            border: 2px solid #34495e;
            pointer-events: none;
        }

        .header-section { display: flex; align-items: center; border-bottom: 3px solid #3498db; padding-bottom: 15px; margin-bottom: 20px; }
        .logo-box { width: 80px; height: 80px; margin-right: 20px; }
        
        .school-info { flex-grow: 1; }
        .school-name { font-size: 26px; font-weight: 800; color: #2c3e50; margin: 0; text-transform: uppercase; }
        
        /* Fixed Photo Container Style */
        .student-photo-box { width: 110px; height: 130px; border: 2px solid #34495e; padding: 2px; background: #fff; z-index: 10; overflow: hidden; }
        .student-photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .report-title-strip { background: #34495e; color: white; text-align: center; padding: 8px; font-weight: bold; font-size: 18px; margin-bottom: 20px; letter-spacing: 2px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; font-size: 14px; }
        .info-item { border-bottom: 1px dashed #ccc; padding: 5px 0; }
        .info-label { font-weight: bold; color: #555; width: 130px; display: inline-block; }

        .score-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .score-table th { background: #f2f2f2; border: 1px solid #333; padding: 10px; font-size: 13px; }
        .score-table td { border: 1px solid #333; padding: 10px; text-align: center; font-size: 14px; }
        .score-table .subject-name { text-align: left; font-weight: bold; }

        .performance-bar-container { background: #eee; height: 12px; border-radius: 6px; width: 100%; margin: 10px 0; overflow: hidden; border: 1px solid #ddd; }
        .performance-fill { height: 100%; background: linear-gradient(to right, #e74c3c, #f1c40f, #2ecc71); }

        .summary-flex { display: flex; justify-content: space-between; gap: 15px; margin-bottom: 25px; }
        .summary-card { flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 5px; text-align: center; background: #f9f9f9; }
        .summary-val { font-size: 18px; font-weight: bold; color: #2c3e50; display: block; margin-top: 5px; }

        .footer { margin-top: 60px; display: flex; justify-content: space-between; padding: 0 10px; position: relative; }
        .sig-line { border-top: 1px solid #000; width: 160px; text-align: center; padding-top: 5px; font-size: 12px; font-weight: bold; }

        .qr-section { position: absolute; bottom: 40px; left: 45%; text-align: center; }

        .no-print-btn {
            position: fixed; top: 20px; right: 20px; background: #27ae60; color: white; 
            padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; z-index: 999;
        }

        @media print {
            .no-print-btn { display: none; }
            body { background: white; padding: 0; }
            .report-card { border: none; box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

<a href="javascript:void(0)" class="no-print-btn" onclick="window.print()">Download Progress Report</a>

<div class="report-card">
    <div class="header-section">
        <div class="logo-box">
            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" width="80" alt="Logo">
        </div>
        <div class="school-info">
            <h1 class="school-name">CMS PRO ACADEMY</h1>
            <p style="margin: 3px 0; font-size: 14px; font-weight: bold; color: #34495e;">ISO 9001:2015 Certified Institution</p>
            <p style="margin: 0; font-size: 12px; color: #666;">Educational Excellence Center, State - 000001</p>
        </div>
        <div class="student-photo-box">
            <img src="<?php echo $photo_path; ?>?v=<?php echo time(); ?>" alt="Student Photo">
        </div>
    </div>

    <div class="report-title-strip">PROGRESS REPORT (SESSION <?php echo date('Y'); ?>)</div>

    <div class="info-grid">
        <div class="info-item"><span class="info-label">Student Name:</span> <strong><?php echo strtoupper($stu['student_name']); ?></strong></div>
        <div class="info-item"><span class="info-label">Roll Number:</span> <?php echo $stu['roll_no']; ?></div>
        <div class="info-item"><span class="info-label">Father's Name:</span> <?php echo strtoupper($stu['father_name'] ?? 'Not Mentioned'); ?></div>
        <div class="info-item"><span class="info-label">Course:</span> <?php echo strtoupper($stu['course']); ?></div>
        <div class="info-item"><span class="info-label">Attendance:</span> <?php echo $total_present; ?> Days Present</div>
        <div class="info-item"><span class="info-label">Report Date:</span> <?php echo date('d-M-Y'); ?></div>
    </div>

    <table class="score-table">
        <thead>
            <tr>
                <th width="40%">SUBJECT NAME</th>
                <th>MAX MARKS</th>
                <th>OBTAINED</th>
                <th>GRADE</th>
                <th>RESULT</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_max = 0;
            $total_got = 0;
            if(mysqli_num_rows($marks_q) > 0) {
                while($m = mysqli_fetch_assoc($marks_q)) {
                    $total_max += $m['total_marks'];
                    $total_got += $m['obtained_marks'];
                    $p_sub = ($m['obtained_marks'] / $m['total_marks']) * 100;
                    $sub_grade = getGrade($p_sub);
                    $status = ($p_sub >= 33) ? "PASS" : "FAIL";
                    
                    echo "<tr>
                            <td class='subject-name'>".strtoupper(htmlspecialchars($m['subject_name']))."</td>
                            <td>".$m['total_marks']."</td>
                            <td>".$m['obtained_marks']."</td>
                            <td>".$sub_grade."</td>
                            <td style='color: ".($status == 'PASS' ? '#27ae60' : '#e74c3c')."; font-weight:bold;'>$status</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Marks not updated yet.</td></tr>";
            }
            $percentage = ($total_max > 0) ? ($total_got / $total_max) * 100 : 0;
            ?>
        </tbody>
    </table>

    <p style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">Performance Analysis: <?php echo round($percentage, 2); ?>%</p>
    <div class="performance-bar-container">
        <div class="performance-fill" style="width: <?php echo $percentage; ?>%;"></div>
    </div>

    <div class="summary-flex">
        <div class="summary-card">
            <span style="font-size: 12px; color: #666; font-weight:bold;">MARKS SECURED</span>
            <span class="summary-val"><?php echo $total_got; ?> / <?php echo $total_max; ?></span>
        </div>
        <div class="summary-card">
            <span style="font-size: 12px; color: #666; font-weight:bold;">PERCENTAGE</span>
            <span class="summary-val"><?php echo round($percentage, 2); ?>%</span>
        </div>
        <div class="summary-card" style="background: #34495e; color: white;">
            <span style="font-size: 12px; color: #fff; opacity: 0.9;">OVERALL GRADE</span>
            <span class="summary-val" style="color: #f1c40f;"><?php echo getGrade($percentage); ?></span>
        </div>
    </div>

    <div style="border: 1px solid #333; padding: 12px; border-radius: 4px; background: #fff;">
        <span style="font-weight: bold; text-decoration: underline; font-size: 14px;">EXAMINER'S REMARKS:</span>
        <p style="margin: 8px 0; font-size: 14px; line-height: 1.5; color: #2c3e50 italic;">
            <?php 
                if($percentage >= 80) echo "Excellent performance! The student shows great dedication and potential.";
                elseif($percentage >= 60) echo "Very good progress. Regular practice in practical sessions will help in achieving top grades.";
                elseif($percentage >= 33) echo "Satisfactory. Needs more focus on core subjects and regular attendance.";
                else echo "Performance is below average. Immediate improvement required.";
            ?>
        </p>
    </div>

    <div class="qr-section">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=VERIFY-<?php echo $stu['roll_no']; ?>" width="65">
        <p style="font-size: 8px; margin-top: 4px; font-weight: bold;">SECURE VERIFIED</p>
    </div>

    <div class="footer">
        <div class="sig-line">Class Coordinator</div>
        <div class="sig-line">Exam Controller</div>
        <div class="sig-line" style="border:none;">
             <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/Signature_of_Anas_Aremeyaw_Anas.png" width="80" style="margin-bottom:-12px; filter: grayscale(1); opacity:0.8;"><br>
             Principal Signature
        </div>
    </div>
</div>

</body>
</html>