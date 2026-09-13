<?php
session_name("STUDENT_SESSION");
session_start();

// दो फ़ोल्डर पीछे जाकर db_config शामिल करना क्योंकि यह फ़ाइल user/ फोल्डर के अंदर है
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

// कोर्स का पूरा नाम सेट करने का लॉजिक (DCA होने पर फुल फॉर्म दिखाएगा)
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
  <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants | <?php echo $course_display_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --moodle-purple: #8A4F8D; 
            --light-purple-bg: #f4edf5;
            --text-dark: #333333;
        }
        
        /* असली बैकग्राउंड इमेज जो स्क्रीनशॉट में दिख रही है */
        body { 
            background: url('../book_bg.jpg') no-repeat center center fixed; 
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text-dark);
        }
        
        /* टॉप नेविगेशन बार */
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

        /* मेन रैपर लेआउट */
        .main-wrapper { display: flex; min-height: calc(100vh - 52px); }

        /* लेफ्ट साइडबार (हूबहू ओरिजिनल स्ट्रक्चर) */
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

        /* मुख्य सफ़ेद कंटेंट कार्ड */
        .content-container { flex-grow: 1; padding: 30px; overflow-y: auto; }
        .main-white-card { 
            background: rgba(255, 255, 255, 0.98); 
            padding: 35px; 
            border-radius: 4px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #dee2e6;
        }
        .course-title { color: var(--moodle-purple); font-weight: 700; font-size: 30px; margin-bottom: 25px; }

        /* ओरिजिनल टैब्स */
        .moodle-tabs { display: flex; gap: 4px; margin-bottom: 30px; }
        .moodle-tab-btn { padding: 10px 20px; font-size: 14px; text-decoration: none; border-radius: 4px; color: var(--moodle-purple); background: #f8f9fa; }
        .moodle-tab-btn.active { background: var(--moodle-purple); color: white; }

        /* फ़िल्टर बॉक्स का आर्किटेक्चर */
        .filter-section { border: 1px solid #dee2e6; border-radius: 4px; padding: 20px; margin-bottom: 20px; background: #fff; position: relative; }
        .filter-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .filter-select { border: 1px solid #ced4da; padding: 6px 12px; border-radius: 4px; font-size: 14px; background-color: #fff; max-width: 200px; }
        .remove-filter-icon { position: absolute; right: 20px; top: 22px; color: #666; cursor: pointer; font-size: 18px; }
        .filter-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; }
        
        .btn-add-condition { background: transparent; border: none; color: var(--moodle-purple); font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 5px; }
        .btn-clear-filters { background: #e9ecef; border: 1px solid #ced4da; color: #495057; font-size: 14px; padding: 6px 16px; border-radius: 4px; text-decoration: none; margin-right: 8px; }
        .btn-apply-filters { background: var(--moodle-purple); border: 1px solid var(--moodle-purple); color: white; font-size: 14px; padding: 6px 16px; border-radius: 4px; text-decoration: none; }

        /* अल्फाबेट फ़िल्टर लिस्ट (A-Z) */
        .alphabet-filter-container { margin-bottom: 15px; font-size: 14px; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
        .alphabet-label { width: 90px; color: #666; font-size: 13.5px; }
        .alphabet-list { display: flex; flex-wrap: wrap; gap: 2px; list-style: none; padding: 0; margin: 0; }
        .alphabet-list a { display: inline-block; padding: 3px 8px; border: 1px solid #dee2e6; text-decoration: none; color: var(--moodle-purple); font-size: 13px; min-width: 28px; text-align: center; }
        .alphabet-list .active { background: var(--moodle-purple); color: white; border-color: var(--moodle-purple); }

        /* पार्टिसिपेंट्स काउंट टेक्स्ट */
        .participants-count { font-size: 15px; color: #333; font-weight: 500; margin: 20px 0 15px 0; }

        /* डेटा टेबल डिज़ाइन */
        .moodle-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .moodle-table th { border-top: 1px solid #dee2e6; border-bottom: 2px solid #dee2e6; padding: 12px 10px; text-align: left; color: var(--moodle-purple); font-weight: 600; }
        .moodle-table td { border-bottom: 1px solid #dee2e6; padding: 12px 10px; vertical-align: middle; color: #444; }
        .moodle-table tr:nth-of-type(even) { background-color: #f8f9fa; }
        .moodle-table th a { color: var(--moodle-purple); text-decoration: none; display: flex; align-items: center; gap: 5px; }

        /* पेजिनेशन कंट्रोल */
        .moodle-pagination { display: flex; justify-content: center; gap: 3px; margin: 25px 0; list-style: none; padding: 0; }
        .moodle-pagination a, .moodle-pagination span { display: inline-block; padding: 5px 11px; border: 1px solid #dee2e6; text-decoration: none; color: var(--moodle-purple); font-size: 13px; border-radius: 4px; }
        .moodle-pagination .active { background: var(--moodle-purple); color: white; border-color: var(--moodle-purple); }
        
        /* बॉटम सलेक्शन रो */
        .bottom-action-row { border-top: 1px solid #dee2e6; padding-top: 15px; display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
        .btn-bulk-action { background: var(--moodle-purple); color: white; padding: 6px 14px; border: none; border-radius: 4px; font-weight: 500; }
        .select-moodle { border: 1px solid #ced4da; padding: 6px 12px; border-radius: 4px; background: #fff; font-size: 13px; color: #495057; width: 180px; }

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
        </div>

        <div class="content-container">
            <div class="main-white-card">
                
                <h1 class="course-title"><?php echo $course_display_name; ?> (New Pattern)</h1>
                
                <div class="moodle-tabs">
                    <a href="../view.php" class="moodle-tab-btn">Course</a>
                    <a href="#" class="moodle-tab-btn active">Participants</a>
                    <a href="#" class="moodle-tab-btn">Activities</a>
                    <a href="#" class="moodle-tab-btn">Competencies</a>
                </div>

                <div class="filter-section shadow-sm">
                    <div class="filter-row">
                        <span class="fs-14">Match</span>
                        <select class="filter-select form-select form-select-sm d-inline-block">
                            <option>Any</option>
                            <option>All</option>
                        </select>
                        <select class="filter-select form-select form-select-sm d-inline-block" style="min-width: 180px;">
                            <option>Select</option>
                            <option>Keyword</option>
                            <option>Status</option>
                            <option>Roles</option>
                        </select>
                    </div>
                    <div class="remove-filter-icon">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </div>
                    <div class="filter-actions">
                        <button class="btn-add-condition"><i class="fa fa-plus"></i> Add condition</button>
                        <div>
                            <a href="#" class="btn-clear-filters">Clear filters</a>
                            <a href="#" class="btn-apply-filters">Apply filters</a>
                        </div>
                    </div>
                </div>

                <div class="participants-count">2340 participants found</div>

                <div class="alphabet-filter-container">
                    <div class="alphabet-label">First name</div>
                    <div class="alphabet-list">
                        <a href="#" class="active">All</a>
                        <?php foreach(range('A','Z') as $char) echo "<a href='#'>$char</a>"; ?>
                    </div>
                </div>

                <div class="alphabet-filter-container mb-4">
                    <div class="alphabet-label">Last name</div>
                    <div class="alphabet-list">
                        <a href="#" class="active">All</a>
                        <?php foreach(range('A','Z') as $char) echo "<a href='#'>$char</a>"; ?>
                    </div>
                </div>

                <table class="moodle-table">
                    <thead>
                        <tr>
                            <th width="3%"><input type="checkbox"></th>
                            <th width="50%"><a href="#">First name / Last name <i class="fa fa-arrow-up-short-wide small"></i></a></th>
                            <th width="15%"><a href="#">Roles</a></th>
                            <th width="15%"><a href="#">Groups</a></th>
                            <th width="17%"><a href="#">Last access to course</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><a href="#" class="text-decoration-none" style="color:var(--moodle-purple);">MR. ANJEET RATAN RATAN RATAN</a></td>
                            <td>Student</td>
                            <td>No groups</td>
                            <td>20 hours 51 mins</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><a href="#" class="text-decoration-none" style="color:var(--moodle-purple);">OM LIKHAR SANT SARAN OM SANT</a></td>
                            <td>Student</td>
                            <td>No groups</td>
                            <td>20 hours 27 mins</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><a href="#" class="text-decoration-none" style="color:var(--moodle-purple);">KM. ABDAL ANJEE</a></td>
                            <td>Student</td>
                            <td>No groups</td>
                            <td>46 mins 57 secs</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><a href="#" class="text-decoration-none" style="color:var(--moodle-purple);">KM. FATIMA ANWAR ALI NADAF ALI</a></td>
                            <td>Student</td>
                            <td>No groups</td>
                            <td>10 hours 22 mins</td>
                        </tr>
                    </tbody>
                </table>

                <div class="moodle-pagination">
                    <span class="active">1</span>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">4</a>
                    <a href="#">5</a>
                    <a href="#">6</a>
                    <a href="#">7</a>
                    <a href="#">8</a>
                    <a href="#">9</a>
                    <a href="#">10</a>
                    <span>...</span>
                    <a href="#">117</a>
                    <a href="#">»</a>
                </div>

                <div class="bottom-action-row">
                    <div>
                        <span class="text-muted me-2">With selected users...</span>
                        <select class="select-moodle form-select form-select-sm d-inline-block">
                            <option>Choose...</option>
                            <option>Send a message</option>
                            <option>Add a new note</option>
                        </select>
                    </div>
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