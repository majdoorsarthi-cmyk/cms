<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_config.php'; 

// Admin Check
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$message = "";

// 1. Fetch Student Data (Added batch_start_time & batch_end_time)
if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT u.name, u.email, u.mobile as user_mobile, 
                     s.father_name, s.roll_no, s.course, s.session, 
                     s.dob, s.gender, s.address, s.photo, s.batch_start_time, s.batch_end_time, s.total_fee 
              FROM users u 
              INNER JOIN students s ON u.id = s.user_id 
              WHERE u.id = '$id'";
    $res = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($res);

    if(!$data){ header("Location: manage_students.php"); exit(); }
} else {
    header("Location: manage_students.php"); exit();
}

// 2. Update Logic
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $session_val = mysqli_real_escape_string($conn, $_POST['session']);
    $batch_start_time = mysqli_real_escape_string($conn, $_POST['batch_start_time']);
    $batch_end_time = mysqli_real_escape_string($conn, $_POST['batch_end_time']);
    $total_fee = mysqli_real_escape_string($conn, $_POST['total_fee']); // Total Fee
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Photo Update Logic
    $photo_name = $data['photo']; 
    if(!empty($_FILES['photo']['name'])){
        $target_dir = "uploads/profile/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $photo_name = "UPD_" . time() . "." . $ext;
        
        if(move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo_name)){
            if($data['photo'] != 'default.png' && !empty($data['photo']) && file_exists($target_dir . $data['photo'])){
                @unlink($target_dir . $data['photo']);
            }
        }
    }

    mysqli_begin_transaction($conn);
    try {
        // Update Users Table
        mysqli_query($conn, "UPDATE users SET name='$name', email='$email', mobile='$mobile' WHERE id='$id'");

        // Update Students Table
        mysqli_query($conn, "UPDATE students SET 
                                father_name='$father_name', 
                                course='$course', 
                                session='$session_val', 
                                batch_start_time='$batch_start_time',
                                batch_end_time='$batch_end_time',
                                total_fee='$total_fee',
                                dob='$dob', 
                                gender='$gender', 
                                address='$address',
                                photo='$photo_name' 
                             WHERE user_id='$id'");

        mysqli_commit($conn);
        
        // Refresh local array values for instant UI sync
        $data['name'] = $name;
        $data['email'] = $email;
        $data['user_mobile'] = $mobile;
        $data['father_name'] = $father_name;
        $data['course'] = $course;
        $data['session'] = $session_val;
        $data['batch_start_time'] = $batch_start_time;
        $data['batch_end_time'] = $batch_end_time;
        $data['total_fee'] = $total_fee;
        $data['dob'] = $dob;
        $data['gender'] = $gender;
        $data['address'] = $address;
        $data['photo'] = $photo_name;

        $message = "<div class='alert-msg success'><i class='fa-solid fa-circle-check me-2'></i> प्रोफ़ाइल सफलतापूर्वक अपडेट हो गई है!</div>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "<div class='alert-msg error'><i class='fa-solid fa-triangle-exclamation me-2'></i> त्रुटि: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Student | Smart CMS Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --primary: #4f46e5; 
            --secondary: #64748b; 
            --bg: #f8fafc; 
            --card: #ffffff; 
            --text-main: #1e293b; 

            /* PhonePe Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }
        
        /* 🖥️ DESKTOP SIDEBAR & LAYOUT */
        .sidebar { width: 280px; background: #0f172a; color: white; position: fixed; height: 100vh; padding: 30px 20px; z-index: 10; }
        .sidebar h2 { color: #818cf8; margin-bottom: 40px; font-size: 24px; text-align: center; }
        .sidebar a { color: #94a3b8; text-decoration: none; display: block; padding: 12px 15px; border-radius: 10px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover { background: rgba(255,255,255,0.05); color: white; }
        .sidebar a.active { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); }
        
        .main-content { margin-left: 280px; padding: 50px; width: calc(100% - 280px); display: flex; justify-content: center; }
        .form-card { background: var(--card); padding: 40px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); width: 100%; max-width: 850px; border: 1px solid #e2e8f0; }
        .header-box { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 35px; }
        .photo-edit-section { display: flex; flex-direction: column; align-items: center; gap: 15px; margin-bottom: 30px; padding: 20px; background: #f1f5f9; border-radius: 20px; }
        .stu-photo-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .file-input-label { background: #fff; padding: 8px 20px; border-radius: 30px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #e2e8f0; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full { grid-column: span 2; }
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group label { font-size: 13px; font-weight: 600; color: var(--secondary); display: flex; gap: 5px; align-items: center; }
        input, select, textarea { padding: 13px 16px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 15px; background: #fff; outline: none; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); }
        
        .btn-update { background: var(--primary); color: white; border: none; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: 700; width: 100%; margin-top: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.2s; }
        .btn-update:hover { opacity: 0.95; }
        
        .alert-msg { padding: 15px 20px; border-radius: 14px; margin-bottom: 25px; font-weight: 600; font-size: 14px; display: flex; align-items: center; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        .mobile-header, .mobile-bottom-nav { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Mobile App UI) */
        @media (max-width: 991px) {
            body { background-color: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; }
            .sidebar { display: none !important; }
            
            .main-content {
                margin-left: 0 !important;
                padding: 78px 12px 85px 12px !important;
                width: 100% !important;
            }

            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 65px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%) !important;
                z-index: 9999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.25);
            }

            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn {
                color: #ffffff;
                font-size: 20px;
                text-decoration: none;
                width: 40px; height: 40px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            }
            .mobile-header h3 {
                color: #ffffff !important;
                font-size: 18px !important;
                font-weight: 700;
                margin: 0;
                line-height: 1.2;
            }
            .mobile-header small {
                color: rgba(255,255,255,0.85);
                font-size: 12.5px !important;
                display: block;
            }

            .form-card {
                padding: 20px 16px !important;
                border-radius: 20px !important;
                border: 1px solid #edf2f7 !important;
                box-shadow: 0 2px 12px rgba(0,0,0,0.03) !important;
            }

            .header-box { display: none !important; }

            .photo-edit-section {
                background: #f8fafc !important;
                padding: 16px !important;
                border-radius: 16px !important;
                margin-bottom: 20px !important;
            }
            .stu-photo-preview {
                width: 110px !important;
                height: 110px !important;
                border: 3px solid #ffffff !important;
                box-shadow: 0 4px 12px rgba(95, 37, 159, 0.15) !important;
            }
            .file-input-label {
                font-size: 14.5px !important;
                padding: 10px 22px !important;
                background: #ffffff !important;
                color: var(--phonepe-purple) !important;
                border: 1.5px solid #e9d5ff !important;
                font-weight: 700 !important;
            }

            .grid { grid-template-columns: 1fr !important; gap: 16px !important; }
            .full { grid-column: span 1 !important; }

            .input-group label {
                font-size: 15px !important;
                font-weight: 700 !important;
                color: #334155 !important;
            }
            input, select, textarea {
                padding: 14px 16px !important;
                font-size: 16px !important;
                border-radius: 12px !important;
                border: 1.5px solid #cbd5e1 !important;
                background: #ffffff !important;
                font-weight: 500 !important;
            }

            .btn-update {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%) !important;
                padding: 16px !important;
                font-size: 17px !important;
                font-weight: 700 !important;
                border-radius: 14px !important;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3) !important;
            }

            .alert-msg {
                font-size: 15px !important;
                padding: 14px 16px !important;
            }

            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 62px;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                z-index: 9998;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #64748b;
                font-size: 11px !important;
                font-weight: 500;
                width: 20%;
            }

            .phonepe-nav-item i { font-size: 19px; margin-bottom: 2px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 700; }
        }
    </style>
</head>
<body>

<!-- Mobile Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="manage_students.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>प्रोफ़ाइल एडिट करें</h3>
            <small>रोल नं: <?= htmlspecialchars($data['roll_no']); ?></small>
        </div>
    </div>
    <div style="color:#fff; font-size:20px;">
        <i class="fa fa-user-check"></i>
    </div>
</div>

<!-- Desktop Sidebar -->
<div class="sidebar">
    <h2>🏦 SMART CMS</h2>
    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
    <a href="manage_students.php" class="active"><i class="fa-solid fa-users-gear me-2"></i> Manage Students</a>
    <a href="collect_fees.php"><i class="fa-solid fa-wallet me-2"></i> Fees Portal</a>
    <a href="logout.php" style="margin-top: 50px; color: #f87171;"><i class="fa-solid fa-power-off me-2"></i> Logout</a>
</div>

<!-- Main Content Area -->
<div class="main-content">
    <div class="form-card">
        
        <!-- Desktop Header -->
        <div class="header-box">
            <div>
                <h2 style="font-size: 24px; font-weight: 800;">प्रोफ़ाइल अपडेट करें</h2>
                <p style="color: var(--secondary); font-size: 14px; margin-top: 5px;">रोल नंबर: <b style="color: var(--primary);"><?= htmlspecialchars($data['roll_no']); ?></b></p>
            </div>
            <a href="manage_students.php" style="text-decoration:none; font-size: 14px; color: var(--primary); font-weight: 700;">← वापस जाएं</a>
        </div>
        
        <?= $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="photo-edit-section">
                <?php $photo_path = (!empty($data['photo'])) ? 'uploads/profile/'.$data['photo'] : 'https://ui-avatars.com/api/?name='.urlencode($data['name']); ?>
                <img src="<?= $photo_path ?>?v=<?= time() ?>" id="preview" class="stu-photo-preview">
                <label class="file-input-label">
                    <i class="fa-solid fa-camera me-1"></i> फोटो बदलें
                    <input type="file" name="photo" style="display: none;" onchange="previewImage(this)">
                </label>
            </div>

            <div class="grid">
                <div class="input-group">
                    <label>छात्र का नाम</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($data['name']); ?>" required>
                </div>
                
                <div class="input-group">
                    <label>पिता का नाम</label>
                    <input type="text" name="father_name" value="<?= htmlspecialchars($data['father_name']); ?>">
                </div>

                <div class="input-group">
                    <label>कोर्स चुनें</label>
                    <select name="course">
                        <option value="DCA" <?= ($data['course'] == 'DCA') ? 'selected' : ''; ?>>DCA</option>
                      <option value="BA" <?= ($data['course'] == 'BA') ? 'selected' : ''; ?>>BA</option>
                        <option value="PGDCA" <?= ($data['course'] == 'PGDCA') ? 'selected' : ''; ?>>PGDCA</option>
                        <option value="Tally" <?= ($data['course'] == 'Tally') ? 'selected' : ''; ?>>Tally Prime</option>
                        <option value="TC ACADAMY VALANTIYAR" <?= ($data['course'] == 'TC ACADAMY VALANTIYAR') ? 'selected' : ''; ?>>TC ACADAMY VALANTIYAR</option>
                        <option value="Olympiad" <?= ($data['course'] == 'Olympiad') ? 'selected' : ''; ?>>CSC Olympiad Computer</option>
                    </select>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-indian-rupee-sign me-1" style="color:var(--primary);"></i> कुल कोर्स फ़ीस (Total Fee)</label>
                    <input type="number" name="total_fee" value="<?= htmlspecialchars($data['total_fee'] ?? 0); ?>" placeholder="उदा. 15000" required min="0">
                </div>

                <!-- 💥 UPDATED BATCH TIMINGS FIELDS 💥 -->
                <div class="input-group">
                    <label><i class="fa-solid fa-clock me-1"></i> बैच शुरू समय (Start Time)</label>
                    <input type="time" name="batch_start_time" value="<?= htmlspecialchars($data['batch_start_time'] ?? ''); ?>" required>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-clock me-1"></i> बैच समाप्त समय (End Time)</label>
                    <input type="time" name="batch_end_time" value="<?= htmlspecialchars($data['batch_end_time'] ?? ''); ?>" required>
                </div>

                <div class="input-group">
                    <label>सत्र (Session)</label>
                    <select name="session">
                        <option value="2024-2025" <?= ($data['session'] == '2024-2025') ? 'selected' : ''; ?>>2024-2025</option>
                        <option value="2025-2026" <?= ($data['session'] == '2025-2026') ? 'selected' : ''; ?>>2025-2026</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>ईमेल एड्रेस</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
                </div>

                <div class="input-group">
                    <label>मोबाइल नंबर</label>
                    <input type="text" name="mobile" value="<?= htmlspecialchars($data['user_mobile']); ?>" required maxlength="10">
                </div>

                <div class="input-group">
                    <label>जन्म तिथि (DOB)</label>
                    <input type="date" name="dob" value="<?= $data['dob']; ?>">
                </div>

                <div class="input-group">
                    <label>लिंग (Gender)</label>
                    <select name="gender">
                        <option value="Male" <?= ($data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?= ($data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>

                <div class="input-group full">
                    <label>स्थायी पता (Address)</label>
                    <textarea name="address" rows="3"><?= htmlspecialchars($data['address']); ?></textarea>
                </div>
            </div>

            <button type="submit" name="update" class="btn-update">
                <i class="fa-solid fa-cloud-arrow-up"></i> जानकारी अपडेट करें (Update Profile)
            </button>
        </form>
    </div>
</div>

<!-- Mobile Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>होम</span>
    </a>
    <a href="add_student.php" class="phonepe-nav-item">
        <i class="fa fa-user-plus"></i>
        <span>नया प्रवेश</span>
    </a>
    <a href="manage_students.php" class="phonepe-nav-item active">
        <i class="fa fa-users"></i>
        <span>छात्र</span>
    </a>
    <a href="collect_fees.php" class="phonepe-nav-item">
        <i class="fa fa-receipt"></i>
        <span>फीस</span>
    </a>
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-bars"></i>
        <span>मेनू</span>
    </a>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('preview').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
</body>
</html>