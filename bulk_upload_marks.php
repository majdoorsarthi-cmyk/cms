<?php
include 'db_config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 🟢 FIX 1: Flexible Admin Auth Check (Prevents Page Redirect Loop)
if (!isset($_SESSION['admin']) && !isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Auto Create Marks Table if missing
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_name VARCHAR(150) NOT NULL,
    total_marks INT NOT NULL DEFAULT 100,
    obtained_marks INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ==========================================
// 🚀 PURE AJAX API HANDLER (ZERO PAGE RELOAD)
// ==========================================
if (isset($_POST['action']) && $_POST['action'] == 'upload_csv') {
    header('Content-Type: application/json');
    
    if (!isset($_FILES["marks_file"]) || $_FILES["marks_file"]["error"] != 0) {
        echo json_encode(['status' => 'error', 'message' => '⚠️ Kripya ek valid CSV file chunein!']);
        exit();
    }

    $filename = $_FILES["marks_file"]["tmp_name"];
    if ($_FILES["marks_file"]["size"] > 0) {
        $file = fopen($filename, "r");
        
        // Skip Header Row
        fgetcsv($file);

        $success_count = 0;
        $error_count = 0;

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            if(empty($column[0])) continue;

            $roll_no  = mysqli_real_escape_string($conn, trim($column[0]));
            $subject  = mysqli_real_escape_string($conn, trim($column[1]));
            $total    = (int)trim($column[2]);
            $obtained = (int)trim($column[3]);

            // Roll No se Student ID nikalein
            $res = mysqli_query($conn, "SELECT id FROM students WHERE roll_no = '$roll_no' LIMIT 1");
            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $s_id = $row['id'];

                // Insert Record
                $insert = mysqli_query($conn, "INSERT INTO marks (student_id, subject_name, total_marks, obtained_marks) 
                                             VALUES ('$s_id', '$subject', '$total', '$obtained')");
                if ($insert) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            } else {
                $error_count++;
            }
        }
        fclose($file);

        echo json_encode([
            'status' => 'success',
            'message' => "🎉 Upload Complete! Success: $success_count | Fail: $error_count",
            'success' => $success_count,
            'errors' => $error_count
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Chunii gayi CSV file khali (empty) hai!']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bulk Upload Marks | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --pp-purple: #5f259f;
            --pp-dark: #3d1668;
            --pp-bg: #f3f4f9;
            --text-main: #0f172a;
        }
        
        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body { 
            background-color: var(--pp-bg); 
            min-height: 100vh;
        }

        /* PhonePe Toast Alert Popup */
        .pp-toast {
            position: fixed;
            top: -90px;
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: white;
            padding: 16px 28px;
            border-radius: 40px;
            font-weight: 800;
            font-size: 16px;
            z-index: 999999;
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            gap: 12px;
            width: 90%;
            max-width: 420px;
            justify-content: center;
        }
        .pp-toast.show { top: 25px; }

        /* 🖥️ DESKTOP VIEW (>= 992px) - CLEAN & PROFESSIONAL */
        @media (min-width: 992px) {
            .mobile-only { display: none !important; }
            .navbar { background: white !important; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
            .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: none; }
            .card-header { background: var(--pp-purple); color: white; border-radius: 20px 20px 0 0 !important; font-weight: 700; padding: 20px; font-size: 18px; }
            .upload-zone { border: 2px dashed #cbd5e1; border-radius: 16px; padding: 35px; text-align: center; background: #f8fafc; cursor: pointer; }
            .btn-save { background: var(--pp-purple); color: white; border-radius: 12px; padding: 14px; font-weight: 700; width: 100%; border: none; font-size: 16px; }
        }

        /* 📱 MOBILE PHONEPE NATIVE INTERFACE (< 992px) - BOLD FONTS */
        @media (max-width: 991px) {
            .desktop-only { display: none !important; }

            /* Top PhonePe Header Bar */
            .pp-header {
                position: fixed; top: 0; left: 0; right: 0; height: 72px;
                background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%);
                z-index: 9999; padding: 0 18px; display: flex; align-items: center;
                justify-content: space-between; box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }
            .pp-profile { display: flex; align-items: center; gap: 14px; }
            .pp-avatar {
                width: 48px; height: 48px; border-radius: 50%; background: #fff;
                color: var(--pp-purple); font-weight: 900; font-size: 22px;
                display: flex; align-items: center; justify-content: center;
                border: 2px solid rgba(255,255,255,0.9);
            }
            .pp-title { color: white; margin: 0; font-size: 20px; font-weight: 900; }
            .pp-subtitle { color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 700; }

            .app-wrapper { padding: 90px 14px 85px 14px !important; }

            /* BOLD MOBILE FONTS & PHONEPE CARD UI */
            .form-label {
                font-size: 17px !important; font-weight: 800 !important;
                color: #0f172a !important; margin-bottom: 8px !important;
            }
            .upload-zone {
                border: 2px dashed var(--pp-purple); border-radius: 20px;
                padding: 30px 15px; text-align: center; background: #ffffff;
                box-shadow: 0 4px 18px rgba(0,0,0,0.04);
            }
            .upload-zone i { font-size: 45px; color: var(--pp-purple); margin-bottom: 12px; }
            .upload-zone p { font-size: 16px; font-weight: 800; color: #334155; margin: 0; }
            
            .btn-save {
                background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%) !important;
                border-radius: 18px !important; padding: 18px !important;
                font-size: 19px !important; font-weight: 900 !important;
                color: white !important; border: none !important; width: 100%;
                box-shadow: 0 8px 22px rgba(95, 37, 159, 0.35) !important;
            }

            /* Sidebar Drawer Overlay */
            .sidebar-overlay {
                display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7);
                backdrop-filter: blur(5px); z-index: 99998; transition: 0.3s;
            }
            .sidebar-overlay.active { display: block !important; }
            .sidebar-drawer {
                position: fixed; top: 0; bottom: 0; left: -100%; width: 82vw; max-width: 320px;
                background: #0f172a; z-index: 99999; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                padding: 24px 18px; box-shadow: 10px 0 30px rgba(0,0,0,0.5);
            }
            .sidebar-drawer.active { left: 0; }
            .drawer-link {
                display: flex; align-items: center; gap: 14px; color: #cbd5e1;
                padding: 16px; border-radius: 14px; font-size: 17px; font-weight: 800;
                text-decoration: none; margin-bottom: 8px;
            }
            .drawer-link.active { background: rgba(95, 37, 159, 0.45); color: #c084fc; }

            /* Fixed Bottom Navigation */
            .pp-bottom-nav {
                position: fixed; bottom: 0; left: 0; right: 0; height: 68px;
                background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                display: flex; justify-content: space-around; align-items: center;
                box-shadow: 0 -4px 25px rgba(0,0,0,0.08);
            }
            .pp-nav-item {
                display: flex; flex-direction: column; align-items: center; text-decoration: none;
                color: #64748b; font-size: 12px; font-weight: 800; width: 25%;
            }
            .pp-nav-item i { font-size: 22px; margin-bottom: 3px; }
            .pp-nav-item.active { color: var(--pp-purple); }
        }
    </style>
</head>
<body>

<!-- PhonePe Notification Toast -->
<div id="ppToast" class="pp-toast">
    <i class="fas fa-check-circle text-success fs-4"></i> 
    <span id="ppToastMsg">Notification</span>
</div>

<!-- Mobile PhonePe Header -->
<div class="pp-header mobile-only">
    <div class="pp-profile">
        <div class="pp-avatar">B</div>
        <div>
            <h3 class="pp-title">BULK UPLOAD</h3>
            <span class="pp-subtitle">Admin Super-App Mode</span>
        </div>
    </div>
    <button type="button" class="btn text-white p-0 fs-3" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
</div>

<!-- Drawer Overlay -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- Mobile Side Drawer Navigation -->
<div class="sidebar-drawer" id="sidebar">
    <div class="d-flex justify-content-between align-items-center mb-4 text-white">
        <h3 class="fw-bold m-0 text-white"><i class="fa fa-shield-halved text-purple me-2"></i>CMS ADMIN</h3>
        <button class="btn text-white p-0 fs-4" onclick="toggleSidebar()"><i class="fa fa-times"></i></button>
    </div>
    <a href="admin_dashboard.php" class="drawer-link"><i class="fa fa-gauge"></i> Dashboard</a>
    <a href="manage_students.php" class="drawer-link"><i class="fa fa-users"></i> Manage Students</a>
    <!-- 🟢 Manage Courses Link -->
    <a href="manage_courses.php" class="drawer-link"><i class="fa fa-graduation-cap"></i> Manage Courses</a>
    <a href="add_marks.php" class="drawer-link"><i class="fa fa-pen-nib"></i> Add Marks</a>
    <a href="bulk_upload_marks.php" class="drawer-link active"><i class="fa fa-file-csv"></i> Bulk CSV Upload</a>
    <a href="logout.php" class="drawer-link text-danger mt-4"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Desktop Header -->
<nav class="navbar navbar-expand-lg mb-4 desktop-only">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="admin_dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Admin Panel
        </a>
    </div>
</nav>

<div class="container app-wrapper mb-5">
    <div class="row justify-content-center">
        
        <!-- MAIN UPLOAD CARD -->
        <div class="col-lg-7 mb-4">
            <div class="card main-card">
                <div class="card-header desktop-only"><i class="fas fa-file-csv me-2"></i> Bulk Marks Upload (CSV)</div>
                <div class="card-body p-3 p-lg-4">

                    <!-- Info Box -->
                    <div class="alert alert-primary border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle fs-3 text-primary"></i>
                        <div>
                            <strong style="font-size: 15px;">CSV File Format Guide:</strong><br>
                            <span style="font-size: 13px;" class="fw-semibold text-secondary">Columns: <code>roll_no, subject_name, total_marks, obtained_marks</code></span>
                        </div>
                    </div>

                    <!-- 🚀 Zero Reload AJAX Form -->
                    <form id="csvUploadForm">
                        <div class="mb-4">
                            <label class="form-label">Select CSV File</label>
                            <div class="upload-zone" onclick="document.getElementById('csvFileInput').click()">
                                <i class="fas fa-cloud-arrow-up"></i>
                                <p id="fileNameDisplay">Click or Drag CSV File Here</p>
                                <small class="text-muted fw-bold mt-1 d-block">Only .csv files are supported</small>
                                <input type="file" name="marks_file" id="csvFileInput" accept=".csv" class="d-none" onchange="updateFileName(this)" required>
                            </div>
                        </div>

                        <button type="submit" id="uploadBtn" class="btn-save mt-2">
                            <i class="fas fa-file-import me-2"></i> UPLOAD & PROCESS NOW
                        </button>
                    </form>

                    <!-- Sample Download Link -->
                    <div class="text-center mt-4">
                        <a href="javascript:void(0)" onclick="downloadSampleCSV()" class="fw-extrabold text-decoration-none" style="color: var(--pp-purple); font-size: 16px;">
                            <i class="fas fa-download me-1"></i> Download Sample CSV Format
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="pp-bottom-nav mobile-only">
    <a href="admin_dashboard.php" class="pp-nav-item"><i class="fa fa-home"></i><span>Home</span></a>
    <a href="manage_students.php" class="pp-nav-item"><i class="fa fa-user-graduate"></i><span>Students</span></a>
    <a href="add_marks.php" class="pp-nav-item"><i class="fa fa-pen-nib"></i><span>Marks</span></a>
    <a href="javascript:void(0)" class="pp-nav-item active" onclick="toggleSidebar()"><i class="fa fa-bars"></i><span>Menu</span></a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toast Alert Notifier
function showToast(msg) {
    const toast = document.getElementById('ppToast');
    document.getElementById('ppToastMsg').innerText = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// Display File Name after selection
function updateFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameDisplay').innerText = "📄 " + input.files[0].name;
    }
}

// 100% PURE AJAX UPLOAD (ZERO PAGE RELOAD)
document.getElementById('csvUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('uploadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> PROCESSING CSV...';

    const formData = new FormData(this);
    formData.append('action', 'upload_csv');

    fetch('bulk_upload_marks.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        showToast(res.message);
        if(res.status === 'success') {
            document.getElementById('csvUploadForm').reset();
            document.getElementById('fileNameDisplay').innerText = "Click or Drag CSV File Here";
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-import me-2"></i> UPLOAD & PROCESS NOW';
    })
    .catch(() => {
        showToast("❌ File Upload Fail Ho Gaya!");
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-import me-2"></i> UPLOAD & PROCESS NOW';
    });
});

// Download Sample CSV JS Logic
function downloadSampleCSV() {
    let csvContent = "data:text/csv;charset=utf-8,roll_no,subject_name,total_marks,obtained_marks\n2026001,Mathematics,100,85\n2026001,Science,100,78\n2026002,Mathematics,100,92";
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "sample_marks.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Sidebar Drawer Switcher
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}
</script>
</body>
</html>