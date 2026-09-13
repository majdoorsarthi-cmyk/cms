<?php
session_start();
require_once 'db_connect.php';

// Agar student login nahi hai, to use login page par bhejein
if (!isset($_SESSION['student_id']) || !isset($_SESSION['current_attempt_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$attempt_id = $_SESSION['current_attempt_id'];
$exam_id = $_SESSION['current_exam_id'];

try {
    // 1. Exam ki details aur bacha hua time nikalna
    $exam_stmt = $pdo->prepare("
        SELECT e.*, st.start_time 
        FROM exams e 
        JOIN student_tests st ON e.id = st.exam_id 
        WHERE st.id = ?
    ");
    $exam_stmt->execute([$attempt_id]);
    $exam = $exam_stmt->fetch();

    if (!$exam) {
        die("परीक्षा का रिकॉर्ड नहीं मिला।");
    }

    // Timer logic (Minutes ko seconds me badal kar bacha hua time nikalna)
    $duration_seconds = $exam['duration_minutes'] * 60;
    $elapsed_seconds = time() - strtotime($exam['start_time']);
    $time_left = $duration_seconds - $elapsed_seconds;

    if ($time_left <= 0) {
        // Agar time khatam ho gaya ho to seedhe submit page par bhejein
        header("Location: submit_test.php?attempt_id=" . $attempt_id);
        exit;
    }

    // 2. Excel (CSV) se upload kiye gaye saare questions nikalna
    $q_stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ? ORDER BY id ASC");
    $q_stmt->execute([$exam_id]);
    $questions = $q_stmt->fetchAll();

    if (empty($questions)) {
        die("<div class='container mt-5 alert alert-warning text-center'>इस परीक्षा में अभी कोई प्रश्न अपलोड नहीं किए गए हैं। कृपया पहले एडमिन पैनल से CSV अपलोड करें।</div>");
    }

    // 3. छात्र द्वारा पहले से दिए गए उत्तरों को निकालना (ताकि रिफ्रेश होने पर टिक गायब न हो)
    $ans_stmt = $pdo->prepare("SELECT question_id, selected_option FROM student_answers WHERE attempt_id = ?");
    $ans_stmt->execute([$attempt_id]);
    $saved_answers = $ans_stmt->fetchAll(PDO::FETCH_KEY_PAIR); // यह [question_id => selected_option] का एरे बना देगा

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($exam['exam_name']); ?> - लाइव टेस्ट</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark: #0f172a;
            --accent-blue: #2563eb;
            --bg-light: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        }

        body { 
            background-color: var(--bg-light); 
            user-select: none; 
            -webkit-user-select: none; 
            font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;
        }

        /* 🎯 टॉप हेडर स्टाइल */
        .exam-header { 
            background: linear-gradient(135deg, #1e293b, var(--primary-dark)); 
            color: white; 
            padding: 16px 28px; 
            border-bottom: 3px solid var(--accent-blue);
        }
        
        .timer-box { 
            background: #ef4444; 
            color: white; 
            padding: 10px 20px; 
            font-weight: 700; 
            border-radius: 12px; 
            font-size: 1.25rem; 
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            letter-spacing: 0.5px;
        }

        /* ❓ मुख्य प्रश्न कार्ड */
        .question-card { 
            background: white; 
            padding: 35px; 
            border-radius: 20px; 
            box-shadow: var(--card-shadow); 
            min-height: 400px;
            border: 1px solid #e2e8f0;
        }

        .question-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.6;
        }

        /* 🔘 नए आधुनिक रेडियो बॉक्स और लेबल्स */
        .option-label { 
            border: 2px solid #e2e8f0; 
            border-radius: 14px; 
            padding: 16px 20px;
            transition: all 0.2s ease; 
            cursor: pointer; 
            display: flex;
            align-items: center;
            width: 100%;
            background-color: #ffffff;
            font-size: 1.05rem;
            color: #475569;
            font-weight: 500;
        }

        .option-label:hover { 
            background-color: #f1f5f9; 
            border-color: #cbd5e1; 
            transform: translateY(-1px);
        }

        .btn-check:checked + .option-label { 
            background-color: #eff6ff; 
            border-color: var(--accent-blue); 
            color: #1e40af; 
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06);
        }

        .option-badge {
            background-color: #f1f5f9;
            color: #64748b;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 14px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-check:checked + .option-label .option-badge {
            background-color: var(--accent-blue);
            color: white;
        }

        /* 🎨 राइट साइडबार और पैलेट */
        .sidebar { 
            background: white; 
            padding: 26px; 
            border-radius: 20px; 
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        .palette-container {
            max-height: 320px; 
            overflow-y: auto;
            padding-right: 4px;
        }

        /* स्क्रॉलबार को क्लीन बनाने के लिए */
        .palette-container::-webkit-scrollbar { width: 6px; }
        .palette-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .palette-btn { 
            width: 44px; 
            height: 44px; 
            margin: 5px; 
            font-weight: 600; 
            border-radius: 10px; 
            font-size: 0.95rem; 
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .palette-btn:hover {
            transform: scale(1.08);
        }

        /* 🚦 स्टेटस इंडिकेटर गाइड */
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 8px;
        }

        .nav-btn {
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 600;
            transition: all 0.2s;
        }
    </style>
</head>
<body oncontextmenu="return false;">

<div class="exam-header d-flex justify-content-between align-items-center shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <div class="text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <i class="bi bi-file-earmark-text-fill fs-4"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">🎯 <?php echo htmlspecialchars($exam['exam_name']); ?></h4>
            <small class="text-white-50 fw-medium">
                <i class="bi bi-person-circle me-1"></i> छात्र: <span class="text-warning fw-bold"><?php echo htmlspecialchars($student_name); ?></span> | ID: <?php echo $student_id; ?>
            </small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="fw-semibold text-white-50 small d-none d-sm-inline"><i class="bi bi-hourglass-split me-1"></i>शेष समय:</span>
        <div id="timer" class="timer-box"><i class="bi bi-clock-history me-2"></i>--:--</div>
    </div>
</div>

<div class="container-fluid mt-4 px-4 mb-5">
    <div class="row g-4">
        <div class="col-md-9">
            <div class="question-card d-flex flex-column justify-content-between">
                <div>
                    <?php foreach ($questions as $index => $q): 
                        $user_ans = $saved_answers[$q['id']] ?? ''; 
                    ?>
                        <div class="question-section" id="q-box-<?php echo $index; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                <span class="badge text-dark px-3 py-2 rounded-pill fw-bold" style="background-color: #e2e8f0;"><i class="bi bi-tags-fill text-primary me-1"></i><?php echo htmlspecialchars($q['subject'] ?? 'सामान्य ज्ञान'); ?></span>
                                <span class="text-muted fw-semibold small">अंक: +1</span>
                            </div>
                            
                            <h3 class="question-title mb-4">
                                <span class="text-primary fw-bold">प्रश्न <?php echo $index + 1; ?>:</span> <?php echo htmlspecialchars($q['question_text']); ?>
                            </h3>
                            
                            <div class="options-list d-flex flex-column gap-3 mt-4">
                                <input type="radio" class="btn-check" name="ans_<?php echo $q['id']; ?>" id="opt_A_<?php echo $q['id']; ?>" value="A" <?php echo ($user_ans === 'A') ? 'checked' : ''; ?> onclick="autoSaveAnswer(<?php echo $attempt_id; ?>, <?php echo $q['id']; ?>, 'A', <?php echo $index; ?>)">
                                <label class="btn option-label text-start" for="opt_A_<?php echo $q['id']; ?>">
                                    <span class="option-badge">A</span> <?php echo htmlspecialchars($q['option_a']); ?>
                                </label>

                                <input type="radio" class="btn-check" name="ans_<?php echo $q['id']; ?>" id="opt_B_<?php echo $q['id']; ?>" value="B" <?php echo ($user_ans === 'B') ? 'checked' : ''; ?> onclick="autoSaveAnswer(<?php echo $attempt_id; ?>, <?php echo $q['id']; ?>, 'B', <?php echo $index; ?>)">
                                <label class="btn option-label text-start" for="opt_B_<?php echo $q['id']; ?>">
                                    <span class="option-badge">B</span> <?php echo htmlspecialchars($q['option_b']); ?>
                                </label>

                                <input type="radio" class="btn-check" name="ans_<?php echo $q['id']; ?>" id="opt_C_<?php echo $q['id']; ?>" value="C" <?php echo ($user_ans === 'C') ? 'checked' : ''; ?> onclick="autoSaveAnswer(<?php echo $attempt_id; ?>, <?php echo $q['id']; ?>, 'C', <?php echo $index; ?>)">
                                <label class="btn option-label text-start" for="opt_C_<?php echo $q['id']; ?>">
                                    <span class="option-badge">C</span> <?php echo htmlspecialchars($q['option_c']); ?>
                                </label>

                                <input type="radio" class="btn-check" name="ans_<?php echo $q['id']; ?>" id="opt_D_<?php echo $q['id']; ?>" value="D" <?php echo ($user_ans === 'D') ? 'checked' : ''; ?> onclick="autoSaveAnswer(<?php echo $attempt_id; ?>, <?php echo $q['id']; ?>, 'D', <?php echo $index; ?>)">
                                <label class="btn option-label text-start" for="opt_D_<?php echo $q['id']; ?>">
                                    <span class="option-badge">D</span> <?php echo htmlspecialchars($q['option_d']); ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-secondary nav-btn shadow-sm" id="prev-btn" onclick="changeQuestion(-1)" disabled><i class="bi bi-arrow-left-circle me-2"></i> पिछला</button>
                    <button class="btn btn-primary nav-btn shadow-sm px-5" id="next-btn" onclick="changeQuestion(1)" style="background-color: var(--accent-blue); border:none;">अगला ➡️</button>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="sidebar d-flex flex-column justify-content-between h-100">
                <div>
                    <h5 class="mb-3 fw-bold text-dark pb-2 border-bottom d-flex align-items-center justify-content-between" style="font-size: 1.1rem;">
                        <span><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>प्रश्न पैलेट</span>
                        <span class="badge bg-primary rounded-pill"><?php echo count($questions); ?> सवाल</span>
                    </h5>
                    
                    <div class="d-flex flex-wrap justify-content-start mb-4 palette-container">
                        <?php foreach ($questions as $index => $q): 
                            $btn_class = isset($saved_answers[$q['id']]) ? 'btn-success text-white border-0' : 'btn-outline-secondary';
                        ?>
                            <button class="btn <?php echo $btn_class; ?> palette-btn shadow-sm" id="palette-btn-<?php echo $index; ?>" onclick="goToQuestion(<?php echo $index; ?>)">
                                <?php echo $index + 1; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="bg-light p-3 rounded-4 mb-4 border">
                    <div class="row g-2 small fw-semibold text-secondary">
                        <div class="col-6"><span class="status-dot bg-success"></span>उत्तर दिया</div>
                        <div class="col-6"><span class="status-dot bg-secondary border"></span>बिना उत्तर</div>
                    </div>
                </div>
                
                <form action="submit_test.php" method="POST" id="submit-form" onsubmit="return confirm('क्या आप सचमुच परीक्षा सबमिट करना चाहते हैं?');">
                    <input type="hidden" name="attempt_id" value="<?php echo $attempt_id; ?>">
                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2.5 shadow-sm border-0" style="border-radius:14px; background: linear-gradient(135deg, #ef4444, #b91c1c);">🛑 टेस्ट सबमिट करें (Submit)</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentQuestion = 0;
const totalQuestions = <?php echo count($questions); ?>;
let timeLeft = <?php echo $time_left; ?>;

// 1. काउंटडाउन टाइमर लॉजिक
const timerInterval = setInterval(function() {
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        alert('समय समाप्त हो गया है! आपका टेस्ट अपने आप सबमिट हो रहा है।');
        document.getElementById('submit-form').submit();
    } else {
        let mins = Math.floor(timeLeft / 60);
        let secs = timeLeft % 60;
        document.getElementById('timer').innerText = 
            (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
        timeLeft--;
    }
}, 1000);

// 2. प्रश्न बदलने का लॉजिक
function goToQuestion(index) {
    document.getElementById('q-box-' + currentQuestion).style.display = 'none';
    currentQuestion = index;
    document.getElementById('q-box-' + currentQuestion).style.display = 'block';
    
    // बटन्स की स्थिति संभालना
    document.getElementById('prev-btn').disabled = (currentQuestion === 0);
    document.getElementById('next-btn').innerText = (currentQuestion === totalQuestions - 1) ? 'खत्म ✨' : 'अगला ➡️';
}

function changeQuestion(step) {
    let target = currentQuestion + step;
    if (target >= 0 && target < totalQuestions) {
        goToQuestion(target);
    }
}

// 3. लाइव ऑटो-सेव फ़ंक्शन
function autoSaveAnswer(attemptId, questionId, selectedOption, index) {
    fetch('save_answer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `attempt_id=${attemptId}&question_id=${questionId}&selected_option=${selectedOption}`
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.message);
        let btn = document.getElementById('palette-btn-' + index);
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success', 'text-white', 'border-0');
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>