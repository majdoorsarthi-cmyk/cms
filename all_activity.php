<?php 
// 1. Session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// 2. Admin login check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// 3. Delete Logic
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM notifications WHERE id='$id'");
    header("Location: all_activity.php?msg=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Activity History | CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #ffffff;
            --main-bg: #f4f7fe;
            --primary: #4361ee;
            --text-dark: #2b3674;
            --text-light: #a3aed0;
            --white: #ffffff;
            --shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background: var(--main-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        /* Fixed Sidebar Styles */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 20px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 14px 0 30px rgba(0,0,0,0.02);
        }

        .sidebar h3 { 
            font-size: 24px; font-weight: 800; text-align: center; 
            margin-bottom: 40px; color: var(--primary); letter-spacing: 1px;
        }
        
        .nav-label { 
            font-size: 12px; text-transform: uppercase; color: var(--text-light); 
            margin: 25px 0 10px 10px; font-weight: 700; letter-spacing: 1px;
        }

        .menu-item { margin-bottom: 8px; }
        
        .dropdown-btn, .nav-link {
            width: 100%; padding: 14px 18px; text-decoration: none;
            font-size: 15px; color: var(--text-light); display: flex;
            align-items: center; background: none; border: none;
            cursor: pointer; border-radius: 12px; transition: all 0.3s ease; font-weight: 500;
        }

        .dropdown-btn i, .nav-link i { margin-right: 12px; font-size: 18px; }
        .dropdown-btn:hover, .nav-link:hover, .active-nav { 
            background: var(--main-bg); color: var(--primary); 
        }

        .dropdown-container { display: none; padding-left: 20px; margin-top: 5px; }
        
        .dropdown-container a { 
            color: var(--text-light); padding: 10px 15px; font-size: 14px; 
            display: block; text-decoration: none; border-radius: 10px; transition: 0.2s;
        }
        .dropdown-container a:hover { color: var(--primary); background: #f0f3ff; }

        .logout-btn { 
            color: #ff5b5b !important; margin-top: 30px !important; 
            border-top: 1px solid #f0f3ff; padding-top: 20px !important;
        }

        /* Main Content Area Adjustment */
        .main { 
            margin-left: 280px; 
            padding: 40px; 
            width: calc(100% - 280px); 
        }

        .header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .history-card {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; color: var(--text-light); font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #f4f7fe; }
        td { padding: 18px 15px; border-bottom: 1px solid #f4f7fe; font-size: 14px; color: var(--text-dark); }
        
        .msg-text { color: #555; font-size: 13px; max-width: 400px; line-height: 1.5; }
        .date-badge { background: #f0f3ff; color: var(--primary); padding: 5px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .btn-delete { color: #ff5b5b; background: #fff5f5; padding: 8px 12px; border-radius: 10px; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .btn-delete:hover { background: #ff5b5b; color: white; }
        .rotate { transform: rotate(180deg); }
        .arrow { transition: 0.3s; margin-left: auto; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>CMS PRO</h3>
    
    <a href="admin_dashboard.php" class="nav-link">
        <i class="fa fa-th-large"></i> Dashboard
    </a>

    <div class="nav-label">Management</div>

    <div class="menu-item">
        <button class="dropdown-btn">
            <span><i class="fa fa-graduation-cap"></i> Academic</span>
            <i class="fa fa-caret-down arrow"></i>
        </button>
        <div class="dropdown-container">
            <a href="add_student.php">New Admission</a>
            <a href="manage_students.php">Manage Students</a>
            <a href="attendance_scanner.php">QR Attendance</a>
            <a href="homework.php">Homework</a>
        </div>
    </div>

    <div class="menu-item">
        <button class="dropdown-btn">
            <span><i class="fa fa-play-circle"></i> E-Learning</span>
            <i class="fa fa-caret-down arrow"></i>
        </button>
        <div class="dropdown-container">
            <a href="admin_upload_video.php">Upload Videos</a>
            <a href="admin_ebook.php">Manage E-Books</a>
            <a href="manage_courses.php">Course Structure</a>
        </div>
    </div>

    <div class="menu-item">
        <button class="dropdown-btn">
            <span><i class="fa fa-file-alt"></i> Examination</span>
            <i class="fa fa-caret-down arrow"></i>
        </button>
        <div class="dropdown-container">
            <a href="admin_add_question.php">Add Questions</a>
            <a href="manage_exams.php">Manage Exams</a>
            <a href="view_results.php">View Results</a>
        </div>
    </div>

    <div class="menu-item">
        <button class="dropdown-btn">
            <span><i class="fa fa-wallet"></i> Finance</span>
            <i class="fa fa-caret-down arrow"></i>
        </button>
        <div class="dropdown-container">
            <a href="collect_fees.php">Collect Fees</a>
            <a href="fee_report.php">Revenue Report</a>
        </div>
    </div>

    <a href="all_activity.php" class="nav-link active-nav">
        <i class="fa fa-history"></i> All Activity History
    </a>

    <div class="nav-label">Users</div>
    <a href="teachers.php" class="nav-link"><i class="fa fa-chalkboard-teacher"></i> Teachers</a>
    
    <a href="logout.php" class="nav-link logout-btn"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
    <div class="header-box">
        <div>
            <h2 style="font-size: 28px;">All Activity History</h2>
            <p style="color: var(--text-light); font-size: 14px;">Broadcasting Logs & History</p>
        </div>
        <div style="text-align: right;">
            <p style="font-weight: 600;"><?php echo date('d M Y'); ?></p>
            <span style="font-size: 12px; color: var(--primary); font-weight: 700;">Admin Panel</span>
        </div>
    </div>

    <div class="history-card">
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div style="background: #ebfbee; color: #27ae60; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
                ✅ Activity record deleted successfully.
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Notice Title</th>
                    <th>Message Details</th>
                    <th>Broadcast Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res = mysqli_query($conn, "SELECT * FROM notifications ORDER BY id DESC");
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-light);">#<?php echo $row['id']; ?></td>
                            <td style="font-weight: 600;"><?php echo $row['title']; ?></td>
                            <td class="msg-text"><?php echo $row['message']; ?></td>
                            <td>
                                <span class="date-badge">
                                    <i class="fa fa-calendar-alt" style="margin-right: 5px;"></i>
                                    <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="all_activity.php?delete_id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Delete this record?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:50px; color:var(--text-light);'>No activity found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Sidebar Dropdown Logic
    var dropdown = document.getElementsByClassName("dropdown-btn");
    for (var i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function() {
            this.classList.toggle("active-nav");
            var arrow = this.querySelector(".arrow");
            if(arrow) arrow.classList.toggle("rotate");
            
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    }
</script>

</body>
</html>