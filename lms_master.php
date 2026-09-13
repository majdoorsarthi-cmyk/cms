<?php
session_start();
include 'db_config.php';

// छात्र लॉगिन चेक
if(!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}

$student = $_SESSION['student'];
$student_id = $student['id']; 
$course_name = $student['course'];

// 1. प्रोग्रेस सेव करने के लिए AJAX लॉजिक (जब छात्र वीडियो देखेगा)
if(isset($_POST['update_progress'])) {
    $lecture_id = mysqli_real_escape_string($conn, $_POST['lecture_id']);
    $check = mysqli_query($conn, "SELECT id FROM lms_progress WHERE student_id = '$student_id' AND lecture_id = '$lecture_id'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO lms_progress (student_id, lecture_id, status) VALUES ('$student_id', '$lecture_id', 'completed')");
    }
    exit('success');
}

// 2. लेक्चर्स और छात्र की अपनी प्रोग्रेस को एक साथ Fetch करना
$lecture_query = "SELECT l.*, p.status as p_status 
                  FROM lms_content l 
                  LEFT JOIN lms_progress p ON l.id = p.lecture_id AND p.student_id = '$student_id'
                  WHERE l.course_id = (SELECT id FROM courses WHERE course_name = '$course_name' LIMIT 1)
                  ORDER BY l.added_date ASC";
$lectures = mysqli_query($conn, $lecture_query);

// YouTube ID निकालने का फंक्शन
function get_video_id($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $vars);
    return $vars['v'] ?? 'dQw4w9WgXcQ';
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart LMS Portal | TC Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1a237e; --secondary: #3f51b5; --accent: #ff9800; --sidebar-width: 280px; }
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
        .sidebar { width: var(--sidebar-width); background: var(--primary); height: 100vh; position: fixed; color: white; padding: 20px; z-index: 1000; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        
        /* LMS Card Design */
        .video-card { border: none; border-radius: 20px; overflow: hidden; background: white; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); height: 100%; position: relative; }
        .video-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .thumbnail-box { position: relative; height: 180px; background: #000; cursor: pointer; }
        .thumbnail-box img { width: 100%; height: 100%; object-fit: cover; opacity: 0.7; transition: 0.3s; }
        .thumbnail-box:hover img { opacity: 0.9; }
        .play-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 50px; opacity: 0.9; }
        
        .status-badge { position: absolute; top: 15px; right: 15px; z-index: 10; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .completed-card { border: 2px solid #4caf50 !important; }
        
        /* Modal & Player */
        .modal-content { border-radius: 25px; border: none; overflow: hidden; }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; background: #000; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

        .sidebar-link { display: flex; align-items: center; padding: 12px 15px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; margin-bottom: 8px; transition: 0.3s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-link i { margin-right: 12px; width: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-white"><i class="fas fa-university me-2"></i>SMART<span>LMS</span></h3>
        <p class="small opacity-50">TC Academy Portal</p>
        <hr class="opacity-25">
    </div>
    <nav>
        <a href="student_dashboard.php" class="sidebar-link"><i class="fas fa-columns"></i> डैशबोर्ड</a>
        <a href="lms_master.php" class="sidebar-link active"><i class="fas fa-play-circle"></i> मेरे लेक्चर्स</a>
        <a href="examination.php" class="sidebar-link"><i class="fas fa-file-signature"></i> परीक्षा / रिजल्ट</a>
        <a href="logout.php" class="sidebar-link text-danger mt-5"><i class="fas fa-power-off"></i> लॉगआउट</a>
    </nav>
</div>

<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm mb-4">
        <h5 class="m-0 fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>कोर्स: <?php echo $course_name; ?></h5>
        <div class="d-flex align-items-center">
            <span class="me-3 fw-bold small text-muted d-none d-md-block"><?php echo $student['name']; ?></span>
            <img src="https://ui-avatars.com/api/?name=<?php echo $student['name']; ?>&background=1a237e&color=fff" class="rounded-circle shadow-sm" width="35">
        </div>
    </div>

    <div class="lms-banner p-4 rounded-4 mb-4 text-white shadow" style="background: linear-gradient(45deg, #1a237e, #3f51b5);">
        <h2 class="fw-bold">सीखना शुरू करें! 🚀</h2>
        <p class="opacity-75">आपके कोर्स के सभी वीडियो लेक्चर्स और नोट्स नीचे दिए गए हैं।</p>
    </div>

    <div class="row g-4">
        <?php if(mysqli_num_rows($lectures) > 0): 
            while($row = mysqli_fetch_assoc($lectures)): 
                $v_id = get_video_id($row['video_link']);
                $is_completed = ($row['p_status'] == 'completed');
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card video-card <?php echo $is_completed ? 'completed-card' : ''; ?>">
                <?php if($is_completed): ?>
                    <span class="status-badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> पूर्ण हुआ</span>
                <?php else: ?>
                    <span class="status-badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> शेष है</span>
                <?php endif; ?>

                <div class="thumbnail-box" onclick="playVideo('<?php echo $v_id; ?>', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['title']); ?>')">
                    <img src="https://img.youtube.com/vi/<?php echo $v_id; ?>/hqdefault.jpg" alt="Thumbnail">
                    <div class="play-icon"><i class="fas fa-play-circle"></i></div>
                </div>

                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3"><?php echo $row['title']; ?></h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary flex-grow-1 rounded-pill" onclick="playVideo('<?php echo $v_id; ?>', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['title']); ?>')">
                            <i class="fas fa-video me-1"></i> क्लास देखें
                        </button>
                        <?php if(!empty($row['pdf_file'])): ?>
                            <a href="uploads/notes/<?php echo $row['pdf_file']; ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="fas fa-file-pdf"></i> नोट्स
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center p-5">
                <i class="fas fa-folder-open fa-3x opacity-25 mb-3"></i>
                <p class="text-muted">अभी कोई लेक्चर अपलोड नहीं किया गया है।</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="videoModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold" id="videoTitle">वीडियो प्लेयर</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopVideo()"></button>
            </div>
            <div class="modal-body p-0">
                <div class="video-container">
                    <iframe id="lecturePlayer" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
                <p class="small text-muted m-0">वीडियो देखने के बाद "Mark Complete" जरूर करें।</p>
                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="markBtn">Mark as Complete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let currentLectureId = null;

function playVideo(vid, id, title) {
    currentLectureId = id;
    $('#videoTitle').text(title);
    $('#lecturePlayer').attr('src', 'https://www.youtube.com/embed/' + vid + '?autoplay=1&rel=0');
    $('#videoModal').modal('show');
    
    // Mark Complete बटन का फंक्शन सेट करना
    $('#markBtn').off('click').on('click', function() {
        updateProgress(id);
    });
}

function stopVideo() {
    $('#lecturePlayer').attr('src', '');
}

function updateProgress(id) {
    $.ajax({
        url: 'lms_master.php',
        type: 'POST',
        data: { update_progress: 1, lecture_id: id },
        success: function(response) {
            if(response === 'success') {
                alert('बहुत बढ़िया! प्रोग्रेस सेव हो गई है।');
                location.reload();
            }
        }
    });
}

// Modal बंद होने पर वीडियो रोकना (अगर क्लोज बटन के अलावा बाहर क्लिक करें)
$('#videoModal').on('hidden.bs.modal', function () {
    stopVideo();
});
</script>

</body>
</html>