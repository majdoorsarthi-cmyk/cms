<?php
session_name("STUDENT_SESSION");
session_start();
include('../db_config.php');

// लॉगआउट हैंडलर
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['user'])) { 
    header("Location: index.php"); 
    exit(); 
}

$u = $_SESSION['user'];
$query = mysqli_query($conn, "SELECT name, course FROM students WHERE roll_no = '$u'");
$student = mysqli_fetch_assoc($query);

// स्टूडेंट के कोर्स की सफाई व फ़ॉर्मेटिंग
$course_raw = trim($student['course'] ?? '');

if (strtoupper($course_raw) == 'DCA' || $course_raw == 'Diploma in Computer Application') {
    $course_display_name = "Diploma in Computer Application";
    $student_course_code = 'DCA';
} elseif (strtoupper($course_raw) == 'PGDCA') {
    $course_display_name = "Post Graduate Diploma in Computer Application";
    $student_course_code = 'PGDCA';
} else {
    $course_display_name = htmlspecialchars($course_raw); 
    $student_course_code = strtoupper($course_raw);
}

// तारीख को सुंदर फॉर्मेट में बदलने का फंक्शन
function formatMoodleDate($date_string, $default_text) {
    if (!empty($date_string)) {
        return date('l, j F Y, g:i A', strtotime($date_string));
    }
    return $default_text;
}

// SQL Strict Filter Criteria
$course_filter_sql = "s.course = '$student_course_code'";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course: <?php echo $course_display_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    :root {
        --moodle-purple: #8A4F8D; 
        --light-purple-bg: #f4edf5;
        --text-dark: #333333;
    }
    
    body { 
        background: url('book_bg.jpg') no-repeat center center fixed; 
        background-size: cover;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--text-dark);
    }
    
    .navbar-custom { 
        background-color: var(--moodle-purple); 
        color: white; 
        padding: 5px 40px; 
        min-height: 52px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .navbar-custom a { color: rgba(255,255,255,0.9); text-decoration: none; padding: 14px 16px; font-size: 15px; }
    .navbar-custom a:hover { color: white; }
    .navbar-custom .active-tab { background-color: rgba(255,255,255,0.15); font-weight: 500; color: white; }
    .user-avatar { width: 34px; height: 34px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; color: white; cursor: pointer; }

    .main-wrapper { display: flex; min-height: calc(100vh - 52px); }

    .sidebar { 
        width: 260px; 
        background: rgba(255, 255, 255, 0.95); 
        border-right: 1px solid #dee2e6; 
        padding: 15px 10px; 
        font-size: 13px;
    }
    .sidebar-course-select { border: 1px solid #ced4da; padding: 6px 10px; border-radius: 4px; background: #fff; font-size: 12.5px; font-weight: 500; cursor: pointer; }
    .sidebar-toggle-btn { background: var(--moodle-purple); color: white; padding: 6px 10px; border-radius: 4px; border: none; }
    
    .sidebar-menu-item { 
        display: flex; 
        align-items: center; 
        padding: 8px 5px; 
        color: #495057; 
        text-decoration: none; 
        font-weight: 500; 
        margin-top: 10px; 
        cursor: pointer;
    }
    
    .sidebar-sub-list { 
        list-style: none; 
        padding-left: 0; 
        margin-bottom: 15px; 
        transition: all 0.3s ease;
    }
    
    .sidebar-sub-list li a { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        padding: 9px 12px; 
        color: #495057; 
        text-decoration: none; 
        font-size: 12.5px; 
        line-height: 1.3;
        border-radius: 6px;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    
    .sidebar-sub-list li a:hover {
        background-color: var(--moodle-purple); 
        color: #ffffff !important; 
    }
    
    .sidebar-sub-list li a i.fa-circle { color: #2ea44f; font-size: 8px; flex-shrink: 0; } 
    .sidebar-sub-list li a i.fa-circle-regular { color: #888; font-size: 8px; flex-shrink: 0; } 
    
    .sidebar-sub-list li a:hover i.fa-circle { color: #e2f0d9; }
    .sidebar-sub-list li a:hover i.fa-circle-regular { color: rgba(255,255,255,0.7); }
    
    .sidebar-sub-list li a .text-truncate { max-width: 100%; }

    .content-container { flex-grow: 1; padding: 30px; overflow-y: auto; }
    .main-white-card { 
        background: rgba(255, 255, 255, 0.98); 
        padding: 35px; 
        border-radius: 4px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #dee2e6;
    }
    .course-title { color: var(--moodle-purple); font-weight: 700; font-size: 30px; margin-bottom: 25px; }

    .moodle-tabs { display: flex; gap: 4px; margin-bottom: 30px; }
    .moodle-tab-btn { padding: 10px 20px; font-size: 14px; text-decoration: none; border-radius: 4px; color: var(--moodle-purple); background: #f8f9fa; }
    .moodle-tab-btn.active { background: var(--moodle-purple); color: white; }

    .section-box { border: 1px solid #dee2e6; border-radius: 6px; margin-bottom: 25px; background: #fff; overflow: hidden; }
    .section-header { background: #fff; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #efefef; }
    .section-title { font-size: 18px; font-weight: 500; color: #212529; margin: 0; display: flex; align-items: center; gap: 12px; }
    .section-icon-bg { background: #f4edf5; color: var(--moodle-purple); width: 26px; height: 26px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 11px; }
    .collapse-text { font-size: 13px; color: var(--moodle-purple); text-decoration: none; }
    .section-body { padding: 0 20px; }

    .subject-row { display: flex; align-items: center; justify-content: space-between; padding: 18px 0; border-bottom: 1px solid #eee; }
    .subject-row:last-child { border-bottom: none; }
    .subject-info-block { display: flex; align-items: flex-start; gap: 14px; }
    
    .moodle-list-icon { 
        color: #d63384; background: #fdf2f7; 
        border: 1px solid #fbcfe8; font-size: 14px; 
        width: 32px; height: 32px; border-radius: 4px; 
        display: flex; align-items: center; justify-content: center; margin-top: 2px;
    }
    .subject-link { color: var(--moodle-purple); font-weight: 500; text-decoration: none; font-size: 15px; }
    .subject-link:hover { text-decoration: underline; }
    .time-details { font-size: 13px; color: #555; margin-top: 4px; }

    .btn-moodle-done { 
        background-color: #e2f0d9; color: #385723; 
        border: 1px solid #bcdca7; font-size: 12.5px; 
        font-weight: 500; padding: 5px 12px; border-radius: 4px; 
        display: flex; align-items: center; gap: 6px; 
    }
    .btn-moodle-done:hover, .btn-moodle-done:focus { background-color: #d2e6c7; color: #385723; border-color: #a8cf92; }

    .btn-moodle-todo {
        background-color: #f8f9fa; color: #6c757d;
        border: 1px solid #ced4da; font-size: 12.5px;
        font-weight: 500; padding: 5px 12px; border-radius: 4px;
        display: flex; align-items: center; gap: 6px;
    }
    .btn-moodle-todo:hover, .btn-moodle-todo:focus { background-color: #e9ecef; color: #495057; border-color: #adb5bd; }

    .moodle-dropdown-menu {
        font-size: 13px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.15);
        padding: 6px 0;
    }
    .moodle-dropdown-menu .dropdown-item {
        padding: 8px 16px;
        color: #333;
    }
    .moodle-dropdown-menu .dropdown-item:hover {
        background-color: #f4edf5;
        color: var(--moodle-purple);
    }
</style>
</head>
<body>

    <div class="navbar-custom d-flex align-items-center">
        <a href="#" style="padding-left:0;"><strong>AISECT</strong></a>
        <a href="my.php">Home</a>
        <a href="my.php">Dashboard</a>
        <a href="my.php" class="active-tab">My courses</a>
        
        <div class="ms-auto dropdown d-flex align-items-center gap-2">
            <div class="user-avatar" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo strtoupper(substr($student['name'] ?? 'US', 0, 2)); ?>
            </div>
            <i class="fa fa-chevron-down text-white small" style="font-size: 9px;"></i>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li class="px-3 py-2 border-bottom"><strong><?php echo htmlspecialchars($student['name'] ?? 'Student'); ?></strong></li>
                <li><a class="dropdown-item text-danger" href="?logout=true"><i class="fa fa-sign-out-alt me-2"></i>Log out</a></li>
            </ul>
        </div>
    </div>

    <div class="main-wrapper">
        
        <div class="sidebar">
            <div class="d-flex align-items-center gap-1 mb-3">
                <div class="sidebar-course-select flex-grow-1 text-truncate">
                    <span><?php echo ($student_course_code == 'DCA') ? 'Diploma in Computer A...' : htmlspecialchars($course_raw); ?></span>
                </div>
                <button class="sidebar-toggle-btn"><i class="fa fa-bars" style="font-size: 12px;"></i></button>
            </div>

            <a href="#" class="sidebar-menu-item"><i class="fa fa-chevron-down me-2" style="font-size: 10px;"></i> General</a>
            
            <a href="javascript:void(0);" class="sidebar-menu-item toggle-menu" data-target="sem1">
                <i class="fa fa-chevron-down me-2 arrow-icon" style="font-size: 10px;"></i> Semester I
            </a>
            <ul class="sidebar-sub-list" id="sem1">
                <?php
                $sem1_query = mysqli_query($conn, "SELECT s.id, s.subject_name, 
                    (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.subject_id = s.id AND quiz_attempts.roll_no = '$u') as attempts 
                    FROM subjects s WHERE s.semester = 'I' AND $course_filter_sql ORDER BY s.id ASC");
                
                if ($sem1_query && mysqli_num_rows($sem1_query) > 0) {
                    while($sub = mysqli_fetch_assoc($sem1_query)) {
                        $done = ($sub['attempts'] > 0);
                        $icon = $done ? 'fa-solid fa-circle' : 'fa-regular fa-circle';
                        echo '<li><a href="mod/quiz/view.php?subject_id='.$sub['id'].'"><i class="'.$icon.'"></i> <span class="text-truncate">'.htmlspecialchars($sub['subject_name']).'</span></a></li>';
                    }
                } else {
                    echo '<li class="px-3 text-muted small">कोई विषय नहीं</li>';
                }
                ?>
            </ul>

            <a href="javascript:void(0);" class="sidebar-menu-item toggle-menu" data-target="sem2">
                <i class="fa fa-chevron-down me-2 arrow-icon" style="font-size: 10px;"></i> Semester II
            </a>
            <ul class="sidebar-sub-list" id="sem2">
                <?php
                $sem2_query = mysqli_query($conn, "SELECT s.id, s.subject_name, 
                    (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.subject_id = s.id AND quiz_attempts.roll_no = '$u') as attempts 
                    FROM subjects s WHERE s.semester = 'II' AND $course_filter_sql ORDER BY s.id ASC");
                
                if ($sem2_query && mysqli_num_rows($sem2_query) > 0) {
                    while($sub = mysqli_fetch_assoc($sem2_query)) {
                        $done = ($sub['attempts'] > 0);
                        $icon = $done ? 'fa-solid fa-circle' : 'fa-regular fa-circle';
                        echo '<li><a href="mod/quiz/view.php?subject_id='.$sub['id'].'"><i class="'.$icon.'"></i> <span class="text-truncate">'.htmlspecialchars($sub['subject_name']).'</span></a></li>';
                    }
                } else {
                    echo '<li class="px-3 text-muted small">कोई विषय नहीं</li>';
                }
                ?>
            </ul>
        </div>

        <div class="content-container">
            <div class="main-white-card">
                
                <h1 class="course-title"><?php echo $course_display_name; ?> (New Pattern)</h1>
                
                <div class="moodle-tabs">
                    <a href="#" class="moodle-tab-btn active">Course</a>
                    <a href="user/index.php" class="moodle-tab-btn">Participants</a>
                    <a href="course/overview.php" class="moodle-tab-btn">Activities</a>
                    <a href="#" class="moodle-tab-btn">Competencies</a>
                </div>

                <div class="section-box">
                    <div class="section-header">
                        <h5 class="section-title">
                            <div class="section-icon-bg"><i class="fa fa-chevron-down"></i></div>
                            General
                        </h5>
                        <a href="#" class="collapse-text">Collapse all</a>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-header">
                        <h5 class="section-title">
                            <div class="section-icon-bg"><i class="fa fa-chevron-down"></i></div>
                            Semester I
                        </h5>
                    </div>
                    <div class="section-body">
                        <?php
                        $subjects = mysqli_query($conn, "SELECT s.*, (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.subject_id = s.id AND quiz_attempts.roll_no = '$u') as attempt_count FROM subjects s WHERE s.semester = 'I' AND $course_filter_sql ORDER BY s.id ASC");
                        
                        if ($subjects && mysqli_num_rows($subjects) > 0) {
                            while($row = mysqli_fetch_assoc($subjects)) {
                                $is_attempted = ($row['attempt_count'] > 0);
                                ?>
                                <div class="subject-row">
                                    <div class="subject-info-block">
                                        <div class="moodle-list-icon"><i class="fa-regular fa-rectangle-list"></i></div>
                                        <div>
                                            <a href="mod/quiz/view.php?subject_id=<?php echo $row['id']; ?>" class="subject-link"><?php echo htmlspecialchars($row['subject_name']); ?></a>
                                            <div class="time-details">
                                                <strong>Opened:</strong> <?php echo formatMoodleDate($row['start_time'] ?? '', 'Wednesday, 10 June 2026, 8:00 AM'); ?> &nbsp;&nbsp;
                                                <strong>Closed:</strong> <?php echo formatMoodleDate($row['end_time'] ?? '', 'Wednesday, 10 June 2026, 8:00 PM'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="<?php echo $is_attempted ? 'btn-moodle-done' : 'btn-moodle-todo'; ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo $is_attempted ? '<i class="fa fa-check" style="font-size:11px;"></i> Done' : 'To do'; ?>
                                        </button>
                                        <ul class="dropdown-menu moodle-dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"><i class="fa-regular fa-circle-check me-2 text-success"></i> activity पूरा चिह्नित करें</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-circle-info me-2 text-secondary"></i> विवरण देखें</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="py-3 text-muted">कोई विषय उपलब्ध नहीं है।</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-header">
                        <h5 class="section-title">
                            <div class="section-icon-bg"><i class="fa fa-chevron-down"></i></div>
                            Semester II
                        </h5>
                    </div>
                    <div class="section-body">
                        <?php
                        $subjects_sem2 = mysqli_query($conn, "SELECT s.*, (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_attempts.subject_id = s.id AND quiz_attempts.roll_no = '$u') as attempt_count FROM subjects s WHERE s.semester = 'II' AND $course_filter_sql ORDER BY s.id ASC");
                        
                        if ($subjects_sem2 && mysqli_num_rows($subjects_sem2) > 0) {
                            while($row = mysqli_fetch_assoc($subjects_sem2)) {
                                $is_attempted = ($row['attempt_count'] > 0);
                                ?>
                                <div class="subject-row">
                                    <div class="subject-info-block">
                                        <div class="moodle-list-icon"><i class="fa-regular fa-rectangle-list"></i></div>
                                        <div>
                                            <a href="mod/quiz/view.php?subject_id=<?php echo $row['id']; ?>" class="subject-link"><?php echo htmlspecialchars($row['subject_name']); ?></a>
                                            <div class="time-details">
                                                <strong>Opened:</strong> <?php echo formatMoodleDate($row['start_time'] ?? '', 'Wednesday, 10 June 2026, 8:00 AM'); ?> &nbsp;&nbsp;
                                                <strong>Closed:</strong> <?php echo formatMoodleDate($row['end_time'] ?? '', 'Wednesday, 10 June 2026, 8:00 PM'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="<?php echo $is_attempted ? 'btn-moodle-done' : 'btn-moodle-todo'; ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo $is_attempted ? '<i class="fa fa-check" style="font-size:11px;"></i> Done' : 'To do'; ?>
                                        </button>
                                        <ul class="dropdown-menu moodle-dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"><i class="fa-regular fa-circle-check me-2 text-success"></i> activity पूरा चिह्नित करें</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-circle-info me-2 text-secondary"></i> विवरण देखें</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="py-3 text-muted">कोई विषय उपलब्ध नहीं है।</div>';
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.toggle-menu').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const subList = document.getElementById(targetId);
            const arrow = this.querySelector('.arrow-icon');
            
            if (subList.style.display === 'none') {
                subList.style.display = 'block';
                arrow.classList.remove('fa-chevron-right');
                arrow.classList.add('fa-chevron-down');
            } else {
                subList.style.display = 'none';
                arrow.classList.remove('fa-chevron-down');
                arrow.classList.add('fa-chevron-right');
            }
        });
    });
    </script>
</body>
</html>