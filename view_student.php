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

// 1. Student ki Basic Detail nikalna
$sql = "SELECT s.*, u.name, u.email, u.role FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.id = '$s_id'";
$res = mysqli_query($conn, $sql);
$stu = mysqli_fetch_assoc($res);

if(!$stu){ echo "Student Not Found!"; exit(); }

// 2. Fees History nikalna (amount_paid column ke saath)
$fees_res = mysqli_query($conn, "SELECT * FROM fees WHERE student_id = '$s_id' ORDER BY payment_date DESC");

// 3. Total Fees Paid calculate karna
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
    <title>Student Profile | Admin</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; display: flex; }
        .sidebar { width: 230px; height: 100vh; background: #2c3e50; color: #fff; position: fixed; padding: 20px; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 5px; }
        .main-content { margin-left: 270px; padding: 40px; width: calc(100% - 270px); }
        
        .profile-header { background: white; padding: 30px; border-radius: 12px; display: flex; align-items: center; gap: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .avatar { width: 120px; height: 120px; background: #3498db; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; font-weight: bold; }
        
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .card h3 { border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; color: #2c3e50; margin-top: 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { color: #7f8c8d; font-weight: 600; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .bg-success { background: #d4edda; color: #155724; }
        .btn-id { background: #3498db; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-size: 14px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Coaching Admin</h3>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="collect_fees.php">💰 Collect Fees</a>
    <a href="attendance_scanner.php">🔍 QR Scanner</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="profile-header">
        <div class="avatar"><?php echo strtoupper(substr($stu['name'], 0, 1)); ?></div>
        <div>
            <h1 style="margin:0;"><?php echo $stu['name']; ?></h1>
            <p style="color:#7f8c8d; margin:5px 0;">Roll No: <b><?php echo $stu['roll_no']; ?></b> | Course: <b><?php echo $stu['course']; ?></b></p>
            <br>
            <a href="id_card.php?id=<?php echo $stu['id']; ?>" class="btn-id">🖨️ Print ID Card</a>
        </div>
    </div>

    <div class="grid-container">
        <div class="card">
            <h3>💰 Fees Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($fees_res) > 0){
                        while($f = mysqli_fetch_assoc($fees_res)){
                            echo "<tr>
                                    <td>{$f['receipt_no']}</td>
                                    <td>₹" . number_format($f['amount_paid']) . "</td>
                                    <td>" . date('d M Y', strtotime($f['payment_date'])) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No fees records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div style="margin-top:20px; font-weight:bold; color:#27ae60;">
                Total Paid: ₹<?php echo number_format($total_paid); ?>
            </div>
        </div>

        <div class="card">
            <h3>📅 Attendance Summary</h3>
            <p>Total Present Days: <b class="badge bg-success" style="font-size:16px;"><?php echo $present_days; ?> Days</b></p>
            <hr>
            <h4>Recent Attendance</h4>
            <table>
                <?php 
                $att_list = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = '$s_id' ORDER BY date DESC LIMIT 5");
                while($a = mysqli_fetch_assoc($att_list)){
                    echo "<tr>
                            <td>" . date('d M, Y', strtotime($a['date'])) . "</td>
                            <td><span class='badge bg-success'>{$a['status']}</span></td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <a href="admin_dashboard.php" style="color: #3498db; text-decoration: none;">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>