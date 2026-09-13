<?php 
// 1. Session start safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// 2. Student login check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
// index.php mein humne 'id' fetch ki thi, ensure karein wo students table ki ID ho
$stu_id = $stu['student_table_id'] ?? $stu['id']; 

// 3. Fee Calculation Logic (Column name 'amount_paid' ke hisaab se)
$course_fee = 5000; 

// Check karein aapki table mein 'amount' hai ya 'amount_paid'
$fee_query = "SELECT SUM(amount_paid) as paid FROM fees WHERE student_id = '$stu_id'";
$fee_exec = mysqli_query($conn, $fee_query);

if($fee_exec) {
    $fee_res = mysqli_fetch_assoc($fee_exec);
    $total_paid = $fee_res['paid'] ?? 0;
} else {
    // Agar amount_paid nahi mila, toh 'amount' try karein
    $fee_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as paid FROM fees WHERE student_id = '$stu_id'"));
    $total_paid = $fee_res['paid'] ?? 0;
}

$due_amount = $course_fee - $total_paid;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Coaching Center</title>
    <style>
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f4f7f6; margin: 0; color: #333; }
        .navbar { background: #1a73e8; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        .alert { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeeba; margin-bottom: 25px; display: flex; align-items: center; }
        .alert-icon { font-size: 20px; margin-right: 15px; }

        .welcome-section { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .welcome-text h1 { margin: 0; color: #1a73e8; font-size: 28px; }
        .welcome-text p { margin: 5px 0 0; color: #666; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .box { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: transform 0.3s ease; }
        .box:hover { transform: translateY(-5px); }
        .box h3 { margin-top: 0; color: #1a73e8; border-bottom: 2px solid #f0f2f5; padding-bottom: 12px; display: flex; align-items: center; }
        
        .btn-group { display: flex; flex-direction: column; gap: 10px; }
        .btn { padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; transition: 0.2s; border: none; }
        .btn-exam { background: #34a853; color: white; }
        .btn-fees { background: #4285f4; color: white; }
        .btn-id { background: #fbbc05; color: #333; }
        .btn-logout { background: #ea4335; color: white; margin-top: 10px; }

        .list-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f1f1; }
        .status-present { background: #e6f4ea; color: #1e8e3e; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-new { background: #ea4335; color: white; font-size: 10px; padding: 2px 5px; border-radius: 3px; margin-left: 5px; vertical-align: middle; }
    </style>
</head>
<body>

<div class="navbar">
    <div style="font-size: 20px; font-weight: bold;">🎓 Student Portal</div>
    <div>
        <span style="margin-right: 15px;"><?php echo $stu['email']; ?></span>
        <span style="background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">ID: <?php echo $stu_id; ?></span>
    </div>
</div>

<div class="container">
    
    <?php if($due_amount > 0): ?>
    <div class="alert">
        <span class="alert-icon">⚠️</span>
        <div>
            <strong>Important:</strong> Aapki ₹<?php echo number_format($due_amount); ?> fees baki hai.
        </div>
    </div>
    <?php endif; ?>

    <div class="welcome-section">
        <div class="welcome-text">
            <h1>Namaste, <?php echo $stu['name']; ?>! 👋</h1>
            <p>Course: <strong><?php echo $stu['course']; ?></strong></p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 12px; color: #999;">FEE STATUS</div>
            <div style="font-size: 20px; font-weight: bold; color: <?php echo ($due_amount > 0) ? '#ea4335' : '#34a853'; ?>">
                <?php echo ($due_amount > 0) ? "Due: ₹" . number_format($due_amount) : "Fully Paid ✅"; ?>
            </div>
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <h3>🚀 Quick Actions</h3>
            <div class="btn-group">
                <a href="online_exam.php" class="btn btn-exam">📝 Take Online Test</a>
                <a href="student_fees.php" class="btn btn-fees">🧾 My Fee History</a>
                <a href="id_card.php?id=<?php echo $stu_id; ?>" class="btn btn-id" target="_blank">🪪 Download ID Card</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>

        <div class="box">
            <h3>📅 Recent Attendance</h3>
            <?php
            // Student ID se attendance nikalna
            $att_query = "SELECT * FROM attendance WHERE student_id='$stu_id' ORDER BY date DESC LIMIT 5";
            $att = mysqli_query($conn, $att_query);
            
            if($att && mysqli_num_rows($att) > 0){
                while($row = mysqli_fetch_assoc($att)){
                    echo "<div class='list-item'>
                            <span>" . date("d M, Y", strtotime($row['date'])) . "</span> 
                            <span class='status-present'>{$row['status']}</span>
                          </div>";
                }
            } else { echo "<p style='color:#999;'>No record found.</p>"; }
            ?>
        </div>

        <div class="box">
            <h3>📚 Study Materials <span class="badge-new">NEW</span></h3>
            <?php
            $hw = mysqli_query($conn, "SELECT * FROM homework ORDER BY id DESC LIMIT 5");
            if($hw && mysqli_num_rows($hw) > 0){
                while($h = mysqli_fetch_assoc($hw)){
                    // Agar homework table mein 'file' ya 'file_path' column ho
                    $file = $h['file_path'] ?? $h['file'] ?? '';
                    echo "<div class='list-item'>
                            <span style='white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;'>{$h['title']}</span>
                            <a href='uploads/{$file}' target='_blank' style='color:#1a73e8; font-weight: bold; text-decoration: none;'>View PDF</a>
                          </div>";
                }
            } else { echo "<p style='color:#999;'>No study material available.</p>"; }
            ?>
        </div>
    </div>
</div>

</body>
</html>