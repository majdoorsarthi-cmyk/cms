<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Check admin session
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

$message = "";

// 1. Fetch Existing Data
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT f.*, u.name, s.roll_no 
              FROM fees f 
              JOIN students s ON f.student_id = s.id 
              JOIN users u ON s.user_id = u.id 
              WHERE f.id = '$id'";
    
    $res = mysqli_query($conn, $query);
    $fee_data = mysqli_fetch_assoc($res);

    if(!$fee_data) {
        die("<div style='color:white; text-align:center; padding:50px;'>Record not found!</div>");
    }
}

// 2. Update Logic
if(isset($_POST['update_fee'])){
    $fee_id = mysqli_real_escape_string($conn, $_POST['fee_id']);
    $new_amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $new_date = mysqli_real_escape_string($conn, $_POST['payment_date']);

    $update_sql = "UPDATE fees SET amount_paid = '$new_amount', payment_date = '$new_date' WHERE id = '$fee_id'";

    if(mysqli_query($conn, $update_sql)){
        $message = "<div class='success-toast'>
                        <i class='fa-solid fa-circle-check'></i> 
                        <div>
                            <strong>Updated Successfully!</strong> Data has been modified.<br>
                            <a href='collect_fees.php' style='color:white;'>← Go Back to Portal</a>
                        </div>
                    </div>";
        // Refresh data after update
        header("Refresh: 2; url=collect_fees.php");
    } else {
        $message = "<div class='error-toast'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fee Record | Premium CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --text-main: #f8fafc;
            --success: #22c55e;
        }

        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { 
            margin: 0; min-height: 100vh; 
            background: radial-gradient(circle at top right, #1e293b, #0f172a); 
            color: var(--text-main); 
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }

        .edit-container {
            width: 100%;
            max-width: 500px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin: 0; color: var(--primary); }
        .header p { color: #94a3b8; font-size: 14px; margin-top: 5px; }

        .student-info {
            background: rgba(99, 102, 241, 0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px dashed var(--primary);
        }
        .student-info div { font-size: 13px; color: #cbd5e1; margin-bottom: 4px; }
        .student-info strong { color: white; font-size: 16px; }

        label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; }
        
        input { 
            width: 100%; padding: 14px 18px; margin-bottom: 20px; 
            background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border);
            border-radius: 12px; color: white; outline: none; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2); }

        .btn-update { 
            background: var(--primary); color: white; border: none; padding: 16px; 
            width: 100%; border-radius: 12px; cursor: pointer; font-weight: 700; 
            font-size: 16px; transition: 0.3s;
        }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4); }

        .btn-cancel {
            display: block; text-align: center; margin-top: 15px;
            color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500;
        }
        .btn-cancel:hover { color: white; }

        .success-toast { 
            background: rgba(34, 197, 94, 0.15); border: 1px solid var(--success); 
            color: #4ade80; padding: 15px; border-radius: 12px; margin-bottom: 25px; 
            display: flex; gap: 15px; align-items: center; font-size: 14px;
        }
    </style>
</head>
<body>

<div class="edit-container">
    <div class="header">
        <h1><i class="fa-solid fa-pen-nib"></i> Edit Payment</h1>
        <p>Modify receipt #<?php echo $fee_data['receipt_no']; ?></p>
    </div>

    <?php echo $message; ?>

    <div class="student-info">
        <div>Student Name:</div>
        <strong><?php echo strtoupper($fee_data['name']); ?></strong>
        <div style="margin-top: 8px;">Roll Number: <strong><?php echo $fee_data['roll_no']; ?></strong></div>
    </div>

    <form method="POST">
        <input type="hidden" name="fee_id" value="<?php echo $fee_data['id']; ?>">

        <label>Amount Paid (₹)</label>
        <input type="number" name="amount" value="<?php echo $fee_data['amount_paid']; ?>" required>

        <label>Payment Date & Time</label>
        <input type="datetime-local" name="payment_date" value="<?php echo date('Y-m-d\TH:i', strtotime($fee_data['payment_date'])); ?>" required>

        <button type="submit" name="update_fee" class="btn-update">
            Save Changes <i class="fa-solid fa-check-double"></i>
        </button>

        <a href="collect_fees.php" class="btn-cancel">Cancel & Go Back</a>
    </form>
</div>

</body>
</html>