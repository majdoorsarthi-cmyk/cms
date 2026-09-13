<?php
// 1. Session check to fix "session already active" error
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php';

// 2. Admin check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Student aur User data join karke nikalna
    $sql = "SELECT s.*, u.name, u.email FROM students s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.id = '$id'";
    $res = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($res);

    if(!$data){ 
        echo "<script>alert('Student not found!'); window.location='admin_dashboard.php';</script>";
        exit(); 
    }

    // QR Code URL (Student ID ke saath)
    $qr_data = $data['id']; 
    $qr_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . $qr_data . "&choe=UTF-8";
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card - <?php echo $data['name']; ?></title>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; }
        
        /* ID Card Styling */
        .id-card-canvas {
            width: 320px;
            background: #fff;
            border-radius: 15px;
            padding: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
            border: 1px solid #ddd;
            margin: 50px auto;
            position: relative;
            overflow: hidden;
        }
        
        /* Blue Header */
        .header { 
            background: #2c3e50; 
            color: white; 
            padding: 20px 10px; 
            margin-bottom: 20px;
        }
        .header h3 { margin: 0; font-size: 18px; letter-spacing: 1px; }
        .header p { margin: 5px 0 0; font-size: 10px; opacity: 0.8; }

        .photo { 
            width: 110px; 
            height: 110px; 
            border-radius: 50%; 
            border: 4px solid #fff; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background: #eee;
            object-fit: cover;
        }
        
        .details { padding: 10px 20px; }
        .details h2 { margin: 10px 0 5px; color: #2c3e50; font-size: 22px; }
        .details p { margin: 4px 0; color: #555; font-size: 14px; }
        .label { color: #888; font-weight: normal; margin-right: 5px; }

        .qr-section { 
            background: #f9f9f9; 
            padding: 15px; 
            border-top: 1px dashed #ddd;
            margin-top: 15px;
        }
        .qr-section img { width: 120px; height: 120px; }
        .footer-msg { font-size: 10px; color: #999; margin-top: 5px; text-transform: uppercase; }

        /* Print Controls */
        .controls { text-align: center; margin-top: 20px; }
        .btn { 
            display: inline-block; 
            padding: 10px 25px; 
            background: #3498db; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-back { background: #95a5a6; margin-right: 10px; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }

        @media print { 
            body { background: white; }
            .controls { display: none; } 
            .id-card-canvas { margin: 0; box-shadow: none; border: 1px solid #000; }
        }
    </style>
</head>
<body>

<div class="id-card-canvas">
    <div class="header">
        <h3>COACHING INSTITUTE</h3>
        <p>Success Through Education</p>
    </div>
    
    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($data['name']); ?>&size=110&background=3498db&color=fff" class="photo" alt="Student Photo">
    
    <div class="details">
        <h2><?php echo htmlspecialchars($data['name']); ?></h2>
        <p><span class="label">Roll No:</span> <b><?php echo $data['roll_no']; ?></b></p>
        <p><span class="label">Course:</span> <b><?php echo $data['course']; ?></b></p>
        <p><span class="label">Expiry:</span> Dec 2026</p>
    </div>

    <div class="qr-section">
        <img src="<?php echo $qr_url; ?>" alt="QR Code">
        <div class="footer-msg">Digital Attendance Verified</div>
    </div>
</div>

<div class="controls">
    <a href="admin_dashboard.php" class="btn btn-back">← Back</a>
    <a href="#" onclick="window.print();" class="btn">🖨️ Print ID Card</a>
</div>

</body>
</html>