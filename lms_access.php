<?php
/**
 * CMS PRO - ALL-IN-ONE ADVANCED LMS ACCESS ENGINE
 * Read, Watch Video, Listen Audio, Download PDF & Practice System
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php';

// Auth Check
if (!isset($_SESSION['student'])) {
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student'];

// Dynamic Schema Initializer for LMS System Tables
function initLmsTables($conn) {
    // LMS Materials Table
    $sql1 = "CREATE TABLE IF NOT EXISTS `lms_materials` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `course_code` VARCHAR(50) DEFAULT 'DCA',
        `subject_name` VARCHAR(100) NOT NULL,
        `chapter_title` VARCHAR(255) NOT NULL,
        `content_type` ENUM('video', 'audio', 'pdf', 'text') NOT NULL,
        `media_url` TEXT NULL,
        `pdf_file` TEXT NULL,
        `text_notes` LONGTEXT NULL,
        `duration_min` INT DEFAULT 10,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    @mysqli_query($conn, $sql1);

    // LMS Progress Tracker
    $sql2 = "CREATE TABLE IF NOT EXISTS `lms_progress` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `student_id` VARCHAR(50) NOT NULL,
        `material_id` INT NOT NULL,
        `status` ENUM('completed', 'in_progress') DEFAULT 'completed',
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `std_mat` (`student_id`, `material_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    @mysqli_query($conn, $sql2);
}
initLmsTables($conn);

// Helper to fetch demo materials if DB is empty
function getLmsMaterials($conn, $course = 'DCA') {
    $materials = [];
    $stmt = $conn->prepare("SELECT * FROM lms_materials WHERE course_code = ? ORDER BY id ASC");
    if ($stmt) {
        $stmt->bind_param("s", $course);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $materials[] = $row;
        }
    }

    // Default Fallback Data if DB Table has no entries yet
    if (empty($materials)) {
        $materials = [
            [
                'id' => 101,
                'subject_name' => 'Computer Fundamentals',
                'chapter_title' => 'अध्याय 1: कम्प्यूटर परिचय एवं कार्यप्रणाली',
                'content_type' => 'video',
                'media_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'pdf_file' => 'downloads/fundamentals_ch1.pdf',
                'text_notes' => 'कम्प्यूटर एक इलेक्ट्रॉनिक उपकरण है जो डाटा इनपुट लेता है, प्रोसेसिंग करता है और आउटपुट प्रदान करता है। हार्डवेयर और सॉफ्टवेयर का विस्तृत विवरण नीचे उपलब्ध है।',
                'duration_min' => 15
            ],
            [
                'id' => 102,
                'subject_name' => 'MS Office Essentials',
                'chapter_title' => 'अध्याय 2: MS Word - डॉक्यूमेंट निर्माण और फ़ॉर्मेटिंग',
                'content_type' => 'pdf',
                'media_url' => '',
                'pdf_file' => 'downloads/ms_word_notes.pdf',
                'text_notes' => 'MS Word का उपयोग लेटर, रिज्यूम और रिपोर्ट्स बनाने के लिए किया जाता है। शॉर्टकट कीज़: Ctrl+C (Copy), Ctrl+V (Paste), Ctrl+S (Save)।',
                'duration_min' => 20
            ],
            [
                'id' => 103,
                'subject_name' => 'Financial Accounting',
                'chapter_title' => 'अध्याय 3: Tally Prime - ऑडियो लेक्चर (Accounting Rules)',
                'content_type' => 'audio',
                'media_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'pdf_file' => 'downloads/tally_rules.pdf',
                'text_notes' => 'अकाउंटिंग के तीन गोल्डन रूल्स: 1. Real Account, 2. Personal Account, 3. Nominal Account। ऑडियो ध्यानपूर्वक सुनें।',
                'duration_min' => 12
            ]
        ];
    }
    return $materials;
}

$student_course = 'DCA';
$lms_data = getLmsMaterials($conn, $student_course);
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>ऑल-इन-वन LMS पोर्टल - TC ACADEMY</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #2563eb;
            --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --sidebar-bg: #0f172a;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --success: #16a34a;
            --danger: #dc2626;
            --app-header-height: 60px;
            --bottom-nav-height: 75px;
            --radius: 18px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Hind', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; padding-bottom: calc(var(--bottom-nav-height) + 20px); }

        /* SIDEBAR */
        .sidebar {
            width: 280px; background: var(--sidebar-bg); height: 100vh; height: 100dvh;
            position: fixed; top: 0; left: 0; padding: 24px 16px; color: white;
            z-index: 10050; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .sidebar-brand { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 800; letter-spacing: 1px; margin: 20px 0 10px 10px; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; padding: 12px 16px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 15px; margin-bottom: 6px; font-weight: 600; transition: 0.2s; }
        .sidebar-menu a.active, .sidebar-menu a:hover { background: rgba(37, 99, 235, 0.15); color: #60a5fa; }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 10040; backdrop-filter: blur(4px); }

        /* MOBILE HEADER */
        .app-header { display: none; position: fixed; top: 0; left: 0; right: 0; height: var(--app-header-height); background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border-bottom: 1px solid #e2e8f0; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 1000; }
        .app-header h3 { font-size: 18px; font-weight: 800; color: var(--primary); }

        /* MAIN CONTENT LAYOUT */
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 28px; transition: all 0.3s ease; }
        .content-body { max-width: 1200px; margin: 0 auto; }

        /* BANNER */
        .big-home-banner {
            background: var(--primary-gradient); border-radius: 20px; padding: 16px 20px; color: #fff;
            display: flex; align-items: center; justify-content: space-between; text-decoration: none;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25); margin-bottom: 24px;
        }

        /* LMS WORKSPACE GRID */
        .lms-grid { display: grid; grid-template-columns: 340px 1fr; gap: 24px; align-items: start; }

        /* LEFT: CHAPTER LIST */
        .chapter-card { background: var(--card-bg); border-radius: var(--radius); padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .chapter-card h3 { font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .item-list { display: flex; flex-direction: column; gap: 10px; }
        .item-box {
            padding: 12px 14px; border-radius: 12px; border: 1px solid #f1f5f9; background: #f8fafc;
            cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 12px;
        }
        .item-box:hover, .item-box.active { background: #eff6ff; border-color: #bfdbfe; }
        .item-icon { width: 38px; height: 38px; border-radius: 10px; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .item-box.active .item-icon { background: #2563eb; color: #fff; }
        .item-details h5 { font-size: 13px; font-weight: 800; color: var(--text-main); line-height: 1.3; }
        .item-details p { font-size: 11px; color: var(--text-sub); font-weight: 600; margin-top: 2px; }

        /* RIGHT: ADVANCED PLAYER & READER */
        .player-card { background: var(--card-bg); border-radius: var(--radius); padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .media-container { width: 100%; border-radius: 14px; overflow: hidden; background: #0f172a; margin-bottom: 20px; position: relative; }
        video, audio { width: 100%; outline: none; }
        
        .tab-buttons { display: flex; gap: 10px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 20px; overflow-x: auto; }
        .tab-btn { padding: 8px 16px; border-radius: 10px; border: none; background: #f1f5f9; color: var(--text-sub); font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .tab-btn.active { background: var(--primary); color: #fff; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .download-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .btn-download { background: var(--success); color: #fff; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2); }

        .notes-reader { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; line-height: 1.7; font-size: 15px; color: #334155; font-weight: 500; }

        /* QUIZ ENGINE */
        .quiz-box { background: #fff5f5; border: 1px solid #fecaca; border-radius: 14px; padding: 20px; }
        .quiz-opt { padding: 10px 14px; background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; margin-top: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .quiz-opt:hover { border-color: var(--primary); background: #eff6ff; }

        /* DOUBT SECTION */
        .doubt-box textarea { width: 100%; border-radius: 12px; border: 1px solid #cbd5e1; padding: 12px; font-size: 14px; outline: none; margin-bottom: 10px; }
        .btn-doubt { background: var(--primary); color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 800; cursor: pointer; }

        /* BOTTOM NAV */
        .mobile-bottom-nav { 
            display: none; position: fixed; bottom: 0; left: 0; right: 0; 
            height: var(--bottom-nav-height); background: #ffffff; 
            box-shadow: 0 -5px 25px rgba(0,0,0,0.08); z-index: 1000; 
            justify-content: space-around; align-items: center; border-top: 1px solid #e2e8f0; 
        }
        .mobile-bottom-nav a { color: #64748b; text-decoration: none; display: flex; flex-direction: column; align-items: center; font-size: 11px; font-weight: 800; gap: 4px; width: 25%; }
        .mobile-bottom-nav a.active { color: var(--primary); }
        .mobile-bottom-nav a i { font-size: 22px; }
        .big-home-nav-btn { background: var(--primary); color: #ffffff !important; border-radius: 16px; padding: 8px 16px; font-size: 12px !important; margin-top: -15px; box-shadow: 0 6px 15px rgba(37, 99, 235, 0.35); border: 3px solid #fff; }

        @media (max-width: 900px) {
            .lms-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .app-header, .mobile-bottom-nav { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; padding: calc(var(--app-header-height) + 16px) 16px calc(var(--bottom-nav-height) + 20px) 16px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- APP HEADER (MOBILE) -->
<div class="app-header">
    <div style="display:flex; align-items:center; gap:12px;">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="font-size: 20px; cursor: pointer; color: var(--primary);"></i>
        <h3>LMS डिजिटल क्लासरूम</h3>
    </div>
    <a href="student_dashboard.php" style="font-size: 20px; color: var(--primary); text-decoration:none;"><i class="fas fa-home"></i></a>
</div>

<!-- SIDEBAR NAVIGATION -->
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-graduation-cap" style="color:#60a5fa;"></i> 
            <span>TC ACADEMY</span>
        </div>
        <i class="fas fa-times" onclick="toggleSidebar()" style="cursor:pointer; font-size:18px; color:#64748b;"></i>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">मुख्य मेनू</div>
        <a href="student_dashboard.php"><i class="fas fa-home"></i> <span>होम (Dashboard)</span></a>
        
        <div class="menu-label">एकेडमिक्स & LMS</div>
        <a href="lms_access.php" class="active"><i class="fas fa-laptop-code"></i> <span>LMS डिजिटल लर्निंग</span></a>
        <a href="attendance.php"><i class="fas fa-fingerprint"></i> <span>आज की हाजिरी</span></a>
        <a href="attendance_history.php"><i class="fas fa-history"></i> <span>हाजिरी का इतिहास</span></a>
        <a href="holidays.php"><i class="fas fa-calendar-day"></i> <span>छुट्टियों की सूची</span></a>
        
        <div class="menu-label">अन्य</div>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>लॉगआउट करें</span></a>
    </div>
</div>

<div class="main-content">
    <div class="content-body">
        
        <!-- BANNER -->
        <a href="student_dashboard.php" class="big-home-banner">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:48px; height:48px; background:rgba(255,255,255,0.2); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:22px;"><i class="fas fa-book-reader"></i></div>
                <div>
                    <h4 style="font-size:16px; font-weight:800;">DCA - डिजिटल कोर्स हब</h4>
                    <p style="font-size:12px; color:#bfdbfe;">पढ़ें, वीडियो देखें, ऑडियो सुनें और PDF डाउनलोड करें</p>
                </div>
            </div>
            <i class="fas fa-chevron-right" style="color:#bfdbfe;"></i>
        </a>

        <!-- MAIN LMS GRID -->
        <div class="lms-grid">
            
            <!-- LEFT: CHAPTER & LESSONS LIST -->
            <div class="chapter-card">
                <h3><i class="fas fa-list-ul" style="color:var(--primary);"></i> पाठ्य सामग्री (Chapters)</h3>
                <div class="item-list" id="itemList">
                    <?php foreach ($lms_data as $index => $item): ?>
                        <div class="item-box <?= ($index === 0) ? 'active' : '' ?>" onclick="loadMaterial(<?= htmlspecialchars(json_encode($item)) ?>, this)">
                            <div class="item-icon">
                                <?php if ($item['content_type'] == 'video'): ?>
                                    <i class="fas fa-play"></i>
                                <?php elseif ($item['content_type'] == 'audio'): ?>
                                    <i class="fas fa-headphones"></i>
                                <?php elseif ($item['content_type'] == 'pdf'): ?>
                                    <i class="fas fa-file-pdf"></i>
                                <?php else: ?>
                                    <i class="fas fa-file-alt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <h5><?= htmlspecialchars($item['chapter_title']) ?></h5>
                                <p><i class="far fa-clock"></i> <?= intval($item['duration_min']) ?> मिनट | <?= htmlspecialchars($item['subject_name']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT: ALL-IN-ONE ADVANCED PLAYER & CONTENT READER -->
            <div class="player-card">
                
                <!-- MEDIA PLAYER SECTION (DYNAMIC VIDEO / AUDIO / PDF BANNER) -->
                <div class="media-container" id="mediaContainer">
                    <!-- Loaded via Javascript -->
                </div>

                <!-- DYNAMIC TAB TOOLBAR -->
                <div class="tab-buttons">
                    <button class="tab-btn active" onclick="switchTab('tabNotes', this)"><i class="fas fa-book-open"></i> थ्योरी नोट्स (Read)</button>
                    <button class="tab-btn" onclick="switchTab('tabPdf', this)"><i class="fas fa-file-download"></i> PDF स्टडी मटेरियल</button>
                    <button class="tab-btn" onclick="switchTab('tabQuiz', this)"><i class="fas fa-vial"></i> प्रैक्टिस क्विज़</button>
                    <button class="tab-btn" onclick="switchTab('tabDoubt', this)"><i class="fas fa-question-circle"></i> सवाल पूछें (Doubt)</button>
                </div>

                <!-- TAB 1: THEORY NOTES (READ) -->
                <div id="tabNotes" class="tab-content active">
                    <h4 id="displayTitle" style="font-size:18px; font-weight:800; margin-bottom:12px; color:var(--text-main);"></h4>
                    <div class="notes-reader" id="displayNotes"></div>
                </div>

                <!-- TAB 2: PDF DOWNLOAD -->
                <div id="tabPdf" class="tab-content">
                    <div class="download-box">
                        <div>
                            <h5 style="font-size:15px; font-weight:800; color:#166534;"><i class="fas fa-file-pdf"></i> ई-बुक एवं स्टडी PDF डाउनलोड</h5>
                            <p style="font-size:12px; color:#15803d; margin-top:2px;">इस पाठ के ओरिजिनल नोट्स ऑफ़लाइन पढ़ने के लिए डाउनलोड करें।</p>
                        </div>
                        <a href="#" id="downloadPdfBtn" class="btn-download" download><i class="fas fa-download"></i> डाउनलोड</a>
                    </div>
                </div>

                <!-- TAB 3: PRACTICE QUIZ -->
                <div id="tabQuiz" class="tab-content">
                    <div class="quiz-box">
                        <h5 style="font-size:15px; font-weight:800; color:#991b1b;"><i class="fas fa-check-circle"></i> अभ्यास प्रश्न (Quick Assessment)</h5>
                        <p style="font-size:13px; font-weight:600; margin-top:10px; color:#1e293b;">प्र.1 इस अध्याय में मुख्य रूप से किस तकनीक का वर्णन किया गया है?</p>
                        <div class="quiz-opt" onclick="checkQuiz(this, true)">अ. बुनियादी कम्प्यूटर सिद्धांत एवं सॉफ्टवेयर</div>
                        <div class="quiz-opt" onclick="checkQuiz(this, false)">ब. केवल प्रिंटिंग तकनीक</div>
                        <div class="quiz-opt" onclick="checkQuiz(this, false)">स. हार्डवेयर असेंबलिंग</div>
                    </div>
                </div>

                <!-- TAB 4: ASK DOUBT -->
                <div id="tabDoubt" class="tab-content">
                    <div class="doubt-box">
                        <h5 style="font-size:15px; font-weight:800; margin-bottom:10px;"><i class="fas fa-comment-dots"></i> टीचर से सवाल पूछें</h5>
                        <textarea id="doubtText" rows="4" placeholder="यहाँ अपना सवाल/समस्या विस्तार से लिखें..."></textarea>
                        <button class="btn-doubt" onclick="sendDoubt()"><i class="fas fa-paper-plane"></i> सवाल भेजें</button>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- BOTTOM MOBILE NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="attendance.php"><i class="fas fa-fingerprint"></i><span>हाजिरी</span></a>
    <a href="student_dashboard.php" class="big-home-nav-btn"><i class="fas fa-home" style="font-size:24px;"></i><span>HOME</span></a>
    <a href="attendance_history.php"><i class="fas fa-history"></i><span>इतिहास</span></a>
</div>

<script>
    const initialData = <?= json_encode($lms_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    document.addEventListener("DOMContentLoaded", () => {
        if (initialData && initialData.length > 0) {
            loadMaterial(initialData[0], document.querySelector('.item-box'));
        }
    });

    function toggleSidebar() {
        document.getElementById('sidebarNav').classList.toggle('active');
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    }

    function loadMaterial(item, element) {
        if(element) {
            document.querySelectorAll('.item-box').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }

        const mediaContainer = document.getElementById('mediaContainer');
        document.getElementById('displayTitle').innerText = item.chapter_title;
        document.getElementById('displayNotes').innerText = item.text_notes || 'इस पाठ के लिए लिखित नोट्स नीचे PDF में संलग्न हैं।';
        document.getElementById('downloadPdfBtn').href = item.pdf_file || '#';

        // Dynamic Media Renderer
        if (item.content_type === 'video') {
            mediaContainer.innerHTML = `
                <video controls autoplay poster="https://via.placeholder.com/800x450/0f172a/ffffff?text=Video+Lecture">
                    <source src="${item.media_url}" type="video/mp4">
                    आपका ब्राउज़र वीडियो टैग का समर्थन नहीं करता है।
                </video>
            `;
        } else if (item.content_type === 'audio') {
            mediaContainer.innerHTML = `
                <div style="padding:40px 20px; text-align:center; color:#fff;">
                    <i class="fas fa-headphones" style="font-size:48px; color:#60a5fa; margin-bottom:16px;"></i>
                    <h4 style="margin-bottom:16px;">ऑडियो लेक्चर सुनें</h4>
                    <audio controls style="width:100%; max-width:500px;">
                        <source src="${item.media_url}" type="audio/mpeg">
                    </audio>
                </div>
            `;
        } else {
            mediaContainer.innerHTML = `
                <div style="padding:50px 20px; text-align:center; color:#fff;">
                    <i class="fas fa-file-pdf" style="font-size:48px; color:#ef4444; margin-bottom:16px;"></i>
                    <h4>पीडीएफ एवं थ्योरी पाठ्य सामग्री</h4>
                    <p style="font-size:13px; color:#94a3b8; margin-top:6px;">नीचे दिए गए टैब से नोट्स पढ़ें या PDF फाइल डाउनलोड करें।</p>
                </div>
            `;
        }
    }

    function checkQuiz(btn, isCorrect) {
        if(isCorrect) {
            btn.style.background = "#dcfce7";
            btn.style.borderColor = "#22c55e";
            alert("सही उत्तर! उत्कृष्ट प्रयास।");
        } else {
            btn.style.background = "#fee2e2";
            btn.style.borderColor = "#ef4444";
            alert("गलत उत्तर! कृपया नोट्स दोबारा पढ़ें।");
        }
    }

    function sendDoubt() {
        const doubt = document.getElementById('doubtText').value;
        if(!doubt.trim()) {
            alert("कृपया अपना सवाल दर्ज करें।");
            return;
        }
        alert("आपका सवाल सफलतापूर्वक शिक्षक को भेज दिया गया है!");
        document.getElementById('doubtText').value = '';
    }
</script>

</body>
</html>