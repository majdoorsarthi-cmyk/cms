<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_config.php';

if(!isset($_SESSION['student']) || !isset($_GET['id'])) { 
    header("Location: index.php"); 
    exit(); 
}

$res_id = $_GET['id'];
$stu_id = $_SESSION['student']['id'];

// Fetch specific result and student info
$query = "SELECT r.*, s.name, s.roll_no, s.course 
          FROM results r 
          JOIN students s ON r.user_id = s.user_id 
          WHERE r.id = '$res_id' AND r.user_id = '$stu_id'";

$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

if(!$data) { die("Result not found."); }

$per = round(($data['obtained_marks'] / $data['total_marks']) * 100, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marksheet - <?php echo $data['name']; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 50px; background: #fff; }
        .marksheet-box { border: 10px double #333; padding: 30px; max-width: 800px; margin: auto; position: relative; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .details { display: flex; justify-content: space-between; margin-bottom: 30px; line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 15px; text-align: center; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .stamp { border: 1px dashed #ccc; width: 150px; height: 80px; display: flex; align-items: center; justify-content: center; color: #ccc; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; margin-bottom:20px;">
    <button onclick="window.print()" style="padding:10px 25px; background:#2ecc71; color:white; border:none; border-radius:5px; cursor:pointer;">
        🖨️ Print / Download PDF
    </button>
</div>

<div class="marksheet-box">
    <div class="header">
        <h1>ACADEMY PORTAL PRO</h1>
        <p>Certificate of Academic Excellence</p>
    </div>

    <div class="details">
        <div>
            <strong>Name:</strong> <?php echo strtoupper($data['name']); ?><br>
            <strong>Roll No:</strong> <?php echo $data['roll_no']; ?><br>
            <strong>Course:</strong> <?php echo $data['course']; ?>
        </div>
        <div style="text-align: right;">
            <strong>Exam:</strong> <?php echo $data['exam_name']; ?><br>
            <strong>Date:</strong> <?php echo date('d M, Y', strtotime($data['result_date'])); ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Maximum Marks</th>
                <th>Obtained Marks</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $data['subject']; ?></td>
                <td><?php echo $data['total_marks']; ?></td>
                <td><?php echo $data['obtained_marks']; ?></td>
                <td><?php echo $per; ?>%</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px; font-weight: bold; font-size: 1.2em;">
        Result Status: <?php echo ($per >= 33) ? "<span style='color:green'>PASSED</span>" : "<span style='color:red'>FAILED</span>"; ?>
    </div>

    <div class="footer">
        <div>
            <p>Date of Issue: <?php echo date('d-m-Y'); ?></p>
        </div>
        <div style="text-align:center;">
            <div class="stamp">Coaching Stamp</div>
            <p>Authorized Signatory</p>
        </div>
    </div>
</div>

</body>
</html>