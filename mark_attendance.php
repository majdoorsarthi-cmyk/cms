<?php 
include 'db_config.php';

// Agar QR code URL se milta hai
if(isset($_GET['qr'])){
    $student_id = mysqli_real_escape_string($conn, $_GET['qr']); // Humne QR mein ID store ki hai
    $today = date('Y-m-d');
    $time = date('H:i:s');

    // 1. Pehle check karein ki ye Student ID database mein hai ya nahi
    $res = mysqli_query($conn, "SELECT id FROM students WHERE id='$student_id'");
    
    if($res && mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        $s_id = $row['id'];

        // 2. Check karein ki aaj ki attendance pehle se lag chuki hai?
        $check = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id='$s_id' AND date='$today'");
        
        if(mysqli_num_rows($check) == 0){
            // 3. Attendance Insert karein
            $insert = mysqli_query($conn, "INSERT INTO attendance (student_id, status, date) VALUES ('$s_id', 'Present', '$today')");
            
            if($insert){
                $status = "success";
                $msg = "✅ Attendance Marked: Present";
            } else {
                $status = "error";
                $msg = "❌ Error: " . mysqli_error($conn);
            }
        } else { 
            $status = "warning";
            $msg = "⚠️ Already Marked for Today!"; 
        }
    } else { 
        $status = "error";
        $msg = "🚫 Invalid Student ID / QR Code"; 
    }
} else {
    header("Location: attendance_scanner.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Status</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .msg-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        .success { color: #27ae60; }
        .warning { color: #f39c12; }
        .error { color: #e74c3c; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 30px; height: 30px; animation: spin 2s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="msg-box">
    <h2 class="<?php echo $status; ?>"><?php echo $msg; ?></h2>
    <p>Redirecting back to scanner in 2 seconds...</p>
    <div class="loader"></div>
    <a href="attendance_scanner.php" class="btn">Scan Next Now</a>
</div>

<script>
    // 2 second baad auto-redirect to scanner
    setTimeout(function(){
        window.location.href = "attendance_scanner.php";
    }, 2000);
</script>

</body>
</html>