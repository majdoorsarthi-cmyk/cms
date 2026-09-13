<?php 
include 'db_config.php';
session_start();
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$msg = "";
if(isset($_POST['add_fee'])){
    $stu_id = $_POST['student_id'];
    $amt = $_POST['amount'];
    $total = $_POST['total_course_fee'];
    $date = $_POST['pay_date'];
    $rem = $_POST['remarks'];

    $q = "INSERT INTO fees (student_id, amount_paid, total_amount, payment_date, remarks) 
          VALUES ('$stu_id', '$amt', '$total', '$date', '$rem')";
    if(mysqli_query($conn, $q)) {
        $msg = "✅ Fees recorded successfully!";
    }
}

$students = mysqli_query($conn, "SELECT u.name, s.user_id, s.roll_no FROM students s JOIN users u ON s.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Fees | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #0f172a; color: white; padding: 40px; }
        .fee-card { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); max-width: 500px; margin: auto; }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; border-radius: 10px; border: none; background: rgba(255,255,255,0.1); color: white; }
        .btn { background: #3b82f6; color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: 600; }
        option { background: #1e293b; }
    </style>
</head>
<body>
    <div class="fee-card">
        <h2>💰 Collect Fees</h2>
        <p style="color: #10b981;"><?php echo $msg; ?></p>
        <form method="POST">
            <label>Select Student</label>
            <select name="student_id" required>
                <?php while($s = mysqli_fetch_assoc($students)): ?>
                    <option value="<?php echo $s['user_id']; ?>"><?php echo $s['name']; ?> (<?php echo $s['roll_no']; ?>)</option>
                <?php endwhile; ?>
            </select>

            <input type="number" name="amount" placeholder="Amount Paid (e.g. 2000)" required>
            <input type="number" name="total_course_fee" placeholder="Total Course Fee (e.g. 5000)" required>
            <input type="date" name="pay_date" value="<?php echo date('Y-m-d'); ?>" required>
            <textarea name="remarks" placeholder="Remarks (e.g. Jan Installment)"></textarea>
            
            <button type="submit" name="add_fee" class="btn">Submit Fee Payment</button>
        </form>
        <br>
        <a href="admin_dashboard.php" style="color: #94a3b8; text-decoration: none; font-size: 14px;">← Back to Dashboard</a>
    </div>
</body>
</html>