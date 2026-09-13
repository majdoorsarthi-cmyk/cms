<?php
include 'db_config.php';

if (!isset($_GET['id'])) {
    die("Student ID not found!");
}

$student_id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. Fetch Student, User and Student Details (Joint Query)
$stu_q = mysqli_query($conn, "SELECT s.*, u.name as student_name, u.email 
                              FROM students s 
                              JOIN users u ON s.user_id = u.id 
                              WHERE s.id = '$student_id'");
$stu = mysqli_fetch_assoc($stu_q);

if (!$stu) {
    die("Student record not found!");
}

// 2. Fetch Marks Data
$marks_q = mysqli_query($conn, "SELECT * FROM marks WHERE student_id = '$student_id' ORDER BY id ASC");

// Function for Grading (Standard Academic Logic)
function calculateGrade($marks, $total) {
    if ($total <= 0) return 'N/A';
    $p = ($marks / $total) * 100;
    if ($p >= 90) return 'A+';
    if ($p >= 80) return 'A';
    if ($p >= 70) return 'B';
    if ($p >= 60) return 'C';
    if ($p >= 33) return 'D';
    return 'F';
}

// Serial Number Logic
$serial_no = "CMS/" . date('Y') . "/" . str_pad($stu['id'], 4, '0', STR_PAD_LEFT);
// Enrollment Number Logic
$enrollment_no = "CMS-" . date('y', strtotime($stu['created_at'] ?? 'now')) . "-" . str_pad($stu['id'], 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Original Marksheet - <?php echo $stu['student_name']; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Poppins:wght@300;400;600&display=swap');

        body { background: #525659; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        
        .marksheet-page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            position: relative;
            box-sizing: border-box;
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
        }

        /* Border design */
        .outer-border {
            border: 5px solid #1a252f;
            height: 275mm;
            padding: 5px;
            position: relative;
        }
        .inner-border {
            border: 2px solid #b8860b; /* Gold Inner Border */
            height: 100%;
            padding: 15px;
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 70px;
            color: rgba(44, 62, 80, 0.04);
            white-space: nowrap;
            user-select: none;
            z-index: 0;
            font-family: 'Cinzel', serif;
        }

        .header { text-align: center; position: relative; z-index: 1; border-bottom: 2px solid #1a252f; padding-bottom: 10px; }
        .header h1 { font-family: 'Cinzel', serif; margin: 0; font-size: 34px; color: #1a252f; letter-spacing: 2px; }
        .header p { margin: 2px; font-size: 13px; font-weight: 600; color: #34495e; }

        .serial-no { position: absolute; top: 10px; right: 10px; font-weight: bold; font-family: 'Courier New', monospace; font-size: 14px; color: #e74c3c; }

        .title-strip {
            background: #1a252f;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        .student-details { width: 100%; margin-bottom: 25px; font-size: 15px; border-collapse: collapse; z-index: 1; position: relative; }
        .student-details td { padding: 8px 5px; border-bottom: 1px solid #eee; }

        .marks-table { width: 100%; border-collapse: collapse; position: relative; z-index: 1; background: rgba(255,255,255,0.8); }
        .marks-table th { background: #f8f9fa; border: 1px solid #000; padding: 12px; font-size: 14px; text-transform: uppercase; }
        .marks-table td { border: 1px solid #000; padding: 10px; text-align: center; font-size: 15px; }

        .result-box { margin-top: 20px; font-size: 16px; font-weight: bold; border: 1px solid #000; padding: 10px; display: inline-block; }

        .footer { margin-top: 80px; display: flex; justify-content: space-between; position: relative; z-index: 1; padding: 0 20px; }
        .sig-box { text-align: center; width: 200px; }
        .sig-box p { border-top: 1px solid #000; margin-top: 40px; font-weight: bold; font-size: 14px; color: #1a252f; }

        .qr-code { position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); text-align: center; }
        .qr-code p { font-size: 10px; font-weight: bold; margin-top: 5px; color: #555; }

        .btn-container { text-align: center; margin: 20px; }
        .print-btn { background: #27ae60; color: white; padding: 15px 40px; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; font-weight: bold; transition: 0.3s; }
        .print-btn:hover { background: #219150; }

        @media print {
            .btn-container { display: none; }
            body { background: white; }
            .marksheet-page { margin: 0; box-shadow: none; width: 100%; padding: 5mm; }
            .outer-border { height: 285mm; }
        }
    </style>
</head>
<body>

<div class="btn-container">
    <button class="print-btn" onclick="window.print()">Print Official Mark Statement</button>
</div>

<div class="marksheet-page">
    <div class="outer-border">
        <div class="inner-border">
            <div class="watermark">CMS PRO ACADEMY</div>
            
            <div class="serial-no">S.No: <?php echo $serial_no; ?></div>

            <div class="header">
                <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" width="70" alt="Logo"><br>
                <h1>CMS PRO ACADEMY</h1>
                <p>An ISO 9001:2015 Certified Educational Institution</p>
                <p>Registered under Ministry of Education (Govt. of India)</p>
                <p>Email: info@cmsproacademy.com | Web: www.cmsproacademy.com</p>
            </div>

            <div class="title-strip">MARK STATEMENT</div>

            <table class="student-details">
                <tr>
                    <td width="22%"><b>NAME OF CANDIDATE</b></td>
                    <td width="33%">: <?php echo strtoupper($stu['student_name']); ?></td>
                    <td width="20%"><b>ROLL NUMBER</b></td>
                    <td width="25%">: <?php echo $stu['roll_no']; ?></td>
                </tr>
                <tr>
                    <td><b>FATHER'S NAME</b></td>
                    <td>: <?php echo strtoupper($stu['father_name'] ?? 'Not Recorded'); ?></td>
                    <td><b>ENROLLMENT NO</b></td>
                    <td>: <?php echo $enrollment_no; ?></td>
                </tr>
                <tr>
                    <td><b>COURSE NAME</b></td>
                    <td>: <?php echo strtoupper($stu['course']); ?></td>
                    <td><b>SESSION / YEAR</b></td>
                    <td>: <?php echo date('Y'); ?></td>
                </tr>
            </table>

            <table class="marks-table">
                <thead>
                    <tr>
                        <th width="10%">CODE</th>
                        <th width="45%">SUBJECTS</th>
                        <th width="15%">MAX MARKS</th>
                        <th width="15%">OBTAINED</th>
                        <th width="15%">GRADE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_max = 0;
                    $total_obtained = 0;
                    $is_failed = false;
                    $counter = 501; // Subject Codes Start
                    
                    if (mysqli_num_rows($marks_q) > 0) {
                        while ($row = mysqli_fetch_assoc($marks_q)) {
                            $total_max += $row['total_marks'];
                            $total_obtained += $row['obtained_marks'];
                            $grade = calculateGrade($row['obtained_marks'], $row['total_marks']);
                            if ($grade == 'F') $is_failed = true;
                            
                            echo "<tr>
                                    <td>".$counter++."</td>
                                    <td style='text-align:left; padding-left:20px;'>".strtoupper($row['subject_name'])."</td>
                                    <td>".$row['total_marks']."</td>
                                    <td><b>".$row['obtained_marks']."</b></td>
                                    <td>$grade</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding:50px;'>No marks data available for this student.</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f2f2f2; font-weight:bold;">
                        <td colspan="2">TOTAL MARKS AGGREGATE</td>
                        <td><?php echo $total_max; ?></td>
                        <td><?php echo $total_obtained; ?></td>
                        <td>
                            <?php 
                                if($total_max > 0) echo round(($total_obtained/$total_max)*100, 1)."%";
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="result-box">
                RESULT: <?php echo (!$is_failed && $total_max > 0) ? "<span style='color:#27ae60'>PASSED</span>" : "<span style='color:#e74c3c'>PROVISIONAL / FAILED</span>"; ?>
            </div>

            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=VERIFY-<?php echo $stu['roll_no']; ?>-<?php echo $total_obtained; ?>" alt="QR Verification">
                <p>Scan to verify authenticity</p>
            </div>

            <div class="footer">
                <div class="sig-box">
                    <p>Signature of Student</p>
                </div>
                <div class="sig-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/Signature_of_Anas_Aremeyaw_Anas.png" width="100" style="margin-bottom:-15px;" alt="Signature">
                    <p>Controller of Examination</p>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>