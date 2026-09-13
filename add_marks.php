<?php
session_start();
include 'db_config.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$status = "";

// Marks Save karne ka logic
if(isset($_POST['save_marks'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['paper']); 
    $obtained_marks = mysqli_real_escape_string($conn, $_POST['marks']);
    $total_marks = mysqli_real_escape_string($conn, $_POST['total_marks']);

    $check = mysqli_query($conn, "SELECT id FROM marks WHERE student_id='$student_id' AND subject_name='$subject_name'");
    if(mysqli_num_rows($check) > 0) {
        $status = "<div class='alert alert-danger'>❌ Is subject ke marks pehle hi bhare ja chuke hain!</div>";
    } else {
        $query = "INSERT INTO marks (student_id, subject_name, total_marks, obtained_marks) 
                  VALUES ('$student_id', '$subject_name', '$total_marks', '$obtained_marks')";
        
        if(mysqli_query($conn, $query)) {
            $status = "<div class='alert alert-success'>✅ Marks Added Successfully!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Marks Management | CMS Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --brand-color: #1a237e; 
            --bg-light: #f0f2f5;
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3d1668;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body { 
            background-color: var(--bg-light); 
            min-height: 100vh;
        }

        /* 🖥️ DESKTOP VIEW (Width >= 992px) - UNTOUCHED & CLEAN */
        @media (min-width: 992px) {
            .navbar { background: white !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: none; }
            .card-header { background: var(--brand-color); color: white; border-radius: 20px 20px 0 0 !important; font-weight: 700; padding: 20px; font-size: 18px; }
            .form-label { font-weight: 600; font-size: 14px; color: #444; }
            .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #ddd; font-size: 14px; }
            .btn-save { background: var(--brand-color); color: white; border-radius: 10px; padding: 12px; font-weight: 700; width: 100%; transition: 0.3s; border: none; font-size: 15px; }
            .btn-save:hover { background: #0d144d; transform: translateY(-2px); }
            .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
            .search-box { border-radius: 20px; padding-left: 40px; }
            .search-icon { position: absolute; left: 15px; top: 13px; color: #888; }
            .mobile-header, .mobile-bottom-nav, .sidebar-drawer, .sidebar-overlay { display: none !important; }
        }

        /* 📱 MOBILE VIEW - PhonePe Native App UI + Bolder Readable Fonts */
        @media (max-width: 991px) {
            .desktop-navbar { display: none !important; }

            /* PhonePe Top Header */
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
                width: 42px;
                height: 42px;
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

            /* Container Adjustments */
            .mobile-content-wrapper {
                padding: 80px 14px 85px 14px !important;
            }

            /* Cards with Soft Shadow and Rounded Corners */
            .main-card {
                background: white;
                border-radius: 18px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
                border: none !important;
                margin-bottom: 20px !important;
                overflow: hidden;
            }

            .card-header {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%) !important;
                color: white !important;
                font-size: 16px !important;
                font-weight: 700 !important;
                padding: 16px !important;
                border: none !important;
            }

            /* Form Elements with LARGER readable fonts */
            .form-label {
                font-size: 14.5px !important;
                font-weight: 700 !important;
                color: var(--text-dark) !important;
                margin-bottom: 6px !important;
            }

            .form-control, .form-select {
                border-radius: 12px !important;
                padding: 13px 14px !important;
                font-size: 15px !important;
                border: 1px solid #cbd5e1 !important;
                background-color: #f8fafc !important;
                font-weight: 500 !important;
            }

            .form-control:focus, .form-select:focus {
                border-color: var(--phonepe-purple) !important;
                box-shadow: 0 0 0 3px rgba(95, 37, 159, 0.15) !important;
            }

            .btn-save {
                background: var(--phonepe-purple) !important;
                border-radius: 12px !important;
                padding: 14px !important;
                font-size: 16px !important;
                font-weight: 700 !important;
                box-shadow: 0 4px 12px rgba(95, 37, 159, 0.3) !important;
            }

            /* Recent Entries Table Section */
            .table-container {
                background: white !important;
                border-radius: 18px !important;
                padding: 16px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
            }

            .table-container h5 {
                font-size: 16px !important;
                font-weight: 700 !important;
            }

            .search-box {
                font-size: 14px !important;
                padding-left: 38px !important;
            }

            table { min-width: 480px !important; }
            th { font-size: 12.5px !important; padding: 12px !important; text-transform: uppercase; }
            td { font-size: 14.5px !important; padding: 12px !important; vertical-align: middle; }

            /* Mobile Side Drawer Menu */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.75);
                backdrop-filter: blur(3px);
                z-index: 99998;
            }
            .sidebar-overlay.active { display: block !important; }

            .sidebar-drawer {
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
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                padding: 0 !important;
                overflow-y: auto !important;
            }

            .sidebar-drawer.active { left: 0 !important; }

            .drawer-header {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%);
                padding: 20px 18px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: white;
            }

            .drawer-close-btn {
                background: rgba(255,255,255,0.2);
                border: none;
                width: 34px;
                height: 34px;
                border-radius: 50%;
                color: white;
                font-size: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .drawer-links {
                padding: 15px 10px;
            }

            .drawer-links a {
                display: flex;
                align-items: center;
                padding: 13px 16px;
                color: #94a3b8;
                text-decoration: none;
                font-size: 15px;
                font-weight: 600;
                border-radius: 10px;
                margin-bottom: 5px;
            }

            .drawer-links a i {
                width: 24px;
                margin-right: 10px;
                font-size: 17px;
            }

            .drawer-links a.active {
                background: rgba(95, 37, 159, 0.3);
                color: #a855f7;
            }

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
                box-shadow: 0 -4px 15px rgba(0,0,0,0.06);
            }

            .phonepe-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #64748b;
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

<!-- Mobile Header (PhonePe App Style) -->
<div class="mobile-header">
    <div class="mobile-profile-box">
        <div class="mobile-avatar">M</div>
        <div>
            <h3>MARKS PORTAL</h3>
            <small>Student Performance</small>
        </div>
    </div>
    <button type="button" class="menu-toggle" onclick="toggleMobileSidebar()">
        <i class="fa fa-bars"></i>
    </button>
</div>

<!-- Mobile Sidebar Drawer Backdrop -->
<div class="sidebar-overlay" id="overlay" onclick="toggleMobileSidebar()"></div>

<!-- Mobile Sidebar Drawer Navigation -->
<div class="sidebar-drawer" id="sidebar">
    <div class="drawer-header">
        <h4 class="m-0 fw-bold fs-5">CMS PRO</h4>
        <button type="button" class="drawer-close-btn" onclick="toggleMobileSidebar()"><i class="fa fa-times"></i></button>
    </div>
    <div class="drawer-links">
        <a href="admin_dashboard.php"><i class="fa fa-gauge"></i> Dashboard</a>
        <a href="manage_students.php"><i class="fa fa-users"></i> Students</a>
        <a href="manage_courses.php"><i class="fa fa-graduation-cap"></i> Courses</a>
        <a href="manage_marks.php" class="active"><i class="fa fa-pen-nib"></i> Marks Entry</a>
        <a href="admin_upload_video.php"><i class="fa fa-video"></i> Videos</a>
        <a href="admin_ebook.php"><i class="fa fa-book"></i> E-Books</a>
        <a href="logout.php" style="color: #ef4444; margin-top: 30px;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Desktop Top Navbar -->
<nav class="navbar navbar-expand-lg mb-4 desktop-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php" style="color: var(--brand-color);">
            <i class="fas fa-arrow-left me-2"></i> Admin Panel
        </a>
    </div>
</nav>

<div class="container mobile-content-wrapper mb-5">
    <div class="row">
        <!-- Marks Form Column -->
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card main-card">
                <div class="card-header"><i class="fas fa-pen-nib me-2"></i> Enter Student Marks</div>
                <div class="card-body p-3 p-lg-4">
                    <?php echo $status; ?>
                    <form method="POST" id="marksForm">
                        <div class="mb-3">
                            <label class="form-label">Student Name</label>
                            <select name="student_id" id="studentSelect" class="form-select" onchange="loadAvailablePapers()" required>
                                <option value="">-- Select Student --</option>
                                <?php
                                $students = mysqli_query($conn, "SELECT s.id, u.name, s.roll_no FROM students s JOIN users u ON s.user_id = u.id ORDER BY u.name ASC");
                                while($st = mysqli_fetch_assoc($students)) {
                                    echo "<option value='{$st['id']}'>{$st['name']} (#{$st['roll_no']})</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Course Category</label>
                            <select id="mainCourse" class="form-select" onchange="loadAvailablePapers()" required>
                                <option value="">-- Select Course --</option>
                                <option value="DCA">DCA</option>
                                <option value="PGDCA">PGDCA</option>
                                <option value="TALLY">Tally Prime</option>
                                <option value="SCHOOL">School Subjects</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Available Paper</label>
                            <select id="paperSelect" name="paper" class="form-select" required>
                                <option value="">-- Select Student & Course --</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Obtained Marks</label>
                                <input type="number" name="marks" class="form-control" placeholder="00" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Total Marks</label>
                                <input type="number" name="total_marks" class="form-control" value="100" required>
                            </div>
                        </div>

                        <button type="submit" name="save_marks" class="btn-save mt-3">SAVE RECORD</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Marks Table Column -->
        <div class="col-lg-7">
            <div class="table-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0"><i class="fas fa-list text-primary me-2"></i> Recent Entries</h5>
                    <div class="position-relative" style="max-width: 200px;">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="tableSearch" class="form-control search-box" placeholder="Search...">
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 450px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="marksTable">
                            <?php
                            $list = mysqli_query($conn, "SELECT m.*, u.name FROM marks m JOIN students s ON m.student_id = s.id JOIN users u ON s.user_id = u.id ORDER BY m.id DESC");
                            if(mysqli_num_rows($list) > 0) {
                                while($row = mysqli_fetch_assoc($list)) {
                                    echo "<tr>
                                        <td><span class='fw-bold text-dark'>{$row['name']}</span></td>
                                        <td><span class='text-muted'>{$row['subject_name']}</span></td>
                                        <td><span class='badge bg-success px-2 py-1 fs-6'>{$row['obtained_marks']} / {$row['total_marks']}</span></td>
                                        <td style='text-align: center;'><a href='delete_mark.php?id={$row['id']}' class='text-danger fs-5' onclick='return confirm(\"Kya aap ise delete karna chahte hain?\")'><i class='fas fa-trash-alt'></i></a></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center text-muted py-4'>Koi marks record nahi mila.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="manage_students.php" class="phonepe-nav-item">
        <i class="fa fa-user-graduate"></i>
        <span>Students</span>
    </a>
    <a href="manage_marks.php" class="phonepe-nav-item active">
        <i class="fa fa-pen-nib"></i>
        <span>Marks</span>
    </a>
    <a href="admin_upload_video.php" class="phonepe-nav-item">
        <i class="fa fa-play-circle"></i>
        <span>Videos</span>
    </a>
    <a href="javascript:void(0)" class="phonepe-nav-item" onclick="toggleMobileSidebar()">
        <i class="fa fa-bars"></i>
        <span>Menu</span>
    </a>
</div>

<script>
const courseData = {
    "DCA": ["Fundamental of Computers", "PC Package (Word, Excel, PPT)", "MS Access / FoxPro", "IT Trends", "Internet & E-Commerce"],
    "PGDCA": ["Fundamentals of IT", "PC Package", "Database Using MS Access", "Fundamentals of Multimedia", "Programming with VB.Net"],
    "TALLY": ["Basics of Accounting", "Voucher Entry", "GST & Taxation", "Payroll Management", "Tally Final Exam"],
    "SCHOOL": ["Mathematics", "Science", "English", "Hindi", "Social Science", "Sanskrit"]
};

function loadAvailablePapers() {
    const studentId = document.getElementById("studentSelect").value;
    const course = document.getElementById("mainCourse").value;
    const paperSelect = document.getElementById("paperSelect");

    if(!studentId || !course) {
        paperSelect.innerHTML = '<option value="">-- Select Student & Course --</option>';
        return;
    }

    // Fetch filled papers using AJAX
    fetch('fetch_filled_papers.php?student_id=' + studentId)
    .then(response => {
        if (!response.ok) throw new Error('File not found');
        return response.json();
    })
    .then(filledPapers => {
        populateSelect(filledPapers);
    })
    .catch(error => {
        populateSelect([]); 
    });

    function populateSelect(filled) {
        paperSelect.innerHTML = '<option value="">-- Choose Paper --</option>';
        const allPapers = courseData[course];
        
        let availableCount = 0;
        if(allPapers) {
            allPapers.forEach(paper => {
                if(!filled.includes(paper)) {
                    let opt = document.createElement("option");
                    opt.value = paper;
                    opt.text = paper;
                    paperSelect.appendChild(opt);
                    availableCount++;
                }
            });
        }
        
        if(availableCount === 0) {
            paperSelect.innerHTML = '<option value="">✅ Is course ke sabhi marks bhare ja chuke hain</option>';
        }
    }
}

// Search Filter in Recent Entries Table
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#marksTable tr');
    rows.forEach(row => {
        row.style.display = (row.innerText.toLowerCase().indexOf(value) > -1) ? '' : 'none';
    });
});

// Toggle Mobile Sidebar Drawer Function
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