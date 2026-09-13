<?php
include 'db_config.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ==========================================
// 🚀 PURE AJAX RESULT HANDLER (ZERO PAGE RELOAD)
// ==========================================
if(isset($_POST['action']) && $_POST['action'] == 'fetch_result') {
    header('Content-Type: application/json');
    $roll_no = mysqli_real_escape_string($conn, $_POST['roll_no']);

    // Student Basic Info Fetch Query
    $student_query = "SELECT s.id, u.name, s.roll_no, s.course FROM students s 
                      JOIN users u ON s.user_id = u.id 
                      WHERE s.roll_no = '$roll_no'";
    $student_res = mysqli_query($conn, $student_query);

    if ($student_res && mysqli_num_rows($student_res) > 0) {
        $student_info = mysqli_fetch_assoc($student_res);
        $student_id = $student_info['id'];

        // Marks Details Query
        $marks_query = "SELECT * FROM marks WHERE student_id = '$student_id'";
        $marks_res = mysqli_query($conn, $marks_query);
        
        $marks = [];
        while($row = mysqli_fetch_assoc($marks_res)) {
            $marks[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'student' => $student_info,
            'marks' => $marks,
            'date' => date('d-M-Y')
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Invalid Roll Number! Please check again.'
        ]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Check Student Result | CMS PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --pp-purple: #5f259f;
            --pp-dark: #3d1668;
            --pp-bg: #f3f4f9;
            --primary: #4361ee;
            --text-dark: #2b3674;
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

        /* PhonePe Toast Alert Notification */
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

        /* 🖥️ DESKTOP VIEW (>= 992px) - ELEGANT CLASSIC LOOK */
        @media (min-width: 992px) {
            .mobile-only { display: none !important; }
            body { background: #f4f7fe; padding: 30px; }
            .search-container { 
                max-width: 500px; margin: 40px auto; background: white; padding: 35px; 
                border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; 
            }
            .input-group { margin-top: 20px; }
            input.desktop-input { 
                width: 100%; padding: 15px; border: 2px solid #edf2f7; border-radius: 12px; 
                outline: none; font-size: 16px; text-align: center; font-weight: 600;
            }
            input.desktop-input:focus { border-color: var(--primary); }
            .btn-check { 
                background: var(--primary); color: white; border: none; padding: 15px 30px; 
                border-radius: 12px; cursor: pointer; font-weight: 700; width: 100%; margin-top: 15px; font-size: 16px;
            }
            .marksheet { 
                max-width: 850px; margin: 30px auto; background: white; padding: 40px; 
                border-radius: 16px; border: 4px double #e2e8f0; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            }
            .header { text-align: center; border-bottom: 2px solid var(--primary); padding-bottom: 12px; margin-bottom: 20px; }
            .student-details { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 15px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #cbd5e1; padding: 12px; text-align: left; }
            th { background: #f8fafc; color: var(--text-dark); font-weight: 700; }
            .total-row { font-weight: bold; background: #f0f4ff; }
            .print-btn { background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 8px; cursor: pointer; font-weight: 700; margin-bottom: 20px; }
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
                box-shadow: 0 8px 25px rgba(0,0,0,0.06); border: none; margin-bottom: 20px;
            }

            /* BOLD MOBILE FONTS & INPUTS */
            .mob-label {
                font-size: 18px !important; font-weight: 800 !important;
                color: #0f172a !important; margin-bottom: 8px !important; display: block;
            }
            .mob-input {
                font-size: 18px !important; font-weight: 800 !important;
                padding: 16px !important; border-radius: 16px !important;
                border: 2px solid #e2e8f0 !important; background-color: #f8fafc !important;
                color: #0f172a !important; text-align: center; width: 100%;
            }
            .mob-input:focus {
                border-color: var(--pp-purple) !important; background-color: #ffffff !important; outline: none;
            }
            
            .btn-mob-search {
                background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%) !important;
                border-radius: 18px !important; padding: 18px !important;
                font-size: 19px !important; font-weight: 900 !important;
                color: white !important; border: none !important; width: 100%;
                margin-top: 18px; box-shadow: 0 8px 22px rgba(95, 37, 159, 0.35) !important;
            }

            /* Mobile Result Card (PhonePe View) */
            .mob-result-card {
                background: #ffffff; border-radius: 22px; padding: 20px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.06); border: 1px solid #f1f5f9;
            }
            .mob-student-header {
                background: #f8fafc; padding: 16px; border-radius: 16px;
                margin-bottom: 18px; border: 1px dashed #cbd5e1;
            }
            .mob-st-name { font-size: 20px !important; font-weight: 900; color: #0f172a; }
            .mob-st-detail { font-size: 15px !important; font-weight: 700; color: #64748b; }
            
            .mob-subject-row {
                display: flex; justify-content: space-between; align-items: center;
                padding: 14px 0; border-bottom: 1px solid #f1f5f9;
            }
            .mob-sub-name { font-size: 17px !important; font-weight: 800; color: #1e293b; }
            .mob-sub-marks { font-size: 16px !important; font-weight: 800; color: #475569; }

            .mob-grand-total {
                background: rgba(95, 37, 159, 0.08); border-radius: 16px;
                padding: 18px; margin-top: 16px; display: flex; justify-content: space-between; align-items: center;
            }
            .mob-gt-text { font-size: 18px !important; font-weight: 900; color: var(--pp-purple); }

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

            /* Fixed Bottom Navigation Bar */
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

        @media print {
            .mobile-only, .pp-header, .pp-bottom-nav, .search-container, .print-btn { display: none !important; }
            .desktop-only { display: block !important; }
            .marksheet { border: none !important; margin: 0 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<!-- Toast Notification -->
<div id="ppToast" class="pp-toast">
    <i class="fas fa-info-circle text-warning fs-4"></i> 
    <span id="ppToastMsg">Notification</span>
</div>

<!-- Mobile PhonePe Header -->
<div class="pp-header mobile-only">
    <div class="pp-profile">
        <div class="pp-avatar">R</div>
        <div>
            <h3 class="pp-title">CHECK RESULT</h3>
            <span class="pp-subtitle">CMS Marksheet Portal</span>
        </div>
    </div>
    <button type="button" class="btn text-white p-0 fs-3" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
</div>

<!-- Drawer Overlay -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- Mobile Side Drawer Navigation -->
<div class="sidebar-drawer" id="sidebar">
    <div class="d-flex justify-content-between align-items-center mb-4 text-white">
        <h3 class="fw-bold m-0 text-white"><i class="fa fa-graduation-cap text-purple me-2"></i>CMS PORTAL</h3>
        <button class="btn text-white p-0 fs-4" onclick="toggleSidebar()"><i class="fa fa-times"></i></button>
    </div>
    <a href="index.php" class="drawer-link"><i class="fa fa-home"></i> Home</a>
    <a href="check_result.php" class="drawer-link active"><i class="fa fa-file-invoice"></i> Check Result</a>
    <a href="login.php" class="drawer-link"><i class="fa fa-right-to-bracket"></i> Login</a>
</div>

<!-- ========================================== -->
<!-- 📱 MOBILE PHONEPE INTERFACE VIEW -->
<!-- ========================================== -->
<div class="container app-wrapper mobile-only">
    
    <!-- Search Box Card -->
    <div class="main-card">
        <label class="mob-label text-center">Enter Roll Number</label>
        <form onsubmit="fetchResultAjax(event, 'mob')">
            <input type="text" id="mobRollNo" class="mob-input" placeholder="Example: 101" required>
            <button type="submit" id="mobSearchBtn" class="btn-mob-search">
                🔍 Get Result
            </button>
        </form>
    </div>

    <!-- Mobile Result Card Container -->
    <div id="mobResultContainer" style="display: none;">
        <div class="mob-result-card">
            
            <div class="mob-student-header">
                <div class="mob-st-name" id="mobStName">Student Name</div>
                <div class="mob-st-detail mt-1"><i class="fa fa-book me-1"></i> <span id="mobStCourse">Course</span></div>
                <div class="mob-st-detail"><i class="fa fa-hashtag me-1"></i> Roll No: <span id="mobStRoll">101</span></div>
                <div class="mob-st-detail"><i class="fa fa-calendar me-1"></i> Date: <span id="mobDate">--</span></div>
            </div>

            <div class="fw-bold fs-6 mb-2 text-dark">Subject Details:</div>
            
            <div id="mobSubjectsList">
                <!-- Subjects populated via JavaScript -->
            </div>

            <div class="mob-grand-total">
                <div>
                    <div class="mob-gt-text">GRAND TOTAL</div>
                    <small class="text-muted fw-bold" id="mobPercentage">0.00%</small>
                </div>
                <div class="mob-gt-text" id="mobGrandTotal">0 / 0</div>
            </div>

            <button onclick="window.print()" class="btn w-100 py-3 mt-3 btn-success fw-bold fs-6 rounded-4">
                <i class="fa fa-print me-2"></i> Print / Save Marksheet
            </button>

        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- 🖥️ DESKTOP VIEW (ELEGANT CLASSIC LOOK) -->
<!-- ========================================== -->
<div class="desktop-only">
    
    <div class="search-container">
        <h2 style="color: var(--text-dark); font-weight: 700;">Check Your Result</h2>
        <p style="color: #718096; font-size: 14px;">Enter your Roll Number to view the marksheet</p>
        
        <form onsubmit="fetchResultAjax(event, 'dt')">
            <div class="input-group">
                <input type="text" id="dtRollNo" class="desktop-input" placeholder="Example: 101" required>
            </div>
            <button type="submit" id="dtSearchBtn" class="btn-check">Show Result</button>
        </form>
    </div>

    <!-- Desktop Marksheet Container -->
    <div class="marksheet" id="dtMarksheetContainer" style="display: none;">
        <button class="print-btn" onclick="window.print()"><i class="fa fa-print"></i> Print Marksheet</button>
        
        <div class="header">
            <h1 style="margin:0; color:var(--primary); font-weight:800;">COACHING INSTITUTE NAME</h1>
            <p style="margin:5px 0; font-size: 14px; color: #64748b;">An ISO Certified Coaching Center</p>
            <h3 style="text-decoration: underline; margin-top: 10px; font-size: 18px; font-weight: 700;">STATEMENT OF MARKS</h3>
        </div>

        <div class="student-details">
            <div>
                <p><b>Student Name:</b> <span id="dtStName">--</span></p>
                <p><b>Course:</b> <span id="dtStCourse">--</span></p>
            </div>
            <div style="text-align: right;">
                <p><b>Roll Number:</b> #<span id="dtStRoll">--</span></p>
                <p><b>Date:</b> <span id="dtDate">--</span></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Subject / Paper Name</th>
                    <th>Max Marks</th>
                    <th>Obtained Marks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="dtMarksTableBody">
                <!-- Rows injected via AJAX -->
            </tbody>
        </table>

        <div style="margin-top: 50px; display: flex; justify-content: space-between;">
            <p>Date: _________</p>
            <p style="text-align: center;">____________________<br>Authorized Signatory</p>
        </div>
    </div>

</div>

<!-- Mobile Bottom Navigation Bar -->
<div class="pp-bottom-nav mobile-only">
    <a href="index.php" class="pp-nav-item"><i class="fa fa-home"></i><span>Home</span></a>
    <a href="check_result.php" class="pp-nav-item active"><i class="fa fa-file-invoice"></i><span>Result</span></a>
    <a href="javascript:void(0)" class="pp-nav-item" onclick="toggleSidebar()"><i class="fa fa-bars"></i><span>Menu</span></a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Notification Toast
function showToast(msg) {
    const toast = document.getElementById('ppToast');
    document.getElementById('ppToastMsg').innerText = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// Side Drawer Handler
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}

// 🚀 ZERO RELOAD AJAX FETCH RESULT
function fetchResultAjax(e, mode) {
    e.preventDefault();

    const rollNo = mode === 'mob' ? document.getElementById('mobRollNo').value : document.getElementById('dtRollNo').value;
    const btn = mode === 'mob' ? document.getElementById('mobSearchBtn') : document.getElementById('dtSearchBtn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Fetching Result...';

    const formData = new FormData();
    formData.append('action', 'fetch_result');
    formData.append('roll_no', rollNo);

    fetch('check_result.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = mode === 'mob' ? '🔍 Get Result' : 'Show Result';

        if(res.status === 'success') {
            renderResultData(res.student, res.marks, res.date);
            showToast("🎉 Result Loaded Successfully!");
        } else {
            showToast(res.message);
            document.getElementById('mobResultContainer').style.display = 'none';
            document.getElementById('dtMarksheetContainer').style.display = 'none';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = mode === 'mob' ? '🔍 Get Result' : 'Show Result';
        showToast("❌ Network error! Try again.");
    });
}

// Dynamic Rendering Helper
function renderResultData(student, marks, dateStr) {
    // 1. Mobile Data Population
    document.getElementById('mobStName').innerText = student.name;
    document.getElementById('mobStCourse').innerText = student.course;
    document.getElementById('mobStRoll').innerText = student.roll_no;
    document.getElementById('mobDate').innerText = dateStr;

    // 2. Desktop Data Population
    document.getElementById('dtStName').innerText = student.name;
    document.getElementById('dtStCourse').innerText = student.course;
    document.getElementById('dtStRoll').innerText = student.roll_no;
    document.getElementById('dtDate').innerText = dateStr;

    let mobSubjectsHtml = '';
    let dtTableHtml = '';
    let grandTotal = 0;
    let maxTotal = 0;

    marks.forEach(item => {
        const obtained = parseFloat(item.obtained_marks);
        const total = parseFloat(item.total_marks);
        grandTotal += obtained;
        maxTotal += total;

        const isPass = obtained >= (total * 0.33);
        const statusText = isPass ? 'Pass' : 'Fail';
        const statusColor = isPass ? '#16a34a' : '#dc2626';

        // Mobile Card List Item
        mobSubjectsHtml += `
            <div class="mob-subject-row">
                <div>
                    <div class="mob-sub-name">${item.subject_name}</div>
                    <small style="color: ${statusColor}; font-weight:800;">Status: ${statusText}</small>
                </div>
                <div class="mob-sub-marks">${obtained} / ${total}</div>
            </div>
        `;

        // Desktop Table Row
        dtTableHtml += `
            <tr>
                <td>${item.subject_name}</td>
                <td>${total}</td>
                <td>${obtained}</td>
                <td style="color: ${statusColor}; font-weight:bold;">${statusText}</td>
            </tr>
        `;
    });

    const percentage = maxTotal > 0 ? ((grandTotal / maxTotal) * 100).toFixed(2) : '0.00';

    // Desktop Total Row
    dtTableHtml += `
        <tr class="total-row">
            <td>GRAND TOTAL</td>
            <td>${maxTotal}</td>
            <td>${grandTotal}</td>
            <td>${percentage}%</td>
        </tr>
    `;

    // Inject HTML & Display
    document.getElementById('mobSubjectsList').innerHTML = mobSubjectsHtml;
    document.getElementById('mobGrandTotal').innerText = `${grandTotal} / ${maxTotal}`;
    document.getElementById('mobPercentage').innerText = `Percentage: ${percentage}%`;
    document.getElementById('mobResultContainer').style.display = 'block';

    document.getElementById('dtMarksTableBody').innerHTML = dtTableHtml;
    document.getElementById('dtMarksheetContainer').style.display = 'block';
}
</script>

</body>
</html>