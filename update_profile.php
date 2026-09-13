<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_config.php';

// 1. Check if Student is logged in
if(!isset($_SESSION['student'])) { header("Location: index.php"); exit(); }

// Session se student ki ID nikaalna
$session_id = $_SESSION['student']['id'] ?? $_SESSION['student']['user_id']; 
$message = "";

// --- 2. FETCH REAL DATA FROM ADMIN FEED ---
// Yahan hum primary ID aur user_id dono check kar rahe hain taki data miss na ho
$query = "SELECT u.name as u_name, u.email as u_email, u.mobile as u_mobile, 
                 s.* FROM users u 
          LEFT JOIN students s ON u.id = s.user_id 
          WHERE u.id = '$session_id' OR s.id = '$session_id' LIMIT 1";

$res = mysqli_query($conn, $query);
$stu_data = mysqli_fetch_assoc($res);

// Fallback logic agar students table me entry na ho
$val_name = $stu_data['name'] ?? $stu_data['u_name'] ?? '';
$val_roll = $stu_data['roll_no'] ?? 'N/A';
$val_course = $stu_data['course'] ?? 'N/A';
$val_batch = $stu_data['batch_time'] ?? 'N/A';
$val_fee = $stu_data['course_fee'] ?? '0';
$val_photo = $stu_data['photo'] ?? '';

// --- 3. UPDATE LOGIC ---
if(isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Photo management logic
    $photo_name = $val_photo; 

    if(!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/profile/"; 
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_photo_name = "STU_" . $session_id . "_" . time() . "." . $file_ext;
        
        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_dir . $new_photo_name)) {
            if(!empty($photo_name) && file_exists($target_dir . $photo_name)) {
                @unlink($target_dir . $photo_name);
            }
            $photo_name = $new_photo_name;
        }
    }

    mysqli_begin_transaction($conn);
    try {
        // Users table update
        mysqli_query($conn, "UPDATE users SET name='$name', email='$email', mobile='$mobile' WHERE id='$session_id'");
        
        // Students table update (Fields like roll_no, course etc are NOT in this query)
        mysqli_query($conn, "UPDATE students SET name='$name', mobile='$mobile', photo='$photo_name', dob='$dob', gender='$gender', address='$address' WHERE user_id='$session_id' OR id='$session_id'");

        mysqli_commit($conn);
        $message = "<div class='alert-custom success'><i class='fas fa-check-circle'></i> प्रोफाइल सफलतापूर्वक अपडेट हो गई!</div>";
        // Refresh data after update
        header("Refresh:2");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "<div class='alert-custom error'><i class='fas fa-exclamation-triangle'></i> त्रुटि: अपडेट विफल।</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | CMS PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4338ca; --bg: #f1f5f9; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        .top-header { background: #0f172a; padding: 40px 0 100px 0; color: white; }
        .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-top: -60px; padding: 30px; border: none; }
        .profile-img-container { position: relative; margin-top: -90px; margin-bottom: 20px; }
        .preview-circle { width: 120px; height: 120px; border-radius: 20px; border: 5px solid white; object-fit: cover; box-shadow: 0 5px 15px rgba(0,0,0,0.1); background: #eee; }
        .readonly-box { background: #f8fafc; border: 1px dashed #cbd5e1; padding: 10px 15px; border-radius: 10px; height: 100%; }
        .readonly-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 2px; display: block; }
        .readonly-value { font-weight: 600; color: #1e293b; display: block; font-size: 14px; }
        .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-top: 10px; }
        .form-control, .form-select { border-radius: 10px; padding: 10px; border: 1px solid #e2e8f0; font-size: 14px; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; transition: 0.3s; width: 100%; max-width: 200px; }
        .alert-custom { border-radius: 10px; padding: 15px; margin-bottom: 20px; font-weight: 500; text-align: center; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    </style>
</head>
<body>

<div class="top-header text-center">
    <h2>Profile Settings</h2>
    <p>Manage your personal information</p>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="main-card">
                <?= $message; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="text-center profile-img-container">
                        <?php 
                        $p_src = (!empty($val_photo) && file_exists('uploads/profile/'.$val_photo)) 
                                 ? 'uploads/profile/'.$val_photo 
                                 : 'https://ui-avatars.com/api/?name='.urlencode($val_name).'&background=4338ca&color=fff'; 
                        ?>
                        <img src="<?= $p_src ?>" class="preview-circle" id="imgPreview">
                        <div class="mt-3 d-flex justify-content-center">
                            <input type="file" name="profile_pic" id="fileInput" class="d-none" onchange="document.getElementById('imgPreview').src = window.URL.createObjectURL(this.files[0])">
                            <label for="fileInput" class="btn btn-sm btn-outline-primary"><i class="fas fa-camera"></i> Change Photo</label>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 mt-2">
                        <div class="col-md-3 col-6">
                            <div class="readonly-box">
                                <span class="readonly-label">Roll Number</span>
                                <span class="readonly-value">#<?= htmlspecialchars($val_roll) ?></span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="readonly-box">
                                <span class="readonly-label">Course</span>
                                <span class="readonly-value"><?= htmlspecialchars($val_course) ?></span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="readonly-box">
                                <span class="readonly-label">Batch</span>
                                <span class="readonly-value"><?= htmlspecialchars($val_batch) ?></span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="readonly-box">
                                <span class="readonly-label">Course Fee</span>
                                <span class="readonly-value">₹<?= number_format((float)$val_fee) ?></span>
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($val_name) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($stu_data['u_email'] ?? $stu_data['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($stu_data['u_mobile'] ?? $stu_data['mobile'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?= $stu_data['dob'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male" <?= (($stu_data['gender'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= (($stu_data['gender'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= (($stu_data['gender'] ?? '') == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Permanent Address</label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($stu_data['address'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="dashboard.php" class="btn btn-light px-4 me-2" style="border-radius:12px;">Back</a>
                        <button type="submit" name="update" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>