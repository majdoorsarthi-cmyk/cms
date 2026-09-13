<?php
/**
 * SMART CMS PRO - EDIT STUDENT STATUS & BATCH (FIXED)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php';

// Admin Auth Check
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$msg = "";
$data = null;

// 1. GET DATA: URL se 'id' lekar sahi record fetch karna
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    /**
     * FIXED QUERY: 
     * Screenshot ke hisaab se service_requests mein 'id' (23) primary hai.
     * Hum use 'students' aur 'users' se join kar rahe hain.
     */
    $sql = "SELECT sr.id as request_id, sr.status as req_status, sr.student_id, 
                   s.roll_no, s.batch_time, s.course, u.name, u.email 
            FROM service_requests sr
            JOIN students s ON sr.student_id = s.id
            JOIN users u ON s.user_id = u.id
            WHERE sr.id = '$id'";
            
    $query = mysqli_query($conn, $sql);
    
    if($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
    }
}

// 2. UPDATE DATA: Status aur Batch update karna
if(isset($_POST['update_status'])) {
    $req_id = mysqli_real_escape_string($conn, $_POST['req_id']);
    $stu_id = mysqli_real_escape_string($conn, $_POST['stu_id']); // Student table ki ID
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $new_roll = mysqli_real_escape_string($conn, $_POST['roll_no']);
    $new_batch = mysqli_real_escape_string($conn, $_POST['batch_time']);

    // Pehle Students table update karein
    $update_stu = "UPDATE students SET roll_no = '$new_roll', batch_time = '$new_batch' WHERE id = '$stu_id'";
    
    // Phir Request table update karein
    $update_req = "UPDATE service_requests SET status = '$new_status' WHERE id = '$req_id'";
    
    if(mysqli_query($conn, $update_stu) && mysqli_query($conn, $update_req)) {
        $msg = "<div class='alert success'><i class='fa-solid fa-circle-check'></i> सफलतापूर्वक अपडेट कर दिया गया!</div>";
        // Refresh updated data
        $query = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($query);
    } else {
        $msg = "<div class='alert error'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Request | SMART CMS PRO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --white: #ffffff; --text: #1e293b; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); padding: 40px 20px; }
        .container { max-width: 580px; margin: auto; background: var(--white); border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
        
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 30px; text-align: center; color: white; }
        .header h2 { margin: 0; font-size: 22px; font-weight: 800; }
        
        .form-content { padding: 35px; }
        .student-info { background: #f1f5f9; padding: 15px; border-radius: 15px; margin-bottom: 25px; border-left: 5px solid var(--primary); }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; }
        
        input, select { width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; box-sizing: border-box; transition: 0.3s; }
        input:focus, select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        
        .btn-update { background: var(--primary); color: white; border: none; padding: 16px; width: 100%; border-radius: 15px; cursor: pointer; font-weight: 700; font-size: 16px; transition: 0.3s; }
        .btn-update:hover { background: #4338ca; transform: translateY(-2px); }
        
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; text-align: center; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .back-btn { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: var(--primary); font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <i class="fa-solid fa-user-check fa-2x" style="margin-bottom: 10px;"></i>
        <h2>स्थिति और बैच प्रबंधित करें</h2>
    </div>

    <div class="form-content">
        <?php echo $msg; ?>

        <?php if($data): ?>
        <div class="student-info">
            <div style="font-weight: 800; font-size: 16px;"><?php echo htmlspecialchars($data['name']); ?></div>
            <div style="font-size: 13px; color: #64748b;"><?php echo htmlspecialchars($data['course']); ?> | <?php echo htmlspecialchars($data['email']); ?></div>
        </div>

        <form method="POST">
            <input type="hidden" name="req_id" value="<?php echo $data['request_id']; ?>">
            <input type="hidden" name="stu_id" value="<?php echo $data['student_id']; ?>">

            <div class="form-group">
                <label>रोल नंबर (Roll Number)</label>
                <input type="text" name="roll_no" value="<?php echo htmlspecialchars($data['roll_no']); ?>" required>
            </div>

            <div class="form-group">
                <label>बैच समय (Batch Time)</label>
                <select name="batch_time" required>
                    <option value="08:00 AM - 09:00 AM" <?php if($data['batch_time'] == '08:00 AM - 09:00 AM') echo 'selected'; ?>>08:00 AM - 09:00 AM</option>
                    <option value="09:00 AM - 10:00 AM" <?php if($data['batch_time'] == '09:00 AM - 10:00 AM') echo 'selected'; ?>>09:00 AM - 10:00 AM</option>
                    <option value="04:00 PM - 05:00 PM" <?php if($data['batch_time'] == '04:00 PM - 05:00 PM') echo 'selected'; ?>>04:00 PM - 05:00 PM</option>
                </select>
            </div>

            <div class="form-group">
                <label>वर्तमान स्थिति (Status)</label>
                <select name="status">
                    <option value="Pending" <?php if($data['req_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Verified" <?php if($data['req_status'] == 'Verified') echo 'selected'; ?>>Verified (Approved)</option>
                    <option value="Rejected" <?php if($data['req_status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
                </select>
            </div>

            <button type="submit" name="update_status" class="btn-update">
                <i class="fa-solid fa-save"></i> जानकारी सेव करें
            </button>
        </form>
        <?php else: ?>
            <div class="alert error">
                <i class="fa-solid fa-circle-exclamation"></i><br>
                कोई रिकॉर्ड नहीं मिला! <br>
                <small>Request ID #<?php echo $_GET['id'] ?? '??'; ?> database mein nahi hai.</small>
            </div>
        <?php endif; ?>

        <a href="verify_requests.php" class="back-btn">← वापस सूची में जाएं</a>
    </div>
</div>

</body>
</html>