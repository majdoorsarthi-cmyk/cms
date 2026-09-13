<?php
/**
 * CMS PRO - Student Document Wallet (My Documents)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php';

// Auth Check (छात्र लॉगिन जांचें)
if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$stu = $_SESSION['student'];
$student_id = $stu['user_id'] ?? ($stu['id'] ?? 0);

// 1. ऑटो टेबल क्रिएशन (Document Wallet Table)
$create_table_sql = "CREATE TABLE IF NOT EXISTS `student_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `doc_type` VARCHAR(100) NOT NULL,
  `doc_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `status` VARCHAR(20) DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($conn, $create_table_sql);

// अगर status कॉलम मौजूद न हो तो जोड़ें
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM `student_documents` LIKE 'status'");
if (mysqli_num_rows($check_col) == 0) {
    mysqli_query($conn, "ALTER TABLE `student_documents` ADD `status` VARCHAR(20) DEFAULT 'Pending' AFTER `file_path` drop");
}

// 2. डॉक्यूमेंट अपलोड लॉजिक (Upload Document)
$upload_msg = "";
if (isset($_POST['upload_doc'])) {
    $doc_type = mysqli_real_escape_string($conn, $_POST['doc_type']);
    $doc_name = mysqli_real_escape_string($conn, $_POST['doc_name']);

    if (isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] == 0) {
        $file_name = $_FILES['doc_file']['name'];
        $file_tmp  = $_FILES['doc_file']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($file_ext, $allowed_exts)) {
            $upload_msg = "<div class='alert-msg error'><i class='fa-solid fa-circle-xmark me-2'></i> सिर्फ PDF, JPG या PNG फाइल ही अपलोड करें।</div>";
        } else {
            if (!is_dir('uploads/documents')) {
                mkdir('uploads/documents', 0777, true);
            }

            $new_file_name = "DOC_" . $student_id . "_" . time() . "." . $file_ext;
            $target_path   = "uploads/documents/" . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $sql = "INSERT INTO student_documents (student_id, doc_type, doc_name, file_path, status) VALUES ('$student_id', '$doc_type', '$doc_name', '$new_file_name', 'Pending')";
                if (mysqli_query($conn, $sql)) {
                    $upload_msg = "<div class='alert-msg success'><i class='fa-solid fa-circle-check me-2'></i> दस्तावेज़ सफलतापूर्वक अपलोड हो गया! सत्यापन लंबित है।</div>";
                } else {
                    $upload_msg = "<div class='alert-msg error'><i class='fa-solid fa-triangle-exclamation me-2'></i> डेटाबेस त्रुटि!</div>";
                }
            } else {
                $upload_msg = "<div class='alert-msg error'><i class='fa-solid fa-triangle-exclamation me-2'></i> फाइल सेव नहीं हो सकी।</div>";
            }
        }
    } else {
        $upload_msg = "<div class='alert-msg error'><i class='fa-solid fa-triangle-exclamation me-2'></i> कृपया फाइल चुनें।</div>";
    }
}

// 3. छात्र के डॉक्यूमेंट्स फेच करें
$docs_query = mysqli_query($conn, "SELECT * FROM student_documents WHERE student_id = '$student_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Document Wallet | डिजिटल दस्तावेज़</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #2563eb;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            
            /* PhonePe App Theme Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-light: #f3e8ff;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg-color); color: var(--text-main); min-height: 100vh; }

        /* Alert Messages */
        .alert-msg { padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-weight: 700; font-size: 15px; }
        .success { background: #e6ffed; color: #15803d; border: 1px solid #bbf7d0; }
        .error { background: #ffdce0; color: #b91c1c; border: 1px solid #fecaca; }

        /* Status Badges */
        .badge { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 800; display: inline-block; }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-approved { background: #dcfce7; color: #15803d; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }

        /* 🖥️ DESKTOP LAYOUT WITH SIDEBAR */
        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Desktop Sidebar Styling */
        .desktop-sidebar {
            width: 260px;
            background: #0f172a;
            color: #ffffff;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            padding-bottom: 20px;
            border-bottom: 1px solid #1e293b;
            margin-bottom: 20px;
        }

        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #2563eb;
            color: #ffffff;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .wallet-header-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 28px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .wallet-header-card h2 { font-size: 24px; font-weight: 800; }
        .wallet-header-card p { font-size: 14px; color: #94a3b8; margin-top: 4px; }

        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .doc-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .doc-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }

        .doc-card-top { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .doc-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: #eff6ff;
            color: #2563eb;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .doc-info h4 { font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .doc-info span { font-size: 12px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; }

        .doc-actions {
            display: flex; gap: 10px; margin-top: 15px; border-top: 1px dashed #e2e8f0; padding-top: 12px;
        }

        .btn-doc {
            flex: 1; text-align: center; padding: 10px; border-radius: 10px;
            text-decoration: none; font-size: 14px; font-weight: 700; display: flex;
            align-items: center; justify-content: center; gap: 6px;
        }
        .btn-view { background: #eff6ff; color: #2563eb; }
        .btn-download { background: #f1f5f9; color: #334155; }

        .upload-section {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }

        .upload-section h3 { font-size: 18px; font-weight: 800; margin-bottom: 16px; color: #0f172a; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 12px 16px; font-size: 15px; border-radius: 12px;
            border: 1.5px solid #cbd5e1; outline: none; background: #f8fafc;
        }

        .btn-submit {
            background: var(--primary); color: #fff; border: none; padding: 14px 24px;
            font-size: 16px; font-weight: 800; border-radius: 12px; cursor: pointer; width: 100%;
        }

        .mobile-header, .mobile-bottom-nav { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Native App Feel & Extra Large Text) */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; }

            /* Desktop Sidebar Hide on Mobile */
            .desktop-sidebar { display: none !important; }

            .main-content { padding: 82px 14px 90px 14px !important; }

            /* Top PhonePe Purple Gradient Header */
            .mobile-header {
                display: flex !important;
                position: fixed; top: 0; left: 0; right: 0; height: 68px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }

            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn {
                color: #ffffff; font-size: 20px; text-decoration: none; width: 42px; height: 42px;
                background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 21px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 14px !important; display: block; font-weight: 500; }

            .wallet-header-card { display: none !important; }

            /* Form Box with Bigger Labels and Inputs */
            .upload-section {
                border-radius: 22px !important; padding: 22px 18px !important;
                border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
            }
            .upload-section h3 { font-size: 21px !important; font-weight: 800; color: #0f172a; margin-bottom: 18px; }

            .form-group label { font-size: 17.5px !important; font-weight: 800 !important; color: #1e293b !important; margin-bottom: 8px; }
            .form-control {
                padding: 16px 18px !important; font-size: 18px !important; font-weight: 700 !important;
                border-radius: 14px !important; border: 2px solid #cbd5e1 !important; color: #0f172a !important;
            }

            .btn-submit {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                padding: 18px !important; font-size: 18.5px !important; font-weight: 800 !important;
                border-radius: 16px !important; box-shadow: 0 6px 20px rgba(95, 37, 159, 0.35) !important;
            }

            /* Document Cards PhonePe UI with Bigger Text */
            .doc-card {
                border-radius: 20px !important; padding: 20px 18px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important; margin-bottom: 16px;
            }

            .doc-icon {
                width: 58px !important; height: 58px !important; border-radius: 16px !important;
                background: var(--phonepe-light) !important; color: var(--phonepe-purple) !important;
                font-size: 28px !important;
            }

            .doc-info h4 { font-size: 20px !important; font-weight: 800; color: #0f172a; }
            .doc-info span { font-size: 14.5px !important; font-weight: 800; color: var(--phonepe-purple); background: var(--phonepe-light); }

            .badge { font-size: 14px !important; font-weight: 800 !important; padding: 7px 16px !important; }

            .btn-doc { padding: 15px !important; font-size: 16.5px !important; font-weight: 800 !important; border-radius: 12px !important; }
            .btn-view { background: #eff6ff !important; color: #2563eb !important; }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 70px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
            }

            .phonepe-nav-item {
                display: flex; flex-direction: column; align-items: center; justify-content: center;
                text-decoration: none; color: #64748b; font-size: 13px !important; font-weight: 700; width: 25%;
            }

            .phonepe-nav-item i { font-size: 22px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body>

<!-- Mobile PhonePe Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="javascript:history.back()" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>डिजिटल डॉक्यूमेंट वॉलेट</h3>
            <small>Document Wallet & Certificates</small>
        </div>
    </div>
    <div style="color:#fff; font-size:24px;">
        <i class="fa-solid fa-id-card"></i>
    </div>
</div>

<div class="app-layout">

    <!-- 🖥️ DESKTOP SIDEBAR -->
    <div class="desktop-sidebar">
        <div>
            <div class="sidebar-brand">
                <i class="fa-solid fa-graduation-cap" style="color:#3b82f6;"></i>
                <span>स्टूडेंट पोर्टल</span>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="student_dashboard.php"><i class="fa-solid fa-house"></i> <span>डैशबोर्ड</span></a>
                </li>
                <li>
                    <a href="video_lectures.php"><i class="fa-solid fa-video"></i> <span>वीडियो लेक्चर्स</span></a>
                </li>
                <li>
                    <a href="my_docs.php" class="active"><i class="fa-solid fa-folder-closed"></i> <span>माई डॉक्यूमेंट्स</span></a>
                </li>
                <li>
                    <a href="profile.php"><i class="fa-solid fa-user"></i> <span>प्रोफाइल</span></a>
                </li>
            </ul>
        </div>
        <div>
            <ul class="sidebar-menu">
                <li>
                    <a href="logout.php" style="color:#ef4444;"><i class="fa-solid fa-power-off"></i> <span>लॉगआउट</span></a>
                </li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">

        <!-- Desktop Header Card -->
        <div class="wallet-header-card">
            <div>
                <h2><i class="fa-solid fa-wallet me-2"></i> स्टूडेंट डॉक्यूमेंट वॉलेट</h2>
                <p>अपने सभी महत्वपूर्ण दस्तावेज़ सुरक्षित रखें और कभी भी डाउनलोड करें।</p>
            </div>
            <div style="font-size: 40px; color: rgba(255,255,255,0.2);">
                <i class="fa-solid fa-folder-closed"></i>
            </div>
        </div>

        <?= $upload_msg; ?>

        <!-- Upload Document Form -->
        <div class="upload-section">
            <h3><i class="fa-solid fa-cloud-arrow-up me-2" style="color:var(--phonepe-purple);"></i> नया दस्तावेज़ अपलोड करें</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>दस्तावेज़ की श्रेणी (Category)</label>
                    <select name="doc_type" class="form-control" required>
                        <option value="Aadhar Card">आधार कार्ड (Aadhar Card)</option>
                        <option value="Marksheet">10th / 12th मार्कशीट</option>
                        <option value="ID Card">स्टूडेंट आईडी कार्ड</option>
                        <option value="Certificate">कोर्स सर्टिफिकेट</option>
                        <option value="Other">अन्य डॉक्यूमेंट</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>दस्तावेज़ का नाम (Document Title)</label>
                    <input type="text" name="doc_name" class="form-control" placeholder="उदा. My Aadhar Card" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label>फाइल चुनें (PDF, JPG, PNG)</label>
                    <input type="file" name="doc_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <button type="submit" name="upload_doc" class="btn-submit">
                    <i class="fa-solid fa-upload me-2"></i> वॉलेट में सुरक्षित करें
                </button>
            </form>
        </div>

        <!-- Uploaded Documents List -->
        <h3 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
            <i class="fa-solid fa-folder-open me-2" style="color:var(--phonepe-purple);"></i> आपके सुरक्षित दस्तावेज़
        </h3>

        <div class="docs-grid">
            <?php
            if ($docs_query && mysqli_num_rows($docs_query) > 0) {
                while ($row = mysqli_fetch_assoc($docs_query)) {
                    $ext = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                    $icon_class = ($ext == 'pdf') ? 'fa-file-pdf' : 'fa-file-image';
                    $file_url = "uploads/documents/" . $row['file_path'];
                    $st = $row['status'] ?? 'Pending';
                    $badge_cls = ($st == 'Approved') ? 'badge-approved' : (($st == 'Rejected') ? 'badge-rejected' : 'badge-pending');
                    ?>
                    <div class="doc-card">
                        <div>
                            <div class="doc-card-top">
                                <div class="doc-icon">
                                    <i class="fa-solid <?= $icon_class; ?>"></i>
                                </div>
                                <div class="doc-info" style="flex:1;">
                                    <h4><?= htmlspecialchars($row['doc_name']); ?></h4>
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                                        <span><?= htmlspecialchars($row['doc_type']); ?></span>
                                        <span class="badge <?= $badge_cls; ?>"><?= $st; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="doc-actions">
                            <a href="<?= $file_url; ?>" target="_blank" class="btn-doc btn-view">
                                <i class="fa-solid fa-eye"></i> देखें
                            </a>
                            <a href="<?= $file_url; ?>" download class="btn-doc btn-download">
                                <i class="fa-solid fa-download"></i> डाउनलोड
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div style='grid-column: 1/-1; text-align:center; padding:40px; background:#fff; border-radius:18px; border:1px solid #e2e8f0;'>
                        <i class='fa-solid fa-folder-minus fa-3x' style='color:#cbd5e1; margin-bottom:12px;'></i>
                        <p style='color:#64748b; font-size:17px; font-weight:700;'>अभी वॉलेट में कोई दस्तावेज़ उपलब्ध नहीं है।</p>
                      </div>";
            }
            ?>
        </div>

    </div>

</div>

<!-- Mobile PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="student_dashboard.php" class="phonepe-nav-item">
        <i class="fa-solid fa-house"></i>
        <span>होम</span>
    </a>
    <a href="video_lectures.php" class="phonepe-nav-item">
        <i class="fa-solid fa-video"></i>
        <span>वीडियो</span>
    </a>
    <a href="my_docs.php" class="phonepe-nav-item active">
        <i class="fa-solid fa-id-card"></i>
        <span>वॉलेट</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa-solid fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

</body>
</html>