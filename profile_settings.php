<?php 
// 1. Output Buffering स्टार्ट करें (रीलोड और Header Sent एरर रोकने के लिए)
ob_start();

// 2. Session and Database Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// 3. Student Login Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$user_id = mysqli_real_escape_string($conn, $stu['user_id'] ?? ($stu['id'] ?? 0)); 

// 4. Fetch Detailed Profile Data
$profile_q = mysqli_query($conn, "SELECT * FROM students WHERE user_id = '$user_id' OR id = '$user_id' LIMIT 1");
$profile_data = ($profile_q && mysqli_num_rows($profile_q) > 0) ? mysqli_fetch_assoc($profile_q) : $stu;

$real_stu_id = $profile_data['id'] ?? 0;
$student_course = $profile_data['course'] ?? 'Not Enrolled';
$roll_no = $profile_data['roll_no'] ?? 'N/A';
$lms_status = $profile_data['status'] ?? 'Pending';

// 5. PROFILE UPDATE LOGIC
$update_msg = "";
if (isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Photo Upload Handling
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "uploads/profile/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $new_filename = "stu_" . $real_stu_id . "_" . time() . "." . strtolower($file_ext);
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
            mysqli_query($conn, "UPDATE students SET photo = '$new_filename' WHERE id = '$real_stu_id'");
        }
    }

    $update_query = "UPDATE students SET name = '$full_name', phone = '$phone' WHERE id = '$real_stu_id'";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['student']['name'] = $full_name; // Refresh session
        $update_msg = "<div class='alert-msg success'><i class='fa-solid fa-circle-check'></i> प्रोफाइल सफलतापूर्वक अपडेट हो गई है!</div>";
        
        // Refresh data from DB
        $profile_q = mysqli_query($conn, "SELECT * FROM students WHERE id = '$real_stu_id' LIMIT 1");
        $profile_data = mysqli_fetch_assoc($profile_q);
    } else {
        $update_msg = "<div class='alert-msg error'><i class='fa-solid fa-circle-xmark'></i> अपडेट करने में समस्या आई।</div>";
    }
}

// 6. Profile Photo Path Check
$stu_photo = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
if(!empty($profile_data['photo'])) {
    if(file_exists('uploads/profile/' . $profile_data['photo'])) {
        $stu_photo = 'uploads/profile/' . $profile_data['photo'];
    } elseif(file_exists('uploads/' . $profile_data['photo'])) {
        $stu_photo = 'uploads/' . $profile_data['photo'];
    }
}

$current_page = 'profile_settings';
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Profile Settings | CMS PRO</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #3b82f6;
            --sidebar-bg: #0f172a;
            --nav-text: #94a3b8;
            --bg: #f8fafc;
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); display: flex; color: #1e293b; min-height: 100vh; overflow-x: hidden; }

        /* 🖥️ DESKTOP SIDEBAR & NAV STYLES (ORIGINAL UNCHANGED) */
        .sidebar { width: 280px; background: var(--sidebar-bg); height: 100vh; position: fixed; padding: 20px; color: white; overflow-y: auto; z-index: 1000; }
        .sidebar-brand { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; padding: 10px; }
        .sidebar-brand i { color: var(--primary); }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 700; margin: 20px 0 10px 10px; letter-spacing: 1px; }
        .sidebar-menu a { color: var(--nav-text); text-decoration: none; padding: 12px 15px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 4px; transition: 0.3s; font-weight: 500; }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .sidebar-menu a.active { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }

        .main-content { margin-left: 280px; width: calc(100% - 280px); }
        .top-nav { background: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 999; }
        .content-body { padding: 30px; }

        .box { background: white; border-radius: 20px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 25px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; transition: 0.3s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .form-control:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

        .btn-update { background: var(--primary); color: white; padding: 14px 30px; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; transition: 0.3s; width: 100%; font-size: 15px; }
        .btn-update:hover { opacity: 0.9; transform: translateY(-1px); }

        .profile-upload-area { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid #f1f5f9; }
        .profile-preview { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e0e7ff; }

        .alert-msg { padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 10px; }
        .alert-msg.success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-msg.error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .mobile-header, .mobile-bottom-nav { display: none; }

        /* 📱 PHONEPE REAL MOBILE APP INTERFACE (BIG & CRISP FONTS) */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; padding-top: 75px; padding-bottom: 95px; display: block !important; }

            .sidebar, .top-nav { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; }
            .content-body { padding: 12px !important; }

            /* App Top Header Bar */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 72px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.4);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { color: #ffffff; font-size: 22px; width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; }
            .mobile-header h3 { color: #ffffff !important; font-size: 22px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.9); font-size: 14px !important; font-weight: 600; display: block; }

            /* App Card Style */
            .box { background: #ffffff !important; border-radius: 24px !important; padding: 22px 18px !important; border: none !important; box-shadow: 0 6px 20px rgba(0,0,0,0.05) !important; margin-bottom: 18px !important; }

            /* PhonePe Photo Upload Component */
            .profile-upload-area {
                flex-direction: column !important; text-align: center !important; gap: 12px !important;
                padding-bottom: 20px !important; margin-bottom: 20px !important;
            }
            .photo-avatar-wrapper { position: relative; margin: 0 auto; width: 110px; height: 110px; }
            .profile-preview { width: 110px !important; height: 110px !important; border: 4px solid var(--phonepe-purple) !important; box-shadow: 0 8px 20px rgba(95, 37, 159, 0.3) !important; }
            
            .upload-badge {
                position: absolute; bottom: 2px; right: 2px; background: var(--phonepe-purple);
                color: #fff; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center;
                justify-content: center; font-size: 16px; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            }

            .file-input-wrapper { display: inline-block; position: relative; margin-top: 6px; }
            .custom-file-btn { background: #f3e8ff; color: var(--phonepe-purple); font-size: 15px !important; font-weight: 800; padding: 10px 20px; border-radius: 50px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
            #photoInput { opacity: 0; position: absolute; left: 0; top: 0; width: 100%; height: 100%; cursor: pointer; }

            /* Big Mobile Form Elements */
            .mobile-grid-1col { display: block !important; }
            .form-group { margin-bottom: 18px !important; }
            .form-label { font-size: 15px !important; font-weight: 800 !important; color: #334155 !important; margin-bottom: 8px !important; text-transform: uppercase; letter-spacing: 0.3px; }
            
            .form-control {
                height: 54px !important; font-size: 17px !important; font-weight: 700 !important;
                border-radius: 16px !important; padding: 12px 18px !important; border: 2px solid #e2e8f0 !important;
                background: #f8fafc !important; color: #0f172a !important;
            }
            .form-control:focus { border-color: var(--phonepe-purple) !important; background: #ffffff !important; box-shadow: 0 0 0 4px rgba(95, 37, 159, 0.1) !important; }
            .form-control:disabled { background: #f1f5f9 !important; color: #64748b !important; }

            /* Action Buttons */
            .btn-update {
                height: 56px !important; background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                font-size: 19px !important; font-weight: 800 !important; border-radius: 18px !important;
                box-shadow: 0 8px 25px rgba(95, 37, 159, 0.35) !important; margin-top: 10px;
            }

            /* PhonePe Bottom Nav */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 76px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 13px !important; font-weight: 700; width: 25%; }
            .phonepe-nav-item i { font-size: 24px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe Top Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="profile.php" class="mobile-back-btn"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h3>Profile Settings</h3>
            <small><?php echo htmlspecialchars($profile_data['name'] ?? 'Student'); ?></small>
        </div>
    </div>
    <div style="color:#fff; font-size:24px;">
        <i class="fa-solid fa-user-shield"></i>
    </div>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-university"></i> <span>CMS ACADEMY</span>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        <a href="dashboard.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
        <a href="profile.php"><i class="fas fa-user-graduate"></i> <span>My Profile</span></a>

        <div class="menu-label">Academic Center</div>
        <a href="dashboard.php?page=lms_portal"><i class="fas fa-graduation-cap"></i> <span>LMS Access Portal</span></a>
        <a href="dashboard.php?page=videos"><i class="fas fa-play-circle"></i> <span>Video Lectures</span></a>
        <a href="online_exam.php"><i class="fas fa-file-signature"></i> <span>Online Exams</span></a>

        <div class="menu-label">Account</div>
        <a href="profile_settings.php" class="active"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    
    <!-- 🖥️ DESKTOP TOP NAV -->
    <div class="top-nav">
        <div><h3 style="font-weight: 800; font-size: 20px;">Profile Settings</h3></div>
        <div style="display:flex; align-items:center; gap:15px;">
            <div style="text-align:right">
                <p style="font-size:14px; font-weight:700; color:#0f172a;"><?= htmlspecialchars($profile_data['name'] ?? 'Student') ?></p>
                <p style="font-size:11px; color:#64748b; font-weight:600;"><?= $roll_no ?></p>
            </div>
            <img src="<?= $stu_photo ?>" style="width:42px; height:42px; border-radius:50%; object-fit:cover;">
        </div>
    </div>

    <div class="content-body">
        <?= $update_msg ?>

        <div style="max-width: 800px; margin: 0 auto;">
            <div class="box">
                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <!-- Profile Upload Area -->
                    <div class="profile-upload-area">
                        <div class="photo-avatar-wrapper">
                            <img src="<?= $stu_photo ?>" class="profile-preview" id="previewImg">
                            <div class="upload-badge"><i class="fa-solid fa-camera"></i></div>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 4px; font-size: 18px; font-weight: 800;">Profile Picture</h4>
                            <p style="font-size: 13px; color: #64748b; margin-bottom: 10px; font-weight: 600;">अपलोड करने के लिए नई फोटो चुनें (JPG/PNG)</p>
                            <div class="file-input-wrapper">
                                <span class="custom-file-btn"><i class="fa-solid fa-cloud-arrow-up"></i> Choose New Photo</span>
                                <input type="file" name="profile_photo" id="photoInput" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Input Fields Grid -->
                    <div class="mobile-grid-1col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($profile_data['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Roll Number</label>
                            <input type="text" class="form-control" value="<?= $roll_no ?>" disabled>
                        </div>
                    </div>

                    <div class="mobile-grid-1col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Portal Password</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($profile_data['password'] ?? $stu['password'] ?? '••••••••') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile_data['phone'] ?? $profile_data['mobile'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mobile-grid-1col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($profile_data['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Enrolled Course</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($student_course) ?>" disabled>
                        </div>
                    </div>

                    <div style="margin-top: 10px;">
                        <button type="submit" name="update_profile" class="btn-update">
                            <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Security Box -->
            <div class="box" style="border-left: 5px solid var(--phonepe-purple) !important;">
                <h4 style="margin-bottom: 8px; font-size: 16px; font-weight: 800; color: #0f172a;">
                    <i class="fas fa-shield-alt" style="color: var(--phonepe-purple);"></i> Security Note
                </h4>
                <p style="font-size: 14px; color: #64748b; line-height: 1.6; font-weight: 600;">
                    आपका Email, Roll Number और Course डिटेल्स लॉक हैं। अगर इनमें कोई बदलाव करवाना है, तो कृपया एडमिन से संपर्क करें।
                </p>
            </div>

        </div>
    </div>
</div>

<!-- 📱 PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="dashboard.php" class="phonepe-nav-item">
        <i class="fa-solid fa-house"></i>
        <span>होम</span>
    </a>
    <a href="fees_history.php" class="phonepe-nav-item">
        <i class="fa-solid fa-wallet"></i>
        <span>फीस</span>
    </a>
    <a href="my_results.php" class="phonepe-nav-item">
        <i class="fa-solid fa-file-invoice"></i>
        <span>रिजल्ट</span>
    </a>
    <a href="profile.php" class="phonepe-nav-item active">
        <i class="fa-solid fa-user"></i>
        <span>प्रोफाइल</span>
    </a>
</div>

<script>
    // Live Image Preview Fix
    document.getElementById('photoInput').onchange = evt => {
        const [file] = document.getElementById('photoInput').files;
        if (file) {
            document.getElementById('previewImg').src = URL.createObjectURL(file);
        }
    }
</script>

</body>
</html>
<?php 
// End Output Buffering
ob_end_flush(); 
?>