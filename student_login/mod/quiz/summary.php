<?php
session_name("STUDENT_SESSION");
session_start();
include('../../../db_config.php'); 

if (!isset($_SESSION['user'])) { 
    header("Location: ../../index.php"); 
    exit(); 
}

$u = $_SESSION['user'];

// विषय आईडी (subject_id) को GET या POST दोनों से सुरक्षित तरीके से प्राप्त करना
$subject_id = isset($_REQUEST['subject_id']) ? (int)$_REQUEST['subject_id'] : 1;
$final_roll = (strpos($u, 'gsa') !== false) ? $u : $u . 'gsa';

// ==========================================
// सबमिट लॉजिक: जब छात्र मॉडल पॉपअप में फाइनल सबमिट करेगा
// ==========================================
if(isset($_POST['final_submit'])) {
    $subject_id = (int)$_POST['subject_id'];
    $ref_number = "REF-" . $final_roll . "-" . date("dmy-His");
    
    // समय और ड्यूरेशन की गणना
    $start_time = isset($_SESSION['quiz_start_time']) ? $_SESSION['quiz_start_time'] : date('Y-m-d H:i:s');
    $end_time = date('Y-m-d H:i:s');
    
    $start_ts = strtotime($start_time);
    $end_ts = time(); 
    $diff = $end_ts - $start_ts;
    $duration_text = floor($diff / 60) . ' mins ' . ($diff % 60) . ' secs';

    // डायनेमिक अटेम्प्ट नंबर गिनने का लॉजिक (MAX + 1) ताकि 1, 2, 3 स्टोर हो
    $count_query = mysqli_query($conn, "SELECT MAX(attempt_number) as max_att FROM quiz_attempts WHERE roll_no = '$final_roll' AND subject_id = '$subject_id'");
    $count_row = mysqli_fetch_assoc($count_query);
    $next_attempt = ($count_row['max_att'] ?? 0) + 1;

    // केवल 3 अटेम्प्ट तक ही डेटाबेस में एंट्री होने दें
    if ($next_attempt <= 3) {
        $insert_query = "INSERT INTO quiz_attempts (roll_no, subject_id, attempt_number, start_time, end_time, duration_text, reference_code) 
                         VALUES ('$final_roll', '$subject_id', '$next_attempt', '$start_time', '$end_time', '$duration_text', '$ref_number')";
        
        if(mysqli_query($conn, $insert_query)) {
            unset($_SESSION['quiz_start_time']); // क्विज़ समय का सेशन खाली करें
            
            header("Location: review.php?subject_id=" . $subject_id);
            exit();
        }
    } else {
        header("Location: review.php?subject_id=" . $subject_id);
        exit();
    }
}

// 1. छात्र का नाम और कोर्स निकालना
$query = mysqli_query($conn, "SELECT name, course FROM students WHERE roll_no = '$u'");
$student = mysqli_fetch_assoc($query);
$username = strtoupper(substr($student['name'] ?? 'ST', 0, 2));
$student_course = trim($student['course'] ?? '');

// 2. डेटाबेस से वर्तमान सब्जेक्ट की जानकारी (Subject Name, Semester, Course) निकालना
$sub_query = mysqli_query($conn, "SELECT subject_name, semester, course FROM subjects WHERE id = '$subject_id'");
$subject_data = mysqli_fetch_assoc($sub_query);

$current_subject_name = $subject_data['subject_name'] ?? 'Subject';
$current_semester = $subject_data['semester'] ?? 'I';
$db_course = $subject_data['course'] ?? $student_course;

// 3. कोर्स डिस्प्ले टाइटल सेट करने का डायनामिक लॉजिक
$display_course_title = "";
$upper_course = strtoupper($db_course);

if ($upper_course == 'DCA' || $upper_course == 'DIPLOMA IN COMPUTER APPLICATION') {
    $display_course_title = "Diploma in Computer Application (New Pattern)";
} elseif ($upper_course == 'PGDCA') {
    $display_course_title = "Post Graduate Diploma in Computer Applications";
} elseif ($upper_course == 'BA') {
    $display_course_title = "Bachelor of Arts (BA)";
} else {
    $display_course_title = htmlspecialchars($db_course);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Summary of attempt | AISECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --moodle-purple: #8A4F8D; }
        body { background: url('../../../book_bg.jpg') no-repeat center center fixed; background-size: cover; font-family: -apple-system, sans-serif; font-size: 13px; }
        .navbar-custom { background-color: var(--moodle-purple); color: white; padding: 5px 40px; min-height: 52px; }
        
        /* टू-कॉलम मूडल लेआउट */
        .content-container { padding: 30px 20px; display: flex; justify-content: center; gap: 20px; max-width: 1250px; margin: 0 auto; align-items: flex-start; }
        .main-white-card { background: white; padding: 30px; border-radius: 4px; border: 1px solid #dee2e6; flex-grow: 1; max-width: 850px; }
        .sidebar-card { background: white; padding: 20px; border-radius: 4px; border: 1px solid #dee2e6; width: 320px; min-width: 320px; }
        
        .course-title-big { font-size: 22px; color: var(--moodle-purple); margin-bottom: 5px; font-weight: 400; }
        .subject-title-sub { font-size: 18px; color: #212529; margin-bottom: 20px; }
        .btn-purple-sm { background: var(--moodle-purple); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; }
        
        /* ग्रिड नेविगेशन स्टाइल्स */
        .nav-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 5px; margin-top: 15px; }
        .nav-box { border: 1px solid #bbb; padding: 6px 0; text-align: center; background: #f8f9fa; border-radius: 3px; font-size: 11px; color: #333; font-weight: 500; }
        
        .moodle-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; display: none; align-items: center; justify-content: center; }
        .moodle-modal { background: white; border-radius: 6px; width: 100%; max-width: 450px; border: 1px solid #999; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom d-flex justify-content-between px-4 text-white">
        <span><strong>AISECT</strong> &nbsp;&nbsp; Home &nbsp;&nbsp; Dashboard &nbsp;&nbsp; My courses</span>
        <div class="user-avatar text-dark bg-light rounded-circle p-1 fw-bold text-center" style="width:32px; height:32px; line-height:22px;"><?php echo $username; ?></div>
    </nav>
    
    <!-- मुख्य लेआउट कंटेनर -->
    <div class="content-container">
        <!-- लेफ्ट side: प्रश्नों की स्थिति -->
        <div class="main-white-card shadow-sm">
            <!-- डायनामिक कोर्स नाम -->
            <div class="course-title-big"><?php echo htmlspecialchars($display_course_title); ?></div>
            <!-- डायनामिक सब्जेक्ट और सेमेस्टर -->
            <div class="subject-title-sub"><?php echo htmlspecialchars($current_subject_name); ?> (Semester <?php echo htmlspecialchars($current_semester); ?>)</div>
            
            <h6 class="fw-bold text-secondary mb-3">Summary of attempt</h6>
            
            <table class="table table-striped table-bordered text-start align-middle">
                <thead>
                    <tr class="table-light text-muted" style="font-size: 12px;">
                        <th style="width: 40%;">Question</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1; $i<=50; $i++): ?>
                    <tr>
                        <td class="ps-3">Question <?php echo $i; ?></td>
                        <td class="text-muted">Answer saved</td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button class="btn-purple-sm" onclick="document.getElementById('submit-modal').style.display='flex'">Submit all and finish</button>
            </div>
        </div>

        <!-- राइट side: Quiz Navigation ग्रिड बॉक्स -->
        <div class="sidebar-card shadow-sm">
            <h6 class="text-muted mb-3" style="font-size: 13px;">Quiz navigation</h6>
            <div class="nav-grid">
                <?php for($j=1; $j<=50; $j++): ?>
                    <div class="nav-box"><?php echo $j; ?></div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- कन्फर्मेशन पॉपअप मॉडल -->
    <div id="submit-modal" class="moodle-modal-overlay">
        <div class="moodle-modal">
            <div class="p-3 border-bottom fw-bold text-start" style="font-size:14px;">Submit all your answers and finish?</div>
            <div class="p-3 text-start text-muted" style="font-size: 12.5px;">Once you submit, you will no longer be able to change your answers for this attempt.</div>
            <div class="p-3 bg-light border-top d-flex justify-content-end gap-2">
                <button class="btn btn-sm btn-secondary" onclick="document.getElementById('submit-modal').style.display='none'">Cancel</button>
                <form method="POST" action="summary.php">
                    <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                    <button type="submit" name="final_submit" class="btn btn-sm text-white" style="background:#8A4F8D;">Submit all and finish</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>