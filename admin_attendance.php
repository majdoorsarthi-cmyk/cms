<?php 
include 'db_config.php';
session_start();
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$msg = "";
if(isset($_POST['save_attendance'])){
    $date = $_POST['att_date'];
    foreach($_POST['status'] as $stu_id => $status){
        mysqli_query($conn, "INSERT INTO attendance (student_id, status, attendance_date) VALUES ('$stu_id', '$status', '$date')");
    }
    $msg = "Attendance saved successfully!";
}

$students = mysqli_query($conn, "SELECT u.name, s.user_id, s.roll_no FROM students s JOIN users u ON s.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 10px; max-width: 800px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .btn { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2>📅 Mark Daily Attendance</h2>
        <?php if($msg) echo "<p style='color:green'>$msg</p>"; ?>
        <form method="POST">
            <input type="date" name="att_date" value="<?php echo date('Y-m-d'); ?>" required>
            <table>
                <tr><th>Roll No</th><th>Name</th><th>Status</th></tr>
                <?php while($row = mysqli_fetch_assoc($students)): ?>
                <tr>
                    <td><?php echo $row['roll_no']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>
                        <input type="radio" name="status[<?php echo $row['user_id']; ?>]" value="Present" checked> P
                        <input type="radio" name="status[<?php echo $row['user_id']; ?>]" value="Absent"> A
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <br>
            <button type="submit" name="save_attendance" class="btn">Save Attendance</button>
        </form>
    </div>
</body>
</html>