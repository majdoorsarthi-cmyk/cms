<?php
include 'db_config.php';

if(!isset($_GET['id'])) {
    echo "Student ID is required!";
    exit();
}

$student_id = mysqli_real_escape_string($conn, $_GET['id']);

/** * UPDATED FETCH LOGIC: 
 * Database screenshot ke hisaab se columns match kiye gaye hain.
 */
$sql = "SELECT s.*, 
        u.name as user_table_name, 
        u.email as student_email
        FROM students s 
        LEFT JOIN users u ON s.user_id = u.id 
        WHERE s.id = '$student_id' OR s.user_id = '$student_id' 
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

$student = mysqli_fetch_assoc($result);

if(!$student) {
    echo "Student record not found in database!";
    exit();
}

// School Info
$school_name = "CMS PROFESSIONAL ACADEMY";
$school_address = "Sector 15, Knowledge Park, New Delhi";
$valid_till = "MARCH 2026";

// PHOTO PATH LOGIC
$photo_path = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
$check_paths = [
    'uploads/profile/' . $student['photo'],
    'uploads/students/' . $student['photo'],
    '../uploads/profile/' . $student['photo']
];

if(!empty($student['photo']) && $student['photo'] != 'default.png') {
    foreach($check_paths as $path) {
        if(file_exists($path)) {
            $photo_path = $path;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - <?php echo ($student['name'] ?? $student['user_table_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page { size: auto; margin: 0mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: #f0f2f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            flex-direction: column;
            padding: 20px;
        }

        /* Standard ID Card Size (PVC CR80) - Printing Fix */
        .id-card {
            width: 325px;
            height: 485px; 
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            border: 1px solid #ddd;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header {
            background: #1a237e;
            color: white;
            padding: 15px 10px;
            text-align: center;
            border-bottom: 4px solid #ffab00;
        }
        .header h2 { font-size: 14px; font-weight: 700; text-transform: uppercase; }
        .header p { font-size: 9px; opacity: 0.9; }

        .photo-area { text-align: center; margin-top: 15px; }
        .photo-area img {
            width: 110px;
            height: 125px;
            border-radius: 8px;
            border: 3px solid #1a237e;
            object-fit: cover;
            background: #eee;
        }

        .name-section { text-align: center; margin-top: 8px; padding: 0 10px; }
        .name-section h3 { font-size: 17px; color: #1a237e; font-weight: 700; }
        .course-badge { 
            background: #e8eaf6; 
            color: #1a237e; 
            font-size: 11px; 
            padding: 2px 12px; 
            border-radius: 20px; 
            display: inline-block;
            font-weight: 600;
        }

        .info-grid { margin: 15px 25px; font-size: 12px; }
        .info-row {
            display: flex;
            margin-bottom: 6px;
            border-bottom: 1px solid #f9f9f9;
        }
        .label { font-weight: 600; color: #666; width: 85px; }
        .value { font-weight: 700; color: #111; flex: 1; }

        .footer-area {
            position: absolute;
            bottom: 35px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px;
        }
        .qr-code img { width: 55px; height: 55px; }
        .sign { text-align: center; }
        .sign p { 
            font-size: 10px; 
            font-weight: 700; 
            border-top: 1px solid #333; 
            color: #1a237e;
            margin-top: 40px; /* Space for signature */
        }

        .bottom-bar {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #1a237e;
            color: white;
            font-size: 9px;
            text-align: center;
            padding: 6px 0;
        }

        .no-print {
            margin-bottom: 15px;
            background: #ffab00;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 700;
            color: #1a237e;
        }

        @media print {
            .no-print { display: none; }
            body { background: none; padding: 0; }
            .id-card { box-shadow: none; border: 1px solid #ccc; margin: 0 auto; }
        }
    </style>
</head>
<body>

    <button class="no-print" onclick="window.print()">🖨️ PRINT ID CARD</button>

    <div class="id-card">
        <div class="header">
            <h2><?php echo $school_name; ?></h2>
            <p><?php echo $school_address; ?></p>
        </div>

        <div class="photo-area">
            <img src="<?php echo $photo_path; ?>" alt="Student Photo">
        </div>

        <div class="name-section">
            <h3><?php echo strtoupper($student['name'] ?? $student['user_table_name']); ?></h3>
            <span class="course-badge"><?php echo strtoupper($student['course'] ?? 'DCA'); ?></span>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <span class="label">Roll No:</span>
                <span class="value">#<?php echo $student['roll_no'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-row">
                <span class="label">Father:</span>
                <span class="value"><?php echo strtoupper($student['father_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Phone:</span>
                <span class="value"><?php echo $student['mobile'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-row">
                <span class="label">DOB:</span>
                <span class="value"><?php echo (!empty($student['dob'])) ? date('d-m-Y', strtotime($student['dob'])) : 'N/A'; ?></span>
            </div>
        </div>

        <div class="footer-area">
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=STU-<?php echo $student['roll_no']; ?>" alt="QR">
            </div>
            <div class="sign">
                <p>Issuing Authority</p>
            </div>
        </div>

        <div class="bottom-bar">
            STUDENT IDENTITY CARD | VALID UPTO: <?php echo $valid_till; ?>
        </div>
    </div>

</body>
</html>