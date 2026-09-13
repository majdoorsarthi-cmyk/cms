<?php
session_start();
include 'db_config.php';

// Admin Auth Check
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Auto Create Table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_name VARCHAR(150) NOT NULL,
    total_marks INT NOT NULL DEFAULT 100,
    obtained_marks INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ==========================================
// 🚀 PURE AJAX API HANDLERS (ZERO RELOAD)
// ==========================================

// 1. SAVE NEW MARKS
if(isset($_POST['action']) && $_POST['action'] == 'save_marks') {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['paper']); 
    $obtained_marks = mysqli_real_escape_string($conn, $_POST['marks']);
    $total_marks = mysqli_real_escape_string($conn, $_POST['total_marks']);

    $check = mysqli_query($conn, "SELECT id FROM marks WHERE student_id='$student_id' AND subject_name='$subject_name'");
    if(mysqli_num_rows($check) > 0) {
        echo json_encode(['status' => 'error', 'message' => '⚠️ Is subject ke marks pehle hi bhare ja chuke hain!']);
    } else {
        $query = "INSERT INTO marks (student_id, subject_name, total_marks, obtained_marks) 
                  VALUES ('$student_id', '$subject_name', '$total_marks', '$obtained_marks')";
        if(mysqli_query($conn, $query)) {
            $new_id = mysqli_insert_id($conn);
            $st_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT u.name FROM students s JOIN users u ON s.user_id = u.id WHERE s.id='$student_id'"));
            
            echo json_encode([
                'status' => 'success', 
                'message' => '✅ Marks Successfully Added!',
                'data' => [
                    'id' => $new_id,
                    'name' => $st_res['name'],
                    'subject' => $subject_name,
                    'obtained' => $obtained_marks,
                    'total' => $total_marks,
                    'date' => date('d M, h:i A')
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => '❌ Database Error! Save nahi ho saka.']);
        }
    }
    exit();
}

// 2. GET SINGLE MARK DETAILS FOR EDIT MODAL
if(isset($_POST['action']) && $_POST['action'] == 'get_mark') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $res = mysqli_query($conn, "SELECT m.*, u.name FROM marks m JOIN students s ON m.student_id = s.id JOIN users u ON s.user_id = u.id WHERE m.id='$id'");
    if($row = mysqli_fetch_assoc($res)) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}

// 3. UPDATE MARKS
if(isset($_POST['action']) && $_POST['action'] == 'update_marks') {
    $id = mysqli_real_escape_string($conn, $_POST['edit_id']);
    $obtained_marks = mysqli_real_escape_string($conn, $_POST['edit_marks']);
    $total_marks = mysqli_real_escape_string($conn, $_POST['edit_total_marks']);

    if(mysqli_query($conn, "UPDATE marks SET obtained_marks='$obtained_marks', total_marks='$total_marks' WHERE id='$id'")) {
        echo json_encode(['status' => 'success', 'message' => '✅ Marks Record Updated!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Update Fail Ho Gaya!']);
    }
    exit();
}

// 4. DELETE RECORD
if(isset($_POST['action']) && $_POST['action'] == 'delete_mark') {
    $del_id = mysqli_real_escape_string($conn, $_POST['id']);
    if(mysqli_query($conn, "DELETE FROM marks WHERE id='$del_id'")) {
        echo json_encode(['status' => 'success', 'message' => '🗑️ Record Deleted!']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PhonePe Style Marks Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --pp-purple: #5f259f;
            --pp-dark: #3d1668;
            --pp-bg: #f3f4f9;
            --pp-green: #059669;
            --pp-card-bg: #ffffff;
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

        /* PhonePe Toast Notification */
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
            max-width: 400px;
            justify-content: center;
        }
        .pp-toast.show { top: 25px; }

        /* 🖥️ DESKTOP VIEW (>= 992px) - UNTOUCHED & PREMIUM */
        @media (min-width: 992px) {
            .mobile-only { display: none !important; }
            .navbar { background: white !important; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
            .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: none; }
            .card-header { background: #1a237e; color: white; border-radius: 20px 20px 0 0 !important; font-weight: 700; padding: 20px; font-size: 18px; }
            .form-label { font-weight: 600; font-size: 14px; color: #333; }
            .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #ddd; font-size: 14px; }
            .btn-save { background: #1a237e; color: white; border-radius: 10px; padding: 13px; font-weight: 700; width: 100%; transition: 0.3s; border: none; font-size: 15px; }
            .table-container { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        }

        /* 📱 MOBILE PHONEPE NATIVE INTERFACE (< 992px) - ENHANCED BOLD FONTS */
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
                box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            }
            .pp-title { color: white; margin: 0; font-size: 20px; font-weight: 900; letter-spacing: -0.3px; }
            .pp-subtitle { color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 700; }

            .app-wrapper { padding: 90px 14px 85px 14px !important; }

            /* Mobile Tab Switcher (PhonePe Segmented Pills) */
            .pp-segmented-tabs {
                background: #e2e8f0; border-radius: 16px; padding: 5px;
                display: flex; gap: 6px; margin-bottom: 20px;
            }
            .pp-tab-btn {
                flex: 1; border: none; background: transparent; padding: 12px;
                border-radius: 12px; font-size: 16px; font-weight: 800; color: #475569;
                transition: 0.2s;
            }
            .pp-tab-btn.active {
                background: white; color: var(--pp-purple);
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            }

            /* Filter Chips */
            .filter-chips-wrapper {
                display: flex; gap: 10px; overflow-x: auto; padding-bottom: 12px;
                margin-bottom: 14px; scrollbar-width: none;
            }
            .filter-chip {
                background: white; border: 2px solid #cbd5e1; border-radius: 25px;
                padding: 8px 18px; font-size: 14px; font-weight: 800; color: #334155;
                white-space: nowrap; cursor: pointer; transition: 0.2s;
            }
            .filter-chip.active {
                background: var(--pp-purple); color: white; border-color: var(--pp-purple);
            }

            /* BOLD & READABLE MOBILE INPUTS */
            .form-label {
                font-size: 17px !important; font-weight: 800 !important;
                color: #0f172a !important; margin-bottom: 8px !important;
            }
            .form-control, .form-select {
                border-radius: 16px !important; padding: 16px 18px !important;
                font-size: 18px !important; font-weight: 800 !important;
                border: 2px solid #cbd5e1 !important; background: white !important;
                color: var(--text-main) !important;
            }
            .form-control:focus, .form-select:focus {
                border-color: var(--pp-purple) !important;
                box-shadow: 0 0 0 4px rgba(95, 37, 159, 0.2) !important;
            }
            .btn-save {
                background: linear-gradient(135deg, var(--pp-purple) 0%, var(--pp-dark) 100%) !important;
                border-radius: 18px !important; padding: 18px !important;
                font-size: 19px !important; font-weight: 900 !important;
                color: white !important; border: none !important; width: 100%;
                box-shadow: 0 8px 22px rgba(95, 37, 159, 0.35) !important;
                letter-spacing: 0.5px;
            }

            /* PhonePe Transaction Cards Style */
            .pp-card-item {
                background: white; border-radius: 20px; padding: 18px;
                margin-bottom: 14px; border: 1px solid #e2e8f0;
                box-shadow: 0 4px 18px rgba(0,0,0,0.04);
                display: flex; align-items: center; justify-content: space-between;
            }
            .pp-card-left { display: flex; align-items: center; gap: 16px; }
            .pp-card-icon {
                width: 52px; height: 52px; border-radius: 16px;
                background: rgba(95, 37, 159, 0.12); color: var(--pp-purple);
                display: flex; align-items: center; justify-content: center;
                font-size: 22px; font-weight: 900; flex-shrink: 0;
            }
            .pp-card-name { font-size: 18px; font-weight: 900; color: #0f172a; margin: 0; }
            .pp-card-sub { font-size: 15px; font-weight: 700; color: #475569; margin: 2px 0; }
            .pp-card-date { font-size: 13px; color: #64748b; font-weight: 600; }
            .pp-card-badge {
                font-size: 18px; font-weight: 900; color: var(--pp-green);
                background: rgba(5, 150, 105, 0.12); padding: 8px 14px;
                border-radius: 14px; text-align: right; white-space: nowrap;
            }

            /* Card Buttons */
            .pp-card-actions { display: flex; gap: 10px; margin-top: 10px; }
            .pp-action-btn {
                border: none; background: #f1f5f9; border-radius: 10px;
                padding: 8px 14px; font-size: 14px; font-weight: 800; color: #334155;
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
                text-decoration: none; margin-bottom: 8px; transition: 0.2s;
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
    </style>
</head>
<body>

<!-- PhonePe Toast Popup -->
<div id="ppToast" class="pp-toast"><i class="fas fa-check-circle text-success fs-4"></i> <span id="ppToastMsg">Notification</span></div>

<!-- Mobile Header Bar -->
<div class="pp-header mobile-only">
    <div class="pp-profile">
        <div class="pp-avatar">M</div>
        <div>
            <h3 class="pp-title">MARKS PORTAL</h3>
            <span class="pp-subtitle">Admin Super-App Mode</span>
        </div>
    </div>
    <button type="button" class="btn text-white p-0 fs-3" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
</div>

<!-- Drawer Backdrop -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- Mobile Side Drawer -->
<div class="sidebar-drawer" id="sidebar">
    <div class="d-flex justify-content-between align-items-center mb-4 text-white">
        <h3 class="fw-black m-0 text-white"><i class="fa fa-shield-halved text-purple me-2"></i>CMS PRO</h3>
        <button class="btn text-white p-0 fs-4" onclick="toggleSidebar()"><i class="fa fa-times"></i></button>
    </div>
    <a href="admin_dashboard.php" class="drawer-link"><i class="fa fa-gauge"></i> Dashboard</a>
    <a href="manage_students.php" class="drawer-link"><i class="fa fa-users"></i> Students</a>
    <a href="manage_courses.php" class="drawer-link"><i class="fa fa-graduation-cap"></i> Courses</a>
    <a href="javascript:void(0)" class="drawer-link active" onclick="toggleSidebar()"><i class="fa fa-pen-nib"></i> Marks Entry</a>
    <a href="logout.php" class="drawer-link text-danger mt-4"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Desktop Top Navbar -->
<nav class="navbar navbar-expand-lg mb-4 desktop-only">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="admin_dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Admin Panel
        </a>
    </div>
</nav>

<div class="container app-wrapper mb-5">
    
    <!-- Mobile Segmented Tab Switcher -->
    <div class="pp-segmented-tabs mobile-only">
        <button class="pp-tab-btn active" id="tabBtnForm" onclick="switchMobileTab('form')">📝 Add Marks</button>
        <button class="pp-tab-btn" id="tabBtnHist" onclick="switchMobileTab('history')">📜 Records (<span id="mobCount">0</span>)</button>
    </div>

    <div class="row">
        <!-- FORM SECTION -->
        <div class="col-lg-5 mb-4" id="formTabSection">
            <div class="card main-card">
                <div class="card-header desktop-only"><i class="fas fa-pen-nib me-2"></i> Enter Student Marks</div>
                <div class="card-body p-3 p-lg-4">

                    <!-- AJAX Form -->
                    <form id="marksForm">
                        <div class="mb-3">
                            <label class="form-label">Student Name</label>
                            <select name="student_id" id="studentSelect" class="form-select" onchange="loadAvailablePapers()" required>
                                <option value="">-- Choose Student --</option>
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
                                <option value="">-- Choose Course --</option>
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
                                <label class="form-label">Obtained</label>
                                <input type="number" name="marks" id="inputMarks" class="form-control" placeholder="00" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Total</label>
                                <input type="number" name="total_marks" class="form-control" value="100" required>
                            </div>
                        </div>

                        <button type="submit" id="saveBtn" class="btn-save mt-2">
                            <i class="fas fa-check-circle me-2"></i> SAVE MARKS RECORD
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- HISTORY / RECORDS SECTION -->
        <div class="col-lg-7 d-none d-lg-block" id="historyTabSection">
            
            <!-- Mobile Filter Chips -->
            <div class="filter-chips-wrapper mobile-only">
                <div class="filter-chip active" onclick="filterByCourse('ALL', this)">All Courses</div>
                <div class="filter-chip" onclick="filterByCourse('DCA', this)">DCA</div>
                <div class="filter-chip" onclick="filterByCourse('PGDCA', this)">PGDCA</div>
                <div class="filter-chip" onclick="filterByCourse('Tally', this)">Tally</div>
            </div>

            <!-- Desktop Table View -->
            <div class="table-container desktop-only">
                <h5 class="fw-bold mb-3"><i class="fas fa-list text-primary me-2"></i> Recent Entries</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Subject</th><th>Marks</th><th class="text-center">Action</th></tr>
                        </thead>
                        <tbody id="desktopTableBody">
                            <?php
                            $list = mysqli_query($conn, "SELECT m.*, u.name FROM marks m JOIN students s ON m.student_id = s.id JOIN users u ON s.user_id = u.id ORDER BY m.id DESC");
                            while($row = mysqli_fetch_assoc($list)) {
                                echo "<tr id='d_row_{$row['id']}'>
                                    <td><b>{$row['name']}</b></td>
                                    <td class='text-muted'>{$row['subject_name']}</td>
                                    <td><span class='badge bg-success px-2 py-1 fs-6'>{$row['obtained_marks']}/{$row['total_marks']}</span></td>
                                    <td class='text-center'>
                                        <button class='btn btn-sm btn-outline-primary me-1' onclick='openEditModal({$row['id']})'><i class='fa fa-pen'></i></button>
                                        <button class='btn btn-sm btn-outline-danger' onclick='deleteRecord({$row['id']})'><i class='fa fa-trash'></i></button>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile PhonePe Cards List (Safe Array Key Check) -->
            <div class="mobile-only" id="mobileCardsContainer">
                <?php
                if($list && mysqli_num_rows($list) > 0) {
                    mysqli_data_seek($list, 0);
                    while($row = mysqli_fetch_assoc($list)) {
                        $first_letter = strtoupper(substr($row['name'], 0, 1));
                        
                        // SAFE KEY CHECK TO PREVENT WARNING & RELOAD
                        $created_at_val = $row['created_at'] ?? date('Y-m-d H:i:s');
                        $formatted_date = date('d M, h:i A', strtotime($created_at_val));
                        
                        echo "<div class='pp-card-item' id='m_card_{$row['id']}' data-subject='{$row['subject_name']}'>
                            <div class='pp-card-left'>
                                <div class='pp-card-icon'>{$first_letter}</div>
                                <div>
                                    <h4 class='pp-card-name'>{$row['name']}</h4>
                                    <p class='pp-card-sub'>{$row['subject_name']}</p>
                                    <span class='pp-card-date'><i class='far fa-clock me-1'></i>{$formatted_date}</span>
                                    <div class='pp-card-actions'>
                                        <button class='pp-action-btn' onclick='openEditModal({$row['id']})'><i class='fa fa-pen me-1'></i>Edit</button>
                                        <button class='pp-action-btn text-danger' onclick='deleteRecord({$row['id']})'><i class='fa fa-trash me-1'></i>Delete</button>
                                    </div>
                                </div>
                            </div>
                            <div class='pp-card-badge'>{$row['obtained_marks']}/{$row['total_marks']}</div>
                        </div>";
                    }
                }
                ?>
            </div>

        </div>
    </div>
</div>

<!-- AJAX Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content style-modal" style="border-radius: 20px;">
            <div class="modal-header" style="background: var(--pp-purple); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Update Marks Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm">
                    <input type="hidden" name="edit_id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" id="editStudentName" class="form-control" readonly disabled style="background:#f1f5f9;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" id="editSubject" class="form-control" readonly disabled style="background:#f1f5f9;">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Obtained</label>
                            <input type="number" name="edit_marks" id="editObtained" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Total Marks</label>
                            <input type="number" name="edit_total_marks" id="editTotal" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-save mt-2"><i class="fas fa-save me-2"></i> UPDATE NOW</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="pp-bottom-nav mobile-only">
    <a href="admin_dashboard.php" class="pp-nav-item"><i class="fa fa-home"></i><span>Home</span></a>
    <a href="manage_students.php" class="pp-nav-item"><i class="fa fa-user-graduate"></i><span>Students</span></a>
    <a href="javascript:void(0)" class="pp-nav-item active"><i class="fa fa-pen-nib"></i><span>Marks</span></a>
    <a href="javascript:void(0)" class="pp-nav-item" onclick="toggleSidebar()"><i class="fa fa-bars"></i><span>Menu</span></a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const courseData = {
    "DCA": ["Fundamental of Computers", "PC Package (Word, Excel, PPT)", "MS Access / FoxPro", "IT Trends", "Internet & E-Commerce"],
    "PGDCA": ["Fundamentals of IT", "PC Package", "Database Using MS Access", "Fundamentals of Multimedia", "Programming with VB.Net"],
    "TALLY": ["Basics of Accounting", "Voucher Entry", "GST & Taxation", "Payroll Management", "Tally Final Exam"],
    "SCHOOL": ["Mathematics", "Science", "English", "Hindi", "Social Science", "Sanskrit"]
};

let editBsModal;
document.addEventListener("DOMContentLoaded", () => {
    editBsModal = new bootstrap.Modal(document.getElementById('editModal'));
    updateRecordCount();
});

// Toast Notifier
function showToast(msg) {
    const toast = document.getElementById('ppToast');
    document.getElementById('ppToastMsg').innerText = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// Filter Papers Dynamic
function loadAvailablePapers() {
    const studentId = document.getElementById("studentSelect").value;
    const course = document.getElementById("mainCourse").value;
    const paperSelect = document.getElementById("paperSelect");

    if(!studentId || !course) {
        paperSelect.innerHTML = '<option value="">-- Select Student & Course --</option>';
        return;
    }

    fetch('fetch_filled_papers.php?student_id=' + studentId)
    .then(res => res.json())
    .then(filledPapers => populateSelect(filledPapers))
    .catch(() => populateSelect([]));

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
            paperSelect.innerHTML = '<option value="">✅ All papers graded for this student</option>';
        }
    }
}

// 100% PURE AJAX SAVE (NO RELOAD)
document.getElementById('marksForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SAVING...';

    const formData = new FormData(this);
    formData.append('action', 'save_marks');

    fetch('manage_marks.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            showToast(res.message);
            document.getElementById('inputMarks').value = '';
            loadAvailablePapers();

            // Append live card to Mobile History
            const firstLetter = res.data.name.charAt(0).toUpperCase();
            const newCardHTML = `
                <div class='pp-card-item' id='m_card_${res.data.id}' data-subject='${res.data.subject}'>
                    <div class='pp-card-left'>
                        <div class='pp-card-icon'>${firstLetter}</div>
                        <div>
                            <h4 class='pp-card-name'>${res.data.name}</h4>
                            <p class='pp-card-sub'>${res.data.subject}</p>
                            <span class='pp-card-date'><i class='far fa-clock me-1'></i>${res.data.date}</span>
                            <div class='pp-card-actions'>
                                <button class='pp-action-btn' onclick='openEditModal(${res.data.id})'><i class='fa fa-pen me-1'></i>Edit</button>
                                <button class='pp-action-btn text-danger' onclick='deleteRecord(${res.data.id})'><i class='fa fa-trash me-1'></i>Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class='pp-card-badge'>${res.data.obtained}/${res.data.total}</div>
                </div>`;
            document.getElementById('mobileCardsContainer').insertAdjacentHTML('afterbegin', newCardHTML);
            
            // Append row to desktop table
            const newRowHTML = `
                <tr id='d_row_${res.data.id}'>
                    <td><b>${res.data.name}</b></td>
                    <td class='text-muted'>${res.data.subject}</td>
                    <td><span class='badge bg-success px-2 py-1 fs-6'>${res.data.obtained}/${res.data.total}</span></td>
                    <td class='text-center'>
                        <button class='btn btn-sm btn-outline-primary me-1' onclick='openEditModal(${res.data.id})'><i class='fa fa-pen'></i></button>
                        <button class='btn btn-sm btn-outline-danger' onclick='deleteRecord(${res.data.id})'><i class='fa fa-trash'></i></button>
                    </td>
                </tr>`;
            document.getElementById('desktopTableBody').insertAdjacentHTML('afterbegin', newRowHTML);

            updateRecordCount();
        } else {
            showToast(res.message);
        }
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> SAVE MARKS RECORD';
    });
});

// OPEN EDIT MODAL (AJAX FETCH)
function openEditModal(id) {
    const formData = new FormData();
    formData.append('action', 'get_mark');
    formData.append('id', id);

    fetch('manage_marks.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            document.getElementById('editId').value = res.data.id;
            document.getElementById('editStudentName').value = res.data.name;
            document.getElementById('editSubject').value = res.data.subject_name;
            document.getElementById('editObtained').value = res.data.obtained_marks;
            document.getElementById('editTotal').value = res.data.total_marks;
            editBsModal.show();
        }
    });
}

// UPDATE MARKS (AJAX)
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update_marks');

    fetch('manage_marks.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            showToast(res.message);
            editBsModal.hide();
            
            const id = document.getElementById('editId').value;
            const newObtained = document.getElementById('editObtained').value;
            const newTotal = document.getElementById('editTotal').value;

            // Update DOM
            const mCard = document.querySelector(`#m_card_${id} .pp-card-badge`);
            if(mCard) mCard.innerText = `${newObtained}/${newTotal}`;

            const dRow = document.querySelector(`#d_row_${id} .badge`);
            if(dRow) dRow.innerText = `${newObtained}/${newTotal}`;
        }
    });
});

// DELETE RECORD (PURE AJAX)
function deleteRecord(id) {
    if(confirm("Kya aap is record ko delete karna chahte hain?")) {
        const formData = new FormData();
        formData.append('action', 'delete_mark');
        formData.append('id', id);

        fetch('manage_marks.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                showToast("🗑️ Record Deleted!");
                const mCard = document.getElementById('m_card_' + id);
                if(mCard) mCard.remove();

                const dRow = document.getElementById('d_row_' + id);
                if(dRow) dRow.remove();

                updateRecordCount();
            }
        });
    }
}

// Switch Mobile Tabs (Pure JS, No Reload)
function switchMobileTab(tab) {
    const formSec = document.getElementById('formTabSection');
    const histSec = document.getElementById('historyTabSection');
    const btnForm = document.getElementById('tabBtnForm');
    const btnHist = document.getElementById('tabBtnHist');

    if(tab === 'form') {
        formSec.classList.remove('d-none');
        histSec.classList.add('d-none', 'd-lg-block');
        btnForm.classList.add('active');
        btnHist.classList.remove('active');
    } else {
        formSec.classList.add('d-none');
        histSec.classList.remove('d-none', 'd-lg-block');
        btnHist.classList.add('active');
        btnForm.classList.remove('active');
    }
}

// Filter Cards By Course Filter Chips
function filterByCourse(course, el) {
    document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');

    const cards = document.querySelectorAll('.pp-card-item');
    cards.forEach(card => {
        if(course === 'ALL') {
            card.style.display = 'flex';
        } else {
            const subj = card.getAttribute('data-subject').toLowerCase();
            card.style.display = subj.includes(course.toLowerCase()) ? 'flex' : 'none';
        }
    });
}

function updateRecordCount() {
    const cnt = document.querySelectorAll('.pp-card-item').length;
    document.getElementById('mobCount').innerText = cnt;
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}
</script>
</body>
</html>