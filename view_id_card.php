<?php
session_start();
include 'db_config.php';

if(!isset($_SESSION['student'])) { header("Location: index.php"); exit(); }

$user_id = $_SESSION['student']['user_id'];
$error = "";
$show_card = false;

// Student ka data fetch karein
$query = "SELECT u.name, s.* FROM users u JOIN students s ON u.id = s.user_id WHERE u.id = '$user_id'";
$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

if(isset($_POST['verify_dob'])) {
    $input_year = $_POST['dob_year'];
    $db_dob = $data['dob']; // Format: YYYY-MM-DD
    $correct_year = date('Y', strtotime($db_dob));

    if($input_year == $correct_year) {
        $show_card = true;
    } else {
        $error = "❌ Invalid Birth Year! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Digital ID Card</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fe; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .verify-box { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center; }
        .id-card { width: 350px; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid #ddd; }
        .id-header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .id-body { padding: 20px; text-align: center; position: relative; }
        .stu-img { width: 100px; height: 100px; border-radius: 50%; border: 4px solid #3b82f6; margin-bottom: 10px; object-fit: cover; }
        .info { margin: 10px 0; font-size: 14px; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .id-footer { background: #f8fafc; padding: 10px; font-size: 10px; color: #64748b; }
        .btn { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        input { padding: 10px; width: 80%; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd; text-align: center; }
    </style>
</head>
<body>

<?php if(!$show_card): ?>
    <div class="verify-box">
        <h3>🔒 Security Check</h3>
        <p>Enter your <b>Birth Year</b> (e.g. 2005) to view ID Card</p>
        <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="number" name="dob_year" placeholder="YYYY" required>
            <br>
            <button type="submit" name="verify_dob" class="btn">Unlock ID Card</button>
        </form>
    </div>
<?php else: ?>
    <div class="id-card">
        <div class="id-header">
            <h3 style="margin:0;">CMS PRO ACADEMY</h3>
            <small>Student Identity Card</small>
        </div>
        <div class="id-body">
            <img src="uploads/profile/<?php echo $data['photo'] ?: 'default.png'; ?>" class="stu-img">
            <h2 style="margin:5px 0; color:#1e293b;"><?php echo $data['name']; ?></h2>
            <div class="info"><b>Roll No:</b> #<?php echo $data['roll_no']; ?></div>
            <div class="info"><b>Course:</b> <?php echo $data['course']; ?></div>
            <div class="info"><b>Batch:</b> <?php echo $data['batch_time']; ?></div>
            <div class="info"><b>Mobile:</b> <?php echo $data['mobile']; ?></div>
        </div>
        <div class="id-footer">
            Valid until: <?php echo date('Y', strtotime('+1 year')); ?> | Authorized Signatory
            <div style="margin-top:5px; text-align:center;">
                <button onclick="window.print()" style="font-size: 9px; cursor:pointer;">🖨️ Print ID Card</button>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>