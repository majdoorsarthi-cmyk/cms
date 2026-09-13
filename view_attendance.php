<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Admin login check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// Default date aaj ki rakhein, agar user ne select nahi ki
$view_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report | Admin</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 230px; height: 100vh; background: #2c3e50; color: #fff; position: fixed; padding: 20px; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; color: white; }
        .main-content { margin-left: 270px; padding: 40px; width: calc(100% - 270px); }
        
        .filter-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
        input[type="date"] { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-filter { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: white; }
        .status-present { color: #27ae60; font-weight: bold; background: #eafaf1; padding: 5px 10px; border-radius: 4px; }
        .no-data { text-align: center; padding: 40px; color: #7f8c8d; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Coaching Admin</h3>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="attendance_scanner.php">🔍 QR Scanner</a>
    <a href="view_attendance.php" style="background:#3498db; color:white;">📅 View Attendance</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <h2>📅 Attendance Report</h2>
    
    <div class="filter-box">
        <form method="GET" style="display:flex; align-items:center; gap:10px;">
            <label><b>Select Date:</b></label>
            <input type="date" name="date" value="<?php echo $view_date; ?>">
            <button type="submit" class="btn-filter">Get Report</button>
        </form>
        <button onclick="window.print()" class="btn-filter" style="background:#2ecc71;">🖨️ Print Report</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Course</th>
                <th>Status</th>
                <th>Marked At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // JOIN query to get student details and attendance
            $sql = "SELECT a.*, s.roll_no, s.course, u.name 
                    FROM attendance a
                    JOIN students s ON a.student_id = s.id
                    JOIN users u ON s.user_id = u.id
                    WHERE a.date = '$view_date'
                    ORDER BY a.id DESC";
            
            $res = mysqli_query($conn, $sql);
            
            if($res && mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    // Agar aapne time column banaya hai to wo dikhayega, warna sirf Status
                    $marked_time = isset($row['created_at']) ? date('h:i A', strtotime($row['created_at'])) : '--';
                    
                    echo "<tr>
                            <td><b>{$row['roll_no']}</b></td>
                            <td>{$row['name']}</td>
                            <td>{$row['course']}</td>
                            <td><span class='status-present'>{$row['status']}</span></td>
                            <td>$marked_time</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='no-data'>❌ No attendance found for " . date('d M, Y', strtotime($view_date)) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>