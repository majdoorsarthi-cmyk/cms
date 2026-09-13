<?php
session_name("STUDENT_SESSION");
session_start();
include('../../../db_config.php'); 

if (!isset($_SESSION['user'])) { 
    header("Location: ../../index.php"); 
    exit(); 
}

// नया अपडेट: क्विज़ शुरू होने का समय सेशन में स्टोर करें
if (!isset($_SESSION['quiz_start_time'])) {
    date_default_timezone_set('Asia/Kolkata');
    $_SESSION['quiz_start_time'] = date('Y-m-d H:i:s');
}

$u = $_SESSION['user'];

// 1. छात्र का नाम और रोल नंबर के आधार पर इनिशियल निकालना
$query = mysqli_query($conn, "SELECT name FROM students WHERE roll_no = '$u'");
$student = mysqli_fetch_assoc($query);
$username = strtoupper(substr($student['name'], 0, 2));

// 2. URL से subject_id प्राप्त करना
if (!isset($_GET['subject_id']) || empty($_GET['subject_id'])) {
    echo "<script>alert('सब्जेक्ट का चयन सही नहीं है!'); window.location.href='../my.php';</script>";
    exit();
}
$subject_id = mysqli_real_escape_string($conn, $_GET['subject_id']);

// सब्जेक्ट का नाम डेटाबेस से निकालना
$sub_query = mysqli_query($conn, "SELECT subject_name FROM subjects WHERE id = '$subject_id'");
$subject_data = mysqli_fetch_assoc($sub_query);
$subject_name = $subject_data['subject_name'] ?? "Diploma in Computer Application";

// 3. डेटाबेस से चुने गए subject_id के 50 रैंडम सवाल निकालना
if (!isset($_SESSION['quiz_start_time'][$subject_id])) {
    date_default_timezone_set('Asia/Kolkata');
    $_SESSION['quiz_start_time'][$subject_id] = date('Y-m-d H:i:s');
}

if (!isset($_SESSION['current_quiz_questions'][$subject_id])) {
    $q_query = mysqli_query($conn, "SELECT * FROM questions WHERE subject_id = '$subject_id' ORDER BY RAND() LIMIT 50");
    $questions = [];
    while($row = mysqli_fetch_assoc($q_query)) {
        $questions[] = $row;
    }
    $_SESSION['current_quiz_questions'][$subject_id] = $questions;
} else {
    $questions = $_SESSION['current_quiz_questions'][$subject_id];
}
$total_questions = count($questions);
$u_check = $_SESSION['user'] . 'gsa';
$count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM quiz_attempts WHERE roll_no = '$u_check' AND subject_id = '$subject_id'");
$count_row = mysqli_fetch_assoc($count_query);

if ($count_row['total'] >= 3) {
    echo "<script>alert('आपकी 3 अटेम्प्ट की सीमा पूरी हो चुकी है!'); window.location.href='view.php?subject_id=$subject_id';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <title>Attempt Quiz | AISECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --moodle-purple: #8A4F8D; }
        body { background: url('../../../book_bg.jpg') no-repeat center center fixed; background-size: cover; font-family: -apple-system, sans-serif; font-size: 14px; }
        .navbar-custom { background-color: var(--moodle-purple); color: white; padding: 5px 40px; min-height: 52px; }
        .content-container { padding: 30px 20px; display: flex; justify-content: center; gap: 20px; max-width: 1200px; margin: 0 auto; }
        .main-white-card { background: white; padding: 30px; border-radius: 4px; border: 1px solid #dee2e6; flex-grow: 1; max-width: 830px; }
        .quiz-nav-card { background: white; padding: 20px; border-radius: 4px; border: 1px solid #dee2e6; width: 280px; flex-shrink: 0; height: fit-content; position: sticky; top: 20px; }
        .course-header-title { border-bottom: 1px solid #dee2e6; padding-bottom: 15px; margin-bottom: 20px; font-size: 24px; color: #6b3b6e; font-weight: bold; }
        .question-block { border: 1px solid #cad1d7; background: #f8f9fa; border-radius: 4px; padding: 20px; margin-bottom: 20px; display: flex; gap: 15px; }
        .q-meta { width: 110px; flex-shrink: 0; font-size: 12.5px; color: #6a737d; border-right: 1px solid #dee2e6; padding-right: 10px; }
        .q-text-block { flex-grow: 1; }
        .nav-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 5px; margin-top: 15px; max-height: 250px; overflow-y: auto; padding-right: 5px; }
        .nav-num { border: 1px solid #dee2e6; padding: 6px 2px; text-align: center; font-size: 11.5px; text-decoration: none; color: #333; border-radius: 2px; background: #fff; }
        .nav-num:hover { background: #e9ecef; color: #000; }
        .timer-box { border: 1px solid #b91c1c; padding: 6px 12px; font-size: 13px; color: #b91c1c; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom d-flex justify-content-between px-4 text-white">
        <span><strong>AISECT</strong> &nbsp;&nbsp; Home &nbsp;&nbsp; Dashboard &nbsp;&nbsp; My courses</span>
        <span class="badge bg-light text-dark rounded-circle py-2 px-2"><?php echo $username; ?></span>
    </nav>
    
    <div class="content-container">
        <form id="quizForm" action="submit_quiz.php" method="POST" class="d-flex gap-4 w-100">
            <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
            
            <div class="main-white-card">
                <div class="course-header-title"><?php echo htmlspecialchars($subject_name); ?></div>
                
                <?php if ($total_questions == 0): ?>
                    <div class="alert alert-warning">इस सब्जेक्ट के लिए अभी कोई सवाल उपलब्ध नहीं हैं।</div>
                <?php else: ?>
                    
                    <?php foreach($questions as $index => $q): $q_num = $index + 1; 
                        // --- रैंडमाइजेशन लॉजिक ---
                        $options_map = ['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']];
                        $keys = ['A', 'B', 'C', 'D'];
                        shuffle($keys);
                        $shuffled = [];
                        foreach ($keys as $k) { $shuffled[$k] = $options_map[$k]; }
                    ?>
                        <div class="question-block" id="q_block_<?php echo $q_num; ?>">
                            <div class="q-meta">
                                <div class="fw-bold text-dark mb-1" style="color:var(--moodle-purple);">Question <?php echo $q_num; ?></div>
                                <div class="mb-1 text-muted small" id="status_<?php echo $q_num; ?>">Not yet answered</div>
                                <div class="mb-2 text-muted small">Marked out of 1.0</div>
                                <a href="#" class="text-secondary text-decoration-none small"><i class="fa-regular fa-flag"></i> Flag question</a>
                            </div>
                            <div class="q-text-block">
                                <p class="fw-semibold text-dark"><?php echo nl2br(htmlspecialchars($q['question_text'])); ?></p>
                                <div class="mt-3">
                                    <?php foreach($shuffled as $key => $option_text): ?>
                                        <div class="my-2 small">
                                            <input type="radio" name="answer[<?php echo $q['id']; ?>]" id="q_<?php echo $q_num; ?>_<?php echo $key; ?>" value="<?php echo $key; ?>" onclick="markAnswered(<?php echo $q_num; ?>)"> 
                                            <label for="q_<?php echo $q_num; ?>_<?php echo $key; ?>">
                                                <?php echo $key; ?>. <?php echo htmlspecialchars($option_text); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                <?php endif; ?>
            </div>

            <div class="quiz-nav-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="timer-box"><i class="fa-regular fa-clock"></i> Time left <span id="timer">2:00:00</span></div>
                </div>
                <h6 class="fw-bold mt-2 text-secondary" style="font-size:13px;">Quiz navigation</h6>
                <div class="nav-grid">
                    <?php for($i=1; $i<=$total_questions; $i++): ?>
                        <a href="#q_block_<?php echo $i; ?>" id="nav_num_<?php echo $i; ?>" class="nav-num"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <div class="mt-4 border-top pt-3">
                    <button type="button" onclick="goToSummary()" class="btn btn-link p-0 text-decoration-none text-muted small">Finish attempt ...</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let totalSeconds = 120 * 60; 
        const timerElement = document.getElementById('timer');
        function updateTimer() {
            let h = Math.floor(totalSeconds / 3600);
            let m = Math.floor((totalSeconds % 3600) / 60);
            let s = totalSeconds % 60;
            timerElement.textContent = `${h}:${m<10?'0'+m:m}:${s<10?'0'+s:s}`;
            if (totalSeconds <= 0) { clearInterval(countdown); document.getElementById('quizForm').submit(); }
            totalSeconds--;
        }
        let countdown = setInterval(updateTimer, 1000);
        function markAnswered(qNum) {
            document.getElementById('status_' + qNum).innerText = "Answered";
            let navNum = document.getElementById('nav_num_' + qNum);
            navNum.style.background = "#e9ecef";
            navNum.style.fontWeight = "bold";
            navNum.style.borderBottom = "3px solid #555";
        }
        function goToSummary() {
            let form = document.getElementById('quizForm');
            form.action = "summary.php?subject_id=<?php echo $subject_id; ?>";
            form.submit();
        }
    </script>
</body>
</html>