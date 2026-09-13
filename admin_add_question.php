<?php
// 1. Database Configuration
include 'db_config.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Admin Auth Check (Prevents Session Redirect Bugs)
if(!isset($_SESSION['admin']) && !isset($_SESSION['admin_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

// Auto Create Questions Table if missing
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course VARCHAR(100) NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ==========================================
// 🚀 PURE AJAX HANDLER (ZERO PAGE RELOAD)
// ==========================================
if(isset($_POST['action']) && $_POST['action'] == 'add_question') {
    header('Content-Type: application/json');

    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $a = mysqli_real_escape_string($conn, $_POST['opt_a']);
    $b = mysqli_real_escape_string($conn, $_POST['opt_b']);
    $c = mysqli_real_escape_string($conn, $_POST['opt_c']);
    $d = mysqli_real_escape_string($conn, $_POST['opt_d']);
    $ans = mysqli_real_escape_string($conn, $_POST['correct_ans']);

    $q = "INSERT INTO questions (course, question_text, option_a, option_b, option_c, option_d, correct_option) 
          VALUES ('$course', '$question', '$a', '$b', '$c', '$d', '$ans')";
    
    if(mysqli_query($conn, $q)) {
        echo json_encode(['status' => 'success', 'message' => '🎉 Question Added Successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Error: ' . mysqli_error($conn)]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Add Question | Admin Portal</title>
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

        /* PhonePe Style Toast Popup */
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

        /* 🖥️ DESKTOP VIEW (>= 992px) - ELEGANT & SHANDAR VIEW */
        @media (min-width: 992px) {
            .mobile-only { display: none !important; }
            body {
                background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), 
                            url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1350&q=80');
                background-size: cover;
                background-attachment: fixed;
            }
            .main-card { 
                background: rgba(255, 255, 255, 0.08); 
                backdrop-filter: blur(20px); 
                border-radius: 24px; 
                border: 1px solid rgba(255, 255, 255, 0.15); 
                padding: 35px;
                color: white;
                box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            }
            .form-label { font-size: 14px; font-weight: 600; color: #60a5fa; margin-top: 15px; }
            .form-control, .form-select {
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.15);
                color: white; border-radius: 12px; padding: 12px 15px;
            }
            .form-control:focus, .form-select:focus {
                background: rgba(255, 255, 255, 0.15); color: white; border-color: #3b82f6;
            }
            option { background: #1e293b; color: white; }
            .btn-save { 
                background: #3b82f6; color: white; border: none; padding: 15px; 
                border-radius: 12px; font-size: 16px; font-weight: 700; width: 100%; margin-top: 25px; 
            }
            .btn-save:hover { background: #2563eb; }
        }

        /* 📱 MOBILE PHONEPE NATIVE INTERFACE (< 992px) - BOLD & LARGE FONTS */
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

            .main-card {
                background: #ffffff; border-radius: 24px; padding: 20px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.06); border: none;
            }

            /* BOLD MOBILE FONTS & INPUTS */
            .form-label {
                font-size: 17px !important; font-weight: 800 !important;
                color: #0f172a !important; margin-top: 16px !important; margin-bottom: 6px !important;
            }
            .form-control, .form-select {
                font-size: 16px !important; font-weight: 700 !important;
                padding: 14px 16px !important; border-radius: 16px !important;
                border: 2px solid #e2e8f0 !important; background-color: #f8fafc !important;
                color: #0f172a !important;
            }
            .form-control:focus, .form-select:focus {
                border-color: var(--pp-purple) !important; background-color: #ffffff !important;
            }
            
            .btn-save {
                background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%) !important;
                border-radius: 18px !important; padding: 18px !important;
                font-size: 19px !important; font-weight: 900 !important;
                color: white !important; border: none !important; width: 100%;
                margin-top: 30px; box-shadow: 0 8px 22px rgba(95, 37, 159, 0.35) !important;
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

<!-- PhonePe Notification Toast Popup -->
<div id="ppToast" class="pp-toast">
    <i class="fas fa-check-circle text-success fs-4"></i> 
    <span id="ppToastMsg">Notification</span>
</div>

<!-- Mobile PhonePe Header -->
<div class="pp-header mobile-only">
    <div class="pp-profile">
        <div class="pp-avatar">Q</div>
        <div>
            <h3 class="pp-title">ADD QUESTION</h3>
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
    <a href="manage_courses.php" class="drawer-link"><i class="fa fa-graduation-cap"></i> Manage Courses</a>
    <a href="admin_add_question.php" class="drawer-link active"><i class="fa fa-pen-nib"></i> Add Question</a>
    <a href="bulk_upload_marks.php" class="drawer-link"><i class="fa fa-file-csv"></i> Bulk CSV Upload</a>
    <a href="logout.php" class="drawer-link text-danger mt-4"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="container app-wrapper my-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="main-card">
                <h2 class="text-center fw-bold mb-4 desktop-only" style="color: #60a5fa;">➕ Add Exam Question</h2>
                <h2 class="fw-extrabold mb-3 mobile-only" style="color: #0f172a; font-size: 22px;">Create New Question</h2>
                
                <!-- 🚀 ZERO RELOAD AJAX FORM -->
                <form id="addQuestionForm">
                    
                    <div class="mb-2">
                        <label class="form-label">Target Course</label>
                        <select name="course" class="form-select" required>
                            <option value="">-- Choose Course --</option>
                            <option value="ADCA">ADCA</option>
                            <option value="DCA">DCA</option>
                            <option value="CCC">CCC</option>
                            <option value="DTP">DTP</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Question Text</label>
                        <textarea name="question" class="form-control" placeholder="Write your question here..." required rows="3"></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 col-lg-6">
                            <label class="form-label">Option A</label>
                            <input type="text" name="opt_a" class="form-control" placeholder="Option A" required>
                        </div>
                        <div class="col-6 col-lg-6">
                            <label class="form-label">Option B</label>
                            <input type="text" name="opt_b" class="form-control" placeholder="Option B" required>
                        </div>
                        <div class="col-6 col-lg-6">
                            <label class="form-label">Option C</label>
                            <input type="text" name="opt_c" class="form-control" placeholder="Option C" required>
                        </div>
                        <div class="col-6 col-lg-6">
                            <label class="form-label">Option D</label>
                            <input type="text" name="opt_d" class="form-control" placeholder="Option D" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correct Answer</label>
                        <select name="correct_ans" class="form-select" required>
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>

                    <button type="submit" id="saveBtn" class="btn-save">
                        🚀 Save Question to Bank
                    </button>

                    <a href="admin_dashboard.php" class="d-block text-center mt-3 text-secondary text-decoration-none fw-bold desktop-only">← Back to Dashboard</a>
                </form>

            </div>

        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="pp-bottom-nav mobile-only">
    <a href="admin_dashboard.php" class="pp-nav-item"><i class="fa fa-home"></i><span>Home</span></a>
    <a href="manage_courses.php" class="pp-nav-item"><i class="fa fa-graduation-cap"></i><span>Courses</span></a>
    <a href="admin_add_question.php" class="pp-nav-item active"><i class="fa fa-pen-nib"></i><span>Questions</span></a>
    <a href="javascript:void(0)" class="pp-nav-item" onclick="toggleSidebar()"><i class="fa fa-bars"></i><span>Menu</span></a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// PhonePe Style Toast Popup Helper
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

// 100% PURE AJAX SUBMIT (ZERO PAGE RELOAD)
document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SAVING QUESTION...';

    const formData = new FormData(this);
    formData.append('action', 'add_question');

    fetch('admin_add_question.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        showToast(res.message);
        if(res.status === 'success') {
            document.getElementById('addQuestionForm').reset();
        }
        btn.disabled = false;
        btn.innerHTML = '🚀 Save Question to Bank';
    })
    .catch(() => {
        showToast("❌ Connection error! Please try again.");
        btn.disabled = false;
        btn.innerHTML = '🚀 Save Question to Bank';
    });
});
</script>

</body>
</html>