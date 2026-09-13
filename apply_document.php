<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php';

// Student login check (Ensure your session variable matches your student login)
if(!isset($_SESSION['student_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

$student_id = $_SESSION['student_id'];
$msg = "";

// --- Handle Application Submission ---
if(isset($_POST['apply_now'])) {
    $doc_type = mysqli_real_escape_string($conn, $_POST['document_type']);
    
    // Check if already applied for the same document and it's pending/verified
    $check = mysqli_query($conn, "SELECT id FROM document_requests WHERE student_id='$student_id' AND document_type='$doc_type' AND admin_status != 'Rejected'");
    
    if(mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert warning'>⚠️ You have already applied for this document. Please check status below.</div>";
    } else {
        // Entry into database (Default fee_status 'Paid' for testing, usually should be 'Pending')
        $query = "INSERT INTO document_requests (student_id, document_type, fee_status, admin_status) 
                  VALUES ('$student_id', '$doc_type', 'Paid', 'Pending')";
        
        if(mysqli_query($conn, $query)) {
            $msg = "<div class='alert success'>✅ Application submitted successfully! Admin will verify it soon.</div>";
        } else {
            $msg = "<div class='alert danger'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Fetch Student's Request History
$history = mysqli_query($conn, "SELECT * FROM document_requests WHERE student_id='$student_id' ORDER BY applied_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Documents | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --bg: #f4f7fe;
            --white: #ffffff;
            --text-dark: #2b3674;
            --text-light: #a3aed0;
            --shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); color: var(--text-dark); padding: 30px; }
        
        .container { max-width: 900px; margin: auto; }
        .card { background: var(--white); padding: 30px; border-radius: 20px; box-shadow: var(--shadow); margin-bottom: 30px; }
        
        h2 { margin-bottom: 20px; font-weight: 700; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-light); }
        
        select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e0e5f2; background: #f4f7fe; outline: none; font-size: 15px; }
        
        .btn-apply { background: var(--primary); color: white; border: none; padding: 12px 25px; border-radius: 12px; cursor: pointer; font-weight: 600; transition: 0.3s; width: 100%; }
        .btn-apply:hover { opacity: 0.9; transform: translateY(-2px); }
        
        /* Status Badges */
        .status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .status-pending { background: #fff4e5; color: #ff9f43; }
        .status-verified { background: #e7f9ed; color: #27ae60; }
        .status-rejected { background: #ffe5e5; color: #e74c3c; }
        
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        .danger { background: #f8d7da; color: #721c24; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; color: var(--text-light); font-size: 13px; padding: 10px; border-bottom: 2px solid #f4f7fe; }
        td { padding: 15px 10px; border-bottom: 1px solid #f4f7fe; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa fa-file-export" style="color:var(--primary);"></i> Document Request</h2>
    
    <?php echo $msg; ?>

    <div class="card">
        <h3 style="margin-bottom:15px; font-size:18px;">New Application</h3>
        <form action="" method="POST">
            <div class="form-group">
                <label>Select Document Type</label>
                <select name="document_type" required>
                    <option value="">-- Choose Document --</option>
                    <option value="ID Card">Student ID Card</option>
                    <option value="Marksheet">Examination Marksheet</option>
                    <option value="Certificate">Transfer/Character Certificate</option>
                    <option value="Report Card">Progress Report Card</option>
                </select>
            </div>
            <p style="font-size: 12px; color: var(--text-light); margin-bottom: 15px;">
                Note: Verification may take 24-48 hours after fee payment.
            </p>
            <button type="submit" name="apply_now" class="btn-apply">Submit Request</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-bottom:15px; font-size:18px;">Your Request History</h3>
        <table>
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Applied Date</th>
                    <th>Fee</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($history) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history)): ?>
                    <tr>
                        <td style="font-weight:600;"><?php echo $row['document_type']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['applied_at'])); ?></td>
                        <td>
                            <?php if($row['fee_status'] == 'Paid'): ?>
                                <span style="color: #27ae60;"><i class="fa fa-check-circle"></i> Paid</span>
                            <?php else: ?>
                                <span style="color: #e74c3c;"><i class="fa fa-clock"></i> Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                $s = $row['admin_status'];
                                $class = ($s == 'Verified') ? 'status-verified' : (($s == 'Rejected') ? 'status-rejected' : 'status-pending');
                                echo "<span class='status $class'>$s</span>";
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; color:var(--text-light);">No requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>