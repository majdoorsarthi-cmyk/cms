<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Student Login Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$u_id = $stu['user_id']; // Users table ki ID
$s_id = $stu['id'];      // Students table ki ID

$message = "";

// Form Submission Handle Karna
if(isset($_POST['update_profile'])){
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['password']);

    // 1. Users table update (Email aur Password)
    $update_user = "UPDATE users SET email='$new_email'";
    if(!empty($new_pass)){
        $update_user .= ", password='$new_pass'"; // Plain text password as per your current system
    }
    $update_user .= " WHERE id='$u_id'";

    // 2. Students table update (Phone)
    $update_stu = "UPDATE students SET phone='$new_phone' WHERE id='$s_id'";

    if(mysqli_query($conn, $update_user) && mysqli_query($conn, $update_stu)){
        $message = "<div style='color:green; background:#e6ffed; padding:10px; border-radius:5px; margin-bottom:15px;'>✅ Profile Updated Successfully!</div>";
        // Session update karein taaki dashboard par naya data dikhe
        $_SESSION['student']['email'] = $new_email;
    } else {
        $message = "<div style='color:red;'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}

// Current data fetch karna display ke liye
$current_data = mysqli_query($conn, "SELECT u.email, s.phone FROM users u JOIN students s ON u.id = s.user_id WHERE s.id = '$s_id'");
$user_info = mysqli_fetch_assoc($current_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | Student Portal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; }
        .header { background: #2c3e50; color: white; padding: 20px 50px; display: flex; justify-content: space-between; }
        .container { padding: 40px; max-width: 500px; margin: auto; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #34495e; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-update { background: #3498db; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-update:hover { background: #2980b9; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #7f8c8d; text-decoration: none; }
    </style>
</head>
<body>

<div class="header">
    <h2 style="margin:0;">⚙️ Account Settings</h2>
    <span style="opacity: 0.8;"><?php echo htmlspecialchars($stu['name']); ?></span>
</div>

<div class="container">
    <div class="form-card">
        <h3>Update My Info</h3>
        <hr style="border:0; border-top:1px solid #eee; margin-bottom:20px;">
        
        <?php echo $message; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo $user_info['email']; ?>" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo $user_info['phone']; ?>" required>
            </div>

            <div class="form-group">
                <label>New Password (Leave blank to keep current)</label>
                <input type="password" name="password" placeholder="Enter new password">
            </div>

            <button type="submit" name="update_profile" class="btn-update">Save Changes</button>
        </form>

        <a href="student_dashboard.php" class="back-link">← Cancel and Go Back</a>
    </div>
</div>

</body>
</html>