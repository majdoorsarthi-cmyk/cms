<?php
/**
 * CMS PRO - Advanced Dynamic Video Lectures & LMS Portal
 */

include 'db_config.php';

// Auth Check
if (!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$stu = $_SESSION['student'];
$user_id = $stu['user_id'] ?? ($stu['id'] ?? null);

// ऑटो टेबल क्रिएशन चेक
$create_table_sql = "CREATE TABLE IF NOT EXISTS `video_lectures` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `video_url` VARCHAR(500) NOT NULL,
  `duration` VARCHAR(50) DEFAULT '00:00',
  `chapter_name` VARCHAR(100) DEFAULT 'सामान्य अध्याय',
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($conn, $create_table_sql);

// अगर टेबल खाली हो तो डमी डेटा डालना
$check_empty = mysqli_query($conn, "SELECT id FROM video_lectures LIMIT 1");
if (mysqli_num_rows($check_empty) == 0) {
    mysqli_query($conn, "INSERT INTO `video_lectures` (`course_id`, `title`, `description`, `video_url`, `duration`, `chapter_name`, `sort_order`) VALUES
    (1, 'अध्याय 1: परिचय एवं मूल बातें', 'इस क्लास में हम विषय के बुनियादी सिद्धांतों और परिचय के बारे में विस्तार से समझेंगे।', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '15:20', 'मॉड्यूल 1: बेसिक कॉन्सेप्ट्स', 1),
    (1, 'अध्याय 2: महत्वपूर्ण प्रश्न और हल', 'इस वीडियो लेक्चर में परीक्षा में आने वाले मुख्य प्रश्नों का अभ्यास कराया गया है।', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '22:45', 'मॉड्यूल 1: बेसिक कॉन्सेप्ट्स', 2),
    (1, 'अध्याय 3: एडवांस्ड टॉपिक पार्ट 1', 'गहन अध्ययन और एडवांस्ड ट्रिक्स सीखें इस विशेष क्लास सत्र के साथ।', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '30:10', 'मॉड्यूल 2: एडवांस्ड थ्योरी', 3);");
}

// वीडियो व कोर्स पैरामीटर्स
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 1;
$active_id = isset($_GET['v']) ? intval($_GET['v']) : 0;

// डेटाबेस से लेक्चर्स फेच करना
$query = "SELECT * FROM video_lectures WHERE course_id = ? ORDER BY chapter_name ASC, sort_order ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$chapters = [];
$active_lecture = null;
$total_lectures = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $chapters[$row['chapter_name']][] = $row;
    $total_lectures++;

    if ($active_id === 0 && $active_lecture === null) {
        $active_lecture = $row;
    } elseif ($row['id'] === $active_id) {
        $active_lecture = $row;
    }
}

if (!$active_lecture && !empty($chapters)) {
    $first_chapter = reset($chapters);
    $active_lecture = $first_chapter[0];
}

// YouTube URL Formatter
function formatVideoUrl($url) {
    if (strpos($url, 'youtu.be/') !== false) {
        $id = substr(parse_url($url, PHP_URL_PATH), 1);
        return "https://www.youtube.com/embed/" . $id;
    } elseif (strpos($url, 'watch?v=') !== false) {
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        return "https://www.youtube.com/embed/" . ($vars['v'] ?? '');
    }
    return $url;
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>TC ACADEMY - वीडियो लेक्चर्स</title>
    
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

            /* PhonePe Purple Palette */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-light: #f3e8ff;
            --phonepe-bg: #f4f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; }

        /* 🖥️ DESKTOP STYLES (Unchanged Original) */
        .sidebar {
            width: 280px; background: var(--sidebar-bg); height: 100vh;
            position: fixed; top: 0; left: 0; padding: 20px 16px; color: white;
            z-index: 10050; transition: transform 0.3s ease; box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .sidebar-brand { font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .close-btn { font-size: 20px; cursor: pointer; color: #94a3b8; display: none; }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin: 16px 0 8px 8px; }
        .sidebar-menu a { color: var(--nav-text); text-decoration: none; padding: 12px 14px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 15px; margin-bottom: 6px; font-weight: 600; }
        .sidebar-menu a.active { background: rgba(37, 99, 235, 0.15); color: #60a5fa; border-left: 4px solid #3b82f6; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); z-index: 10040; backdrop-filter: blur(4px); }
        .app-header { display: none; }
        .mobile-bottom-nav { display: none; }

        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 24px; transition: all 0.3s ease; }
        .content-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }

        .video-player-card { background: #000; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15); position: relative; aspect-ratio: 16/9; }
        .video-player-card iframe, .video-player-card video { width: 100%; height: 100%; border: none; }

        .video-details-card { background: #fff; border-radius: 20px; padding: 24px; margin-top: 20px; border: 1px solid #e2e8f0; }
        .chapter-badge { background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 20px; display: inline-block; margin-bottom: 10px; }
        
        .playlist-card { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; height: fit-content; max-height: calc(100vh - 120px); display: flex; flex-direction: column; }
        .playlist-header { padding: 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 800; display: flex; justify-content: space-between; align-items: center; }
        .playlist-body { overflow-y: auto; flex: 1; }

        .chapter-group-title { padding: 10px 16px; background: #f1f5f9; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .lecture-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; text-decoration: none; color: #334155; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
        .lecture-item:hover { background: #f8fafc; }
        .lecture-item.active { background: #eff6ff; border-left: 4px solid #2563eb; }
        .lecture-item.active .lecture-title { color: #2563eb; font-weight: 800; }
        
        .lecture-icon { width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #64748b; flex-shrink: 0; }
        .lecture-item.active .lecture-icon { background: #2563eb; color: #fff; }

        @media (max-width: 992px) {
            .content-layout { grid-template-columns: 1fr; }
            .playlist-card { max-height: 500px; }
        }

        /* 📱 MOBILE VIEW (PhonePe Full App Interface + High Legibility Fonts) */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; padding-bottom: 90px; }
            .close-btn { display: block; }
            .sidebar { transform: translateX(-100%); } 
            .sidebar.active { transform: translateX(0); }

            /* PhonePe App Header */
            .app-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 68px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                align-items: center;
                justify-content: space-between;
                padding: 0 18px;
                box-shadow: 0 4px 20px rgba(95, 37, 159, 0.3);
                z-index: 10000;
            }
            .app-header-title {
                color: #ffffff !important;
                font-weight: 800;
                font-size: 19px !important; /* Larger Header Font */
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .app-header-title i { color: #ffffff !important; font-size: 22px !important; }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 82px 14px 90px 14px !important;
            }

            /* Video Player Card Mobile */
            .video-player-card {
                border-radius: 18px !important;
                box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
            }

            /* Video Details Mobile - Bigger Fonts */
            .video-details-card {
                padding: 20px 16px !important;
                border-radius: 18px !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03) !important;
            }
            .chapter-badge {
                background: var(--phonepe-light) !important;
                color: var(--phonepe-purple) !important;
                font-size: 13.5px !important; /* Larger Badge Font */
                font-weight: 800 !important;
                padding: 7px 14px !important;
            }
            .video-details-card h2 {
                font-size: 20px !important; /* Larger Title Font */
                font-weight: 800 !important;
                color: #0f172a !important;
                line-height: 1.35;
                margin-top: 6px;
            }
            .video-details-card p {
                font-size: 15px !important; /* Larger Body Text */
                color: #475569 !important;
                font-weight: 500;
            }

            /* PhonePe Style Playlist Cards */
            .playlist-card {
                border-radius: 20px !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;
            }
            .playlist-header {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                color: #ffffff !important;
                font-size: 17px !important; /* Larger Playlist Header */
                padding: 16px 18px !important;
            }
            .playlist-header span { color: #fff !important; }
            .playlist-header span i { color: #e9d5ff !important; }

            .chapter-group-title {
                font-size: 13.5px !important;
                font-weight: 800 !important;
                background: #f1f5f9 !important;
                color: var(--phonepe-purple) !important;
                padding: 12px 18px !important;
            }

            .lecture-item {
                padding: 16px 18px !important;
                gap: 14px !important;
            }
            .lecture-item.active {
                background: var(--phonepe-light) !important;
                border-left: 5px solid var(--phonepe-purple) !important;
            }
            .lecture-item.active .lecture-title {
                color: var(--phonepe-purple) !important;
                font-weight: 800 !important;
            }
            .lecture-title {
                font-size: 16.5px !important; /* Larger Lecture Item Title */
                font-weight: 700 !important;
                color: #1e293b !important;
            }
            .lecture-icon {
                width: 44px !important; height: 44px !important;
                font-size: 18px !important;
            }
            .lecture-item.active .lecture-icon {
                background: var(--phonepe-purple) !important;
                color: #fff !important;
            }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 68px;
                background: #ffffff;
                border-top: 1px solid #e2e8f0;
                z-index: 10000;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
            }
            .mobile-bottom-nav a {
                color: #64748b;
                text-decoration: none;
                display: flex;
                flex-direction: column;
                align-items: center;
                font-size: 12px !important; /* Larger Navigation Labels */
                font-weight: 700;
                gap: 3px;
                width: 25%;
            }
            .mobile-bottom-nav a.active { color: var(--phonepe-purple) !important; font-weight: 800; }
            .mobile-bottom-nav a i { font-size: 22px; }
            .big-home-nav-btn {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                color: #ffffff !important;
                border-radius: 18px;
                padding: 8px 16px;
                font-size: 12px !important;
                margin-top: -18px;
                box-shadow: 0 6px 18px rgba(95, 37, 159, 0.4);
                border: 3px solid #fff;
            }
        }
    </style>
</head>
<body>

<div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- MOBILE PHONEPE APP HEADER -->
<div class="app-header">
    <div class="app-header-title">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="cursor: pointer;"></i>
        <span>TC ACADEMY - वीडियो क्लासेस</span>
    </div>
    <a href="student_dashboard.php" style="font-size: 22px; color: #ffffff; text-decoration:none;"><i class="fas fa-home"></i></a>
</div>

<!-- SIDEBAR MAIN MENU -->
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-brand">
        <div style="display:flex; align-items:center; gap:10px;"><i class="fas fa-graduation-cap"></i> <span>TC ACADEMY</span></div>
        <i class="fas fa-times close-btn" onclick="toggleSidebar()"></i>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">मुख्य मेनू</div>
        <a href="student_dashboard.php"><i class="fas fa-home" style="color:#60a5fa;"></i> <span>होम (Dashboard)</span></a>

        <div class="menu-label">एकेडमिक्स & LMS</div>
        <a href="lms_access.php"><i class="fas fa-laptop-code" style="color:#38bdf8;"></i> <span>LMS Access</span></a>
        <a href="video_lectures.php" class="active"><i class="fas fa-video" style="color:#a7f3d0;"></i> <span>वीडियो लेक्चर्स</span></a>
        <a href="online_exam.php"><i class="fas fa-file-signature" style="color:#fde047;"></i> <span>ऑनलाइन एग्जाम</span></a>
        <a href="homework.php"><i class="fas fa-tasks" style="color:#f472b6;"></i> <span>होमवर्क & असाइनमेंट</span></a>

        <div class="menu-label">हाजिरी सिस्टम</div>
        <a href="attendance.php"><i class="fas fa-fingerprint" style="color:#60a5fa;"></i> <span>आज की हाजिरी</span></a>
        <a href="attendance_history.php"><i class="fas fa-history" style="color:#cbd5e1;"></i> <span>हाजिरी का इतिहास</span></a>
        <a href="holidays.php"><i class="fas fa-calendar-day" style="color:#fbbf24;"></i> <span>छुट्टियों की सूची</span></a>

        <div class="menu-label">अकाउंट</div>
        <a href="profile_settings.php"><i class="fas fa-user-cog" style="color:#94a3b8;"></i> <span>प्रोफाइल सेटिंग्स</span></a>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>लॉगआउट</span></a>
    </div>
</div>

<!-- MAIN CONTENT CONTAINER -->
<div class="main-content">
    <div class="content-layout">
        
        <!-- LEFT COLUMN: VIDEO PLAYER & DETAILS -->
        <div>
            <?php if ($active_lecture): ?>
                <div class="video-player-card">
                    <?php 
                        $final_video_url = formatVideoUrl($active_lecture['video_url']);
                        if (strpos($final_video_url, 'youtube.com') !== false): 
                    ?>
                        <iframe src="<?= htmlspecialchars($final_video_url) ?>?autoplay=1&rel=0&modestbranding=1" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <?php else: ?>
                        <video controls autoplay controlsList="nodownload">
                            <source src="<?= htmlspecialchars($final_video_url) ?>" type="video/mp4">
                            आपका ब्राउज़र वीडियो सपोर्ट नहीं करता है।
                        </video>
                    <?php endif; ?>
                </div>

                <div class="video-details-card">
                    <span class="chapter-badge"><i class="fas fa-folder me-1"></i> <?= htmlspecialchars($active_lecture['chapter_name']) ?></span>
                    <h2><?= htmlspecialchars($active_lecture['title']) ?></h2>
                    <p style="margin-top:8px; margin-bottom:15px;"><i class="far fa-clock me-1" style="color:var(--phonepe-purple);"></i> समय अवधि: <strong><?= htmlspecialchars($active_lecture['duration']) ?></strong></p>
                    <hr style="border:0; border-top:1px solid #e2e8f0; margin-bottom:15px;">
                    <h4 style="font-size:16px; font-weight:800; color:#334155; margin-bottom:8px;">विवरण (Description):</h4>
                    <p style="line-height:1.7;"><?= nl2br(htmlspecialchars($active_lecture['description'])) ?></p>
                </div>
            <?php else: ?>
                <div style="background:#fff; border-radius:20px; padding:60px 20px; text-align:center; color:#64748b; border:1px solid #e2e8f0;">
                    <i class="fas fa-video-slash fa-3x" style="color:#cbd5e1; margin-bottom:15px;"></i>
                    <h3>कोई वीडियो लेक्चर उपलब्ध नहीं है।</h3>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT COLUMN: PLAYLIST SIDEBAR -->
        <div>
            <div class="playlist-card">
                <div class="playlist-header">
                    <span><i class="fas fa-list-ul me-2"></i> पाठ्यक्रम वीडियो सूची</span>
                    <span style="font-size:13px; background:rgba(255,255,255,0.2); padding:4px 10px; border-radius:12px; font-weight:800;"><?= $total_lectures ?> वीडियो</span>
                </div>

                <div class="playlist-body">
                    <?php foreach ($chapters as $chapter_name => $lectures): ?>
                        <div class="chapter-group-title"><i class="fas fa-bookmark me-1"></i> <?= htmlspecialchars($chapter_name) ?></div>
                        
                        <?php foreach ($lectures as $lec): ?>
                            <?php $is_curr = ($active_lecture['id'] ?? 0) === $lec['id']; ?>
                            <a href="?course_id=<?= $course_id ?>&v=<?= $lec['id'] ?>" class="lecture-item <?= $is_curr ? 'active' : '' ?>">
                                <div class="lecture-icon">
                                    <i class="fas <?= $is_curr ? 'fa-play' : 'fa-play-circle' ?>"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="lecture-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($lec['title']) ?></div>
                                    <div style="font-size:13px; color:#64748b; margin-top:3px; font-weight:600;"><i class="far fa-clock me-1"></i> <?= htmlspecialchars($lec['duration']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- BOTTOM PHONEPE MOBILE NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="attendance.php"><i class="fas fa-fingerprint"></i><span>हाजिरी</span></a>
    <a href="student_dashboard.php" class="big-home-nav-btn"><i class="fas fa-home" style="font-size:24px;"></i><span>HOME</span></a>
    <a href="video_lectures.php" class="active"><i class="fas fa-video"></i><span>वीडियो</span></a>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebarNav').classList.toggle('active');
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }
</script>

</body>
</html>