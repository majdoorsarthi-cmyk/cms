<?php 
// 1. Session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// 2. Admin login check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// --- Video Delete Logic ---
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM videos WHERE id = '$id'");
    header("Location: admin_upload_video.php?msg=deleted");
    exit();
}

// --- Video Upload Logic ---
$video_status = "";
if(isset($_POST['add_video'])){
    $v_title = mysqli_real_escape_string($conn, $_POST['v_title']);
    $v_url = mysqli_real_escape_string($conn, $_POST['v_url']);
    $v_course = mysqli_real_escape_string($conn, $_POST['v_course']);
    
    // Database table auto-creation
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        video_url TEXT,
        course_name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Check for duplicate URL
    $check_duplicate = mysqli_query($conn, "SELECT id FROM videos WHERE video_url = '$v_url'");
    if(mysqli_num_rows($check_duplicate) > 0){
        $video_status = "<div style='color: #e74c3c; background: rgba(231, 76, 60, 0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>⚠️ Error: This Video URL is already uploaded!</div>";
    } else {
        $v_query = "INSERT INTO videos (title, video_url, course_name) VALUES ('$v_title', '$v_url', '$v_course')";
        if(mysqli_query($conn, $v_query)){
            $video_status = "<div style='color: #2ecc71; background: rgba(46, 204, 113, 0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>✅ Video successfully uploaded!</div>";
        }
    }
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'){
    $video_status = "<div style='color: #e74c3c; background: rgba(231, 76, 60, 0.1); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;'>🗑️ Video deleted successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Upload Videos | CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary: #2563eb; 
            --sidebar-bg: #1e293b; 
            --main-bg: #f8fafc; 
            --danger: #ef4444;
            --success: #2ecc71;
            
            /* PhonePe UI Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3d1668;
            --phonepe-bg: #f5f6fa;
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
            background-color: var(--main-bg); 
            min-height: 100vh;
        }

        /* 🎨 COMMON BASE STYLES */
        .sidebar-brand-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .sidebar h3 { color: #3b82f6; text-align: left; font-size: 22px; font-weight: 700; margin: 0; }
        .sidebar a { color: #94a3b8; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; margin: 5px 0; border-radius: 10px; font-size: 14px; transition: 0.3s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: var(--primary); color: white; }
        .sidebar a i { margin-right: 12px; font-size: 16px; width: 20px; text-align: center; }
        .nav-label { font-size: 11px; text-transform: uppercase; color: #64748b; margin: 20px 0 10px 10px; font-weight: 700; letter-spacing: 0.5px; }

        .upload-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; align-items: start; }
        .card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .form-group { margin-bottom: 18px; }
        label { font-size: 13.5px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block; }
        input, select { width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px; outline: none; font-size: 14px; color: #1e293b; background: #fff; transition: 0.2s; }
        input:focus, select:focus { border-color: var(--primary); }
        
        .btn-upload { width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 15px; }
        .btn-upload:hover { background: #1d4ed8; }

        /* Table Style */
        .table-container { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; padding: 15px; text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #1e293b; vertical-align: middle; }
        .badge { padding: 5px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; background: rgba(37,99,235,0.1); color: var(--primary); display: inline-block; }
        .btn-delete { color: var(--danger); text-decoration: none; font-size: 18px; display: inline-block; padding: 5px; }
        .btn-delete:hover { color: #b91c1c; }

        .mobile-header, .sidebar-overlay, .sidebar-close-btn, .mobile-bottom-nav { display: none; }

        /* 🖥️ DESKTOP VIEW (Screen width >= 992px) - UNTOUCHED & SHANDAR LAYOUT */
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

            .upload-grid {
                grid-template-columns: 1fr !important;
                gap: 18px !important;
            }

            /* Card & Form Updates with Bolder and Bigger Typography */
            .card {
                background: #ffffff !important;
                border-radius: 18px !important;
                padding: 20px 16px !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
                border: 1px solid #edf2f7;
            }
            .card h4 {
                font-size: 17px !important;
                font-weight: 700 !important;
                color: #1e293b !important;
            }

            label {
                font-size: 14px !important;
                font-weight: 700 !important;
                color: #334155 !important;
            }

            input, select {
                padding: 13px 14px !important;
                border-radius: 12px !important;
                font-size: 15px !important;
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
            }

            .btn-upload {
                background: var(--phonepe-purple) !important;
                padding: 14px !important;
                border-radius: 12px !important;
                font-size: 16px !important;
                font-weight: 700 !important;
            }

            .table-container {
                background: #ffffff !important;
                border-radius: 18px !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
                border: 1px solid #edf2f7;
            }

            table { min-width: 500px !important; }
            th { font-size: 12px !important; padding: 12px !important; }
            td { font-size: 14px !important; padding: 12px !important; }
            td div { font-size: 14.5px !important; font-weight: 600 !important; }
            .badge { font-size: 12px !important; padding: 6px 10px !important; }

            /* Overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.65);
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
                background: #ffffff !important;
                z-index: 99999 !important;
                box-shadow: 10px 0 35px rgba(0,0,0,0.25) !important;
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
                color: #475569 !important;
            }
            .sidebar a.active {
                background: #f3e8ff !important;
                color: var(--phonepe-purple) !important;
            }
            .nav-label { font-size: 11px !important; margin: 16px 0 6px 18px !important; }

            /* PhonePe Style Fixed Bottom Navigation */
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
                box-shadow: 0 -4px 15px rgba(0,0,0,0.06);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #718096;
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
                color: var(--phonepe-purple) !important;
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
            <h3>E-LEARNING PANEL</h3>
            <small>Video Management System</small>
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
        <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a>
        
        <div class="nav-label">Academic</div>
        <a href="manage_students.php"><i class="fa-solid fa-users"></i> <span>Students</span></a>
        
        <div class="nav-label">E-Learning</div>
        <a href="admin_upload_video.php" class="active"><i class="fa-solid fa-circle-play"></i> <span>Upload Videos</span></a>
        <a href="admin_ebook.php"><i class="fa-solid fa-file-pdf"></i> <span>E-Books</span></a>
        
        <a href="logout.php" style="margin-top: 40px; color: var(--danger);"><i class="fa-solid fa-power-off"></i> <span>Logout</span></a>
    </div>
</div>

<!-- Main Content Area -->
<div class="main">
    <h2 style="margin-bottom: 22px; color: #1e293b; font-weight: 700;">Video Management</h2>

    <div class="upload-grid">
        <div class="card">
            <h4 style="margin-bottom: 20px;"><i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Add New Video</h4>
            <?php echo $video_status; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Video Title</label>
                    <input type="text" name="v_title" placeholder="Enter video title" required>
                </div>
                <div class="form-group">
                    <label>YouTube Embed Link</label>
                    <input type="text" name="v_url" placeholder="https://www.youtube.com/embed/..." required>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <select name="v_course" required>
                        <option value="All">All Courses</option>
                        <option value="ADCA">ADCA</option>
                        <option value="DCA">DCA</option>
                        <option value="CCC">CCC</option>
                        <option value="Tally">Tally Prime</option>
                    </select>
                </div>
                <button type="submit" name="add_video" class="btn-upload">Publish Video</button>
            </form>
        </div>

        <div class="table-container">
            <div style="padding: 18px 20px; font-weight: 700; border-bottom: 1px solid #f1f5f9; font-size: 16px; color: #1e293b;">Recently Uploaded Videos</div>
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table>
                    <thead>
                        <tr>
                            <th>Video Title</th>
                            <th>Course</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $video_list = mysqli_query($conn, "SELECT * FROM videos ORDER BY id DESC");
                        if(mysqli_num_rows($video_list) > 0){
                            while($row = mysqli_fetch_assoc($video_list)){
                                echo "<tr>
                                        <td>
                                            <div>{$row['title']}</div>
                                            <div style='font-size:11px; color:#94a3b8; font-weight: 500; margin-top:2px;'>Added: ".date('d M Y', strtotime($row['created_at']))."</div>
                                        </td>
                                        <td><span class='badge'>{$row['course_name']}</span></td>
                                        <td style='text-align: center;'>
                                            <a href='?delete_id={$row['id']}' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this video?\")'>
                                                <i class='fa-solid fa-trash-can'></i>
                                            </a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; color:#94a3b8; padding: 25px;'>No videos uploaded yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
    <a href="admin_upload_video.php" class="phonepe-nav-item active">
        <i class="fa fa-play-circle"></i>
        <span>Videos</span>
    </a>
    <a href="admin_ebook.php" class="phonepe-nav-item">
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