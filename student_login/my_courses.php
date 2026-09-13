<?php
session_name("STUDENT_SESSION");
session_start();
include('../db_config.php');

if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }

$u = $_SESSION['user'];
$query = mysqli_query($conn, "SELECT name, course FROM students WHERE roll_no = '$u'");
$student = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta charset="UTF-8">
    <title>My courses | AISECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* बॉडी को flex कॉलम में रखा है ताकि फूटर हमेशा नीचे रहे */
        body { 
            background: url('book_bg.jpg') no-repeat center center fixed; 
            background-size: cover; 
            font-family: sans-serif; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            margin: 0; 
        }
        
        .navbar-custom { background-color: #8e44ad; color: white; padding: 10px 5%; }
        .nav-links a { color: white; text-decoration: none; margin-right: 20px; font-weight: 500; }
        
        /* मुख्य कंटेनर जो पेज का बाकी हिस्सा लेगा */
        .wrapper { flex: 1; padding: 40px 5%; }
        
        .content-box { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 30px; 
            border-radius: 4px; 
            border: 1px solid #dee2e6; 
            max-width: 900px; 
            margin: 0 auto; 
        }
        
        .course-item { 
            border: 1px solid #dee2e6; 
            padding: 15px; 
            border-radius: 4px; 
            display: flex; 
            align-items: center; 
            margin-top: 20px; 
            background: white;
        }
        
        .course-img { width: 80px; height: 60px; background: #c5c5c5; margin-right: 20px; border-radius: 2px; }
        
        /* फूटर की स्टाइलिंग */
        .footer { 
            background: #222; 
            color: #888; 
            text-align: center; 
            padding: 20px; 
            font-size: 14px; 
            margin-top: auto; 
        }
    </style>
</head>
<body>

    <div class="navbar-custom d-flex align-items-center">
        <div class="me-4"><img src="logo.png" height="30"> <b>AISECT</b></div>
        <div class="nav-links">
            <a href="my.php">Home</a>
            <a href="my_courses.php">Dashboard</a>
            <a href="my_courses.php" style="border-bottom: 2px solid white;">My courses</a>
        </div>
        <div class="ms-auto"><span class="badge bg-light text-dark"><?php echo substr($student['name'], 0, 2); ?></span></div>
    </div>

    <div class="wrapper">
        <div class="content-box">
            <h2 class="mb-4">My courses</h2>
            <h5 class="text-secondary border-bottom pb-2">Course overview</h5>
            
            <div class="d-flex gap-2 my-4">
                <select class="form-select w-auto"><option>All</option></select>
                <input type="text" class="form-control" style="max-width: 250px;" placeholder="Search">
                <select class="form-select w-auto"><option>Sort by course name</option></select>
                <select class="form-select w-auto"><option>List</option></select>
            </div>

            <div class="course-item">
                <div class="course-img"></div>
                <div>
                    <h5 class="text-primary mb-1">
                        <a href="view.php" class="text-decoration-none"><?php echo htmlspecialchars($student['course']); ?></a>
                    </h5>
                    <div class="text-muted small">Skill Courses</div>
                    <div class="progress mt-2" style="width: 200px; height: 10px;">
                        <div class="progress-bar" style="width: 0%; background-color: #8e44ad;"></div>
                    </div>
                    <small class="text-muted">0% complete</small>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Copyright &copy; 2026 - AISECT Group of Universities
    </div>

</body>
</html>