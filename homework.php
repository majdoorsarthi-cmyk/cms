<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// Login Check
if(!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) { 
    header("Location: index.php"); 
    exit(); 
}

// --- DYNAMIC COLUMN CHECKER ---
$id_col = "id"; 
$check_hw_id = mysqli_query($conn, "SHOW COLUMNS FROM `homework` LIKE 'hw_id'");
if($check_hw_id && mysqli_num_rows($check_hw_id) > 0) {
    $id_col = "hw_id";
}

// --- DELETE LOGIC (PRG Pattern) ---
if(isset($_GET['del'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['del']);
    $file_q = mysqli_query($conn, "SELECT file_path FROM homework WHERE $id_col = '$del_id'");
    $file_data = mysqli_fetch_assoc($file_q);
    
    if($file_data) {
        $path = "uploads/" . $file_data['file_path'];
        if(file_exists($path)) { @unlink($path); } 
        mysqli_query($conn, "DELETE FROM homework WHERE $id_col = '$del_id'");
        header("Location: homework.php?msg=deleted");
        exit();
    }
}

// --- UPLOAD LOGIC ---
if(isset($_POST['upload'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp = $_FILES['pdf_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if($file_ext != "pdf"){
            header("Location: homework.php?msg=err_pdf");
            exit();
        } else {
            $new_file_name = "HW_" . time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
            $target_path = "uploads/" . $new_file_name;

            if(move_uploaded_file($file_tmp, $target_path)){
                $sql = "INSERT INTO homework (title, file_path) VALUES ('$title', '$new_file_name')";
                if(mysqli_query($conn, $sql)){
                    header("Location: homework.php?msg=uploaded");
                    exit();
                } else {
                    header("Location: homework.php?msg=err_sql");
                    exit();
                }
            } else {
                header("Location: homework.php?msg=err_perm");
                exit();
            }
        }
    } else {
        header("Location: homework.php?msg=err_file");
        exit();
    }
}

// Status Messages
$message = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'uploaded') {
        $message = "<div class='alert-msg success'><i class='fa-solid fa-circle-check me-2'></i> होमवर्क/नोट्स सफलतापूर्वक अपलोड हो गया!</div>";
    } else if($_GET['msg'] == 'deleted') {
        $message = "<div class='alert-msg warning'><i class='fa-solid fa-trash-can me-2'></i> होमवर्क सफलता से डिलीट कर दिया गया।</div>";
    } else if($_GET['msg'] == 'err_pdf') {
        $message = "<div class='alert-msg error'><i class='fa-solid fa-circle-xmark me-2'></i> त्रुटी: सिर्फ PDF फाइल्स ही अपलोड करें।</div>";
    } else if($_GET['msg'] == 'err_file') {
        $message = "<div class='alert-msg error'><i class='fa-solid fa-triangle-exclamation me-2'></i> कृपया एक सही PDF फ़ाइल चुनें।</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Manage Homework | Admin Smart CMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar-bg: #1c2b36; 
            --primary: #28a745; 
            --bg: #f0f2f5; 
            
            /* PhonePe App Purple Palette */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-light-purple: #f3e8ff;
            --phonepe-bg: #f4f5f9;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; background: var(--bg); color: #333; display: flex; min-height: 100vh; }
        
        /* 🖥️ DESKTOP STYLES (Unchanged Original) */
        .sidebar { width: 240px; height: 100vh; background: var(--sidebar-bg); color: #fff; position: fixed; padding: 20px; z-index: 10; }
        .sidebar h2 { font-size: 20px; color: #3498db; text-align: center; margin-bottom: 30px; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 12px; margin: 8px 0; border-radius: 5px; transition: 0.3s; }
        .sidebar a:hover, .active-nav { background: #243642; color: white; }
        
        .main-content { margin-left: 280px; padding: 40px; width: calc(100% - 280px); }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 650px; }
        label { font-weight: 600; display: block; margin-bottom: 8px; color: #444; }
        input[type="text"], input[type="file"] { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; }
        .btn-upload { background: var(--primary); color: white; border: none; padding: 14px; width: 100%; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.2s; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 30px; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; }
        .btn-view { color: #007bff; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .btn-del { color: #dc3545; text-decoration: none; font-size: 14px; font-weight: 600; }

        .alert-msg { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; font-size: 15px; }
        .success { background: #e6ffed; color: #15803d; border: 1px solid #bbf7d0; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .error { background: #ffdce0; color: #b91c1c; border: 1px solid #fecaca; }

        .mobile-header, .mobile-bottom-nav, .mobile-hw-list { display: none; }

        /* 📱 MOBILE VIEW (Full PhonePe Real App UI + High Legibility Larger Fonts) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
            .sidebar, table { display: none !important; }

            /* PhonePe Top Native App Header */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 68px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999;
                padding: 0 18px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.3);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn {
                color: #ffffff;
                font-size: 20px;
                text-decoration: none;
                width: 42px; height: 42px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 19px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 13px !important; display: block; font-weight: 500; }

            .main-content {
                margin-left: 0 !important;
                padding: 84px 14px 90px 14px !important;
                width: 100% !important;
            }

            /* PhonePe Form Card Container */
            .card {
                max-width: 100% !important;
                padding: 22px 18px !important;
                border-radius: 20px !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
            }
            .card h2 { font-size: 20px !important; font-weight: 800; color: #0f172a; margin-bottom: 20px; }

            /* Larger Inputs & Touch Targets */
            label { font-size: 16px !important; font-weight: 800 !important; color: #334155 !important; margin-bottom: 8px; }
            
            input[type="text"] {
                padding: 16px 18px !important;
                font-size: 17px !important; /* Extra Legible Font */
                font-weight: 600 !important;
                border-radius: 14px !important;
                border: 2px solid #cbd5e1 !important;
                margin-bottom: 20px !important;
                background: #f8fafc;
            }
            input[type="text"]:focus {
                border-color: var(--phonepe-purple) !important;
                background: #fff;
                outline: none;
            }

            input[type="file"] {
                padding: 14px !important;
                font-size: 15.5px !important;
                border-radius: 14px !important;
                border: 2px dashed var(--phonepe-purple) !important;
                background: var(--phonepe-light-purple) !important;
                color: var(--phonepe-purple) !important;
                font-weight: 700;
                margin-bottom: 22px !important;
            }

            .btn-upload {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                padding: 18px !important;
                font-size: 18px !important; /* Bold Large Action Button */
                font-weight: 800 !important;
                border-radius: 16px !important;
                box-shadow: 0 6px 20px rgba(95, 37, 159, 0.3) !important;
            }

            /* PhonePe Card List for Uploaded Items */
            .mobile-hw-list {
                display: flex !important;
                flex-direction: column;
                gap: 14px;
                margin-top: 18px;
            }

            .phonepe-hw-card {
                background: #ffffff;
                border-radius: 20px;
                padding: 18px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03);
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-hw-top { display: flex; align-items: flex-start; gap: 14px; }
            .phonepe-pdf-icon {
                width: 52px; height: 52px;
                background: #fef2f2;
                color: #dc2626;
                border-radius: 16px;
                display: flex; align-items: center; justify-content: center;
                font-size: 26px;
                flex-shrink: 0;
            }

            .phonepe-hw-info h4 {
                font-size: 18px !important; /* Larger File Title */
                font-weight: 800;
                color: #0f172a;
                margin: 0 0 4px 0;
                line-height: 1.4;
            }

            .phonepe-hw-info p {
                font-size: 14px !important;
                color: #64748b;
                font-weight: 600;
                margin: 0;
                word-break: break-all;
            }

            .phonepe-hw-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 12px;
                border-top: 1px dashed #e2e8f0;
                padding-top: 12px;
                margin-top: 4px;
            }

            .phonepe-action-btn {
                padding: 10px 18px;
                border-radius: 12px;
                font-size: 15px !important; /* Larger Action Button Font */
                font-weight: 800;
                text-decoration: none;
                display: flex; align-items: center; gap: 6px;
            }

            .phonepe-btn-view { background: #eff6ff; color: #2563eb; }
            .phonepe-btn-del { background: #fef2f2; color: #dc2626; }

            /* PhonePe Bottom App Bar */
            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 68px;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                z-index: 9998;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #64748b;
                font-size: 12px !important;
                font-weight: 700;
                width: 25%;
            }

            .phonepe-nav-item i { font-size: 22px; margin-bottom: 3px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body>

<!-- Mobile Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>होमवर्क व स्टडी मटेरियल</h3>
            <small>Admin PDF Notes Portal</small>
        </div>
    </div>
    <div style="color:#fff; font-size:22px;">
        <i class="fa-solid fa-file-pdf"></i>
    </div>
</div>

<!-- Desktop Sidebar -->
<div class="sidebar">
    <h2>Admin Portal</h2>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="add_student.php">➕ Add Student</a>
    <a href="homework.php" class="active-nav">📚 Homework</a>
    <a href="logout.php" style="margin-top:50px; color:#ff4d4d;">🚪 Logout</a>
</div>

<!-- Main Content Area -->
<div class="main-content">
    
    <div class="card">
        <h2><i class="fa-solid fa-cloud-arrow-up text-primary me-2" style="color:var(--phonepe-purple);"></i> स्टडी मटेरियल अपलोड करें</h2>
        
        <?php echo $message; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <label>विषय / टॉपिक का नाम (Topic Title)</label>
            <input type="text" name="title" placeholder="उदा. Tally Prime Full Notes" required autocomplete="off">
            
            <label>PDF डॉक्यूमेंट चुनें (Select PDF File)</label>
            <input type="file" name="pdf_file" accept=".pdf" required>
            
            <button type="submit" name="upload" class="btn-upload">
                <i class="fa-solid fa-upload me-2"></i> अपलोड व पब्लिश करें
            </button>
        </form>
    </div>

    <!-- 🖥️ DESKTOP TABLE VIEW -->
    <h3 class="d-none d-lg-block" style="margin-top:40px; font-size:20px;">हाल ही में अपलोड किए गए नोट्स</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>File Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM homework ORDER BY $id_col DESC");
            $has_data = false;
            if($res && mysqli_num_rows($res) > 0) {
                $has_data = true;
                while($row = mysqli_fetch_assoc($res)){
                    echo "<tr>
                            <td><strong>{$row['title']}</strong></td>
                            <td>{$row['file_path']}</td>
                            <td>
                                <a href='uploads/{$row['file_path']}' target='_blank' class='btn-view'>👁️ View</a>
                                <a href='?del={$row[$id_col]}' class='btn-del' onclick=\"return confirm('क्या आप इस फ़ाइल को हटाना चाहते हैं?');\">🗑️ Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3' align='center'>कोई फ़ाइल उपलब्ध नहीं है।</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- 📱 MOBILE PHONEPE CARD LIST VIEW -->
    <h3 class="d-lg-none" style="margin-top:28px; font-size:19px; font-weight:800; color:#0f172a;">
        अपलोड की गई फ़ाइलें (Recently Uploaded)
    </h3>
    
    <div class="mobile-hw-list">
        <?php
        if($has_data) {
            mysqli_data_seek($res, 0); // Reset pointer
            while($row = mysqli_fetch_assoc($res)){
                ?>
                <div class="phonepe-hw-card">
                    <div class="phonepe-hw-top">
                        <div class="phonepe-pdf-icon">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div class="phonepe-hw-info">
                            <h4><?= htmlspecialchars($row['title']); ?></h4>
                            <p><i class="fa fa-paperclip me-1"></i> <?= htmlspecialchars($row['file_path']); ?></p>
                        </div>
                    </div>
                    <div class="phonepe-hw-actions">
                        <a href="uploads/<?= $row['file_path']; ?>" target="_blank" class="phonepe-action-btn phonepe-btn-view">
                            <i class="fa-solid fa-eye"></i> देखें (View)
                        </a>
                        <a href="?del=<?= $row[$id_col]; ?>" class="phonepe-action-btn phonepe-btn-del" onclick="return confirm('क्या आप इस फ़ाइल को हटाना चाहते हैं?');">
                            <i class="fa-solid fa-trash-can"></i> डिलीट
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div style='text-align:center; padding:30px 15px; background:#fff; border-radius:18px; border:1px solid #e2e8f0;'><p style='color:#64748b; font-size:16px; font-weight:700; margin:0;'>कोई फ़ाइल उपलब्ध नहीं है।</p></div>";
        }
        ?>
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
    <a href="homework.php" class="phonepe-nav-item active">
        <i class="fa fa-book-open"></i>
        <span>होमवर्क</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

</body>
</html>