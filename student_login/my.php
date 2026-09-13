<?php
session_name("STUDENT_SESSION");
session_start();
include('../db_config.php');

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

// कोर्स का पूरा नाम सेट करने का लॉजिक (DCA होने पर फुल फॉर्म दिखाएगा)
$course_display_name = trim($student['course'] ?? '');
if (strtoupper($course_display_name) == 'DCA' || $course_display_name == 'Diploma in Computer Application') {
    $course_full_name = "Diploma in Computer Application";
} elseif (strtoupper($course_display_name) == 'PGDCA') {
    $course_full_name = "Post Graduate Diploma in Computer Application";
} else {
    $course_full_name = htmlspecialchars($course_display_name); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | AGU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --moodle-purple: #8A4F8D;
            --moodle-bg-gray: #f8f9fa;
        }
        
        /* बैकग्राउंड इमेज सेटअप - स्क्रीनशॉट के अनुसार */
        body { 
            background: url('book_bg.jpg') no-repeat center center fixed; 
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* टॉप नेविगेशन बार */
        .navbar-custom { 
            background-color: var(--moodle-purple); 
            color: white; 
            padding: 5px 40px; 
            display: flex; 
            align-items: center; 
            min-height: 52px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom a { color: rgba(255,255,255,0.9); text-decoration: none; padding: 14px 16px; font-size: 15px; }
        .navbar-custom a:hover { color: white; }
        .navbar-custom .active-tab { background-color: rgba(255,255,255,0.15); font-weight: 500; color: white; }
        .user-avatar { width: 34px; height: 34px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; color: white; cursor: pointer; }

        /* मुख्य कंटेनर लेआउट */
        .dashboard-wrapper { max-width: 1100px; margin: 30px auto; padding: 0 20px; position: relative; }
        
        /* स्क्रीनशॉट वाला छोटा पर्पल आइकॉन बटन (राइट साइड में) */
        .right-toggle-box {
            position: absolute; right: -5px; top: 0;
            background: var(--moodle-purple); color: white;
            padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;
        }

        /* सफ़ेद मुख्य बॉक्स */
        .main-container { 
            background: rgba(255, 255, 255, 0.98); 
            padding: 35px; 
            border-radius: 4px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            border: 1px solid #dee2e6;
        }
        
        .dashboard-title { color: var(--moodle-purple); font-weight: 700; font-size: 32px; margin-bottom: 25px; }
        .section-heading { color: #6a737d; font-size: 18px; font-weight: 500; margin-top: 15px; margin-bottom: 20px; border-bottom: 1px solid #f0f2f5; padding-bottom: 10px; }

        /* सर्च और फ़िल्टर कंट्रोल्स बार */
        .filter-bar { display: flex; gap: 10px; margin-bottom: 20px; align-items: center; flex-wrap: wrap; }
        .filter-btn { border: 1px solid #ced4da; background: #fff; padding: 6px 12px; border-radius: 4px; color: #495057; font-size: 14px; }
        .search-input { border: 1px solid #ced4da; padding: 6px 12px; border-radius: 4px; font-size: 14px; min-width: 280px; }

        /* कोर्स ओवरव्यू ग्रिड रो (थंबनेल के साथ) */
        .course-card-row { 
            border: 1px solid #e9ecef; padding: 15px; border-radius: 6px; 
            display: flex; align-items: center; background: #fff; position: relative;
        }
        /* इमेज जैसा चेक पैटर्न वाला थंबनेल बॉक्स */
        .course-thumbnail { 
            width: 75px; height: 50px; 
            background-color: #adb5bd; 
            background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,.1) 5px, rgba(255,255,255,.1) 10px);
            border-radius: 4px; margin-right: 15px; 
        }
        .course-details { flex-grow: 1; }
        .course-link { color: var(--moodle-purple); font-weight: 600; text-decoration: none; font-size: 15px; }
        .course-link:hover { text-decoration: underline; }
        .course-tag { font-size: 13px; color: #495057; margin: 2px 0; }
        .course-progress-text { font-size: 13px; color: #6c757d; }
        .three-dots-menu { color: #6c757d; cursor: pointer; padding: 5px; }

        /* माई कोर्सेस सूची नीचे */
        .my-courses-list { padding-top: 10px; }
        .my-course-item { display: flex; align-items: center; gap: 10px; font-size: 14.5px; color: var(--moodle-purple); font-weight: 500; }
    </style>
</head>
<body>

    <!-- 1. टॉप प्रोफेशनल बार -->
    <div class="navbar-custom">
        <a href="#" style="padding-left:0;"><strong>AISECT</strong></a>
        <a href="my.php">Home</a>
        <a href="my.php" class="active-tab">Dashboard</a>
        <a href="my_courses.php">My courses</a>
        
        <div class="ms-auto dropdown d-flex align-items-center gap-2">
            <div class="user-avatar" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo strtoupper(substr($student['name'] ?? 'US', 0, 2)); ?>
            </div>
            <i class="fa fa-chevron-down text-white small" style="font-size: 9px;"></i>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li class="px-3 py-2 border-bottom"><strong><?php echo htmlspecialchars($student['name'] ?? 'Student'); ?></strong></li>
                <li><a class="dropdown-item text-danger" href="?logout=true"><i class="fa fa-sign-out-alt me-2"></i>Log out</a></li>
            </ul>
        </div>
    </div>

    <!-- 2. मेन कंटेंट रैपर -->
    <div class="dashboard-wrapper">
        
        <!-- स्क्रीनशॉट वाला छोटा पर्पल आइकॉन साइड बॉक्स -->
        <div class="right-toggle-box d-none d-lg-block">
            <i class="fa-regular fa-window-maximize"></i>
        </div>

        <div class="main-container">
            <!-- डैशबोर्ड हेडिंग -->
            <h1 class="dashboard-title">Dashboard</h1>
          <h3 class="fw-bold mb-4" style="color: #333; font-size: 28px;">
        Hi, <?php echo strtoupper(htmlspecialchars($student['name'] ?? 'STUDENT')); ?>! 👋
    </h3>
            
            <!-- कोर्स ओवरव्यू सेक्शन -->
            <div class="section-heading">Course overview</div>
            
            <!-- स्क्रीनशॉट की तरह सर्च और फ़िल्टर ग्रुप -->
            <div class="filter-bar">
                <button class="filter-btn">All <i class="fa fa-chevron-down ms-1" style="font-size:10px;"></i></button>
                <input type="text" class="search-input" placeholder="Search" value="Search">
                <button class="filter-btn ms-auto">Sort by course name <i class="fa fa-chevron-down ms-1" style="font-size:10px;"></i></button>
                <button class="filter-btn">List <i class="fa fa-chevron-down ms-1" style="font-size:10px;"></i></button>
            </div>

            <!-- मुख्य कोर्स कार्ड रो -->
            <div class="course-card-row shadow-sm mb-4">
                <div class="course-thumbnail"></div>
                <div class="course-details">
                    <div>
                        <i class="fa fa-star text-warning me-1" style="font-size: 13px;"></i>
                        <a href="view.php" class="course-link"><?php echo $course_full_name; ?> (New Pattern)</a>
                    </div>
                    <div class="course-tag">Skill Courses</div>
                    <div class="course-progress-text">44% complete</div>
                </div>
                <div class="three-dots-menu">
                    <i class="fa fa-ellipsis-v"></i>
                </div>
            </div>

            <!-- माई कोर्सेस सेक्शन -->
            <div class="section-heading">My courses</div>
            <div class="my-courses-list">
                <div class="my-course-item">
                    <i class="fa fa-graduation-cap"></i>
                    <span><?php echo $course_full_name; ?> (New Pattern)</span>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>