<?php
/**
 * CMS PRO - Advanced Document Wallet System
 */

// DB Configuration file include karein
include 'db_config.php';

// Auth Check (Student logged in hai ya nahi)
if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$stu = $_SESSION['student'];
$student_id = $stu['user_id'] ?? ($stu['id'] ?? null);

// 1. Auto Create Document Table if not exists
$create_table_sql = "CREATE TABLE IF NOT EXISTS `document_wallet` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `document_type` VARCHAR(100) NOT NULL,
  `document_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `file_size` VARCHAR(50) NOT NULL,
  `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($conn, $create_table_sql);

// Directory path for uploads
$upload_dir = "uploads/documents/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$message = '';
$message_type = '';

// 2. Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_doc'])) {
    $doc_type = mysqli_real_escape_string($conn, $_POST['document_type']);
    $doc_name = mysqli_real_escape_string($conn, $_POST['document_name']);
    
    if (isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] === 0) {
        $file_name = $_FILES['doc_file']['name'];
        $file_tmp  = $_FILES['doc_file']['tmp_name'];
        $file_size = $_FILES['doc_file']['size'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        
        // Validation (Max 5MB)
        if (!in_array($ext, $allowed)) {
            $message = "केवल JPG, PNG या PDF फाइल अपलोड करें!";
            $message_type = "danger";
        } elseif ($file_size > 5242880) {
            $message = "फाइल का साइज़ 5MB से कम होना चाहिए!";
            $message_type = "danger";
        } else {
            // Unique File Naming
            $new_file_name = "DOC_" . $student_id . "_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $target_path   = $upload_dir . $new_file_name;
            
            // Format size for display
            $formatted_size = round($file_size / 1024, 2) . ' KB';
            if ($file_size > 1048576) {
                $formatted_size = round($file_size / 1048576, 2) . ' MB';
            }

            if (move_uploaded_file($file_tmp, $target_path)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO document_wallet (student_id, document_type, document_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "isssss", $student_id, $doc_type, $doc_name, $target_path, $ext, $formatted_size);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "दस्तावेज़ सफलतापूर्वक अपलोड हो गया!";
                    $message_type = "success";
                } else {
                    $message = "डेटाबेस एरर! पुनः प्रयास करें।";
                    $message_type = "danger";
                }
            } else {
                $message = "फ़ाइल अपलोड करने में समस्या आई।";
                $message_type = "danger";
            }
        }
    } else {
        $message = "कृपया एक फ़ाइल चुनें!";
        $message_type = "danger";
    }
}

// 3. Handle File Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Fetch File to remove physically
    $stmt = mysqli_prepare($conn, "SELECT file_path FROM document_wallet WHERE id = ? AND student_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $delete_id, $student_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($res)) {
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
        
        $del_stmt = mysqli_prepare($conn, "DELETE FROM document_wallet WHERE id = ? AND student_id = ?");
        mysqli_stmt_bind_param($del_stmt, "ii", $delete_id, $student_id);
        mysqli_stmt_execute($del_stmt);
        
        $message = "दस्तावेज़ डिलीट कर दिया गया है!";
        $message_type = "warning";
    }
}

// 4. Fetch All Documents
$stmt = mysqli_prepare($conn, "SELECT * FROM document_wallet WHERE student_id = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$documents = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>TC ACADEMY - डॉक्यूमेंट वॉलेट</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Hind:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb; --sidebar-bg: #0f172a;
            --nav-text: #94a3b8; --bg: #f8fafc; --card-bg: #ffffff;
            --text-main: #0f172a; --text-sub: #64748b;
            --app-header-height: 60px; --bottom-nav-height: 75px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Hind', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; padding-bottom: calc(var(--bottom-nav-height) + 20px); }

        .sidebar {
            width: 280px; background: var(--sidebar-bg); height: 100vh;
            position: fixed; top: 0; left: 0; padding: 20px 16px; color: white;
            z-index: 10050; transition: transform 0.3s ease; box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .sidebar-brand { font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .close-btn { font-size: 20px; cursor: pointer; color: #94a3b8; display: none; }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin: 16px 0 8px 8px; }
        .sidebar-menu a { color: var(--nav-text); text-decoration: none; padding: 12px 14px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 15px; margin-bottom: 6px; font-weight: 600; }
        .sidebar-menu a.active { background: rgba(37, 99, 235, 0.15); color: #60a5fa; border-left: 4px solid #3b82f6; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); z-index: 10040; backdrop-filter: blur(4px); }
        .app-header { display: none; position: fixed; top: 0; left: 0; right: 0; height: var(--app-header-height); background: #ffffff; align-items: center; justify-content: space-between; padding: 0 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); z-index: 1000; }
        .app-header-title { font-weight: 800; font-size: 18px; display: flex; align-items: center; gap: 12px; }

        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 24px; transition: all 0.3s ease; }
        
        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        .grid-container { display: grid; grid-template-columns: 320px 1fr; gap: 24px; }
        
        .card { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .card-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: var(--primary); }

        .btn-upload { width: 100%; background: var(--primary); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
        
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .doc-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; position: relative; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; justify-content: space-between; }
        .doc-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        .doc-icon { width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
        .doc-title { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .doc-type { font-size: 11px; background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 6px; font-weight: 700; display: inline-block; margin-bottom: 10px; }
        .doc-meta { font-size: 11px; color: #94a3b8; margin-bottom: 14px; }
        
        .badge { font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 12px; text-transform: uppercase; float: right; }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-verified { background: #dcfce7; color: #16a34a; }

        .doc-actions { display: flex; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 12px; }
        .btn-action { flex: 1; padding: 8px; border-radius: 8px; text-align: center; text-decoration: none; font-size: 12px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .btn-view { background: #f1f5f9; color: #334155; }
        .btn-delete { background: #fee2e2; color: #ef4444; }

        .mobile-bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; height: var(--bottom-nav-height); background: #ffffff; box-shadow: 0 -5px 25px rgba(0,0,0,0.08); z-index: 1000; justify-content: space-around; align-items: center; border-top: 1px solid #e2e8f0; }
        .mobile-bottom-nav a { color: #64748b; text-decoration: none; display: flex; flex-direction: column; align-items: center; font-size: 11px; font-weight: 800; gap: 4px; width: 25%; }
        .mobile-bottom-nav a.active { color: var(--primary); }
        .mobile-bottom-nav a i { font-size: 22px; }
        .big-home-nav-btn { background: #2563eb; color: #ffffff !important; border-radius: 16px; padding: 8px 16px; font-size: 12px !important; margin-top: -15px; box-shadow: 0 6px 15px rgba(37, 99, 235, 0.35); border: 3px solid #fff; }

        @media (max-width: 992px) {
            .grid-container { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .app-header { display: flex; } .mobile-bottom-nav { display: flex; } .close-btn { display: block; }
            .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; padding: calc(var(--app-header-height) + 16px) 16px calc(var(--bottom-nav-height) + 20px) 16px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- MOBILE HEADER -->
<div class="app-header">
    <div class="app-header-title">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="font-size: 22px; cursor: pointer; color: var(--primary);"></i>
        <span>TC ACADEMY</span>
    </div>
    <a href="student_dashboard.php" style="font-size: 20px; color: var(--primary); text-decoration:none;"><i class="fas fa-home"></i></a>
</div>

<!-- SIDEBAR MAIN MENU -->
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div style="display:flex; align-items:center; gap:10px;"><i class="fas fa-graduation-cap"></i> <span>TC ACADEMY</span></div>
        <i class="fas fa-times close-btn" onclick="toggleSidebar()"></i>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">मुख्य मेनू</div>
        <a href="student_dashboard.php"><i class="fas fa-home" style="color:#60a5fa;"></i> <span>होम (Dashboard)</span></a>

        <div class="menu-label">एकेडमिक्स & LMS</div>
        <a href="lms_access.php"><i class="fas fa-laptop-code" style="color:#38bdf8;"></i> <span>LMS Access</span></a>
        <a href="video_lectures.php"><i class="fas fa-video" style="color:#a7f3d0;"></i> <span>वीडियो लेक्चर्स</span></a>
        <a href="document_wallet.php" class="active"><i class="fas fa-wallet" style="color:#fde047;"></i> <span>डॉक्यूमेंट वॉलेट</span></a>

        <div class="menu-label">हाजिरी सिस्टम</div>
        <a href="attendance.php"><i class="fas fa-fingerprint" style="color:#60a5fa;"></i> <span>आज की हाजिरी</span></a>
        <a href="attendance_history.php"><i class="fas fa-history" style="color:#cbd5e1;"></i> <span>हाजिरी का इतिहास</span></a>

        <div class="menu-label">अकाउंट</div>
        <a href="profile_settings.php"><i class="fas fa-user-cog" style="color:#94a3b8;"></i> <span>प्रोफाइल सेटिंग्स</span></a>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>लॉगआउट</span></a>
    </div>
</div>

<!-- MAIN CONTENT CONTAINER -->
<div class="main-content">
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?>">
            <i class="fas fa-info-circle"></i> <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="grid-container">
        
        <!-- LEFT: UPLOAD FORM CARD -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-cloud-upload-alt" style="color: var(--primary);"></i>
                <span>नया डॉक्यूमेंट जोड़ें</span>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>दस्तावेज़ की श्रेणी (Category)</label>
                    <select name="document_type" class="form-control" required>
                        <option value="Aadhaar Card">आधार कार्ड (Aadhaar)</option>
                        <option value="Marks Sheet">मार्क्स शीट (10th/12th)</option>
                        <option value="Identity Card">आईडी कार्ड (Student ID)</option>
                        <option value="Signature">हस्ताक्षर (Signature)</option>
                        <option value="Income Certificate">आय प्रमाण पत्र</option>
                        <option value="Caste Certificate">जाति प्रमाण पत्र</option>
                        <option value="Other">अन्य (Other Document)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>फ़ाइल का नाम (Document Title)</label>
                    <input type="text" name="document_name" class="form-control" placeholder="उदा. 10th Marksheet Org" required>
                </div>

                <div class="form-group">
                    <label>फ़ाइल चुनें (File)</label>
                    <input type="file" name="doc_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <span style="font-size:11px; color:#94a3b8; display:block; margin-top:4px;">फॉर्मेट: JPG, PNG, PDF (मैक्स 5MB)</span>
                </div>

                <button type="submit" name="upload_doc" class="btn-upload">
                    <i class="fas fa-upload"></i> सुरक्षित सेव करें
                </button>
            </form>
        </div>

        <!-- RIGHT: WALLET DOCUMENTS GRID -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-folder-open" style="color: var(--primary);"></i>
                <span>आपके सुरक्षित दस्तावेज़</span>
            </div>

            <?php if (mysqli_num_rows($documents) > 0): ?>
                <div class="doc-grid">
                    <?php while ($doc = mysqli_fetch_assoc($documents)): ?>
                        <div class="doc-card">
                            <div>
                                <span class="badge badge-<?= strtolower($doc['status']) ?>">
                                    <?= $doc['status'] == 'Verified' ? 'सत्यापित' : 'लंबित' ?>
                                </span>
                                
                                <div class="doc-icon">
                                    <?php if ($doc['file_type'] == 'pdf'): ?>
                                        <i class="fas fa-file-pdf" style="color: #ef4444;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-file-image" style="color: #3b82f6;"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="doc-title" title="<?= htmlspecialchars($doc['document_name']) ?>">
                                    <?= htmlspecialchars($doc['document_name']) ?>
                                </div>
                                <span class="doc-type"><?= htmlspecialchars($doc['document_type']) ?></span>
                                
                                <div class="doc-meta">
                                    <div><i class="far fa-hdd"></i> <?= $doc['file_size'] ?></div>
                                    <div><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($doc['upload_date'])) ?></div>
                                </div>
                            </div>

                            <div class="doc-actions">
                                <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> देखें
                                </a>
                                <a href="?delete_id=<?= $doc['id'] ?>" onclick="return confirm('क्या आप इस दस्तावेज़ को हटाना चाहते हैं?');" class="btn-action btn-delete">
                                    <i class="fas fa-trash-alt"></i> डिलीट
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center; padding: 40px 10px; color:#94a3b8;">
                    <i class="fas fa-wallet fa-3x" style="margin-bottom: 12px; opacity:0.5;"></i>
                    <p style="font-size:14px;">कोई दस्तावेज़ वॉलेट में अपलोड नहीं किया गया है।</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- BOTTOM MOBILE NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="attendance.php"><i class="fas fa-fingerprint"></i><span>हाजिरी</span></a>
    <a href="student_dashboard.php" class="big-home-nav-btn"><i class="fas fa-home" style="font-size:24px;"></i><span>HOME</span></a>
    <a href="document_wallet.php" class="active"><i class="fas fa-wallet"></i><span>वॉलेट</span></a>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebarNav').classList.toggle('active');
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }
</script>

</body>
</html>