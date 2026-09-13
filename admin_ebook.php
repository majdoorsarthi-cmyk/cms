<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_config.php'; 

// Admin Auth Check
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$msg = "";

// --- File Upload Logic ---
if(isset($_POST['upload_pdf'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    
    // File details
    $original_name = $_FILES['ebook']['name'];
    $file_tmp = $_FILES['ebook']['tmp_name'];
    $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    
    // 1. Sirf PDF allow karein
    if($file_ext != "pdf"){
        $msg = "<div style='color: #ff4d4d; background: rgba(255,77,77,0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>❌ Sirf PDF file upload karein!</div>";
    } else {
        // 2. File name sanitize karein
        $clean_name = str_replace(' ', '_', $original_name);
        $new_filename = "EBOOK_" . time() . "_" . $clean_name;
        $upload_path = "uploads/ebooks/";

        // Folder create karein agar nahi hai
        if (!is_dir($upload_path)) { mkdir($upload_path, 0777, true); }

        // 3. Duplicate Title check karein
        $check = mysqli_query($conn, "SELECT id FROM ebooks WHERE title = '$title' AND course_name = '$course'");
        if(mysqli_num_rows($check) > 0) {
            $msg = "<div style='color: #f39c12; background: rgba(243,156,18,0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>⚠️ Is title ki book pehle se maujood hai!</div>";
        } else {
            if(move_uploaded_file($file_tmp, $upload_path . $new_filename)){
                // Database table check aur insert
                mysqli_query($conn, "CREATE TABLE IF NOT EXISTS ebooks (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255),
                    course_name VARCHAR(100),
                    file_path TEXT,
                    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");

                $query = "INSERT INTO ebooks (title, course_name, file_path) VALUES ('$title', '$course', '$new_filename')";
                if(mysqli_query($conn, $query)){
                    $msg = "<div style='color: #2ecc71; background: rgba(46,204,113,0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>✅ E-Book Uploaded Successfully!</div>";
                }
            } else {
                $msg = "<div style='color: #ff4d4d; background: rgba(255,77,77,0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>❌ Upload error! Folder permissions check karein.</div>";
            }
        }
    }
}

// Delete Logic
if(isset($_GET['delete'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $get_file = mysqli_query($conn, "SELECT file_path FROM ebooks WHERE id='$id'");
    if($f_data = mysqli_fetch_assoc($get_file)) {
        $full_path = "uploads/ebooks/" . $f_data['file_path'];
        if(file_exists($full_path)) { unlink($full_path); } // Delete physical file
        mysqli_query($conn, "DELETE FROM ebooks WHERE id='$id'");
        header("Location: admin_ebook.php?deleted=success");
        exit();
    }
}

if(isset($_GET['deleted']) && $_GET['deleted'] == 'success'){
    $msg = "<div style='color: #ff4d4d; background: rgba(255,77,77,0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>🗑️ E-Book deleted successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage E-Books | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #3b82f6;
            --sidebar-bg: #1e293b;
            --main-bg: #0f172a;
            --card-bg: #1e293b;
            --text-light: #94a3b8;
            --danger: #ef4444;
            --success: #2ecc71;
            
            /* PhonePe UI Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3d1668;
            --phonepe-bg: #0f172a;
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Poppins', sans-serif; 
            -webkit-tap-highlight-color: transparent; 
        }

        html, body {
            width: 100%;
            overflow-x: hidden;
            background: var(--main-bg); 
            color: white; 
            min-height: 100vh;
        }

        /* 🎨 COMMON BASE STYLES */
        .sidebar-brand-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .sidebar h3 { color: var(--primary); text-align: left; font-size: 22px; font-weight: 700; margin: 0; }
        .sidebar a { color: var(--text-light); text-decoration: none; display: flex; align-items: center; padding: 12px 15px; margin: 5px 0; border-radius: 10px; font-size: 14px; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 12px; font-size: 16px; width: 20px; text-align: center; }
        .sidebar a:hover, .active-nav { background: rgba(59, 130, 246, 0.1); color: var(--primary) !important; font-weight: 600; }
        
        .main { padding: 40px; }
        .form-card { background: var(--card-bg); padding: 30px; border-radius: 20px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        
        h2 { font-size: 22px; font-weight: 600; margin-bottom: 25px; display: flex; align-items: center; }
        h2 i { margin-right: 15px; color: var(--primary); }

        label { font-size: 13.5px; font-weight: 600; color: var(--text-light); margin-bottom: 8px; display: block; }
        input, select { width: 100%; padding: 12px 14px; margin-bottom: 18px; background: #0f172a; border: 1px solid #334155; border-radius: 10px; color: white; outline: none; transition: 0.3s; font-size: 14px; }
        input:focus, select:focus { border-color: var(--primary); }
        
        .btn-upload { background: var(--primary); color: white; border: none; padding: 14px; border-radius: 10px; cursor: pointer; width: 100%; font-weight: 600; font-size: 15px; transition: 0.3s; }
        .btn-upload:hover { background: #2563eb; }

        .table-container { background: var(--card-bg); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { color: #64748b; font-size: 12px; text-transform: uppercase; padding: 15px; text-align: left; background: rgba(0,0,0,0.1); font-weight: 700; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; color: #e2e8f0; vertical-align: middle; }
        
        .course-badge { background: rgba(59, 130, 246, 0.15); color: #3b82f6; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; display: inline-block; }
        .btn-delete { color: var(--danger); text-decoration: none; padding: 8px; border-radius: 6px; transition: 0.3s; font-size: 18px; display: inline-block; }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.1); }

        .mobile-header, .sidebar-overlay, .sidebar-close-btn, .mobile-bottom-nav { display: none; }

        /* 🖥️ DESKTOP VIEW (Screen width >= 992px) - UNTOUCHED & PERFECT */
        @media (min-width: 992px) {
            .sidebar { 
                width: 260px !important; 
                background: var(--sidebar-bg) !important; 
                height: 100vh !important; 
                position: fixed !important; 
                top: 0 !important;
                left: 0 !important;
                padding: 25px 15px !important; 
                overflow-y: auto !important; 
                z-index: 1000 !important; 
                display: block !important;
                border-right: 1px solid rgba(255,255,255,0.1) !important;
            }

            .main { 
                margin-left: 260px !important; 
                padding: 40px !important; 
                width: calc(100% - 260px) !important; 
                min-height: 100vh;
            }
        }

        /* 📱 MOBILE VIEW - PhonePe Native App Style with LARGER READABLE FONTS */
        @media (max-width: 991px) {
            body {
                background: var(--phonepe-bg) !important;
            }

            /* PhonePe Header Bar */
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
            
            .mobile-profile-box {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .mobile-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #ffffff;
                color: var(--phonepe-purple);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                font-weight: 800;
                border: 2px solid rgba(255,255,255,0.85);
            }
            .mobile-header h3 { 
                font-size: 16px !important; 
                font-weight: 700 !important; 
                color: #ffffff !important; 
                margin: 0; 
                line-height: 1.2;
            }
            .mobile-header small {
                font-size: 12px !important;
                color: rgba(255,255,255,0.9);
                display: block;
                font-weight: 500;
            }

            .menu-toggle { 
                font-size: 18px; 
                color: #ffffff; 
                cursor: pointer; 
                border: none; 
                background: rgba(255,255,255,0.2); 
                width: 40px; 
                height: 40px; 
                border-radius: 50%; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
            }

            .main { 
                margin-left: 0 !important; 
                width: 100% !important; 
                padding: 80px 14px 85px 14px !important; 
            }

            /* Card & Form Updates with Bolder and Bigger Typography */
            .form-card {
                background: var(--card-bg) !important;
                border-radius: 18px !important;
                padding: 20px 16px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;
                border: 1px solid rgba(255,255,255,0.08) !important;
                margin-bottom: 20px !important;
            }
            
            h2 {
                font-size: 18px !important;
                font-weight: 700 !important;
                margin-bottom: 20px !important;
            }

            label {
                font-size: 14px !important;
                font-weight: 700 !important;
                color: #cbd5e1 !important;
            }

            input, select {
                padding: 13px 14px !important;
                border-radius: 12px !important;
                font-size: 15px !important;
                background: #0f172a !important;
                border: 1px solid #334155 !important;
            }

            .btn-upload {
                background: var(--phonepe-purple) !important;
                padding: 14px !important;
                border-radius: 12px !important;
                font-size: 16px !important;
                font-weight: 700 !important;
            }

            .table-container {
                background: var(--card-bg) !important;
                border-radius: 18px !important;
                padding: 16px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;
                border: 1px solid rgba(255,255,255,0.08) !important;
            }
            .table-container h3 {
                font-size: 16px !important;
                font-weight: 700 !important;
            }

            table { min-width: 500px !important; }
            th { font-size: 12px !important; padding: 12px !important; }
            td { font-size: 14.5px !important; padding: 12px !important; }
            .course-badge { font-size: 12px !important; padding: 6px 10px !important; }

            /* Table overflow container */
            .table-responsive-box {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Backdrop Overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.75);
                backdrop-filter: blur(3px);
                z-index: 99998;
            }
            .sidebar-overlay.active { display: block !important; }

            /* Mobile Sidebar Drawer */
            .sidebar { 
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: -100% !important;
                width: 80vw !important; 
                max-width: 300px !important;
                height: 100% !important;
                background: #1e293b !important;
                z-index: 99999 !important;
                box-shadow: 10px 0 35px rgba(0,0,0,0.5) !important;
                padding: 0 !important;
                overflow-y: auto !important;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                display: block !important;
            }

            .sidebar.active { 
                left: 0 !important;
            }

            .sidebar-brand-box {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%) !important;
                padding: 20px 18px !important;
                margin-bottom: 12px !important;
            }
            .sidebar-brand-box h3 { color: #ffffff !important; font-size: 20px !important; }

            .sidebar-close-btn {
                display: flex !important;
                align-items: center;
                justify-content: center;
                background: rgba(255,255,255,0.2) !important;
                border: none;
                width: 34px;
                height: 34px;
                border-radius: 50%;
                color: #ffffff !important;
                font-size: 16px;
                cursor: pointer;
            }

            .sidebar a {
                padding: 12px 18px !important;
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #94a3b8 !important;
            }
            .sidebar a.active-nav {
                background: rgba(95, 37, 159, 0.25) !important;
                color: #a855f7 !important;
            }

            /* PhonePe Style Fixed Bottom Navigation */
            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 62px;
                background: #1e293b;
                border-top: 1px solid rgba(255,255,255,0.1);
                z-index: 9998;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 15px rgba(0,0,0,0.2);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #94a3b8;
                font-size: 11px;
                font-weight: 600;
                width: 20%;
                transition: 0.2s;
            }

            .phonepe-nav-item i {
                font-size: 19px;
                margin-bottom: 3px;
            }

            .phonepe-nav-item.active {
                color: #a855f7 !important;
                font-weight: 700;
            }
        }
    </style>
</head>
<body>

<!-- Mobile Fixed Header (PhonePe App Style) -->
<div class="mobile-header">
    <div class="mobile-profile-box">
        <div class="mobile-avatar">A</div>
        <div>
            <h3>E-BOOK LIBRARY</h3>
            <small>Admin Digital Library</small>
        </div>
    </div>
    <button type="button" class="menu-toggle" onclick="toggleMobileSidebar()">
        <i class="fa fa-bars"></i>
    </button>
</div>

<!-- Backdrop Overlay -->
<div class="sidebar-overlay" id="overlay" onclick="toggleMobileSidebar()"></div>

<!-- Sidebar Navigation -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand-box">
        <h3>CMS PRO</h3>
        <button type="button" class="sidebar-close-btn" onclick="toggleMobileSidebar()"><i class="fa fa-times"></i></button>
    </div>

    <div style="padding: 0 10px;">
        <a href="admin_dashboard.php"><i class="fa fa-gauge"></i> <span>Dashboard</span></a>
        <a href="manage_students.php"><i class="fa fa-users"></i> <span>Students</span></a>
        <a href="admin_upload_video.php"><i class="fa fa-video"></i> <span>Videos</span></a>
        <a href="admin_ebook.php" class="active-nav"><i class="fa fa-book"></i> <span>E-Books</span></a>
        <a href="logout.php" style="margin-top: 40px; color: var(--danger);"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
    </div>
</div>

<!-- Main Content -->
<div class="main">
    <div class="form-card">
        <h2><i class="fas fa-file-upload"></i> Upload E-Book</h2>
        <?php echo $msg; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Book Title</label>
            <input type="text" name="title" placeholder="e.g. Fundamental of Computer" required>
            
            <label>Assign to Course</label>
            <select name="course" required>
                <option value="All">All Students</option>
                <option value="ADCA">ADCA</option>
                <option value="DCA">DCA</option>
                <option value="CCC">CCC</option>
                <option value="Tally">Tally Prime</option>
            </select>

            <label>Choose PDF File</label>
            <input type="file" name="ebook" accept=".pdf" required>
            
            <button type="submit" name="upload_pdf" class="btn-upload">
                <i class="fas fa-cloud-upload-alt" style="margin-right: 8px;"></i> Publish to Library
            </button>
        </form>
    </div>

    <div class="table-container">
        <h3 style="font-size: 18px; margin-bottom: 15px;"><i class="fas fa-list" style="margin-right: 10px; color: var(--primary);"></i> Uploaded E-Books</h3>
        <div class="table-responsive-box">
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM ebooks ORDER BY id DESC");
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)){
                            echo "<tr>
                                    <td><b>{$row['title']}</b></td>
                                    <td><span class='course-badge'>{$row['course_name']}</span></td>
                                    <td style='color: #94a3b8;'>".date('d M, Y', strtotime($row['uploaded_at']))."</td>
                                    <td style='text-align: center;'>
                                        <a href='?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Kya aap is book ko delete karna chahte hain?\")'>
                                            <i class='fa fa-trash-alt'></i>
                                        </a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 30px; color: #64748b;'>Abhi tak koi book upload nahi hui hai.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PhonePe Mobile App Style Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="manage_students.php" class="phonepe-nav-item">
        <i class="fa fa-user-graduate"></i>
        <span>Students</span>
    </a>
    <a href="admin_upload_video.php" class="phonepe-nav-item">
        <i class="fa fa-play-circle"></i>
        <span>Videos</span>
    </a>
    <a href="admin_ebook.php" class="phonepe-nav-item active">
        <i class="fa fa-book"></i>
        <span>E-Books</span>
    </a>
    <a href="javascript:void(0)" class="phonepe-nav-item" onclick="toggleMobileSidebar()">
        <i class="fa fa-bars"></i>
        <span>Menu</span>
    </a>
</div>

<script>
    // Toggle Mobile Sidebar Drawer Function without Page Reload
    function toggleMobileSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('overlay');
        
        if (sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = ''; 
        } else {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; 
        }
    }
</script>
</body>
</html>