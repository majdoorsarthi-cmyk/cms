<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Admin check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// Student ID check
if(!isset($_GET['id'])){
    header("Location: admin_dashboard.php");
    exit();
}

$s_id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. Student ki Detail nikalna (Photo ke saath)
$sql = "SELECT s.*, u.name, u.email FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.id = '$s_id'";
$res = mysqli_query($conn, $sql);
$stu = mysqli_fetch_assoc($res);

if(!$stu){ echo "Student Not Found!"; exit(); }

// Photo logic
$photo_path = !empty($stu['photo']) ? "uploads/students/" . $stu['photo'] : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";

// 2. Fees History
$fees_res = mysqli_query($conn, "SELECT * FROM fees WHERE student_id = '$s_id' ORDER BY payment_date DESC");

// 3. Total Fees Paid
$total_paid_res = mysqli_query($conn, "SELECT SUM(amount_paid) as total FROM fees WHERE student_id = '$s_id'");
$total_paid = mysqli_fetch_assoc($total_paid_res)['total'] ?? 0;

// 4. Attendance Count
$att_count_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE student_id = '$s_id' AND status='Present'");
$present_days = mysqli_fetch_assoc($att_count_res)['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Control Panel | <?php echo $stu['name']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 230px; height: 100vh; background: #2c3e50; color: #fff; position: fixed; padding: 20px; }
        .sidebar h3 { font-size: 18px; border-bottom: 1px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 5px; transition: 0.3s; }
        .sidebar a:hover { background: #34495e; color: #fff; }
        
        .main-content { margin-left: 270px; padding: 40px; width: calc(100% - 270px); }
        
        .profile-header { background: white; padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .profile-img { width: 110px; height: 110px; border-radius: 12px; object-fit: cover; border: 3px solid #3498db; }
        
        .action-buttons { margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-id { background: #4361ee; color: white; }
        .btn-mark { background: #2ecc71; color: white; }
        .btn-report { background: #f39c12; color: white; }
        .btn-cert { background: #9b59b6; color: white; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }

        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .card h3 { border-bottom: 2px solid #f8f9fa; padding-bottom: 10px; color: #2c3e50; margin-top: 0; font-size: 18px; display: flex; align-items: center; gap: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
        th { color: #95a5a6; font-weight: 600; text-transform: uppercase; font-size: 11px; }
        
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .bg-present { background: #d4edda; color: #155724; }
        
        .stats-footer { margin-top:15px; padding: 15px; background: #f8f9fa; border-radius: 8px; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3><i class="fas fa-university"></i> CMS ADMIN</h3>
    <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_students.php"><i class="fas fa-users"></i> Students</a>
    <a href="collect_fees.php"><i class="fas fa-hand-holding-usd"></i> Collect Fees</a>
    <a href="attendance_scanner.php"><i class="fas fa-qrcode"></i> QR Scanner</a>
    <a href="logout.php" style="margin-top: 50px; color: #e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="profile-header">
        <img src="<?php echo $photo_path; ?>" class="profile-img">
        <div style="flex-grow: 1;">
            <h1 style="margin:0; font-size: 24px; color: #2c3e50;"><?php echo strtoupper($stu['name']); ?></h1>
            <p style="color:#7f8c8d; margin:5px 0;">
                Roll: <b>#<?php echo $stu['roll_no']; ?></b> | 
                Course: <b><?php echo $stu['course']; ?></b> | 
                Email: <b><?php echo $stu['email']; ?></b>
            </p>
            
            <div class="action-buttons">
                <a href="print_id_card.php?id=<?php echo $stu['id']; ?>" target="_blank" class="btn btn-id">
                    <i class="fas fa-id-card"></i> ID Card
                </a>
                <a href="print_marksheet.php?id=<?php echo $stu['id']; ?>" target="_blank" class="btn btn-mark">
                    <i class="fas fa-file-invoice"></i> Marksheet
                </a>
                <a href="print_report_card.php?id=<?php echo $stu['id']; ?>" target="_blank" class="btn btn-report">
                    <i class="fas fa-chart-bar"></i> Progress Report
                </a>
                <a href="print_certificate.php?id=<?php echo $stu['id']; ?>" target="_blank" class="btn btn-cert">
                    <i class="fas fa-certificate"></i> Certificate
                </a>
            </div>
        </div>
    </div>

    <div class="grid-container">
        <div class="card">
            <h3><i class="fas fa-history" style="color: #27ae60;"></i> Fees Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($fees_res) > 0){
                        while($f = mysqli_fetch_assoc($fees_res)){
                            echo "<tr>
                                    <td>#{$f['receipt_no']}</td>
                                    <td style='font-weight:bold;'>₹" . number_format($f['amount_paid']) . "</td>
                                    <td>" . date('d M Y', strtotime($f['payment_date'])) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center;'>No fees records.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="stats-footer">
                <span style="color:#7f8c8d;">Total Fees Collected</span>
                <span style="font-weight:bold; color:#27ae60; font-size: 18px;">₹<?php echo number_format($total_paid); ?></span>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-calendar-check" style="color: #3498db;"></i> Attendance Analysis</h3>
            <div style="text-align: center; padding: 10px;">
                <span style="font-size: 30px; font-weight: bold; color: #3498db;"><?php echo $present_days; ?></span>
                <p style="margin:0; color: #7f8c8d; font-size: 13px;">Total Days Present</p>
            </div>
            <hr style="border:0; border-top:1px solid #f1f1f1; margin: 15px 0;">
            <h4>Recent Records</h4>
            <table>
                <?php 
                $att_list = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = '$s_id' ORDER BY date DESC LIMIT 5");
                if(mysqli_num_rows($att_list) > 0){
                    while($a = mysqli_fetch_assoc($att_list)){
                        echo "<tr>
                                <td><i class='far fa-calendar-alt'></i> " . date('d M, Y', strtotime($a['date'])) . "</td>
                                <td style='text-align:right;'><span class='status-pill bg-present'>{$a['status']}</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td>No attendance recorded yet.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <a href="admin_dashboard.php" style="color: #7f8c8d; text-decoration: none; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

</body>
</html>