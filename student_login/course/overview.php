<?php
session_name("STUDENT_SESSION");
session_start();

// एक फ़ोल्डर पीछे जाकर db_config शामिल करना क्योंकि यह फ़ाइल course/ फोल्डर के अंदर है
include('../../db_config.php');

// लॉगआउट हैंडलर
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['user'])) { 
    header("Location: ../index.php"); 
    exit(); 
}

$u = $_SESSION['user'];
$query = mysqli_query($conn, "SELECT name, course FROM students WHERE roll_no = '$u'");
$student = mysqli_fetch_assoc($query);

// कोर्स का पूरा नाम सेट करने का लॉजिक
$course_raw = trim($student['course'] ?? '');
if (strtoupper($course_raw) == 'DCA' || $course_raw == 'Diploma in Computer Application') {
    $course_display_name = "Diploma in Computer Application";
} elseif (strtoupper($course_raw) == 'PGDCA') {
    $course_display_name = "Post Graduate Diploma in Computer Application";
} else {
    $course_display_name = htmlspecialchars($course_raw); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course activities: <?php echo $course_display_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --moodle-purple: #8A4F8D; 
            --light-purple-bg: #f4edf5;
            --text-dark: #333333;
        }
        
        /* बैकग्राउंड इमेज पाथ सेट टू वन फोल्डर बैक */
        body { 
            background: url('../book_bg.jpg') no-repeat center center fixed; 
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text-dark);
        }
        
        /* टॉप बार */
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

        /* मेन लेआउट */
        .main-wrapper { display: flex; min-height: calc(100vh - 52px); }

        /* लेफ्ट साइडबार (हूबहू स्क्रीनशॉट की तरह) */
        .sidebar { 
            width: 260px; 
            background: rgba(255, 255, 255, 0.95); 
            border-right: 1px solid #dee2e6; 
            padding: 15px 10px; 
            font-size: 13px;
        }
        .sidebar-course-select { border: 1px solid #ced4da; padding: 6px 10px; border-radius: 4px; background: #fff; font-size: 12.5px; font-weight: 500; }
        .sidebar-toggle-btn { background: var(--moodle-purple); color: white; padding: 6px 10px; border-radius: 4px; border: none; }
        
        .sidebar-menu-item { display: flex; align-items: center; padding: 8px 5px; color: #495057; text-decoration: none; font-weight: 500; margin-top: 10px; }
        .sidebar-sub-list { list-style: none; padding-left: 10px; margin-bottom: 15px; }
        .sidebar-sub-list li { padding: 6px 0; color: #555; display: flex; align-items: center; gap: 8px; line-height: 1.2; font-size: 12.5px; }
        .sidebar-sub-list li i.fa-circle { color: #2ea44f; font-size: 9px; }
        .sidebar-sub-list li i.fa-circle-regular { color: #888; font-size: 9px; }

        /* मुख्य सफ़ेद कंटेंट बॉक्स */
        .content-container { flex-grow: 1; padding: 30px; overflow-y: auto; }
        .main-white-card { 
            background: rgba(255, 255, 255, 0.98); 
            padding: 35px; 
            border-radius: 4px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #dee2e6;
        }
        .course-title { color: var(--moodle-purple); font-weight: 700; font-size: 30px; margin-bottom: 25px; }

        /* टैब रो */
        .moodle-tabs { display: flex; gap: 4px; margin-bottom: 20px; }
        .moodle-tab-btn { padding: 10px 20px; font-size: 14px; text-decoration: none; border-radius: 4px; color: var(--moodle-purple); background: #f8f9fa; }
        .moodle-tab-btn.active { background: var(--moodle-purple); color: white; }

        /* एक्टिविटीज हेडिंग्स और टेक्स्ट */
        .activities-heading { font-size: 18px; font-weight: 400; color: #6a737d; margin-top: 25px; margin-bottom: 8px; }
        .activities-subtext { font-size: 13.5px; color: #333; margin-bottom: 25px; }

        /* एक्टिविटीज आउटर बॉक्स पैनल */
        .activity-panel { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; margin-bottom: 25px; overflow: hidden; }
        .activity-panel-header { padding: 14px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #efefef; }
        .panel-icon-bg { background: #fdf2f7; color: #d63384; width: 26px; height: 26px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 1px solid #fbcfe8; }
        .panel-title { font-size: 18px; font-weight: 500; color: #212529; margin: 0; }

        /* एक्टिविटीज ग्रिड डेटा टेबल स्ट्रक्चर */
        .activity-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .activity-table th { background: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 12px 16px; text-align: left; color: #333; font-weight: 600; }
        .activity-table td { border-bottom: 1px solid #dee2e6; padding: 14px 16px; vertical-align: middle; color: #333; }
        .activity-table tr:last-child td { border-bottom: none; }
        
        /* लिंक्स और सब-टेक्स्ट */
        .activity-link { color: var(--moodle-purple); font-weight: 500; text-decoration: none; font-size: 14px; display: block; }
        .activity-link:hover { text-decoration: underline; }
        .activity-sem-tag { font-size: 11.5px; color: #666; margin-top: 2px; display: block; }

        /* अलर्ट वार्निंग आइकॉन */
        .warning-icon { color: #f0ad4e; font-size: 11px; margin-right: 4px; }
        .today-highlight { font-weight: bold; color: #333; }

        /* बटन डिजाइन */
        .btn-status-done { 
            background-color: #e2f0d9; color: #385723; 
            border: 1px solid #bcdca7; font-size: 12.5px; 
            font-weight: 500; padding: 4px 12px; border-radius: 4px; 
            display: inline-flex; align-items: center; gap: 6px; 
        }
        .btn-status-todo { 
            background-color: #f8f9fa; color: #495057; 
            border: 1px solid #ced4da; font-size: 12.5px; 
            font-weight: 500; padding: 4px 12px; border-radius: 4px; 
            display: inline-flex; align-items: center; gap: 6px; 
        }

        /* छोटा पर्पल हेल्प क्वेश्चन मार्क आइकॉन */
        .help-sticky-icon { position: fixed; right: 25px; bottom: 25px; background: var(--moodle-purple); color: white; width: 36px; height: 36px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    <div class="navbar-custom d-flex align-items-center">
        <a href="#" style="padding-left:0;"><strong>AISECT</strong></a>
        <a href="../my.php">Home</a>
        <a href="../my.php">Dashboard</a>
        <a href="../my.php" class="active-tab">My courses</a>
        
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
                    <span><?php echo ($course_raw == 'DCA') ? 'Diploma in Computer A...' : $course_raw; ?></span>
                </div>
                <button class="sidebar-toggle-btn"><i class="fa fa-bars" style="font-size: 12px;"></i></button>
            </div>

            <a href="#" class="sidebar-menu-item"><i class="fa fa-chevron-down me-2" style="font-size: 10px;"></i> General</a>
            
            <a href="#" class="sidebar-menu-item"><i class="fa fa-chevron-down me-2" style="font-size: 10px;"></i> Semester I</a>
            <ul class="sidebar-sub-list">
                <li><i class="fa-solid fa-circle"></i> <span class="text-truncate">Information Technology Tools and...</span></li>
                <li><i class="fa-solid fa-circle"></i> <span class="text-truncate">Windows and MS Office (Semeste...</span></li>
                <li><i class="fa-solid fa-circle"></i> <span class="text-truncate">Database Concept and Introducati...</span></li>
                <li><i class="fa-solid fa-circle"></i> <span class="text-truncate">Object Oriented Programming Wit...</span></li>
                <li><i class="fa-regular fa-circle"></i> <span class="text-truncate">Communication and Personality D...</span></li>
            </ul>

            <a href="#" class="sidebar-menu-item"><i class="fa fa-chevron-right me-2" style="font-size: 10px;"></i> Semester II</a>
            <ul class="sidebar-sub-list">
                <li><i class="fa-regular fa-circle"></i> <span class="text-truncate">Introduction to Internet and Web T...</span></li>
                <li><i class="fa-regular fa-circle"></i> <span class="text-truncate">Introduction to Financial Accountin...</span></li>
                <li><i class="fa-regular fa-circle"></i> <span class="text-truncate">Programming and Problem Solvin...</span></li>
                <li><i class="fa-regular fa-circle"></i> <span class="text-truncate">Introduction to Cyber Security (Se...</span></li>
            </ul>
        </div>

        <div class="content-container">
            <div class="main-white-card">
                
                <h1 class="course-title"><?php echo $course_display_name; ?> (New Pattern)</h1>
                
                <div class="moodle-tabs">
                    <a href="../view.php" class="moodle-tab-btn">Course</a>
                    <a href="../user/index.php" class="moodle-tab-btn">Participants</a>
                    <a href="#" class="moodle-tab-btn active">Activities</a>
                    <a href="#" class="moodle-tab-btn">Competencies</a>
                </div>

                <div class="activities-heading">Activities</div>
                <div class="activities-subtext">An overview of all activities in the course, with dates and other information.</div>

                <div class="activity-panel shadow-sm">
                    <div class="activity-panel-header">
                        <div class="panel-icon-bg"><i class="fa-regular fa-rectangle-list"></i></div>
                        <h5 class="panel-title">Quizzes</h5>
                    </div>
                    
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th width="45%">Name</th>
                                <th width="25%">Due date</th>
                                <th width="15%">Status</th>
                                <th width="15%">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Information Technology Tools and Network Basics (Semester I)</a>
                                    <span class="activity-sem-tag">Semester I</span>
                                </td>
                                <td>Wednesday, 10 June, 20:00</td>
                                <td>
                                    <span class="btn-status-done"><i class="fa fa-check"></i> Done <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Windows and MS Office (Semester I)</a>
                                    <span class="activity-sem-tag">Semester I</span>
                                </td>
                                <td>Thursday, 11 June, 20:00</td>
                                <td>
                                    <span class="btn-status-done"><i class="fa fa-check"></i> Done <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Database Concept and Introduction to SQL (Semester I)</a>
                                    <span class="activity-sem-tag">Semester I</span>
                                </td>
                                <td>Yesterday, 12 June, 20:00</td>
                                <td>
                                    <span class="btn-status-done"><i class="fa fa-check"></i> Done <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Object Oriented Programming With C++ (Semester I)</a>
                                    <span class="activity-sem-tag">Semester I</span>
                                </td>
                                <td>
                                    <i class="fa-solid fa-triangle-exclamation warning-icon"></i> 
                                    <span class="today-highlight">Today, 13 June, 20:00</span>
                                </td>
                                <td>
                                    <span class="btn-status-done"><i class="fa fa-check"></i> Done <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Communication and Personality Development (Semester I)</a>
                                    <span class="activity-sem-tag">Semester I</span>
                                </td>
                                <td>Monday, 15 June, 20:00</td>
                                <td>
                                    <span class="btn-status-todo">To do <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Introduction to Internet and Web Technology (Semester II)</a>
                                    <span class="activity-sem-tag">Semester II</span>
                                </td>
                                <td>Tuesday, 16 June, 20:00</td>
                                <td>
                                    <span class="btn-status-todo">To do <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Introduction to Financial Accounting with Tally (Semester II)</a>
                                    <span class="activity-sem-tag">Semester II</span>
                                </td>
                                <td>Thursday, 18 June, 20:00</td>
                                <td>
                                    <span class="btn-status-todo">To do <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Programming and Problem Solving Through Python (Semester II)</a>
                                    <span class="activity-sem-tag">Semester II</span>
                                </td>
                                <td>Friday, 19 June, 20:00</td>
                                <td>
                                    <span class="btn-status-todo">To do <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" class="activity-link">Introduction to Cyber Security (Semester II)</a>
                                    <span class="activity-sem-tag">Semester II</span>
                                </td>
                                <td>Saturday, 20 June, 20:00</td>
                                <td>
                                    <span class="btn-status-todo">To do <i class="fa fa-chevron-down" style="font-size:8px;"></i></span>
                                </td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="help-sticky-icon">
        <i class="fa-regular fa-comment-dots"></i>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>