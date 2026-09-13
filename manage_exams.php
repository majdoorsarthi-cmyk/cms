<?php
include 'db_config.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 🟢 Session Redirect Fix
if(!isset($_SESSION['admin']) && !isset($_SESSION['admin_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

// ==========================================
// 🚀 PURE AJAX API HANDLERS (ZERO PAGE RELOAD)
// ==========================================

// 1. AJAX Question Delete Handler
if(isset($_POST['action']) && $_POST['action'] == 'delete_question') {
    header('Content-Type: application/json');
    $id = intval($_POST['id']);
    $del = mysqli_query($conn, "DELETE FROM questions WHERE id = '$id'");
    if($del) {
        echo json_encode(['status' => 'success', 'message' => '🗑️ Question deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to delete question!']);
    }
    exit();
}

// 2. AJAX Filter & Fetch Handler
if(isset($_GET['action']) && $_GET['action'] == 'fetch_questions') {
    header('Content-Type: application/json');
    $course = mysqli_real_escape_string($conn, $_GET['course'] ?? '');
    $where = !empty($course) ? "WHERE course = '$course'" : "";
    
    $res = mysqli_query($conn, "SELECT * FROM questions $where ORDER BY id DESC");
    $data = [];
    while($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit();
}

// Initial Server Load
$questions = mysqli_query($conn, "SELECT * FROM questions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Exam Bank | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --pp-purple: #5f259f;
            --pp-dark: #3d1668;
            --pp-bg: #f3f4f9;
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

        /* PhonePe Toast Popup Notification */
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

        /* 🖥️ DESKTOP VIEW (>= 992px) - ELEGANT & SHANDAR GLASSMORPHISM */
        @media (min-width: 992px) {
            .mobile-only { display: none !important; }
            body { 
                background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), 
                            url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1350&q=80');
                background-size: cover; background-attachment: fixed; color: white; padding: 40px;
            }
            .main-container {
                max-width: 1100px; margin: auto;
                background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);
                padding: 30px; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
            h2 { color: #3b82f6; font-weight: 700; }
            .filter-box { margin-bottom: 25px; display: flex; gap: 10px; }
            select { 
                padding: 10px 15px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); 
                color: white; border-radius: 10px; outline: none;
            }
            option { background: #1e293b; color: white; }
            .btn-dt { padding: 10px 20px; border-radius: 10px; border: none; font-weight: 600; font-size: 14px; text-decoration: none; cursor: pointer; }
            .btn-add { background: #2ecc71; color: white; }
            .btn-delete { background: #ef4444; color: white; border: none; padding: 6px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #3b82f6; font-size: 13px; text-transform: uppercase; }
            td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; vertical-align: top; color: #f1f5f9; }
            .badge-dt { background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; }
            .correct-ans { color: #2ecc71; font-weight: 700; }
            .options-list { font-size: 13px; opacity: 0.85; margin-top: 5px; line-height: 1.6; }
        }

        /* 📱 MOBILE PHONEPE NATIVE INTERFACE (< 992px) - LARGE FONTS & CARDS */
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

            /* Filter Selection Box */
            .filter-card-mobile {
                background: #ffffff; border-radius: 20px; padding: 16px;
                box-shadow: 0 4px 18px rgba(0,0,0,0.04); margin-bottom: 20px;
            }
            .form-select-mob {
                font-size: 17px !important; font-weight: 800 !important;
                padding: 14px 16px !important; border-radius: 14px !important;
                border: 2px solid #e2e8f0 !important; background-color: #f8fafc !important;
                color: #0f172a !important; width: 100%;
            }

            /* Question Card (PhonePe Style) */
            .q-card {
                background: #ffffff; border-radius: 22px; padding: 20px;
                margin-bottom: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.05);
                border: 1px solid #f1f5f9; transition: transform 0.2s ease;
            }
            .q-card-header {
                display: flex; justify-content: space-between; align-items: center;
                margin-bottom: 12px;
            }
            .badge-mob {
                background: rgba(95, 37, 159, 0.12); color: var(--pp-purple);
                font-size: 14px; font-weight: 900; padding: 6px 14px; border-radius: 30px;
            }
            .q-text {
                font-size: 18px !important; font-weight: 800 !important;
                color: #0f172a !important; line-height: 1.4; margin-bottom: 14px;
            }
            .options-grid {
                display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
                background: #f8fafc; padding: 14px; border-radius: 16px; margin-bottom: 14px;
            }
            .opt-item {
                font-size: 15px !important; font-weight: 700; color: #334155;
            }
            .ans-bar {
                display: flex; justify-content: space-between; align-items: center;
                padding-top: 10px; border-top: 1px solid #f1f5f9;
            }
            .correct-ans-mob {
                color: #16a34a; font-size: 16px; font-weight: 900;
            }
            .btn-delete-mob {
                background: #fee2e2; color: #dc2626; border: none;
                padding: 10px 18px; border-radius: 12px; font-size: 15px; font-weight: 800;
                display: flex; align-items: center; gap: 6px;
            }

            /* Mobile Side Drawer Overlay */
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

<!-- PhonePe Notification Toast Popup -->
<div id="ppToast" class="pp-toast">
    <i class="fas fa-check-circle text-success fs-4"></i> 
    <span id="ppToastMsg">Notification</span>
</div>

<!-- Mobile PhonePe Header -->
<div class="pp-header mobile-only">
    <div class="pp-profile">
        <div class="pp-avatar">M</div>
        <div>
            <h3 class="pp-title">EXAM BANK</h3>
            <span class="pp-subtitle">Manage Question Paper</span>
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
    <a href="manage_courses.php" class="drawer-link"><i class="fa fa-graduation-cap"></i> Manage Courses</a>
    <a href="admin_add_question.php" class="drawer-link"><i class="fa fa-plus-circle"></i> Add Question</a>
    <a href="manage_exams.php" class="drawer-link active"><i class="fa fa-list-check"></i> Manage Questions</a>
    <a href="bulk_upload_marks.php" class="drawer-link"><i class="fa fa-file-csv"></i> Bulk CSV Upload</a>
    <a href="logout.php" class="drawer-link text-danger mt-4"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- =================================================== -->
<!-- 📱 MOBILE PHONEPE APP VIEW (AJAX CARDS & BOLD FONTS) -->
<!-- =================================================== -->
<div class="container app-wrapper mobile-only">
    
    <!-- Filter Card -->
    <div class="filter-card-mobile">
        <label class="form-label fw-bold fs-6 mb-2 text-dark">Filter By Course:</label>
        <select class="form-select-mob" id="mobCourseFilter" onchange="filterQuestionsAjax(this.value)">
            <option value="">All Courses</option>
            <option value="ADCA">ADCA</option>
            <option value="DCA">DCA</option>
            <option value="CCC">CCC</option>
            <option value="DTP">DTP</option>
        </select>
    </div>

    <!-- Add Question Button -->
    <a href="admin_add_question.php" class="btn w-100 py-3 mb-4 text-white fw-bold fs-5 text-decoration-none rounded-4 d-flex justify-content-center align-items-center gap-2" style="background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%); shadow: 0 6px 20px rgba(95, 37, 159, 0.3);">
        <i class="fa fa-plus-circle"></i> Add New Question
    </a>

    <!-- Questions Container for Mobile -->
    <div id="mobQuestionsList">
        <?php 
        mysqli_data_seek($questions, 0);
        if(mysqli_num_rows($questions) > 0): 
            while($q = mysqli_fetch_assoc($questions)): 
        ?>
            <div class="q-card" id="q-card-mob-<?php echo $q['id']; ?>">
                <div class="q-card-header">
                    <span class="badge-mob"><?php echo $q['course']; ?></span>
                    <small class="text-muted fw-bold">ID: #<?php echo $q['id']; ?></small>
                </div>
                <div class="q-text"><?php echo htmlspecialchars($q['question_text']); ?></div>
                <div class="options-grid">
                    <div class="opt-item">A: <?php echo htmlspecialchars($q['option_a']); ?></div>
                    <div class="opt-item">B: <?php echo htmlspecialchars($q['option_b']); ?></div>
                    <div class="opt-item">C: <?php echo htmlspecialchars($q['option_c']); ?></div>
                    <div class="opt-item">D: <?php echo htmlspecialchars($q['option_d']); ?></div>
                </div>
                <div class="ans-bar">
                    <span class="correct-ans-mob"><i class="fa fa-check-circle me-1"></i> Ans: Option <?php echo $q['correct_option']; ?></span>
                    <button type="button" class="btn-delete-mob" onclick="deleteQuestionAjax(<?php echo $q['id']; ?>)">
                        <i class="fa fa-trash-can"></i> Delete
                    </button>
                </div>
            </div>
        <?php 
            endwhile; 
        else: 
        ?>
            <div class="text-center py-5 text-muted fw-bold fs-5">No questions found!</div>
        <?php endif; ?>
    </div>

</div>

<!-- =================================================== -->
<!-- 🖥️ DESKTOP VIEW (ELEGANT & SHANDAR GLASSMORPHISM) -->
<!-- =================================================== -->
<div class="main-container desktop-only">
    <div class="header-flex">
        <h2>⚙️ Question Bank Management</h2>
        <a href="admin_add_question.php" class="btn-dt btn-add">+ Add New Question</a>
    </div>

    <!-- Desktop AJAX Filter -->
    <div class="filter-box">
        <select id="dtCourseFilter" onchange="filterQuestionsAjax(this.value)">
            <option value="">All Courses</option>
            <option value="ADCA">ADCA</option>
            <option value="DCA">DCA</option>
            <option value="CCC">CCC</option>
            <option value="DTP">DTP</option>
        </select>
        <button type="button" onclick="filterQuestionsAjax('')" class="btn-dt" style="background: rgba(255,255,255,0.1); color: white;">Reset</button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th width="10%">Course</th>
                    <th width="45%">Question Details</th>
                    <th width="20%">Options</th>
                    <th width="12%">Correct</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
            <tbody id="dtQuestionsBody">
                <?php 
                mysqli_data_seek($questions, 0);
                if(mysqli_num_rows($questions) > 0): 
                    while($row = mysqli_fetch_assoc($questions)): 
                ?>
                    <tr id="q-row-dt-<?php echo $row['id']; ?>">
                        <td><span class="badge-dt"><?php echo $row['course']; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($row['question_text']); ?></strong></td>
                        <td class="options-list">
                            A: <?php echo htmlspecialchars($row['option_a']); ?><br>
                            B: <?php echo htmlspecialchars($row['option_b']); ?><br>
                            C: <?php echo htmlspecialchars($row['option_c']); ?><br>
                            D: <?php echo htmlspecialchars($row['option_d']); ?>
                        </td>
                        <td><span class="correct-ans">Option <?php echo $row['correct_option']; ?></span></td>
                        <td>
                            <button type="button" class="btn-delete" onclick="deleteQuestionAjax(<?php echo $row['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-white-50">No questions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <a href="admin_dashboard.php" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 13px;">← Back to Admin Dashboard</a>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="pp-bottom-nav mobile-only">
    <a href="admin_dashboard.php" class="pp-nav-item"><i class="fa fa-home"></i><span>Home</span></a>
    <a href="manage_courses.php" class="pp-nav-item"><i class="fa fa-graduation-cap"></i><span>Courses</span></a>
    <a href="manage_exams.php" class="pp-nav-item active"><i class="fa fa-list-check"></i><span>Questions</span></a>
    <a href="javascript:void(0)" class="pp-nav-item" onclick="toggleSidebar()"><i class="fa fa-bars"></i><span>Menu</span></a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toast Alert Helper
function showToast(msg) {
    const toast = document.getElementById('ppToast');
    document.getElementById('ppToastMsg').innerText = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// Sidebar Drawer Switcher
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}

// 🚀 ZERO RELOAD DELETE AJAX
function deleteQuestionAjax(id) {
    if(!confirm("Kya aap sach me ye question delete karna chahte hain?")) return;

    const formData = new FormData();
    formData.append('action', 'delete_question');
    formData.append('id', id);

    fetch('manage_exams.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        showToast(res.message);
        if(res.status === 'success') {
            // Remove from Mobile Card List
            const mobCard = document.getElementById(`q-card-mob-${id}`);
            if(mobCard) mobCard.remove();

            // Remove from Desktop Table Row
            const dtRow = document.getElementById(`q-row-dt-${id}`);
            if(dtRow) dtRow.remove();
        }
    })
    .catch(() => showToast("❌ Error deleting question!"));
}

// 🚀 ZERO RELOAD FILTER AJAX
function filterQuestionsAjax(course) {
    document.getElementById('mobCourseFilter').value = course;
    document.getElementById('dtCourseFilter').value = course;

    fetch(`manage_exams.php?action=fetch_questions&course=${encodeURIComponent(course)}`)
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            renderQuestions(res.data);
            showToast(`Filtered: ${course || 'All Courses'}`);
        }
    })
    .catch(() => showToast("❌ Filter fail ho gaya!"));
}

// Render Questions Dynamically without Page Refresh
function renderQuestions(data) {
    const mobContainer = document.getElementById('mobQuestionsList');
    const dtContainer = document.getElementById('dtQuestionsBody');

    if(!data || data.length === 0) {
        mobContainer.innerHTML = `<div class="text-center py-5 text-muted fw-bold fs-5">No questions found!</div>`;
        dtContainer.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-white-50">No questions found.</td></tr>`;
        return;
    }

    let mobHtml = '';
    let dtHtml = '';

    data.forEach(q => {
        // Mobile PhonePe Card Template
        mobHtml += `
            <div class="q-card" id="q-card-mob-${q.id}">
                <div class="q-card-header">
                    <span class="badge-mob">${q.course}</span>
                    <small class="text-muted fw-bold">ID: #${q.id}</small>
                </div>
                <div class="q-text">${escapeHtml(q.question_text)}</div>
                <div class="options-grid">
                    <div class="opt-item">A: ${escapeHtml(q.option_a)}</div>
                    <div class="opt-item">B: ${escapeHtml(q.option_b)}</div>
                    <div class="opt-item">C: ${escapeHtml(q.option_c)}</div>
                    <div class="opt-item">D: ${escapeHtml(q.option_d)}</div>
                </div>
                <div class="ans-bar">
                    <span class="correct-ans-mob"><i class="fa fa-check-circle me-1"></i> Ans: Option ${q.correct_option}</span>
                    <button type="button" class="btn-delete-mob" onclick="deleteQuestionAjax(${q.id})">
                        <i class="fa fa-trash-can"></i> Delete
                    </button>
                </div>
            </div>
        `;

        // Desktop Table Row Template
        dtHtml += `
            <tr id="q-row-dt-${q.id}">
                <td><span class="badge-dt">${q.course}</span></td>
                <td><strong>${escapeHtml(q.question_text)}</strong></td>
                <td class="options-list">
                    A: ${escapeHtml(q.option_a)}<br>
                    B: ${escapeHtml(q.option_b)}<br>
                    C: ${escapeHtml(q.option_c)}<br>
                    D: ${escapeHtml(q.option_d)}
                </td>
                <td><span class="correct-ans">Option ${q.correct_option}</span></td>
                <td>
                    <button type="button" class="btn-delete" onclick="deleteQuestionAjax(${q.id})">Delete</button>
                </td>
            </tr>
        `;
    });

    mobContainer.innerHTML = mobHtml;
    dtContainer.innerHTML = dtHtml;
}

// XSS Safety Helper
function escapeHtml(str) {
    if(!str) return '';
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>

</body>
</html>