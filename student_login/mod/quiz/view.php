<?php
session_name("STUDENT_SESSION");
session_start();
include('../../../db_config.php'); 

if (!isset($_SESSION['user'])) { 
    header("Location: ../../index.php"); 
    exit(); 
}

// 1. सेशन से रोल नंबर प्राप्त करें
$u = $_SESSION['user']; 

// 2. स्टूडेंट का नाम और कोर्स डेटाबेस से निकालें
$query = mysqli_query($conn, "SELECT name, course FROM students WHERE roll_no = '$u'");
$student = mysqli_fetch_assoc($query);
$student_full_name = $student['name'] ?? 'Student';
$username_initials = strtoupper(substr($student_full_name, 0, 2));

$course_raw = trim($student['course'] ?? '');
if (strtoupper($course_raw) == 'DCA' || $course_raw == 'Diploma in Computer Application') {
    $student_course_code = 'DCA';
} elseif (strtoupper($course_raw) == 'PGDCA') {
    $student_course_code = 'PGDCA';
} else {
    $student_course_code = strtoupper($course_raw); // जैसे BA, B.Com आदि
}

// 3. डेटाबेस से छात्र के संबंधित कोर्स (BA / DCA आदि) के ही विषय निकालें
$subjects = [];
$sem1_subjects = [];
$sem2_subjects = [];

$sub_sql = mysqli_query($conn, "SELECT id, subject_name, semester, exam_date, is_manual_active FROM subjects WHERE course = '$student_course_code' ORDER BY id ASC");
if ($sub_sql && mysqli_num_rows($sub_sql) > 0) {
    while ($s_row = mysqli_fetch_assoc($sub_sql)) {
        $subjects[$s_row['id']] = $s_row['subject_name'];
        
        // सेमेस्टर अनुसार सूची अलग करें
        if (trim($s_row['semester']) == 'I' || trim($s_row['semester']) == '1') {
            $sem1_subjects[$s_row['id']] = $s_row['subject_name'];
        } else {
            $sem2_subjects[$s_row['id']] = $s_row['subject_name'];
        }
    }
}

// वर्तमान सेलेक्टेड विषय ID
$current_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
if ($current_id <= 0 || !array_key_exists($current_id, $subjects)) {
    $first_key = !empty($subjects) ? array_key_first($subjects) : 0;
    $current_id = $first_key;
}

$current_subject = $subjects[$current_id] ?? 'Subject Not Available';

date_default_timezone_set('Asia/Kolkata');
$current_time = time();

// डेटाबेस से इस सब्जेक्ट की वास्तविक Exam Date और Manual Active स्टेटस निकालें
$date_query = mysqli_query($conn, "SELECT exam_date, is_manual_active, semester FROM subjects WHERE id = '$current_id'");
$date_row = mysqli_fetch_assoc($date_query);
$db_exam_date = $date_row['exam_date'] ?? date('Y-m-d'); 
$subject_semester = $date_row['semester'] ?? 'I';

// 1. आज की तारीख (Object format)
$today_date_obj = new DateTime(date('Y-m-d'));

// 2. डेटाबेस से परीक्षा की तारीख
$target_date_str = $db_exam_date; 
$target_date_obj = new DateTime($target_date_str);

// 3. एडमिन ओवरराइड चेक
$is_admin_override = (($date_row['is_manual_active'] ?? 0) == 1);

// 4. अगर आज की तारीख, परीक्षा की तारीख के बराबर या उसके बाद है
$is_today_or_past = ($today_date_obj >= $target_date_obj);

// 5. एक्सपायरी लॉजिक: अगर आज की तारीख परीक्षा की तारीख से बड़ी हो गई है
$is_quiz_expired = ($today_date_obj > $target_date_obj);

// अगर एडमिन ने ऑन किया है, तो एक्सपायरी को ओवरराइड करें
if ($is_admin_override) {
    $is_quiz_expired = false;
}

$start_time = strtotime($target_date_str . ' 08:00:00');
$quiz_date_display = date('l, d F Y', $start_time);

// डेटाबेस से इस विशेष सब्जेक्ट के अटेम्प्ट्स निकालना
$attempts = [];
$u_with_suffix = $u; 
$attempt_query = mysqli_query($conn, "SELECT * FROM quiz_attempts WHERE roll_no = '$u_with_suffix' AND subject_id = '$current_id' ORDER BY attempt_number ASC");
if ($attempt_query) {
    while ($row = mysqli_fetch_assoc($attempt_query)) {
        $attempts[] = $row;
    }
}
$total_attempts_done = count($attempts);

// ==================== फॉलबैक लॉजिक ====================
$fallback_attempts = [];
if ($total_attempts_done == 0) {
    $fallback_query = mysqli_query($conn, "SELECT * FROM quiz_attempts WHERE roll_no = '$u' AND subject_id = '$current_id' ORDER BY id DESC LIMIT 1");
    if ($fallback_query && mysqli_num_rows($fallback_query) > 0) {
        $fallback_attempts[] = mysqli_fetch_assoc($fallback_query);
    }
}
// ======================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($current_subject); ?> | AISECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --moodle-purple: #8A4F8D; }
        
        body { 
            background: url('book_bg.jpg') no-repeat center center fixed; 
            background-size: cover; 
            font-family: -apple-system, sans-serif; 
            color: #333; 
            overflow-x: hidden; 
        }
        
        .navbar-custom { background-color: var(--moodle-purple); color: white; padding: 5px 30px; min-height: 52px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .nav-link-moodle { color: rgba(255, 255, 255, 0.85); text-decoration: none; font-size: 14px; font-weight: 500; padding: 6px 12px; border-radius: 4px; transition: all 0.2s ease; }
        .nav-link-moodle:hover, .nav-link-moodle.active { color: #ffffff; background-color: rgba(255, 255, 255, 0.15); }
        .main-wrapper { display: flex; min-height: calc(100vh - 52px); }
        .sidebar { width: 310px; background: rgba(255, 255, 255, 0.96); border-right: 1px solid #dee2e6; padding: 20px 10px; flex-shrink: 0; box-shadow: 2px 0 5px rgba(0,0,0,0.02); max-height: calc(100vh - 52px); overflow-y: auto; }
        .sidebar-course-select { font-size: 14px; font-weight: 700; color: #333; }
        .sidebar-toggle-btn { background: none; border: 1px solid #dee2e6; border-radius: 4px; padding: 2px 6px; color: #6c757d; }
        .sidebar-menu-item { display: flex; align-items: center; padding: 10px 12px; color: #333; text-decoration: none; font-weight: 600; font-size: 13.5px; border-radius: 6px; margin-bottom: 4px; transition: background 0.2s; }
        .sidebar-menu-item:hover { background-color: #f1f3f5; color: #000; }
        .sidebar-sub-list { list-style: none; padding-left: 0; margin-bottom: 15px; }
        .sidebar-sub-list li a { display: flex; align-items: center; gap: 8px; padding: 9px 12px; color: #495057; text-decoration: none; font-size: 12.5px; font-weight: 500; border-radius: 6px; transition: all 0.15s ease; line-height: 1.4; }
        .sidebar-sub-list li a:hover { background-color: var(--moodle-purple) !important; color: #ffffff !important; }
        .sidebar-sub-list li a.active-course { background-color: rgba(138, 79, 141, 0.1); color: var(--moodle-purple); font-weight: 600; border-left: 3px solid var(--moodle-purple); border-radius: 0 6px 6px 0; }
        .content-container { flex-grow: 1; padding: 35px 25px; display: flex; justify-content: center; overflow-y: auto; }
        .main-white-card { background: rgba(255, 255, 255, 0.98); padding: 35px 40px; border-radius: 4px; border: 1px solid #dee2e6; width: 100%; max-width: 830px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .moodle-breadcrumb { font-size: 13px; color: #6a737d; margin-bottom: 25px; }
        .moodle-breadcrumb a { color: var(--moodle-purple); text-decoration: none; }
        .moodle-breadcrumb a:hover { text-decoration: underline; }
        .quiz-purple-ribbon { background-color: var(--moodle-purple); color: white; padding: 6px 14px; font-size: 13.5px; font-weight: 500; border-radius: 4px; display: inline-block; margin-bottom: 25px; }
        .quiz-icon-header { color: #d63384; background: #fdf2f7; border: 1px solid #fbcfe8; font-size: 16px; width: 34px; height: 34px; border-radius: 4px; display: flex; align-items: center; justify-content: center; }
        .quiz-main-title { color: #212529; font-weight: 700; font-size: 24px; line-height: 1.3; }
        .requirements-box { background-color: #f8f9fa; border: 1px solid #e1e4e6; border-radius: 4px; padding: 15px; margin-bottom: 25px; max-width: 500px; }
        .quiz-info-text { font-size: 13.5px; color: #212529; margin-bottom: 6px; }
        
        /* असली इंटरफ़ेस जैसी टेबल सेटिंग: एक व्यवस्थित सीमित चौड़ाई का ग्रिड */
        .attempts-flex-container { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 25px; width: 100%; }
        .attempt-card-table { max-width: 340px; width: 100%; border-collapse: collapse; font-size: 13px; border: 1px solid #dee2e6; margin-bottom: 10px; }
        .attempt-card-table th { background-color: #f8f9fa; color: #495057; font-weight: 600; padding: 8px 12px; border: 1px solid #dee2e6; }
        .attempt-card-table td { padding: 8px 12px; border: 1px solid #dee2e6; color: #212529; background: #fff; line-height: 1.5; }
        
        .btn-moodle-purple { background-color: var(--moodle-purple); color: white; border: none; padding: 8px 18px; font-size: 13px; font-weight: 500; border-radius: 4px; text-decoration:none; display:inline-block; transition: background 0.2s; }
        .btn-moodle-purple:hover { background-color: #734075; color: white; }
        .btn-moodle-disabled { background-color: #6c757d !important; color: #e9ecef !important; cursor: not-allowed; opacity: 0.65; }
        .user-avatar { width: 32px; height: 32px; background: #fff; color: var(--moodle-purple); font-weight: 700; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        
        .moodle-reference-footer { font-size: 13px; color: #2ea44f; font-weight: bold; margin-top: 15px; }
        .moodle-reference-line { margin-bottom: 4px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold me-4" style="font-size: 18px; letter-spacing: 0.5px;">AISECT</span>
            <a href="../../../dashboard.php" class="nav-link-moodle">Home</a>
            <a href="../../index.php" class="nav-link-moodle">Dashboard</a>
            <a href="#" class="nav-link-moodle active">My courses</a>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <div class="user-avatar"><?php echo $username_initials; ?></div>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="sidebar">
            <div class="d-flex align-items-center gap-1 mb-3">
                <div class="sidebar-course-select flex-grow-1 text-truncate">
                    <span><?php echo ($student_course_code == 'DCA') ? 'Diploma in Computer A...' : htmlspecialchars($student_course_code); ?></span>
                </div>
                <button class="sidebar-toggle-btn"><i class="fa fa-bars" style="font-size: 12px;"></i></button>
            </div>
            
            <a href="#" class="sidebar-menu-item"><i class="fa fa-chevron-down me-2" style="font-size: 10px;"></i> General</a>
            
            <a href="javascript:void(0);" class="sidebar-menu-item">Semester I</a>
            <ul class="sidebar-sub-list">
                <?php 
                if (!empty($sem1_subjects)) {
                    foreach ($sem1_subjects as $s_id => $s_name) { 
                        $isActive = ($s_id == $current_id) ? 'active-course' : '';
                ?>
                        <li>
                            <a href="view.php?subject_id=<?php echo $s_id; ?>" class="<?php echo $isActive; ?>">
                                <i class="fa-solid fa-circle" style="font-size: 8px;"></i> 
                                <span class="text-truncate"><?php echo htmlspecialchars($s_name); ?></span>
                            </a>
                        </li>
                <?php 
                    }
                } else {
                    echo '<li class="px-3 text-muted small">No subjects found</li>';
                }
                ?>
            </ul>

            <a href="javascript:void(0);" class="sidebar-menu-item">Semester II</a>
            <ul class="sidebar-sub-list">
                <?php 
                if (!empty($sem2_subjects)) {
                    foreach ($sem2_subjects as $s_id => $s_name) { 
                        $isActive = ($s_id == $current_id) ? 'active-course' : '';
                ?>
                        <li>
                            <a href="view.php?subject_id=<?php echo $s_id; ?>" class="<?php echo $isActive; ?>">
                                <i class="fa-regular fa-circle" style="font-size: 8px;"></i> 
                                <span class="text-truncate"><?php echo htmlspecialchars($s_name); ?></span>
                            </a>
                        </li>
                <?php 
                    }
                } else {
                    echo '<li class="px-3 text-muted small">No subjects found</li>';
                }
                ?>
            </ul>
        </div>

        <div class="content-container">
            <div class="main-white-card">
                <?php if ($current_id > 0 && !empty($subjects)): ?>
                    <div class="moodle-breadcrumb">
                        <a href="../../"><?php echo htmlspecialchars($student_course_code); ?> (New Pattern)</a> &nbsp;>&nbsp; 
                        <a href="#">Semester <?php echo htmlspecialchars($subject_semester); ?></a> &nbsp;>&nbsp; 
                        <span class="text-muted"><?php echo htmlspecialchars($current_subject); ?></span>
                    </div>
                    
                    <div class="quiz-purple-ribbon"><?php echo htmlspecialchars($current_subject); ?> (Semester <?php echo htmlspecialchars($subject_semester); ?>)</div>

                    <div class="quiz-info-text"><strong>Opened:</strong> <?php echo $quiz_date_display; ?>, 8:00 AM</div>
                    <div class="quiz-info-text mb-4"><strong>Closed:</strong> <?php echo $quiz_date_display; ?>, 8:00 PM</div>

                    <div class="requirements-box">
                        <h6 class="fw-bold" style="font-size:13.5px;">Completion requirements</h6>
                        <ul class="list-unstyled p-0 m-0 small <?php echo ($total_attempts_done > 0) ? 'text-success' : 'text-muted'; ?> fw-medium">
                            <li><i class="fa-solid <?php echo ($total_attempts_done > 0) ? 'fa-check' : 'fa-circle-notch'; ?> me-1"></i> Done: Make attempts: 1</li>
                            <li><i class="fa-solid <?php echo ($total_attempts_done > 0) ? 'fa-check' : 'fa-circle-notch'; ?> me-1"></i> Done: Receive a grade</li>
                        </ul>
                    </div>

                    <div class="text-start mb-4">
                        <?php 
                        // लॉजिक: अगर एडमिन ने ऑन किया है OR आज परीक्षा का दिन है और वह एक्सपायर नहीं हुआ है
                        if ($is_admin_override || ($is_today_or_past && !$is_quiz_expired)): 
                            if ($total_attempts_done == 0): ?>
                                <a href="attempt.php?subject_id=<?php echo $current_id; ?>" class="btn-moodle-purple">Attempt quiz now</a>
                            <?php elseif ($total_attempts_done < 3): ?>
                                <a href="attempt.php?subject_id=<?php echo $current_id; ?>" class="btn-moodle-purple">Re-attempt quiz</a>
                            <?php else: ?>
                                <button class="btn-moodle-purple btn-moodle-disabled" disabled>Attempts exhausted (Max: 3)</button>
                            <?php endif; 
                        else: 
                            echo "<p class='text-danger'>This quiz is currently not available.</p>";
                        endif; 
                        ?>
                    </div>

                    <div class="quiz-info-text">Attempts allowed: 3</div>
                    <div class="quiz-info-text">Time limit: 2 hours</div>
                    <div class="quiz-info-text">Grading method: Highest grade</div>
                    <div class="quiz-info-text mb-4">Grade to pass: 17 out of 50</div>

                    <?php if ($total_attempts_done > 0) { ?>
                        <h4 class="fw-bold text-secondary mb-3" style="font-size:15px; color:var(--moodle-purple) !important;">Your attempts</h4>
                        <div class="attempts-flex-container">
                            <?php 
                            $display_attempts = array_reverse($attempts);
                            foreach ($display_attempts as $att) { 
                                $start_formatted = date('l, d F Y, h:i A', strtotime($att['start_time']));
                                $end_formatted = date('l, d F Y, h:i A', strtotime($att['end_time']));
                            ?>
                                <table class="attempt-card-table">
                                    <thead><tr><th colspan="2">Attempt <?php echo $att['attempt_number']; ?></th></tr></thead>
                                    <tbody>
                                        <tr><td><strong>Status</strong></td><td>Finished</td></tr>
                                        <tr><td><strong>Started</strong></td><td><?php echo $start_formatted; ?></td></tr>
                                        <tr><td><strong>Completed</strong></td><td><?php echo $end_formatted; ?></td></tr>
                                        <tr><td><strong>Duration</strong></td><td><?php echo !empty($att['duration_text']) ? $att['duration_text'] : 'N/A'; ?></td></tr>
                                        <tr><td colspan="2" class="text-muted small italic bg-light"><i class="fa-solid fa-lock me-1"></i> Review not permitted</td></tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <?php if (empty($fallback_attempts)): ?>
                            <p class="text-muted small mt-4">You haven't attempted this quiz yet.</p>
                        <?php endif; ?>
                    <?php } ?>

                    <?php if ($total_attempts_done == 0 && !empty($fallback_attempts)) { ?>
                        <h4 class="fw-bold text-secondary mb-3" style="font-size:15px; color:var(--moodle-purple) !important;">Your attempts</h4>
                        <div class="attempts-flex-container">
                            <?php 
                            foreach ($fallback_attempts as $att) { 
                                $start_formatted = date('l, d F Y, h:i A', strtotime($att['start_time']));
                                $end_formatted = date('l, d F Y, h:i A', strtotime($att['end_time']));
                            ?>
                                <table class="attempt-card-table">
                                    <thead><tr><th colspan="2">Attempt <?php echo $att['attempt_number']; ?></th></tr></thead>
                                    <tbody>
                                        <tr><td><strong>Status</strong></td><td>Finished</td></tr>
                                        <tr><td><strong>Started</strong></td><td><?php echo $start_formatted; ?></td></tr>
                                        <tr><td><strong>Completed</strong></td><td><?php echo $end_formatted; ?></td></tr>
                                        <tr><td><strong>Duration</strong></td><td><?php echo $att['duration_text'] ?? '21 mins 37 secs'; ?></td></tr>
                                        <tr><td colspan="2" class="text-muted small italic bg-light"><i class="fa-solid fa-lock me-1"></i> Review not permitted</td></tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    <?php } ?>
                        
                    <div class="text-center mt-2 mb-2">
                        <a href="view.php?subject_id=<?php echo $current_id; ?>" class="btn btn-secondary btn-sm" style="background-color:#8A4F8D; border:none; padding: 8px 18px; font-size: 13px;">Back to the course</a>
                    </div>

                    <?php if ($total_attempts_done > 0) { ?>
                        <div class="moodle-reference-footer">
                            <?php foreach ($attempts as $att) { 
                                $clean_ref = str_replace('REF-', '', $att['reference_code']);
                                ?>
                                <div class="moodle-reference-line">
                                    Attempt <?php echo $att['attempt_number']; ?>: Reference Number: <?php echo htmlspecialchars($clean_ref); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } elseif (!empty($fallback_attempts)) { ?>
                        <div class="moodle-reference-footer">
                            <?php foreach ($fallback_attempts as $att) { 
                                $clean_ref = str_replace('REF-', '', $att['reference_code']);
                                ?>
                                <div class="moodle-reference-line">
                                    Attempt <?php echo $att['attempt_number']; ?>: Reference Number: <?php echo htmlspecialchars($clean_ref); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <h5>No Subjects Available</h5>
                        <p class="mb-0">There are no subjects assigned to your course (<?php echo htmlspecialchars($student_course_code); ?>) yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>