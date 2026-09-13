<?php
// Session and Database Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Auth Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$user_id = $stu['user_id'] ?? $stu['id']; 

// Navigation Logic (For active class in sidebar)
$current_page = 'e_book';

// Student ka data fetch karein
$profile_q = mysqli_query($conn, "SELECT * FROM students WHERE user_id = '$user_id'");
$profile_data = mysqli_fetch_assoc($profile_q);
$student_course = $profile_data['course'] ?? '';

// E-books fetch logic
$ebook_res = mysqli_query($conn, "SELECT * FROM ebooks WHERE course_name = '$student_course' OR course_name = 'All' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Library | CMS PRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f4f7fe; display: flex; color: #333; min-height: 100vh; }

        /* SIDEBAR STYLES (Same as Dashboard) */
        .sidebar { width: 260px; background: #0f172a; color: white; height: 100vh; position: fixed; padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-brand { font-size: 22px; font-weight: 600; margin-bottom: 40px; text-align: center; color: #3498db; }
        .sidebar-menu { list-style: none; flex: 1; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; padding: 12px 15px; display: block; border-radius: 12px; transition: 0.3s; font-size: 14px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(52, 152, 219, 0.1); color: #3498db; font-weight: 500; }
        
        /* MAIN CONTENT AREA */
        .main-content { margin-left: 260px; width: calc(100% - 260px); }
        .content-padding { padding: 30px; }

        /* HEADER BOX */
        .header-box { background: white; padding: 20px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* EBOOK GRID */
        .ebook-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .ebook-card { background: white; padding: 25px; border-radius: 20px; text-align: center; border: 1px solid #edf2f7; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .ebook-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        
        .book-icon { width: 60px; height: 60px; background: rgba(52, 152, 219, 0.1); color: #3498db; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px; }
        .book-title { font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        .book-meta { font-size: 11px; color: #64748b; margin-bottom: 20px; }
        
        .btn-download { background: #3498db; color: white; text-decoration: none; padding: 10px 0; border-radius: 10px; font-size: 13px; font-weight: 600; display: block; width: 100%; transition: 0.3s; }
        .btn-download:hover { background: #2980b9; box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3); }
        
        .no-data { grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 20px; color: #94a3b8; }

        @media (max-width: 992px) { .sidebar { display: none; } .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">🎓 CMS PRO</div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="dashboard.php?page=videos"><i class="fas fa-play-circle"></i> Courses Video</a></li>
        <li><a href="dashboard.php?page=idcard"><i class="fas fa-id-card"></i> My Digital ID Card</a></li>
        <li><a href="online_exam.php"><i class="fas fa-edit"></i> Online Exam</a></li>
        <li><a href="my_results.php"><i class="fas fa-chart-bar"></i> My Results</a></li>
        <li><a href="my_fees.php"><i class="fas fa-wallet"></i> Fees History</a></li>
        <li><a href="homework.php"><i class="fas fa-book"></i> Homework</a></li>
        <li><a href="e_book.php" class="active"><i class="fas fa-book-open"></i> E-Book</a></li>
        <li><a href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
        <li><a href="update_profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
    </ul>
    <div style="padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
        <a href="logout.php" style="color: #ef4444; text-decoration: none; font-size: 13px; padding-left: 15px; font-weight: 600;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="content-padding">
        
        <div class="header-box">
            <div>
                <h2 style="font-size: 18px;"><i class="fas fa-book-reader" style="color: #3498db; margin-right: 10px;"></i> Digital E-Library</h2>
                <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Study material for <b><?php echo htmlspecialchars($student_course); ?></b></p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 13px; font-weight: 600;"><?php echo htmlspecialchars($stu['name']); ?></p>
                <p style="font-size: 11px; color: #64748b;">Roll No: <?php echo $profile_data['roll_no'] ?? 'N/A'; ?></p>
            </div>
        </div>

        <div class="ebook-grid">
            <?php 
            if($ebook_res && mysqli_num_rows($ebook_res) > 0) {
                while($book = mysqli_fetch_assoc($ebook_res)) {
                    $file_link = "uploads/ebooks/" . $book['file_path'];
            ?>
                <div class="ebook-card">
                    <div class="book-icon"><i class="fas fa-file-pdf"></i></div>
                    <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                    <div class="book-meta">
                        <i class="far fa-clock"></i> Uploaded: <?php echo date('d M, Y', strtotime($book['uploaded_at'])); ?>
                    </div>
                    <a href="<?php echo $file_link; ?>" class="btn-download" target="_blank">
                        <i class="fas fa-cloud-download-alt"></i> View / Download
                    </a>
                </div>
            <?php 
                }
            } else {
                echo '<div class="no-data">
                        <i class="fas fa-book-open" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                        <p>Filhal aapke course ke liye koi E-Book upload nahi ki gayi hai.</p>
                      </div>';
            }
            ?>
        </div>

    </div>
</div>

</body>
</html>