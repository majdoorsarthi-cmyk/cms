<?php
include 'db_config.php';

if(!isset($_GET['id'])) {
    echo "Student ID is required!";
    exit();
}

$student_id = mysqli_real_escape_string($conn, $_GET['id']);

// SQL Join: Fetching everything from students and the name from users
$sql = "SELECT s.*, u.name FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.id = '$student_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

if(!$student) {
    echo "Data not found!";
    exit();
}

$school_name = "TC ACADEMY PROFESSIONAL ";
$issue_date = date('d-m-Y');
$serial_no = "CMS/CERT/" . date('Y') . "/" . str_pad($student['id'], 4, '0', STR_PAD_LEFT);

// --- PHOTO PATH LOGIC UPDATED & FIXED ---
$photo_db = $student['photo'];
$photo_path = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png"; // Default Placeholder

if(!empty($photo_db)) {
    // List of possible directories where your photos might be
    $dirs = ['uploads/profile/', 'uploads/students/', 'uploads/'];
    
    foreach($dirs as $dir) {
        if(file_exists($dir . $photo_db)) {
            $photo_path = $dir . $photo_db;
            break; // Found the photo, exit loop
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - <?php echo $student['name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600;700&family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <style>
        /* CSS remains exactly as you had it */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: #525659; 
            display: flex; 
            justify-content: center; 
            padding: 20px 0;
            font-family: 'Poppins', sans-serif;
        }

        .certificate {
            width: 1120px;
            height: 790px;
            background: white;
            padding: 40px;
            position: relative;
            border: 15px solid #2b3674;
            background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png');
            text-align: center;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 15px; left: 15px; right: 15px; bottom: 15px;
            border: 3px solid #b8860b;
            pointer-events: none;
        }

        .serial-box { position: absolute; top: 40px; left: 60px; font-weight: bold; color: #555; font-size: 14px; }

        .student-photo {
            position: absolute;
            top: 45px;
            right: 65px;
            width: 115px;
            height: 140px;
            border: 2px solid #2b3674;
            background: white;
            z-index: 10;
        }
        .student-photo img { width: 100%; height: 100%; object-fit: cover; }

        .school-name { font-family: 'Cinzel', serif; font-size: 45px; color: #2b3674; margin-top: 20px; }
        .sub-title { font-size: 16px; letter-spacing: 4px; color: #4361ee; text-transform: uppercase; margin-bottom: 30px; }
        .main-heading { font-size: 65px; font-family: 'Dancing Script', cursive; color: #b8860b; margin: 10px 0; }
        .content { font-size: 22px; line-height: 1.8; color: #333; margin-top: 20px; }
        .student-name { font-size: 38px; font-weight: 700; color: #2b3674; text-decoration: underline; padding: 0 10px; }
        .course-name { color: #b8860b; font-weight: 700; font-size: 26px; }

        .footer-sign { margin-top: 50px; display: flex; justify-content: space-between; padding: 0 80px; }
        .sign-box { text-align: center; width: 220px; }
        .sign-line { border-top: 2px solid #2b3674; margin-top: 10px; padding-top: 5px; font-weight: 700; font-size: 14px; color: #2b3674; }

        .qr-code { position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); text-align: center; }
        .qr-code img { width: 70px; }
        .badge { position: absolute; bottom: 40px; right: 80px; width: 100px; }

        @media print {
            body { background: white; padding: 0; }
            .certificate { box-shadow: none; margin: 0; border-width: 15px !important; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }

        .no-print {
            position: fixed; top: 20px; right: 20px;
            background: #27ae60; color: white; padding: 12px 25px;
            border: none; border-radius: 8px; cursor: pointer; font-weight: 600; z-index: 100;
        }
    </style>
</head>
<body>

    <button class="no-print" onclick="window.print()">Download/Print Certificate</button>

    <div class="certificate">
        <div class="serial-box">Sr No: <?php echo $serial_no; ?></div>

        <div class="student-photo">
            <img src="<?php echo $photo_path; ?>?v=<?php echo time(); ?>" alt="Student Photo">
        </div>
        
        <div class="school-name"><?php echo $school_name; ?></div>
        <p class="sub-title">An ISO 9001:2015 Certified Institution</p>

        <h1 class="main-heading">Certificate of Completion</h1>

        <div class="content">
            This is to certify that <br>
            <span class="student-name"><?php echo strtoupper($student['name']); ?></span> <br>
            Son/Daughter of <strong><?php echo strtoupper($student['father_name'] ?? '-----------'); ?></strong> <br>
            has successfully completed the prescribed course of study in <br>
            <span class="course-name"><?php echo strtoupper($student['course']); ?></span> <br>
            attained at this academy with Roll No: <strong><?php echo $student['roll_no']; ?></strong>.
        </div>

        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=VERIFY-<?php echo $student['roll_no']; ?>" alt="QR">
            <p style="font-size: 9px;">Scan to Verify</p>
        </div>

        <img src="https://cdn-icons-png.flaticon.com/512/604/604646.png" class="badge" alt="Gold Badge">

        <div class="footer-sign">
            <div class="sign-box">
                <p><strong><?php echo $issue_date; ?></strong></p>
                <div class="sign-line">Date of Issue</div>
            </div>
            <div class="sign-box">
                <br>
                <div class="sign-line">Authorized Signatory</div>
            </div>
        </div>
    </div>

</body>
</html>