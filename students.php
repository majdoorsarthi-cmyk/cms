<?php
session_start();
include 'db_config.php';

// Security: Admin Check[cite: 13]
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// --- DELETE STUDENT LOGIC (PRG Pattern to fix reload loop) ---[cite: 13]
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Marks delete query[cite: 13]
    mysqli_query($conn, "DELETE FROM marks WHERE student_id = '$id'");
    $del = mysqli_query($conn, "DELETE FROM students WHERE id = '$id'");
    
    if($del) {
        // Clean URL Redirect to stop browser reload warning
        header("Location: students.php?msg=deleted");
        exit();
    }
}

// Flash Message Handling
$message = "";
if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $message = "<div class='alert success'>✅ Student record deleted successfully!</div>";
}

// --- FETCH ALL STUDENTS (GET Method for Smooth Search) ---[cite: 13]
$search_query = "";
$search_text = "";
if(!empty($_GET['search_text'])) {
    $search_text = mysqli_real_escape_string($conn, $_GET['search_text']);
    $search_query = " WHERE u.name LIKE '%$search_text%' OR s.roll_no LIKE '%$search_text%'";
}

$sql = "SELECT s.*, u.name, u.email, u.mobile FROM students s 
        JOIN users u ON s.user_id = u.id 
        $search_query 
        ORDER BY s.id DESC";
$result = mysqli_query($conn, $sql);

// Store rows array for both Desktop Table & Mobile Cards
$students_data = [];
if($result && mysqli_num_rows($result) > 0) {
    while($r = mysqli_fetch_assoc($result)) {
        $students_data[] = $r;
    }
}
$total_students = count($students_data);
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Students | CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --primary: #4361ee;
            --secondary: #718096;
            --success: #2dce89;
            --danger: #f5365c;
            --bg: #f4f7fe;
            --white: #ffffff;
            --text-dark: #2b3674;

            /* PhonePe Mobile App Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3d1668;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-dark); font-family: 'Poppins', sans-serif; min-height: 100vh; }

        /* 🖥️ DESKTOP STYLES (SIDEBAR + CLEAN LAYOUT) */
        .sidebar { 
            width: 260px; height: 100vh; background: var(--sidebar-bg); 
            position: fixed; top: 0; left: 0; color: white; padding: 20px; z-index: 100;
        }
        .logo { font-size: 20px; font-weight: 700; margin-bottom: 40px; text-align: center; color: var(--primary); border-bottom: 1px solid #334155; padding-bottom: 15px; }
        .nav-links { list-style: none; }
        .nav-links li { margin: 8px 0; }
        .nav-links a { color: #cbd5e1; text-decoration: none; display: flex; align-items: center; padding: 12px; border-radius: 8px; transition: 0.3s; font-size: 14px; }
        .nav-links a:hover, .nav-links a.active { background: var(--primary); color: white; }
        .nav-links i { margin-right: 12px; width: 20px; font-size: 16px; }

        .main-wrapper { margin-left: 260px; width: calc(100% - 260px); }

        .top-nav {
            background: var(--white);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .container { padding: 30px 40px; }

        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-add { 
            background: var(--primary); color: white; padding: 12px 24px; 
            border-radius: 12px; text-decoration: none; font-weight: 600; 
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3); transition: 0.3s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4); }

        .search-box {
            background: var(--white);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
        }
        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #e0e5f2;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
        }
        .btn-search {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .table-container {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; padding: 15px; 
            color: #a3aed0; font-size: 13px; 
            text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid #f4f7fe;
        }
        td { padding: 18px 15px; border-bottom: 1px solid #f4f7fe; font-size: 14px; font-weight: 500; }
        
        .student-info { display: flex; align-items: center; gap: 12px; }
        .avatar { 
            width: 40px; height: 40px; background: #e0e5f2; 
            border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; color: var(--primary); font-weight: bold;
        }

        .badge { padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .badge-roll { background: #eef2ff; color: var(--primary); }

        .action-btns { display: flex; gap: 8px; }
        .btn-icon {
            width: 35px; height: 35px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.3s;
        }
        .edit { background: #eef2ff; color: var(--primary); }
        .delete { background: #fff5f5; color: var(--danger); }
        .btn-icon:hover { transform: scale(1.1); }

        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        /* Hide Mobile Elements on Desktop */
        .mobile-header, .mobile-bottom-nav, .mobile-student-cards, .mobile-add-fab, .mobile-search-form { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Full Native UI + Bigger Readable Fonts) */
        @media (max-width: 991px) {
            body { background: var(--phonepe-bg) !important; }
            .sidebar, .top-nav, .header-flex, .search-box, .table-container { display: none !important; }

            .main-wrapper { margin-left: 0 !important; width: 100% !important; }
            .container { padding: 78px 12px 90px 12px !important; }

            /* PhonePe Purple Top App Header */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 65px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 12px; }
            .mobile-back-btn {
                color: #ffffff;
                font-size: 20px;
                text-decoration: none;
                width: 38px; height: 38px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 18px !important; font-weight: 700; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 12px !important; display: block; }

            .mobile-count-pill {
                background: rgba(255,255,255,0.2);
                color: #ffffff;
                font-size: 12.5px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                border: 1px solid rgba(255,255,255,0.3);
            }

            /* Mobile Search Bar */
            .mobile-search-form {
                display: flex !important;
                gap: 8px;
                margin-bottom: 14px;
            }
            .mobile-search-form input {
                flex: 1;
                padding: 12px 16px;
                border: 1.5px solid #cbd5e1;
                border-radius: 14px;
                outline: none;
                font-size: 15px;
                font-family: 'Poppins', sans-serif;
                background: #ffffff;
            }
            .mobile-search-form input:focus { border-color: var(--phonepe-purple); }
            .mobile-search-btn {
                background: var(--phonepe-purple);
                color: white;
                border: none;
                padding: 0 18px;
                border-radius: 14px;
                font-weight: 600;
                font-size: 15px;
                cursor: pointer;
            }

            /* PhonePe Transaction Card Style */
            .mobile-student-cards {
                display: flex !important;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-stu-card {
                background: #ffffff;
                border-radius: 18px;
                padding: 16px;
                box-shadow: 0 3px 12px rgba(0,0,0,0.03);
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .phonepe-card-top {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .phonepe-avatar {
                width: 52px; height: 52px;
                background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
                color: var(--phonepe-purple);
                border-radius: 16px;
                display: flex; align-items: center; justify-content: center;
                font-size: 22px;
                font-weight: 800;
                flex-shrink: 0;
            }

            .phonepe-info { flex-grow: 1; }

            /* BIGGER READABLE FONTS FOR MOBILE */
            .phonepe-info h4 {
                font-size: 18.5px !important; /* Bold Large Name */
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2px;
            }

            .phonepe-info p {
                font-size: 14px !important;
                color: #64748b;
                margin: 0;
                font-weight: 500;
            }

            .phonepe-meta-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8fafc;
                padding: 10px 14px;
                border-radius: 12px;
            }

            .phonepe-roll-tag {
                background: #e0e7ff;
                color: #3730a3;
                font-weight: 700;
                font-size: 13.5px !important;
                padding: 4px 10px;
                border-radius: 8px;
            }

            .phonepe-mobile-tag {
                color: #334155;
                font-weight: 600;
                font-size: 13.5px !important;
            }

            /* Mobile Action Buttons Bar */
            .phonepe-actions {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 8px;
                margin-top: 2px;
            }

            .phonepe-btn {
                padding: 10px !important;
                border-radius: 12px !important;
                font-size: 14.5px !important; /* Large Font Buttons */
                font-weight: 700 !important;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                border: none;
            }

            .phonepe-btn-edit { background: #e0e7ff !important; color: #4361ee !important; }
            .phonepe-btn-report { background: #e6fffa !important; color: #0d9488 !important; }
            .phonepe-btn-delete { background: #fee2e2 !important; color: #dc2626 !important; }

            /* PhonePe Floating Action Button (FAB) */
            .mobile-add-fab {
                display: flex !important;
                position: fixed;
                bottom: 75px; right: 20px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%);
                color: white;
                padding: 12px 20px;
                border-radius: 30px;
                text-decoration: none;
                font-size: 15px;
                font-weight: 700;
                box-shadow: 0 6px 20px rgba(95, 37, 159, 0.4);
                z-index: 9997;
                align-items: center;
                gap: 8px;
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
                width: 25%;
            }

            .phonepe-nav-item i { font-size: 19px; margin-bottom: 2px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 700; }
        }
    </style>
</head>
<body>

<!-- 📱 PhonePe App Top Header Bar (Mobile Only) -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="admin_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>विद्यार्थी निर्देशिका (Students)</h3>
            <small>Manage Student Directory</small>
        </div>
    </div>
    <div class="mobile-count-pill">
        <?= $total_students; ?> Total
    </div>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<aside class="sidebar">
    <div class="logo">CMS PRO PANEL</div>
    <ul class="nav-links">
        <li><a href="admin_dashboard.php"><i class="fa fa-gauge"></i> Dashboard</a></li>
        <li><a href="students.php" class="active"><i class="fa fa-user-graduate"></i> Manage Students</a></li>
        <li><a href="generate_docs.php"><i class="fa fa-file-invoice"></i> Documents</a></li>
        <li><a href="verify.php"><i class="fa fa-check-double"></i> Verification</a></li>
        <li><a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrapper">

    <!-- 🖥️ Desktop Top Nav Bar -->
    <div class="top-nav">
        <h2 style="color: var(--primary);">CMS <span style="font-weight: 300; color: var(--secondary);">PRO</span></h2>
        <div class="admin-profile">
            <span style="font-size: 14px; color: var(--secondary);">Welcome, <b>Admin</b></span>
        </div>
    </div>

    <div class="container">

        <!-- 🖥️ Desktop Header -->
        <div class="header-flex">
            <div>
                <h1>Student Directory</h1>
                <p style="color: var(--secondary); font-size: 14px;">Manage and view all your coaching students</p>
            </div>
            <a href="add_student.php" class="btn-add"><i class="fa fa-plus"></i> Add New Student</a>
        </div>

        <?php echo $message; ?>

        <!-- 📱 Mobile Search Box -->
        <form class="mobile-search-form" method="GET">
            <input type="text" name="search_text" placeholder="नाम या रोल नंबर खोजें..." value="<?php echo htmlspecialchars($search_text); ?>">
            <button type="submit" class="mobile-search-btn"><i class="fa fa-search"></i></button>
        </form>

        <!-- 🖥️ Desktop Search Box -->
        <form class="search-box" method="GET">
            <input type="text" name="search_text" placeholder="Search by name or roll number..." value="<?php echo htmlspecialchars($search_text); ?>">
            <button type="submit" class="btn-search">
                <i class="fa fa-search"></i> Search
            </button>
        </form>

        <!-- 🖥️ DESKTOP TABLE VIEW -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Mobile</th>
                        <th>Course/Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($students_data) > 0) { 
                        foreach($students_data as $row) { ?>
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="avatar"><?php echo strtoupper(substr($row['name'], 0, 1)); ?></div>
                                    <div>
                                        <div style="color: var(--text-dark); font-weight: 700;"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div style="font-size: 11px; color: var(--secondary);"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-roll">#<?php echo $row['roll_no']; ?></span></td>
                            <td><?php echo $row['mobile']; ?></td>
                            <td><b style="color: #4a5568;"><?php echo $row['course'] ?? 'N/A'; ?></b></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn-icon edit" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="students.php?delete=<?php echo $row['id']; ?>" 
                                       class="btn-icon delete" 
                                       title="Delete" 
                                       onclick="return confirm('क्या आप इस विद्यार्थी को डिलीट करना चाहते हैं?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a href="view_marks.php?id=<?php echo $row['id']; ?>" class="btn-icon" style="background: #e6fffa; color: #38b2ac;" title="View Report">
                                        <i class="fa fa-file-invoice"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } } else { ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--secondary);">
                                <i class="fa fa-folder-open" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                                No students found.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- 📱 MOBILE PHONEPE CARDS VIEW -->
        <div class="mobile-student-cards">
            <?php if(count($students_data) > 0) { 
                foreach($students_data as $row) { ?>
                <div class="phonepe-stu-card">
                    <div class="phonepe-card-top">
                        <div class="phonepe-avatar">
                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                        </div>
                        <div class="phonepe-info">
                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                            <p><i class="fa-regular fa-envelope me-1"></i> <?php echo htmlspecialchars($row['email']); ?></p>
                        </div>
                    </div>

                    <div class="phonepe-meta-bar">
                        <span class="phonepe-roll-tag">#<?php echo $row['roll_no']; ?> • <?php echo $row['course'] ?? 'N/A'; ?></span>
                        <span class="phonepe-mobile-tag"><i class="fa-solid fa-phone me-1"></i> <?php echo $row['mobile']; ?></span>
                    </div>

                    <div class="phonepe-actions">
                        <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="phonepe-btn phonepe-btn-edit">
                            <i class="fa fa-pen"></i> Edit
                        </a>
                        <a href="view_marks.php?id=<?php echo $row['id']; ?>" class="phonepe-btn phonepe-btn-report">
                            <i class="fa fa-file-invoice"></i> Report
                        </a>
                        <a href="students.php?delete=<?php echo $row['id']; ?>" 
                           class="phonepe-btn phonepe-btn-delete" 
                           onclick="return confirm('क्या आप इस विद्यार्थी का रिकॉर्ड हटाना चाहते हैं?')">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            <?php } } else { ?>
                <div style="text-align: center; padding: 35px 15px; background: white; border-radius: 18px; border: 1px solid #e2e8f0;">
                    <i class="fa fa-user-slash" style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px; display:block;"></i>
                    <p style="font-weight: 600; color: #64748b;">कोई विद्यार्थी नहीं मिला।</p>
                </div>
            <?php } ?>
        </div>

    </div>

</div>

<!-- 📱 PhonePe Floating Add Student Button (Mobile Only) -->
<a href="add_student.php" class="mobile-add-fab">
    <i class="fa fa-plus"></i> नया छात्र जोड़ें
</a>

<!-- 📱 PhonePe Bottom Navigation Bar (Mobile Only) -->
<div class="mobile-bottom-nav">
    <a href="admin_dashboard.php" class="phonepe-nav-item">
        <i class="fa fa-home"></i>
        <span>होम</span>
    </a>
    <a href="students.php" class="phonepe-nav-item active">
        <i class="fa fa-user-graduate"></i>
        <span>विद्यार्थी</span>
    </a>
    <a href="generate_docs.php" class="phonepe-nav-item">
        <i class="fa fa-file-invoice"></i>
        <span>दस्तावेज़</span>
    </a>
    <a href="logout.php" class="phonepe-nav-item" style="color:#ef4444;">
        <i class="fa fa-power-off"></i>
        <span>लॉगआउट</span>
    </a>
</div>

</body>
</html>