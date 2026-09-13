<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_config.php'; 

// Admin Check
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

// --- 1. DELETE LOGIC ---
if(isset($_GET['delete'])){
    $u_id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    $photo_query = mysqli_query($conn, "SELECT photo FROM students WHERE user_id = '$u_id'");
    $photo_data = mysqli_fetch_assoc($photo_query);
    if($photo_data && $photo_data['photo'] != 'default.png'){
        @unlink("uploads/profile/" . $photo_data['photo']);
    }

    $res = mysqli_query($conn, "SELECT id FROM students WHERE user_id = '$u_id'");
    if($row = mysqli_fetch_assoc($res)){
        $s_id = $row['id'];
        mysqli_query($conn, "DELETE FROM fees WHERE student_id = '$s_id'");
    }
    mysqli_query($conn, "DELETE FROM students WHERE user_id = '$u_id'");
    mysqli_query($conn, "DELETE FROM users WHERE id = '$u_id'");
    
    header("Location: manage_students.php?msg=deleted");
    exit();
}

// --- 2. STATUS TOGGLE ---
if(isset($_GET['toggle_status'])){
    $id = mysqli_real_escape_string($conn, $_GET['toggle_status']);
    $st = mysqli_real_escape_string($conn, $_GET['current']);
    $new_st = ($st == 'Active') ? 'Suspended' : 'Active';
    mysqli_query($conn, "UPDATE users SET status = '$new_st' WHERE id = '$id'");
    header("Location: manage_students.php?msg=updated");
    exit();
}

// --- 3. FETCH SEARCH DATA (Added batch_start_time, batch_end_time, total_fee) ---
$search = mysqli_real_escape_string($conn, $_REQUEST['search'] ?? "");
$query = "SELECT u.id as user_id, u.name as u_name, u.email, u.status, u.mobile, 
                 s.roll_no, s.course, s.photo, s.father_name, s.is_verified,
                 s.batch_start_time, s.batch_end_time, s.total_fee
          FROM users u 
          INNER JOIN students s ON u.id = s.user_id 
          WHERE u.role = 'student' 
          AND (u.name LIKE '%$search%' OR s.roll_no LIKE '%$search%' OR u.mobile LIKE '%$search%')
          ORDER BY s.id DESC";
$result = mysqli_query($conn, $query);

$students_data = [];
if(mysqli_num_rows($result) > 0){
    while($r = mysqli_fetch_assoc($result)){
        $students_data[] = $r;
    }
}

// AJAX Search Response (बिना पेज रीलोड के लाइव डेटा भेजने के लिए)
if(isset($_GET['ajax_search'])){
    header('Content-Type: application/json');
    echo json_encode($students_data);
    exit();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>छात्र प्रबंधन | Smart CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar: #1e293b; 
            --primary: #3b82f6; 
            --bg: #f1f5f9; 
            
            /* PhonePe Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        
        /* 🖥️ DESKTOP SIDEBAR & LAYOUT */
        .sidebar { width: 260px; background: var(--sidebar); min-height: 100vh; position: fixed; padding: 20px; color: white; transition: 0.3s; z-index: 10; }
        .sidebar .brand { font-size: 22px; font-weight: 800; color: #fff; text-align: center; margin-bottom: 40px; display: block; text-decoration: none; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 15px; display: flex; align-items: center; gap: 10px; border-radius: 10px; margin-bottom: 5px; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar a.active { background: var(--primary); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

        .main-wrapper { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .table-card { background: white; border-radius: 16px; border: none; box-shadow: 0 4px 25px rgba(0,0,0,0.04); overflow: hidden; }
        .table thead { background: #f8fafc; }
        .table thead th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; padding: 18px 20px; border: none; }
        .table tbody td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .stu-img-box { width: 45px; height: 45px; border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; position: relative; flex-shrink: 0; }
        .stu-img-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .status-active { background: #dcfce7; color: #15803d; font-size: 13px; padding: 4px 12px; border-radius: 20px; font-weight: 600; display: inline-block; }
        .status-suspended { background: #fee2e2; color: #b91c1c; font-size: 13px; padding: 4px 12px; border-radius: 20px; font-weight: 600; display: inline-block; }

        .btn-action { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #fff; color: #64748b; transition: 0.2s; text-decoration: none; }
        .btn-action:hover { border-color: var(--primary); color: var(--primary); background: #eff6ff; }
        .btn-delete:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

        .search-box { position: relative; }
        .search-box input { padding-left: 40px; border-radius: 12px; border: 1px solid #e2e8f0; width: 300px; height: 45px; }
        .search-box i { position: absolute; left: 15px; top: 14px; color: #94a3b8; }

        .batch-badge { font-size: 11px; background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px; font-weight: 600; display: inline-block; margin-top: 2px; }

        .mobile-header, .mobile-bottom-nav, .mobile-student-list, .mobile-search-card { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full App Style with Larger Fonts) */
        @media (max-width: 991px) {
            body {
                background: var(--phonepe-bg) !important;
                font-family: 'Poppins', sans-serif !important;
            }

            .sidebar, .table-card, .page-header, .search-box { display: none !important; }

            /* PhonePe Header */
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
                width: 40px;
                height: 40px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
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

            .main-wrapper {
                margin-left: 0 !important;
                padding: 78px 12px 85px 12px !important;
                width: 100% !important;
            }

            /* PhonePe Search Bar */
            .mobile-search-card {
                display: block !important;
                background: #ffffff;
                border-radius: 14px;
                padding: 6px 14px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.04);
                margin-bottom: 14px;
                border: 1px solid #e2e8f0;
            }
            .mobile-search-card form { display: flex; align-items: center; gap: 12px; }
            .mobile-search-card input {
                border: none !important;
                outline: none !important;
                width: 100%;
                height: 44px;
                font-size: 15px !important;
                color: #1a202c;
                background: transparent;
            }
            .mobile-search-card i { color: var(--phonepe-purple); font-size: 18px; }

            /* PhonePe Student List Tiles */
            .mobile-student-list {
                display: flex !important;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-student-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 16px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03);
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-card-top { display: flex; align-items: center; justify-content: space-between; }
            .phonepe-user-info { display: flex; align-items: center; gap: 12px; }

            .phonepe-avatar {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #f0f3ff;
                flex-shrink: 0;
            }

            .phonepe-user-details h4 {
                font-size: 16.5px !important;
                font-weight: 700;
                color: #111827;
                margin: 0 0 3px 0;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .phonepe-user-details p {
                font-size: 13.5px !important;
                color: #4b5563;
                margin: 0;
            }

            .phonepe-upi-id {
                display: inline-block;
                background: #f3e8ff;
                color: var(--phonepe-purple);
                font-size: 12.5px !important;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 8px;
                margin-top: 5px;
            }

            .phonepe-card-middle {
                display: flex;
                flex-direction: column;
                gap: 6px;
                background: #f8fafc;
                padding: 10px 14px;
                border-radius: 12px;
                font-size: 13.5px !important;
            }

            .phonepe-info-row { display: flex; align-items: center; justify-content: space-between; }
            .phonepe-course-tag { font-weight: 700; color: #1e293b; }

            .phonepe-card-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                border-top: 1px dashed #e2e8f0;
                padding-top: 12px;
            }

            .phonepe-action-btn {
                padding: 8px 14px;
                border-radius: 10px;
                font-size: 13px !important;
                font-weight: 600;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 6px;
                border: none;
            }

            .phonepe-btn-edit { background: #eff6ff; color: #2563eb; }
            .phonepe-btn-status { background: #f0fdf4; color: #166534; }
            .phonepe-btn-status-suspended { background: #fff7ed; color: #c2410c; }
            .phonepe-btn-delete { background: #fef2f2; color: #dc2626; }

            /* PhonePe Bottom Navigation Bar */
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

<!-- PhonePe App Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>छात्र निर्देशिका</h3>
            <small>Student Directory & Actions</small>
        </div>
    </div>
    <div style="color:#fff; font-size:20px;">
        <i class="fa fa-shield-alt"></i>
    </div>
</div>

<!-- Desktop Sidebar -->
<div class="sidebar">
    <a href="#" class="brand">🏦 SMART CMS</a>
    <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> डैशबोर्ड</a>
    <a href="add_student.php"><i class="fa-solid fa-user-plus"></i> नया प्रवेश</a>
    <a href="manage_students.php" class="active"><i class="fa-solid fa-users"></i> छात्र सूची</a>
    <a href="collect_fees.php"><i class="fa-solid fa-receipt"></i> फीस जमा करें</a>
    <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> लॉगआउट</a>
</div>

<!-- Main Wrapper -->
<div class="main-wrapper">
    
    <!-- Mobile Search Bar (No-Reload Live Search) -->
    <div class="mobile-search-card">
        <form onsubmit="return false;">
            <i class="fa fa-search"></i>
            <input type="text" id="mobileSearchInput" placeholder="नाम, मोबाइल या रोल नंबर से खोजें..." value="<?= htmlspecialchars($search) ?>">
            <i class="fa fa-times-circle" id="clearMobileSearch" style="cursor:pointer; display:none; color:#a0aec0;"></i>
        </form>
    </div>

    <!-- Desktop Header -->
    <div class="page-header">
        <div>
            <h2 class="fw-bold mb-1">Student Directory</h2>
            <p class="text-muted small">कुल नामांकित छात्रों की सूची और प्रबंधन</p>
        </div>
        
        <div class="search-box">
            <i class="fa fa-search"></i>
            <input type="text" id="desktopSearchInput" class="form-control" placeholder="नाम या रोल नंबर से खोजें..." value="<?= htmlspecialchars($search) ?>">
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> डेटा सफलतापूर्वक अपडेट किया गया!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- 🖥️ DESKTOP TABLE -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>छात्र का विवरण (Profile)</th>
                        <th>एप्लीकेशन नंबर</th>
                        <th>कोर्स व समय (Course & Batch)</th>
                        <th>मोबाइल</th>
                        <th>कुल फ़ीस</th>
                        <th>स्थिति (Status)</th>
                        <th class="text-end">कार्रवाई (Actions)</th>
                    </tr>
                </thead>
                <tbody id="desktopTableBody">
                    <!-- Rendered via JS/PHP -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- 📱 MOBILE PHONEPE CARDS LIST -->
    <div class="mobile-student-list" id="mobileCardsContainer">
        <!-- Rendered via JS/PHP -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Initial Data Load
let initialData = <?= json_encode($students_data); ?>;

// Helper to format time strings (24h -> 12h AM/PM)
function formatTime(timeStr) {
    if (!timeStr) return '';
    let [hours, minutes] = timeStr.split(':');
    hours = parseInt(hours);
    if (isNaN(hours)) return timeStr;
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes ? minutes.substr(0, 2) : '00';
    return `${hours < 10 ? '0' + hours : hours}:${minutes} ${ampm}`;
}

function renderStudents(data) {
    const desktopBody = document.getElementById('desktopTableBody');
    const mobileContainer = document.getElementById('mobileCardsContainer');
    
    desktopBody.innerHTML = '';
    mobileContainer.innerHTML = '';

    if (!data || data.length === 0) {
        desktopBody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">कोई छात्र नहीं मिला।</td></tr>`;
        mobileContainer.innerHTML = `<div class="text-center py-5 bg-white rounded-3 border"><i class="fa fa-users-slash text-muted mb-2" style="font-size:32px;"></i><p class="text-muted mb-0">कोई छात्र नहीं मिला।</p></div>`;
        return;
    }

    data.forEach(row => {
        let photo = (row.photo && row.photo !== '') ? 'uploads/profile/' + row.photo : `https://ui-avatars.com/api/?name=${encodeURIComponent(row.u_name)}&background=random`;
        let verifiedBadge = (row.is_verified == 1) 
            ? `<i class="fa-solid fa-circle-check text-success" title="Verified"></i>` 
            : `<i class="fa-solid fa-circle-xmark text-danger" title="Not Verified"></i>`;
        
        let statusBadge = (row.status === 'Active') 
            ? `<span class="status-active">● सक्रिय</span>` 
            : `<span class="status-suspended">● निलंबित</span>`;

        let batchTiming = '';
        if (row.batch_start_time && row.batch_end_time) {
            batchTiming = `${formatTime(row.batch_start_time)} - ${formatTime(row.batch_end_time)}`;
        } else if (row.batch_time) {
            batchTiming = row.batch_time;
        }

        let totalFeeFormatted = row.total_fee ? '₹' + parseInt(row.total_fee).toLocaleString('en-IN') : '₹0';

        // 1. Render Desktop Row
        let desktopRow = `
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div class="stu-img-box"><img src="${photo}" alt="Student"></div>
                        <div>
                            <div class="fw-bold text-dark">${row.u_name} ${verifiedBadge}</div>
                            <div class="text-muted" style="font-size: 11px;">पिता: ${row.father_name || ''}</div>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-light text-primary border px-3 py-2">${row.roll_no}</span></td>
                <td>
                    <div class="fw-medium text-dark">${row.course}</div>
                    ${batchTiming ? `<div class="batch-badge"><i class="fa-regular fa-clock me-1"></i>${batchTiming}</div>` : ''}
                </td>
                <td class="text-muted small">${row.mobile}</td>
                <td class="fw-bold text-success">${totalFeeFormatted}</td>
                <td>${statusBadge}</td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="edit_student.php?id=${row.user_id}" class="btn-action" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="manage_students.php?toggle_status=${row.user_id}&current=${row.status}" class="btn-action" title="Toggle Status"><i class="fa-solid ${row.status === 'Active' ? 'fa-user-slash text-warning' : 'fa-user-check text-success'}"></i></a>
                        <a href="manage_students.php?delete=${row.user_id}" class="btn-action btn-delete" onclick="return confirm('क्या आप वाकई डिलीट करना चाहते हैं?')" title="Delete"><i class="fa-solid fa-trash-can"></i></a>
                    </div>
                </td>
            </tr>
        `;
        desktopBody.insertAdjacentHTML('beforeend', desktopRow);

        // 2. Render Mobile PhonePe Card
        let mobileCard = `
            <div class="phonepe-student-card">
                <div class="phonepe-card-top">
                    <div class="phonepe-user-info">
                        <img src="${photo}" class="phonepe-avatar" alt="Student">
                        <div class="phonepe-user-details">
                            <h4>${row.u_name} ${verifiedBadge}</h4>
                            <p><i class="fa fa-user-friends me-1"></i> पिता: ${row.father_name || ''}</p>
                            <span class="phonepe-upi-id"><i class="fa fa-id-badge me-1"></i> ${row.roll_no}</span>
                        </div>
                    </div>
                    <div>${statusBadge}</div>
                </div>

                <div class="phonepe-card-middle">
                    <div class="phonepe-info-row">
                        <div class="phonepe-course-tag"><i class="fa fa-graduation-cap text-primary me-1"></i> ${row.course}</div>
                        <div class="fw-bold text-success">${totalFeeFormatted}</div>
                    </div>
                    <div class="phonepe-info-row text-muted" style="font-size:12.5px;">
                        <div><i class="fa fa-phone-alt me-1"></i> ${row.mobile}</div>
                        ${batchTiming ? `<div><i class="fa-regular fa-clock me-1"></i> ${batchTiming}</div>` : ''}
                    </div>
                </div>

                <div class="phonepe-card-actions">
                    <a href="edit_student.php?id=${row.user_id}" class="phonepe-action-btn phonepe-btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i> एडिट
                    </a>
                    <a href="manage_students.php?toggle_status=${row.user_id}&current=${row.status}" class="phonepe-action-btn ${row.status === 'Active' ? 'phonepe-btn-status-suspended' : 'phonepe-btn-status'}">
                        <i class="fa-solid ${row.status === 'Active' ? 'fa-user-slash' : 'fa-user-check'}"></i> ${row.status === 'Active' ? 'रोकें' : 'सक्रिय'}
                    </a>
                    <a href="manage_students.php?delete=${row.user_id}" class="phonepe-action-btn phonepe-btn-delete" onclick="return confirm('क्या आप वाकई डिलीट करना चाहते हैं?')">
                        <i class="fa-solid fa-trash-can"></i> डिलीट
                    </a>
                </div>
            </div>
        `;
        mobileContainer.insertAdjacentHTML('beforeend', mobileCard);
    });
}

// Render Initial Data
renderStudents(initialData);

// ⚡ NO-RELOAD LIVE SEARCH (AJAX FETCH)
let searchTimeout = null;
function handleLiveSearch(query) {
    const clearBtn = document.getElementById('clearMobileSearch');
    if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetch(`manage_students.php?ajax_search=1&search=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                renderStudents(data);
            })
            .catch(err => console.error("Search Error:", err));
    }, 250);
}

// Input Listeners
const mobileInput = document.getElementById('mobileSearchInput');
const desktopInput = document.getElementById('desktopSearchInput');

if(mobileInput) {
    mobileInput.addEventListener('input', (e) => handleLiveSearch(e.target.value));
}
if(desktopInput) {
    desktopInput.addEventListener('input', (e) => handleLiveSearch(e.target.value));
}

document.getElementById('clearMobileSearch')?.addEventListener('click', () => {
    mobileInput.value = '';
    handleLiveSearch('');
});
</script>

</body>
</html>