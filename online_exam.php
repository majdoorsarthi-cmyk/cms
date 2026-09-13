<?php 
/**
 * CMS PRO - Advanced Subject-Wise CBT Exam Engine with PhonePe Mobile UI
 */

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

include 'db_config.php'; 

// 1. Auth Check (छात्र लॉगिन जांचें)
if (!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$stu_id = $stu['user_id'] ?? ($stu['id'] ?? null);

// 2. छात्र का प्रोफाइल और कोर्स/सेमेस्टर निकालें
$stmt = mysqli_prepare($conn, "SELECT course, roll_no, semester FROM students WHERE user_id = ? OR id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $stu_id, $stu_id);
mysqli_stmt_execute($stmt);
$p_res = mysqli_stmt_get_result($stmt);
$p_data = mysqli_fetch_assoc($p_res);

if (!$p_data) {
    die("<div style='padding:50px; text-align:center; font-family:sans-serif; background:#f8fafc; min-height:100vh;'>
            <h2 style='color:#ef4444;'>❌ त्रुटि: छात्र प्रोफाइल नहीं मिली!</h2>
            <p style='color:#64748b;'>कृपया अपने एडमिन से संपर्क करें।</p>
            <br><a href='student_dashboard.php' style='color:#2563eb; font-weight:700;'>← डैशबोर्ड पर वापस जाएं</a>
         </div>");
}

$stu_course = trim($p_data['course']);
$roll_no = $p_data['roll_no'];
$stu_sem = $p_data['semester'] ?? 'I';

// ऑटो टेबल क्रिएशन (Exam Results)
$create_res_table = "CREATE TABLE IF NOT EXISTS `exam_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `roll_no` VARCHAR(100),
  `course` VARCHAR(150),
  `subject_name` VARCHAR(255),
  `total_questions` INT,
  `marks_obtained` INT,
  `exam_date` DATETIME,
  `status` VARCHAR(20) DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $create_res_table);

// 3. परीक्षा सबमिट (Result Processing)
$score = 0;
$submitted = false;
$total_q = 0;
$selected_subject_name = "";

if (isset($_POST['submit_exam'])) {
    $submitted = true;
    $selected_subject_name = mysqli_real_escape_string($conn, $_POST['subject_name'] ?? 'General Exam');
    
    if (isset($_POST['answers']) && is_array($_POST['answers'])) {
        foreach ($_POST['answers'] as $q_id => $user_ans) {
            $total_q++;
            $q_id = intval($q_id);
            $user_ans = mysqli_real_escape_string($conn, $user_ans);
            
            $check_q = mysqli_query($conn, "SELECT correct_option FROM questions WHERE id = '$q_id'");
            $res = mysqli_fetch_assoc($check_q);
            if ($res && strtoupper($user_ans) == strtoupper($res['correct_option'])) { 
                $score++; 
            }
        }
    }

    $today = date('Y-m-d H:i:s');
    $ins_stmt = mysqli_prepare($conn, "INSERT INTO exam_results (student_id, roll_no, course, subject_name, total_questions, marks_obtained, exam_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins_stmt, "isssiis", $stu_id, $roll_no, $stu_course, $selected_subject_name, $total_q, $score, $today);
    mysqli_stmt_execute($ins_stmt);
}

// 4. चुने गए विषय (Subject) के प्रश्न फेच करना
$selected_subject_id = $_GET['subject_id'] ?? null;
$current_subject = null;
$questions_list = [];

if ($selected_subject_id) {
    // विषय की जानकारी फेच करें
    $sub_q = mysqli_query($conn, "SELECT * FROM subjects WHERE id = '$selected_subject_id'");
    $current_subject = mysqli_fetch_assoc($sub_q);

    if ($current_subject) {
        $s_name = $current_subject['subject_name'];
        // उस विषय के 10 रैंडम प्रश्न फेच करें
        $q_stmt = mysqli_prepare($conn, "SELECT * FROM questions WHERE subject_id = ? OR subject_name = ? ORDER BY RAND() LIMIT 10");
        mysqli_stmt_bind_param($q_stmt, "is", $selected_subject_id, $s_name);
        mysqli_stmt_execute($q_stmt);
        $q_res = mysqli_stmt_get_result($q_stmt);

        if ($q_res) {
            while ($row = mysqli_fetch_assoc($q_res)) {
                $questions_list[] = $row;
            }
        }
    }
}

// सभी विषयों की सूची (अगर कोई विषय नहीं चुना गया)
$all_subjects_query = mysqli_query($conn, "SELECT * FROM subjects ORDER BY id ASC");
$questions_count = count($questions_list);
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>CBT Live Exam | <?= htmlspecialchars($stu_course) ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb; 
            --sidebar-bg: #0f172a;
            --nav-text: #94a3b8; 
            --bg: #f8fafc; 
            --card-bg: #ffffff;
            --text-main: #0f172a; 
            --text-sub: #64748b;

            /* PhonePe Mobile Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-light: #f3e8ff;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; user-select: none; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; }

        /* 🖥️ DESKTOP SIDEBAR */
        .desktop-sidebar {
            width: 270px; background: var(--sidebar-bg); height: 100vh;
            position: fixed; top: 0; left: 0; padding: 24px 16px; color: white;
            z-index: 10050; flex-shrink: 0;
        }
        .sidebar-brand { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a { color: var(--nav-text); text-decoration: none; padding: 12px 16px; display: flex; align-items: center; gap: 14px; border-radius: 12px; font-size: 15px; font-weight: 700; transition: all 0.2s; }
        .sidebar-menu a.active, .sidebar-menu a:hover { background: #2563eb; color: #ffffff; }

        .main-content { margin-left: 270px; width: calc(100% - 270px); padding: 30px; }

        /* Subject Grid Cards (Desktop) */
        .subject-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .subject-card { background: #ffffff; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: transform 0.2s; position: relative; }
        .subject-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        
        .sub-tag { background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 20px; display: inline-block; margin-bottom: 12px; }
        .sub-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 10px; line-height: 1.4; }
        .sub-date { font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 6px; }

        .btn-start-exam { display: block; text-align: center; background: #2563eb; color: #ffffff; padding: 12px; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 15px; }

        /* Exam Questions CBT Layout */
        .exam-layout { display: grid; grid-template-columns: 1fr 320px; gap: 24px; }
        .timer-bar { background: #ffffff; padding: 18px 24px; border-radius: 18px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; }
        .timer-badge { font-size: 20px; font-weight: 800; color: #ef4444; background: #fef2f2; padding: 8px 18px; border-radius: 12px; border: 1px solid #fecaca; }

        .q-card { background: #fff; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .q-number { font-size: 13px; font-weight: 800; color: var(--primary); background: #eff6ff; padding: 4px 12px; border-radius: 20px; display: inline-block; margin-bottom: 14px; }
        .q-text { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 20px; line-height: 1.6; }

        .option-label { display: flex; align-items: center; padding: 16px 20px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; font-size: 16px; font-weight: 700; color: #334155; }
        .option-label:hover { background: #eff6ff; border-color: #93c5fd; }
        .option-label input[type="radio"] { margin-right: 14px; width: 20px; height: 20px; accent-color: var(--primary); }

        .palette-card { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 20px; position: sticky; top: 20px; }
        .palette-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-top: 15px; }
        .palette-btn { width: 100%; height: 44px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f1f5f9; color: #475569; font-weight: 800; font-size: 15px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .palette-btn.answered { background: #22c55e; color: white; border-color: #16a34a; }

        .btn-submit { background: #16a34a; color: white; border: none; padding: 18px; border-radius: 14px; font-weight: 800; font-size: 17px; width: 100%; cursor: pointer; margin-top: 15px; box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3); }

        .score-card { background: #fff; border-radius: 20px; padding: 40px; text-align: center; border: 1px solid #e2e8f0; max-width: 600px; margin: 40px auto; }
        .score-circle { width: 140px; height: 140px; border-radius: 50%; background: #dcfce7; color: #16a34a; border: 6px solid #22c55e; display: flex; align-items: center; justify-content: center; margin: 20px auto; font-size: 34px; font-weight: 800; }

        .mobile-header, .mobile-bottom-nav { display: none; }

        /* 📱 MOBILE VIEW (PhonePe Style UI & Extra Large Fonts) */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; }
            .desktop-sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 82px 14px 90px 14px !important; }

            /* PhonePe Purple Top Bar */
            .mobile-header {
                display: flex !important; position: fixed; top: 0; left: 0; right: 0; height: 68px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                z-index: 9999; padding: 0 18px; align-items: center; justify-content: space-between;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.35);
            }
            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { color: #ffffff; font-size: 20px; width: 42px; height: 42px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 13.5px !important; font-weight: 500; display: block; }

            /* Mobile Larger Fonts & Cards */
            .sub-title { font-size: 20px !important; font-weight: 800 !important; color: #0f172a; }
            .btn-start-exam { background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important; padding: 16px !important; font-size: 18px !important; font-weight: 800 !important; border-radius: 14px !important; box-shadow: 0 6px 18px rgba(95, 37, 159, 0.3) !important; }

            .exam-layout { grid-template-columns: 1fr !important; }
            
            /* Question Box Big Text */
            .q-card { border-radius: 22px !important; padding: 22px 18px !important; }
            .q-number { font-size: 14.5px !important; padding: 6px 14px !important; font-weight: 800; }
            .q-text { font-size: 20px !important; font-weight: 800 !important; color: #0f172a; line-height: 1.5; }
            
            /* Option Label Extra Big Text */
            .option-label { padding: 18px 20px !important; font-size: 18px !important; font-weight: 800 !important; border-radius: 16px !important; border: 2.5px solid #cbd5e1 !important; color: #0f172a !important; margin-bottom: 14px; }
            .option-label input[type="radio"] { width: 24px !important; height: 24px !important; margin-right: 16px !important; }

            .timer-bar { border-radius: 20px !important; padding: 16px !important; }
            .timer-badge { font-size: 22px !important; font-weight: 800; }

            .btn-submit { padding: 20px !important; font-size: 20px !important; border-radius: 16px !important; }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                height: 72px; background: #ffffff; border-top: 1px solid #e2e8f0; z-index: 9998;
                justify-content: space-around; align-items: center; box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
            }
            .phonepe-nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b; font-size: 13px !important; font-weight: 700; width: 25%; }
            .phonepe-nav-item i { font-size: 23px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body oncontextmenu="return false;">

<!-- Mobile PhonePe Header Bar -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="student_dashboard.php" class="mobile-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h3>CBT ऑनलाइन परीक्षा</h3>
            <small><?= htmlspecialchars($stu_course); ?> (Sem <?= htmlspecialchars($stu_sem); ?>)</small>
        </div>
    </div>
    <div style="color:#fff; font-size:24px;">
        <i class="fa-solid fa-laptop-code"></i>
    </div>
</div>

<!-- 🖥️ DESKTOP SIDEBAR -->
<div class="desktop-sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-graduation-cap" style="color:#3b82f6;"></i>
        <span>TC ACADEMY</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="student_dashboard.php"><i class="fa-solid fa-house"></i> <span>डैशबोर्ड</span></a></li>
        <li><a href="video_lectures.php"><i class="fa-solid fa-video"></i> <span>वीडियो लेक्चर्स</span></a></li>
        <li><a href="online_exam.php" class="active"><i class="fa-solid fa-laptop-code"></i> <span>ऑनलाइन एग्जाम</span></a></li>
        <li><a href="my_docs.php"><i class="fa-solid fa-folder-closed"></i> <span>माई डॉक्यूमेंट्स</span></a></li>
        <li><a href="logout.php" style="color:#ef4444;"><i class="fa-solid fa-power-off"></i> <span>लॉगआउट</span></a></li>
    </ul>
</div>

<!-- MAIN CONTENT CONTAINER -->
<div class="main-content">

    <?php if ($submitted): ?>
        <!-- RESULT BOARD -->
        <div class="score-card">
            <i class="fa-solid fa-circle-check fa-4x" style="color:#22c55e; margin-bottom:15px;"></i>
            <h1 style="font-size:24px; font-weight:800; color:#0f172a;">परीक्षा सफलतापूर्वक जमा हुई!</h1>
            <p style="color:#64748b; font-size:15px; margin-top:6px;">विषय: <b><?= htmlspecialchars($selected_subject_name); ?></b></p>
            
            <div class="score-circle">
                <?= $score ?> / <?= $total_q ?>
            </div>

            <p style="font-size:16px; font-weight:700; color:#334155;">कोर्स: <span style="color:var(--primary);"><?= htmlspecialchars($stu_course) ?></span></p>
            <p style="font-size:14px; color:#94a3b8; margin-top:4px;">रोल नंबर: <?= htmlspecialchars($roll_no) ?></p>

            <br>
            <a href="online_exam.php" style="display:inline-block; padding:14px 28px; background:var(--phonepe-purple); color:#fff; text-decoration:none; font-weight:800; border-radius:14px; font-size:16px;">
                <i class="fa-solid fa-arrow-left me-2"></i> अन्य परीक्षा चुनें
            </a>
        </div>

    <?php elseif (!$selected_subject_id): ?>

        <!-- STEP 1: SUBJECT SELECTION PAGE -->
        <div style="margin-bottom: 20px;">
            <h2 style="font-size: 22px; font-weight: 800; color: #0f172a;">
                <i class="fa-solid fa-book-open me-2" style="color:var(--phonepe-purple);"></i> विषय का चयन करें (Select Subject)
            </h2>
            <p style="color: #64748b; font-size: 15px; font-weight: 600;">अपने सेमेस्टर और कोर्स के अनुसार टेस्ट शुरू करने के लिए विषय चुनें:</p>
        </div>

        <div class="subject-grid">
            <?php if (mysqli_num_rows($all_subjects_query) > 0): ?>
                <?php while ($sub = mysqli_fetch_assoc($all_subjects_query)): ?>
                    <div class="subject-card">
                        <span class="sub-tag">Semester <?= htmlspecialchars($sub['semester']); ?></span>
                        <h3 class="sub-title"><?= htmlspecialchars($sub['subject_name']); ?></h3>
                        
                        <div class="sub-date">
                            <i class="fa-regular fa-calendar-check" style="color:var(--phonepe-purple);"></i>
                            <span>परीक्षा तिथि: <?= date('d M, Y', strtotime($sub['exam_date'])); ?></span>
                        </div>

                        <a href="online_exam.php?subject_id=<?= $sub['id']; ?>" class="btn-start-exam">
                            <i class="fa-solid fa-pen-to-square me-2"></i> परीक्षा शुरू करें
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding:50px; background:#fff; border-radius:20px; border:1px solid #e2e8f0;">
                    <i class="fa-solid fa-folder-open fa-3x" style="color:#cbd5e1; margin-bottom:15px;"></i>
                    <h3 style="color:#64748b;">कोई विषय उपलब्ध नहीं है।</h3>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <!-- STEP 2: LIVE QUESTIONS CBT EXAM PAGE -->
        <div class="timer-bar">
            <div>
                <span style="font-size: 12px; font-weight:800; color: #64748b; text-transform:uppercase;">चयनित विषय</span>
                <div style="font-size:18px; font-weight:800; color:var(--phonepe-purple);"><?= htmlspecialchars($current_subject['subject_name']) ?></div>
            </div>
            <div class="timer-badge">
                <i class="fa-regular fa-clock me-1"></i> <span id="timer">10:00</span>
            </div>
        </div>

        <form id="examForm" method="POST">
            <input type="hidden" name="subject_name" value="<?= htmlspecialchars($current_subject['subject_name']); ?>">

            <div class="exam-layout">
                <!-- QUESTIONS LIST -->
                <div>
                    <?php if ($questions_count > 0): ?>
                        <?php foreach ($questions_list as $index => $q): ?>
                            <div class="q-card" id="q_box_<?= $index + 1 ?>">
                                <span class="q-number">प्रश्न <?= $index + 1 ?> of <?= $questions_count ?></span>
                                <div class="q-text"><?= htmlspecialchars($q['question_text']) ?></div>
                                
                                <label class="option-label">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="A" onchange="markAnswered(<?= $index + 1 ?>)">
                                    (A) <?= htmlspecialchars($q['option_a']) ?>
                                </label>
                                
                                <label class="option-label">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="B" onchange="markAnswered(<?= $index + 1 ?>)">
                                    (B) <?= htmlspecialchars($q['option_b']) ?>
                                </label>
                                
                                <label class="option-label">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="C" onchange="markAnswered(<?= $index + 1 ?>)">
                                    (C) <?= htmlspecialchars($q['option_c']) ?>
                                </label>
                                
                                <label class="option-label">
                                    <input type="radio" name="answers[<?= $q['id'] ?>]" value="D" onchange="markAnswered(<?= $index + 1 ?>)">
                                    (D) <?= htmlspecialchars($q['option_d']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="background:#fff; border-radius:20px; padding:60px 20px; text-align:center; color:#64748b; border:1px solid #e2e8f0;">
                            <i class="fa-solid fa-file-excel fa-3x" style="color:#cbd5e1; margin-bottom:15px;"></i>
                            <h3>इस विषय में कोई प्रश्न उपलब्ध नहीं हैं।</h3>
                            <p>कृपया एडमिन पैनल से <b><?= htmlspecialchars($current_subject['subject_name']) ?></b> में प्रश्न जोड़ें।</p>
                            <br>
                            <a href="online_exam.php" style="color:var(--primary); font-weight:800; text-decoration:none;">← विषय सूची पर वापस जाएं</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- QUESTION PALETTE WIDGET -->
                <?php if ($questions_count > 0): ?>
                    <div>
                        <div class="palette-card">
                            <h4 style="font-size:15px; font-weight:800; color:#0f172a; margin-bottom:8px;"><i class="fa-solid fa-border-all me-1"></i> प्रश्न नेविगेटर</h4>
                            <p style="font-size:13px; color:#64748b;">हरे रंग का मतलब आपने उत्तर दे दिया है।</p>
                            
                            <div class="palette-grid">
                                <?php for($i = 1; $i <= $questions_count; $i++): ?>
                                    <a href="#q_box_<?= $i ?>" id="pal_btn_<?= $i ?>" class="palette-btn"><?= $i ?></a>
                                <?php endfor; ?>
                            </div>

                            <hr style="border:0; border-top:1px solid #e2e8f0; margin:20px 0;">

                            <button type="submit" name="submit_exam" class="btn-submit">
                                <i class="fa-solid fa-paper-plane me-2"></i> टेस्ट जमा करें (Submit)
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </form>

    <?php endif; ?>

</div>

<!-- Mobile PhonePe Bottom Navigation Bar -->
<div class="mobile-bottom-nav">
    <a href="student_dashboard.php" class="phonepe-nav-item">
        <i class="fa-solid fa-house"></i>
        <span>होम</span>
    </a>
    <a href="video_lectures.php" class="phonepe-nav-item">
        <i class="fa-solid fa-video"></i>
        <span>वीडियो</span>
    </a>
    <a href="online_exam.php" class="phonepe-nav-item active">
        <i class="fa-solid fa-laptop-code"></i>
        <span>एग्जाम</span>
    </a>
    <a href="my_docs.php" class="phonepe-nav-item">
        <i class="fa-solid fa-id-card"></i>
        <span>वॉलेट</span>
    </a>
</div>

<script>
    function markAnswered(qNum) {
        const btn = document.getElementById('pal_btn_' + qNum);
        if(btn) {
            btn.classList.add('answered');
        }
    }

    // TIMER LOGIC (10 Minutes)
    let timeInSeconds = 600; 
    const display = document.querySelector('#timer');
    
    if(display) {
        const timerInterval = setInterval(function () {
            let minutes = Math.floor(timeInSeconds / 60);
            let seconds = timeInSeconds % 60;
            
            seconds = seconds < 10 ? '0' + seconds : seconds;
            display.textContent = minutes + ":" + seconds;
            
            if(timeInSeconds < 60) {
                display.style.color = "#dc2626";
            }

            if (--timeInSeconds < 0) {
                clearInterval(timerInterval);
                alert("समय समाप्त हो गया है! आपका एग्जाम ऑटोमेटिक सबमिट किया जा रहा है।");
                
                let form = document.getElementById('examForm');
                if(form) {
                    let submitBtn = document.createElement('input');
                    submitBtn.type = 'hidden';
                    submitBtn.name = 'submit_exam';
                    submitBtn.value = '1';
                    form.appendChild(submitBtn);
                    form.submit();
                }
            }
        }, 1000);
    }

    // Anti-Cheating Tab Switch Guard
    let tabSwitchCount = 0;
    window.onblur = function() {
        let form = document.getElementById('examForm');
        if(form) {
            tabSwitchCount++;
            if (tabSwitchCount <= 2) {
                alert("⚠️ चेतावनी: परीक्षा के दौरान टैब न बदलें! (" + tabSwitchCount + "/2)");
            } else {
                alert("❌ उल्लंघन का पता चला! परीक्षा ऑटो-सबमिट की जा रही है।");
                let submitBtn = document.createElement('input');
                submitBtn.type = 'hidden';
                submitBtn.name = 'submit_exam';
                submitBtn.value = '1';
                form.appendChild(submitBtn);
                form.submit();
            }
        }
    };
</script>

</body>
</html>