<?php 
/**
 * CMS PRO - Smart Live Attendance, Geofenced Center & Interactive Calendar
 * PhonePe Style Mobile Interface Update
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php'; 

// Auth Check
if(!isset($_SESSION['student'])) { 
    if(isset($_GET['ajax']) || isset($_POST['ajax'])) {
        echo json_encode(['status' => 'error', 'message' => 'अनाधिकृत पहुंच! (Unauthenticated access)']);
        exit();
    }
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];
$user_id = $stu['user_id'] ?? ($stu['id'] ?? null); 

// Dynamic Safe Column Lookup Engine
function getSafeColumn($conn, $table, $possible_names) {
    foreach($possible_names as $name) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$name'");
        if($check && mysqli_num_rows($check) > 0) return $name;
    }
    return $possible_names[0]; 
}

$att_col = getSafeColumn($conn, 'attendance', ['student_id', 'user_id']);
$att_date_col = getSafeColumn($conn, 'attendance', ['attendance_date', 'date']);

// Auto Database Alter Execution
function addColumnIfNotExists($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check && mysqli_num_rows($check) == 0) {
        @mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

// Database Columns Check
addColumnIfNotExists($conn, 'attendance', 'user_id', "INT NULL");
addColumnIfNotExists($conn, 'attendance', 'student_id', "INT NULL");
addColumnIfNotExists($conn, 'attendance', 'student_name', "VARCHAR(255) NULL");
addColumnIfNotExists($conn, 'attendance', 'date', "DATE NULL");
addColumnIfNotExists($conn, 'attendance', 'status', "VARCHAR(50) DEFAULT 'present'");
addColumnIfNotExists($conn, 'attendance', 'latitude', "VARCHAR(100) NULL");
addColumnIfNotExists($conn, 'attendance', 'longitude', "VARCHAR(100) NULL");
addColumnIfNotExists($conn, 'attendance', 'photo_path', "VARCHAR(500) NULL");
addColumnIfNotExists($conn, 'attendance', 'signature_path', "VARCHAR(500) NULL");
addColumnIfNotExists($conn, 'attendance', 'in_time', "TIME NULL");
addColumnIfNotExists($conn, 'attendance', 'out_time', "TIME NULL");
addColumnIfNotExists($conn, 'attendance', 'ip_address', "VARCHAR(100) NULL");
addColumnIfNotExists($conn, 'attendance', 'device_info', "TEXT NULL");

// Fetch Real Student Data
$profile_stmt = $conn->prepare("SELECT id, name FROM students WHERE user_id = ? OR id = ? LIMIT 1");
$profile_stmt->bind_param("ss", $user_id, $user_id);
$profile_stmt->execute();
$profile_res = $profile_stmt->get_result();
$profile_data = $profile_res->fetch_assoc();
$real_stu_id = $profile_data['id'] ?? $user_id;
$student_name = $profile_data['name'] ?? ($_SESSION['student']['name'] ?? 'छात्र');

// Festivals List
$festivals = [
    '01-01' => 'नव वर्ष', '01-14' => 'मकर संक्रांति', '01-26' => 'गणतंत्र दिवस',
    '03-08' => 'महाशिवरात्रि', '03-25' => 'होली', '04-14' => 'अम्बेडकर जयंती',
    '08-15' => 'स्वतंत्रता दिवस', '08-26' => 'कृष्ण जन्माष्टमी', '10-02' => 'गांधी जयंती',
    '10-12' => 'दशहरा', '10-31' => 'दीपावली', '12-25' => 'क्रिसमस'
];

// Distance Calculation Function (Haversine Formula) in Meters
function getDistanceMeters($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

// Data Fetcher Engine
function getCalendarAndStats($conn, $real_stu_id, $att_col, $att_date_col, $current_month, $current_year, $festivals, $student_name) {
    $cal_data = [];
    $stmt = $conn->prepare("SELECT `$att_date_col` as att_date, status, in_time, out_time, photo_path, latitude, longitude FROM attendance WHERE (`$att_col` = ? OR user_id = ?) AND MONTH(`$att_date_col`) = ? AND YEAR(`$att_date_col`) = ?");
    $stmt->bind_param("ssii", $real_stu_id, $real_stu_id, $current_month, $current_year);
    $stmt->execute();
    $res = $stmt->get_result();
    
    while($row = $res->fetch_assoc()) {
        $cal_data[$row['att_date']] = $row;
    }

    $timestamp = mktime(0, 0, 0, $current_month, 1, $current_year);
    $days_in_month = date('t', $timestamp);
    $first_day_of_week = date('w', $timestamp);

    $total_present = 0; $total_absent = 0; $total_holidays = 0;
    $days_array = [];
    $today_str = date('Y-m-d');

    for ($day = 1; $day <= $days_in_month; $day++) {
        $date_str = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
        $mm_dd = sprintf('%02d-%02d', $current_month, $day);
        $day_of_week = date('w', strtotime($date_str)); 
        
        $status_class = '';
        $holiday_name = '';
        $time_str = '--:--';
        $out_time_str = '--:--';
        $photo_url = '';

        if($day_of_week == 0) {
            $status_class = 'sunday-holiday';
            $holiday_name = 'रविवार (छुट्टी)';
            $total_holidays++;
        } elseif(isset($festivals[$mm_dd])) {
            $status_class = 'holiday';
            $holiday_name = $festivals[$mm_dd];
            $total_holidays++;
        }

        if (isset($cal_data[$date_str])) {
            $row_info = $cal_data[$date_str];
            $st = ucfirst($row_info['status']);
            if (strtolower($st) == 'present') { $status_class = 'present'; $total_present++; }
            elseif (strtolower($st) == 'absent') { $status_class = 'absent'; $total_absent++; }
            
            $time_str = !empty($row_info['in_time']) ? date('h:i A', strtotime($row_info['in_time'])) : '--:--';
            $out_time_str = !empty($row_info['out_time']) ? date('h:i A', strtotime($row_info['out_time'])) : '--:--';
            $photo_url = $row_info['photo_path'] ?? '';
        } elseif ($date_str < $today_str && $status_class == '') {
            $status_class = 'absent';
            $total_absent++;
        }

        $days_array[] = [
            'day'        => $day,
            'date'       => $date_str,
            'status'     => $status_class,
            'is_today'   => ($date_str === $today_str),
            'is_sunday'  => ($day_of_week == 0),
            'label'      => $holiday_name,
            'time'       => $time_str,
            'out_time'   => $out_time_str,
            'photo'      => $photo_url
        ];
    }

    return [
        'student_name'  => $student_name,
        'month_name'    => date('F Y', $timestamp),
        'days_in_month' => $days_in_month,
        'first_day_of_week' => $first_day_of_week,
        'stats'         => ['present' => $total_present, 'absent' => $total_absent, 'holidays' => $total_holidays],
        'calendar'      => $days_array
    ];
}

// HANDLE AJAX ATTENDANCE SUBMISSION
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_attendance') {
    header('Content-Type: application/json');
    
    $ip_address  = $_SERVER['REMOTE_ADDR'] ?? '';
    $device_info = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $today = date('Y-m-d');
    $curr_time = date('H:i:s');
    $status = 'present';
    
    $lat = floatval($_POST['lat'] ?? 0);
    $lng = floatval($_POST['lng'] ?? 0);
    $base64_image = $_POST['live_photo'] ?? '';
    $base64_signature = $_POST['live_signature'] ?? '';

    // GPS CENTER COORDINATES
    $center_lat = 23.389839;
    $center_lng = 79.535585;
    $max_distance_meters = 400; 

    $distance = getDistanceMeters($lat, $lng, $center_lat, $center_lng);

    if($distance > $max_distance_meters) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'आप कोचिंग सेंटर के बाहर हैं! (दूरी: ' . round($distance) . ' मीटर)। केवल कोचिंग परिसर से हाजिरी लगाई जा सकती है।'
        ]);
        exit();
    }

    if(empty($base64_signature)) {
        echo json_encode(['status' => 'error', 'message' => 'डिजिटल सिग्नेचर अनिवार्य है!']);
        exit();
    }

    $check_stmt = $conn->prepare("SELECT id, in_time, out_time FROM attendance WHERE (`$att_col` = ? OR user_id = ?) AND `$att_date_col` = ? LIMIT 1");
    $check_stmt->bind_param("sss", $real_stu_id, $real_stu_id, $today);
    $check_stmt->execute();
    $existing_rec = $check_stmt->get_result()->fetch_assoc();

    if ($existing_rec) {
        if (empty($existing_rec['out_time'])) {
            $update_sql = "UPDATE attendance SET out_time = ? WHERE id = ?";
            $up_stmt = $conn->prepare($update_sql);
            $up_stmt->bind_param("si", $curr_time, $existing_rec['id']);
            
            if ($up_stmt->execute()) {
                echo json_encode([
                    'status' => 'success', 
                    'type' => 'out_time_marked',
                    'message' => 'आपका सेंटर से (जाने का समय) सफलता पूर्वक दर्ज हो गया है!'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'जाने के समय में अपडेट करने में त्रुटि: ' . $up_stmt->error]);
            }
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'आपकी आज की आने जाने दोनों की हाजिरी दर्ज हो चुकी है!']);
            exit();
        }
    }

    $rel_sig_dir   = __DIR__ . '/uploads/signatures/';
    $rel_photo_dir = __DIR__ . '/uploads/attendance/';
    @mkdir($rel_sig_dir, 0777, true);
    @mkdir($rel_photo_dir, 0777, true);

    $photo_db_path = "";
    if (!empty($base64_image) && strpos($base64_image, ';base64,') !== false) {
        $photoName = $real_stu_id . "_photo_" . time() . '.png';
        $img_parts = explode(";base64,", $base64_image);
        $decoded_photo = base64_decode($img_parts[1]);
        if(@file_put_contents($rel_photo_dir . $photoName, $decoded_photo)) {
            $photo_db_path = "uploads/attendance/" . $photoName;
        }
    }

    $signature_db_path = "";
    if (!empty($base64_signature) && strpos($base64_signature, ';base64,') !== false) {
        $sigName = $real_stu_id . "_sig_" . time() . '.png';
        $sig_parts = explode(";base64,", $base64_signature);
        $decoded_sig = base64_decode($sig_parts[1]);
        if (@file_put_contents($rel_sig_dir . $sigName, $decoded_sig)) {
            $signature_db_path = "uploads/signatures/" . $sigName;
        }
    }

    $sql = "INSERT INTO attendance (user_id, student_id, student_name, date, status, photo_path, latitude, longitude, in_time, ip_address, device_info, signature_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $ins_stmt = $conn->prepare($sql);
    if($ins_stmt) {
        $ins_stmt->bind_param("sissssssssss", $user_id, $real_stu_id, $student_name, $today, $status, $photo_db_path, $lat, $lng, $curr_time, $ip_address, $device_info, $signature_db_path);

        if($ins_stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'type' => 'in_time_marked',
                'message' => 'सेंटर पर (आने का समय) सफलता पूर्वक दर्ज हो गया है!'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'डेटाबेस त्रुटि: ' . $ins_stmt->error]);
        }
    }
    exit();
}

// FETCH CALENDAR VIA AJAX
if(isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    $m = isset($_GET['m']) ? intval($_GET['m']) : date('m');
    $y = isset($_GET['y']) ? intval($_GET['y']) : date('Y');
    
    $resData = getCalendarAndStats($conn, $real_stu_id, $att_col, $att_date_col, $m, $y, $festivals, $student_name);
    echo json_encode($resData);
    exit();
}

$today_date = date('Y-m-d');
$today_check = $conn->prepare("SELECT id, in_time, out_time FROM attendance WHERE (`$att_col` = ? OR user_id = ?) AND `$att_date_col` = ? LIMIT 1");
$today_check->bind_param("sss", $real_stu_id, $real_stu_id, $today_date);
$today_check->execute();
$today_rec = $today_check->get_result()->fetch_assoc();

$is_in_marked = !empty($today_rec['in_time']);
$is_out_marked = !empty($today_rec['out_time']);

$current_month = date('m');
$current_year = date('Y');
$initial_data = getCalendarAndStats($conn, $real_stu_id, $att_col, $att_date_col, $current_month, $current_year, $festivals, $student_name);
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>TC ACADEMY - उपस्थिति पोर्टल</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Hind:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --phonepe-purple: #5f259f;
            --phonepe-dark-purple: #3f1d70;
            --phonepe-bg: #f4f5f9;
            --primary: #5f259f; 
            --primary-dark: #4b1c80; 
            --sidebar-bg: #0f172a;
            --nav-text: #94a3b8; 
            --bg: #f4f5f9; 
            --card-bg: #ffffff;
            --text-main: #0f172a; 
            --text-sub: #64748b;
            --app-header-height: 64px; 
            --bottom-nav-height: 72px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Hind', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; padding-bottom: calc(var(--bottom-nav-height) + 20px); }

        /* PHONEPE STYLE MOBILE HEADER */
        .app-header { 
            display: none; 
            position: fixed; 
            top: 0; left: 0; right: 0; 
            height: var(--app-header-height); 
            background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%); 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 16px; 
            box-shadow: 0 4px 15px rgba(95, 37, 159, 0.25); 
            z-index: 1000; 
            color: #ffffff;
        }
        .app-header-title { font-weight: 800; font-size: 20px; display: flex; align-items: center; gap: 14px; letter-spacing: 0.3px; }

        .big-home-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 20px; padding: 18px 22px; color: #fff; display: flex;
            align-items: center; justify-content: space-between; text-decoration: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); margin-bottom: 20px; transition: transform 0.2s ease;
        }
        .big-home-banner:active { transform: scale(0.98); }
        .big-home-left { display: flex; align-items: center; gap: 14px; }
        .big-home-icon { width: 52px; height: 52px; background: var(--phonepe-purple); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #fff; }

        .sidebar {
            width: 280px; background: var(--sidebar-bg); height: 100vh;
            position: fixed; top: 0; left: 0; padding: 20px 16px; color: white;
            z-index: 10050; transition: transform 0.3s ease; box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .sidebar-brand { font-size: 19px; font-weight: 800; color: #fff; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .close-btn { font-size: 22px; cursor: pointer; color: #94a3b8; display: none; }
        .menu-label { font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 700; margin: 16px 0 8px 8px; }
        .sidebar-menu a { color: var(--nav-text); text-decoration: none; padding: 12px 14px; display: flex; align-items: center; gap: 12px; border-radius: 12px; font-size: 16px; margin-bottom: 6px; font-weight: 600; }
        .sidebar-menu a.active { background: rgba(95, 37, 159, 0.25); color: #a78bfa; border-left: 4px solid #8b5cf6; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); z-index: 10040; backdrop-filter: blur(4px); }
        
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 24px; transition: all 0.3s ease; }
        .content-body { max-width: 1100px; margin: 0 auto; }

        /* PHONEPE STYLE ACTION CARD */
        .action-card {
            background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark-purple) 100%);
            color: white; border-radius: 26px; padding: 28px 24px; margin-bottom: 24px;
            box-shadow: 0 12px 28px rgba(95, 37, 159, 0.3); text-align: center; position: relative; overflow: hidden;
        }
        .action-card h3 { font-size: 24px; font-weight: 800; margin-bottom: 10px; letter-spacing: 0.2px; }
        
        .live-clock-badge {
            display: inline-block; background: rgba(255,255,255,0.15); padding: 10px 20px; 
            border-radius: 24px; font-size: 15px; font-weight: 700; margin-bottom: 22px;
            backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25); color: #ffffff;
        }

        /* BIGGER ACCESSIBLE BUTTONS */
        .btn-mark-live { 
            background: #ffffff; color: var(--phonepe-purple); border: none; padding: 18px 26px; 
            border-radius: 18px; font-size: 18px; font-weight: 800; cursor: pointer; 
            display: inline-flex; align-items: center; justify-content: center; gap: 12px; 
            width: 100%; max-width: 420px; box-shadow: 0 10px 25px rgba(0,0,0,0.18); transition: all 0.2s ease; 
        }
        .btn-mark-live:active { transform: scale(0.97); }
        .btn-mark-disabled { background: rgba(255,255,255,0.2); color: #ffffff; cursor: not-allowed; box-shadow: none; border: 1px solid rgba(255,255,255,0.3); font-size: 16px; }

        .camera-wrapper { display: none; background: rgba(0,0,0,0.5); padding: 12px; border-radius: 20px; margin: 0 auto 15px auto; max-width: 380px; backdrop-filter: blur(8px); }
        #live-camera { width: 100%; border-radius: 14px; background: #000; transform: scaleX(-1); }
        #snapshot-canvas { display: none; }

        .signature-container { display: none; margin: 18px auto; max-width: 380px; text-align: left; }
        .signature-container label { font-size: 14px; font-weight: 700; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .signature-pad { background: #ffffff; border-radius: 16px; border: 2px dashed #cbd5e1; touch-action: none; cursor: crosshair; width: 100%; height: 160px; box-shadow: inset 0 2px 6px rgba(0,0,0,0.05); }
        .btn-clear-sig { background: rgba(255,255,255,0.25); color: #fff; border: 1px solid rgba(255,255,255,0.4); padding: 6px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer; }

        /* MODAL POPUPS */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.85); backdrop-filter: blur(8px); z-index: 30000; align-items: center; justify-content: center; padding: 20px; }
        .modal-content { background: #fff; width: 100%; max-width: 440px; border-radius: 26px; padding: 26px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.25); position: relative; }
        .modal-content h3 { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 16px; }
        .preview-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .preview-box { border: 1px solid #e2e8f0; border-radius: 16px; padding: 10px; background: #f8fafc; }
        .preview-box p { font-size: 12px; font-weight: 700; color: var(--text-sub); margin-bottom: 8px; }
        .preview-box img { width: 100%; height: 110px; object-fit: contain; border-radius: 12px; background: #fff; border: 1px solid #cbd5e1; }
        
        .modal-actions { display: flex; gap: 12px; justify-content: center; }
        .modal-actions button { flex: 1; padding: 16px; border: none; border-radius: 14px; font-weight: 800; font-size: 16px; cursor: pointer; }
        .btn-retake { background: #f1f5f9; color: #475569; }
        .btn-submit { background: #16a34a; color: white; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35); }

        /* STATS CARDS - PHONEPE GRID STYLE */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--card-bg); padding: 20px 16px; border-radius: 22px; border: 1px solid #e2e8f0; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .stat-card h4 { font-size: 13px; color: var(--text-sub); font-weight: 700; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card h2 { font-size: 28px; font-weight: 800; }

        /* CALENDAR STYLE */
        .calendar-card { background: var(--card-bg); border-radius: 26px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .calendar-header h3 { font-size: 22px; font-weight: 800; color: #0f172a; }
        .calendar-nav-btns { display: flex; gap: 8px; }
        .calendar-nav-btns button { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 18px; border-radius: 14px; color: var(--text-main); font-size: 15px; font-weight: 800; cursor: pointer; }

        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; }
        .cal-day-name { font-size: 14px; font-weight: 800; color: var(--text-sub); padding-bottom: 8px; text-transform: uppercase; }
        .cal-day-name.sunday-header { color: #ef4444 !important; }
        
        .cal-cell { 
            aspect-ratio: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; 
            border-radius: 18px; font-size: 17px; font-weight: 800; background: #f8fafc; border: 1px solid #f1f5f9;
            position: relative; transition: all 0.2s ease; cursor: pointer;
        }
        .cal-cell:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.08); }
        
        .cal-cell.present { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%) !important; color: #15803d !important; border: 1px solid #86efac !important; }
        .cal-cell.absent { background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%) !important; color: #be123c !important; border: 1px solid #fda4af !important; }
        .cal-cell.sunday-holiday { background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%) !important; color: #991b1b !important; border: 1px solid #f87171 !important; }
        .cal-cell.holiday { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%) !important; color: #4338ca !important; border: 1px solid #a5b4fc !important; }
        .cal-cell.today-cell { outline: 3px solid #f59e0b; outline-offset: -2px; }
        .cal-cell .holiday-tag { font-size: 10px; font-weight: 800; margin-top: 2px; max-width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cal-cell.empty { background: transparent; border: none; cursor: default; }

        /* PHONEPE STYLE BOTTOM NAV BAR FOR MOBILE */
        .mobile-bottom-nav { 
            display: none; position: fixed; bottom: 0; left: 0; right: 0; 
            height: var(--bottom-nav-height); background: #ffffff; 
            box-shadow: 0 -6px 25px rgba(0,0,0,0.1); z-index: 1000; 
            justify-content: space-around; align-items: center; border-top: 1px solid #e2e8f0; 
        }
        .mobile-bottom-nav a { 
            color: #64748b; text-decoration: none; display: flex; 
            flex-direction: column; align-items: center; font-size: 13px; 
            font-weight: 800; gap: 4px; width: 25%; 
        }
        .mobile-bottom-nav a.active { color: var(--phonepe-purple); }
        .mobile-bottom-nav a i { font-size: 24px; }

        .big-home-nav-btn {
            background: var(--phonepe-purple); color: #ffffff !important; border-radius: 20px;
            padding: 10px 18px; font-size: 13px !important; margin-top: -20px;
            box-shadow: 0 8px 20px rgba(95, 37, 159, 0.4); border: 3px solid #ffffff;
        }

        .app-toast-area { margin-bottom: 18px; }
        .app-toast { padding: 16px 20px; border-radius: 16px; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .app-toast.success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .app-toast.error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        /* PHONEPE MOBILE SPECIFIC ENHANCEMENTS & BIGGER FONTS */
        @media (max-width: 768px) {
            .app-header { display: flex; } 
            .mobile-bottom-nav { display: flex; } 
            .close-btn { display: block; }
            .sidebar { transform: translateX(-100%); } 
            .sidebar.active { transform: translateX(0); }
            .main-content { 
                margin-left: 0; 
                width: 100%; 
                padding: calc(var(--app-header-height) + 16px) 16px calc(var(--bottom-nav-height) + 20px) 16px; 
            }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card h4 { font-size: 13px; }
            .stat-card h2 { font-size: 26px; }
            .calendar-grid { gap: 6px; } 
            .cal-cell { font-size: 16px; border-radius: 14px; }
            .action-card h3 { font-size: 22px; }
            .btn-mark-live { font-size: 17px; padding: 16px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- PHONEPE HEADER FOR MOBILE -->
<div class="app-header">
    <div class="app-header-title">
        <i class="fas fa-bars" onclick="toggleSidebar()" style="font-size: 24px; cursor: pointer;"></i>
        <span>TC ACADEMY</span>
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
        <a href="student_dashboard.php" style="background: rgba(255,255,255,0.08); color: #fff; font-size: 16px;"><i class="fas fa-home" style="color:#a78bfa;"></i> <span>होम (Dashboard)</span></a>

        <div class="menu-label">एकेडमिक्स & LMS</div>
        <a href="lms_access.php"><i class="fas fa-laptop-code" style="color:#38bdf8;"></i> <span>LMS Access</span></a>
        <a href="video_lectures.php"><i class="fas fa-video" style="color:#a7f3d0;"></i> <span>वीडियो लेक्चर्स</span></a>
        <a href="online_exam.php"><i class="fas fa-file-signature" style="color:#fde047;"></i> <span>ऑनलाइन एग्जाम</span></a>
        <a href="homework.php"><i class="fas fa-tasks" style="color:#f472b6;"></i> <span>होमवर्क & असाइनमेंट</span></a>

        <div class="menu-label">हाजिरी सिस्टम</div>
        <a href="attendance.php" class="active"><i class="fas fa-fingerprint" style="color:#a78bfa;"></i> <span>आज की हाजिरी</span></a>
        <a href="attendance_history.php"><i class="fas fa-history" style="color:#cbd5e1;"></i> <span>हाजिरी का इतिहास</span></a>
        <a href="holidays.php"><i class="fas fa-calendar-day" style="color:#fbbf24;"></i> <span>छुट्टियों की सूची</span></a>

        <div class="menu-label">रिकॉर्ड्स & फीस</div>
        <a href="document_wallet.php"><i class="fas fa-id-card" style="color:#34d399;"></i> <span>डॉक्यूमेंट वॉलेट</span></a>
        <a href="exam_results.php"><i class="fas fa-poll-h" style="color:#c084fc;"></i> <span>परीक्षा परिणाम</span></a>
        <a href="fees_receipts.php"><i class="fas fa-receipt" style="color:#4ade80;"></i> <span>फीस & रसीद</span></a>

        <div class="menu-label">अकाउंट</div>
        <a href="profile_settings.php"><i class="fas fa-user-cog" style="color:#94a3b8;"></i> <span>प्रोफाइल सेटिंग्स</span></a>
        <a href="logout.php" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> <span>लॉगआउट</span></a>
    </div>
</div>

<!-- DATE DETAIL POPUP MODAL -->
<div id="dateDetailModal" class="modal-overlay">
    <div class="modal-content">
        <i class="fas fa-times" onclick="closeDateModal()" style="position:absolute; top:20px; right:22px; font-size:22px; cursor:pointer; color:#64748b;"></i>
        <h3 id="popDateTitle">दिनांक विवरण</h3>
        
        <div id="popBodyContent" style="text-align:left; font-size:16px; color:#334155; line-height:1.8;">
            <!-- JS dynamic content -->
        </div>

        <div style="margin-top:22px;">
            <button onclick="closeDateModal()" style="width:100%; padding:14px; background:var(--phonepe-purple); color:#fff; border:none; border-radius:14px; font-weight:800; font-size:16px; cursor:pointer;">बंद करें</button>
        </div>
    </div>
</div>

<!-- PREVIEW MODAL -->
<div id="photoPreviewModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-file-signature" style="color:#16a34a;"></i> विवरण का सत्यापन करें</h3>
        
        <div class="preview-grid">
            <div class="preview-box">
                <p>लाइव फोटो</p>
                <img id="previewModalImg" src="" alt="Photo">
            </div>
            <div class="preview-box">
                <p>डिजिटल हस्ताक्षर</p>
                <img id="previewModalSig" src="" alt="Signature">
            </div>
        </div>

        <div class="modal-actions">
            <button onclick="retakePhoto()" class="btn-retake"><i class="fas fa-redo"></i> पुनः प्रयास</button>
            <button id="btnConfirmSubmit" onclick="confirmSubmitAjax()" class="btn-submit"><i class="fas fa-check-circle"></i> सबमिट करें</button>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="content-body">
        
        <a href="student_dashboard.php" class="big-home-banner">
            <div class="big-home-left">
                <div class="big-home-icon"><i class="fas fa-home"></i></div>
                <div>
                    <h4 style="font-size:18px; font-weight:800;">स्टूडेंट डैशबोर्ड</h4>
                    <p style="font-size:13px; color:#94a3b8;">मुख्य पृष्ठ (Home Page) पर जाने के लिए यहाँ क्लिक करें</p>
                </div>
            </div>
            <i class="fas fa-chevron-right" style="color:#a78bfa; font-size: 20px;"></i>
        </a>

        <div id="toastArea" class="app-toast-area"></div>

        <!-- MAIN ACTION CARD (PHONEPE STYLE) -->
        <div class="action-card">
            <h3>TC ACADEMY COMPUTER CENTER - ATTENDANCE SYSTEM</h3>
            <div class="live-clock-badge" id="liveClock">समय लोड हो रहा है...</div>
            
            <div id="attendanceActionContainer">
                <?php if($is_in_marked && $is_out_marked): ?>
                    <button type="button" class="btn-mark-live btn-mark-disabled" disabled>
                        <i class="fas fa-check-circle" style="color:#4ade80; font-size: 22px;"></i> आपकी आज की (आने और जाने की) हाजिरी हो चुकी है
                    </button>
                <?php else: ?>
                    <button type="button" id="startBtn" class="btn-mark-live" onclick="initLiveAttendance()">
                        <i class="fas fa-map-marker-alt" style="color:#ef4444; font-size: 22px;"></i> 
                        <span id="startBtnText"><?= $is_in_marked ? 'जाने का समय(Chack Out) दर्ज करने कैमरा खोलें' : 'आने का समय (Chack In) दर्ज करने कैमरा खोलें' ?></span>
                    </button>

                    <input type="file" id="fallbackCameraInput" accept="image/*" capture="user" style="display: none;" onchange="handleFallbackImage(event)">

                    <div id="camera-wrapper" class="camera-wrapper">
                        <video id="live-camera" autoplay playsinline muted></video>
                        <canvas id="snapshot-canvas"></canvas>
                    </div>

                    <div id="signature-container" class="signature-container">
                        <label>
                            <span><i class="fas fa-pen-nib"></i> डिजिटल सिग्नेचर (हस्ताक्षर करें)</span>
                            <button type="button" class="btn-clear-sig" onclick="clearSignature()"><i class="fas fa-eraser"></i> साफ़ करें</button>
                        </label>
                        <canvas id="signaturePad" class="signature-pad"></canvas>
                    </div>

                    <form id="liveAttendanceForm" style="display:none;">
                        <input type="hidden" name="lat" id="lat">
                        <input type="hidden" name="lng" id="lng">
                        <input type="hidden" name="live_photo" id="live_photo">
                        <input type="hidden" name="live_signature" id="live_signature">
                        
                        <button type="button" id="captureBtn" class="btn-mark-live" style="background:#16a34a; color:#ffffff; margin-top:14px;" onclick="validateAndPreview()">
                            <i class="fas fa-check-double"></i> सत्यापन व पूर्वावलोकन
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- STATS GRID -->
        <div class="stats-grid">
            <div class="stat-card"><h4>कुल दिन</h4><h2 id="statTotalDays"><?= $initial_data['days_in_month'] ?></h2></div>
            <div class="stat-card"><h4>उपस्थित</h4><h2 id="statPresent" style="color:#16a34a;"><?= $initial_data['stats']['present'] ?></h2></div>
            <div class="stat-card"><h4>अनुपस्थित</h4><h2 id="statAbsent" style="color:#dc2626;"><?= $initial_data['stats']['absent'] ?></h2></div>
            <div class="stat-card"><h4>छुट्टियां (रविवार)</h4><h2 id="statHolidays" style="color:#ef4444;"><?= $initial_data['stats']['holidays'] ?></h2></div>
        </div>

        <!-- CALENDAR CARD -->
        <div class="calendar-card">
            <div class="calendar-header">
                <h3 id="calendarMonthTitle"><i class="fas fa-calendar-alt" style="color:var(--phonepe-purple);"></i> <?= $initial_data['month_name'] ?></h3>
                <div class="calendar-nav-btns">
                    <button type="button" onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i> पिछला</button>
                    <button type="button" onclick="changeMonth(1)">अगला <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="calendar-grid" id="calendarGrid"></div>
        </div>

    </div>
</div>

<!-- PHONEPE STYLE BOTTOM MOBILE NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="attendance.php" class="active"><i class="fas fa-fingerprint"></i><span>हाजिरी</span></a>
    <a href="student_dashboard.php" class="big-home-nav-btn"><i class="fas fa-home" style="font-size:26px;"></i><span>HOME</span></a>
    <a href="attendance_history.php"><i class="fas fa-history"></i><span>इतिहास</span></a>
</div>

<script>
    let currentMonth = <?= $current_month ?>;
    let currentYear = <?= $current_year ?>;
    let globalStudentName = "<?= htmlspecialchars($student_name, ENT_QUOTES) ?>";
    let streamRef = null;
    let usingFallback = false;

    let sigCanvas = document.getElementById('signaturePad');
    let sigCtx = sigCanvas ? sigCanvas.getContext('2d') : null;
    let isDrawing = false;
    let isSigned = false;

    document.addEventListener("DOMContentLoaded", () => {
        renderCalendar(<?= json_encode($initial_data) ?>);
        updateClock();
        setInterval(updateClock, 1000);
    });

    function updateClock() {
        const now = new Date();
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const timeString = now.toLocaleTimeString('hi-IN', timeOptions);
        const dateOptions = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
        const dateString = now.toLocaleDateString('hi-IN', dateOptions);
        
        const clockEl = document.getElementById('liveClock');
        if(clockEl) clockEl.innerHTML = `<i class="far fa-clock"></i> ${timeString} | ${dateString}`;
    }

    function toggleSidebar() {
        document.getElementById('sidebarNav').classList.toggle('active');
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    function setupSignaturePad() {
        if(!sigCanvas) return;
        sigCanvas.width = sigCanvas.offsetWidth;
        sigCanvas.height = sigCanvas.offsetHeight;
        sigCtx.lineWidth = 3;
        sigCtx.lineCap = 'round';
        sigCtx.strokeStyle = '#0f172a';

        function getPos(e) {
            let rect = sigCanvas.getBoundingClientRect();
            let clientX = e.touches ? e.touches[0].clientX : e.clientX;
            let clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function startDraw(e) {
            isDrawing = true;
            let pos = getPos(e);
            sigCtx.beginPath();
            sigCtx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            let pos = getPos(e);
            sigCtx.lineTo(pos.x, pos.y);
            sigCtx.stroke();
            isSigned = true;
        }

        function stopDraw() { isDrawing = false; }

        sigCanvas.addEventListener('mousedown', startDraw);
        sigCanvas.addEventListener('mousemove', draw);
        sigCanvas.addEventListener('mouseup', stopDraw);
        sigCanvas.addEventListener('touchstart', startDraw, { passive: false });
        sigCanvas.addEventListener('touchmove', draw, { passive: false });
        sigCanvas.addEventListener('touchend', stopDraw);
    }

    function clearSignature() {
        if(sigCtx) {
            sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
            isSigned = false;
        }
    }

    function initLiveAttendance() {
        const startBtn = document.getElementById('startBtn');
        startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GPS Fetch हो रहा है...';
        startBtn.disabled = true;

        if (!("geolocation" in navigator)) {
            alert("आपके डिवाइस में GPS सपोर्ट नहीं है।");
            startBtn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('lat').value = pos.coords.latitude;
                document.getElementById('lng').value = pos.coords.longitude;
                startCamera();
            },
            (err) => {
                startBtn.innerHTML = '<i class="fas fa-location-arrow"></i> पुनः प्रयास करें';
                startBtn.disabled = false;
                alert("GPS लोकेशन प्राप्त करने में विफल! मोबाइल की लोकेशन चालू करें।");
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function startCamera() {
        const startBtn = document.getElementById('startBtn');
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false })
                .then(stream => {
                    streamRef = stream;
                    const video = document.getElementById('live-camera');
                    video.srcObject = stream;
                    video.play();
                    
                    startBtn.style.display = 'none';
                    document.getElementById('camera-wrapper').style.display = 'block';
                    document.getElementById('signature-container').style.display = 'block';
                    document.getElementById('liveAttendanceForm').style.display = 'block';
                    setupSignaturePad();
                })
                .catch(() => triggerNativeCameraFallback());
        } else {
            triggerNativeCameraFallback();
        }
    }

    function triggerNativeCameraFallback() {
        usingFallback = true;
        document.getElementById('fallbackCameraInput').click(); 
    }

    function handleFallbackImage(event) {
        const file = event.target.files[0];
        if(!file) {
            document.getElementById('startBtn').disabled = false;
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('live_photo').value = e.target.result; 
            document.getElementById('startBtn').style.display = 'none';
            document.getElementById('signature-container').style.display = 'block';
            document.getElementById('liveAttendanceForm').style.display = 'block';
            setupSignaturePad();
        }
        reader.readAsDataURL(file);
    }

    function validateAndPreview() {
        if(!isSigned) {
            alert("कृपया स्क्रीन पर अपने डिजिटल सिग्नेचर (हस्ताक्षर) करें!");
            return;
        }

        let photoDataUrl = "";
        if(!usingFallback) {
            const video = document.getElementById('live-camera');
            const canvas = document.getElementById('snapshot-canvas');
            const context = canvas.getContext('2d');
            
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            photoDataUrl = canvas.toDataURL('image/png');
        } else {
            photoDataUrl = document.getElementById('live_photo').value;
        }

        const sigDataUrl = sigCanvas.toDataURL('image/png');

        document.getElementById('live_photo').value = photoDataUrl;
        document.getElementById('live_signature').value = sigDataUrl;

        document.getElementById('previewModalImg').src = photoDataUrl;
        document.getElementById('previewModalSig').src = sigDataUrl;

        const video = document.getElementById('live-camera');
        if(video && !usingFallback) video.pause();

        document.getElementById('photoPreviewModal').style.display = 'flex';
    }

    function retakePhoto() {
        document.getElementById('photoPreviewModal').style.display = 'none';
        if(usingFallback) {
            triggerNativeCameraFallback();
        } else {
            const video = document.getElementById('live-camera');
            if(video) video.play();
        }
    }

    /* DYNAMIC NO-RELOAD AJAX SUBMISSION FIX */
    function confirmSubmitAjax() {
        const submitBtn = document.getElementById('btnConfirmSubmit');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> सबमिट हो रहा है...';
        submitBtn.disabled = true;

        if(streamRef) streamRef.getTracks().forEach(t => t.stop());

        let formData = new FormData();
        formData.append('action', 'mark_attendance');
        formData.append('lat', document.getElementById('lat').value);
        formData.append('lng', document.getElementById('lng').value);
        formData.append('live_photo', document.getElementById('live_photo').value);
        formData.append('live_signature', document.getElementById('live_signature').value);

        fetch('attendance.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            document.getElementById('photoPreviewModal').style.display = 'none';
            const toastArea = document.getElementById('toastArea');
            
            if(data.status === 'success') {
                toastArea.innerHTML = `<div class="app-toast success"><i class="fas fa-check-circle" style="font-size:20px;"></i> ${data.message}</div>`;
                
                // Hide camera/signature controls smoothly
                document.getElementById('camera-wrapper').style.display = 'none';
                document.getElementById('signature-container').style.display = 'none';
                document.getElementById('liveAttendanceForm').style.display = 'none';

                if(data.type === 'in_time_marked') {
                    // In-time marked; transform button to Out-time action dynamically without page reload!
                    const actionContainer = document.getElementById('attendanceActionContainer');
                    actionContainer.innerHTML = `
                        <button type="button" id="startBtn" class="btn-mark-live" onclick="initLiveAttendance()">
                            <i class="fas fa-map-marker-alt" style="color:#ef4444; font-size: 22px;"></i> 
                            <span>Out Time (प्रस्थान) दर्ज करने के लिए कैमरा खोलें</span>
                        </button>
                        <input type="file" id="fallbackCameraInput" accept="image/*" capture="user" style="display: none;" onchange="handleFallbackImage(event)">
                        <div id="camera-wrapper" class="camera-wrapper"><video id="live-camera" autoplay playsinline muted></video><canvas id="snapshot-canvas"></canvas></div>
                        <div id="signature-container" class="signature-container">
                            <label><span><i class="fas fa-pen-nib"></i> डिजिटल सिग्नेचर (हस्ताक्षर करें)</span><button type="button" class="btn-clear-sig" onclick="clearSignature()"><i class="fas fa-eraser"></i> साफ़ करें</button></label>
                            <canvas id="signaturePad" class="signature-pad"></canvas>
                        </div>
                        <form id="liveAttendanceForm" style="display:none;">
                            <input type="hidden" name="lat" id="lat"><input type="hidden" name="lng" id="lng">
                            <input type="hidden" name="live_photo" id="live_photo"><input type="hidden" name="live_signature" id="live_signature">
                            <button type="button" id="captureBtn" class="btn-mark-live" style="background:#16a34a; color:#ffffff; margin-top:14px;" onclick="validateAndPreview()"><i class="fas fa-check-double"></i> सत्यापन व पूर्वावलोकन</button>
                        </form>
                    `;
                    sigCanvas = document.getElementById('signaturePad');
                    sigCtx = sigCanvas ? sigCanvas.getContext('2d') : null;
                } else {
                    // Both In & Out Time completed
                    const actionContainer = document.getElementById('attendanceActionContainer');
                    actionContainer.innerHTML = `
                        <button type="button" class="btn-mark-live btn-mark-disabled" disabled>
                            <i class="fas fa-check-circle" style="color:#4ade80; font-size: 22px;"></i> आपकी आज की (आने और जाने) की हाजिरी पूर्ण हो चुकी है
                        </button>
                    `;
                }

                // Dynamically re-fetch calendar & stats without reloading page
                fetchCalendarData(currentMonth, currentYear);

            } else {
                toastArea.innerHTML = `<div class="app-toast error"><i class="fas fa-exclamation-triangle" style="font-size:20px;"></i> ${data.message}</div>`;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> सबमिट करें';
                submitBtn.disabled = false;
            }
        })
        .catch(err => {
            alert("सर्वर से कनेक्शन में त्रुटि!");
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> सबमिट करें';
            submitBtn.disabled = false;
        });
    }

    function changeMonth(delta) {
        currentMonth += delta;
        if(currentMonth < 1) { currentMonth = 12; currentYear--; }
        else if(currentMonth > 12) { currentMonth = 1; currentYear++; }
        fetchCalendarData(currentMonth, currentYear);
    }

    function fetchCalendarData(m, y) {
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = `<div style="grid-column: span 7; padding: 40px; text-align: center; color: var(--text-sub);"><i class="fas fa-spinner fa-spin fa-2x"></i></div>`;

        fetch(`attendance.php?ajax=1&m=${m}&y=${y}`)
            .then(res => res.json())
            .then(data => renderCalendar(data))
            .catch(err => console.error(err));
    }

    function renderCalendar(data) {
        if(data.student_name) {
            globalStudentName = data.student_name;
        }

        document.getElementById('calendarMonthTitle').innerHTML = `<i class="fas fa-calendar-alt" style="color:var(--phonepe-purple);"></i> ${data.month_name}`;
        document.getElementById('statTotalDays').innerText = data.days_in_month;
        document.getElementById('statPresent').innerText = data.stats.present;
        document.getElementById('statAbsent').innerText = data.stats.absent;
        document.getElementById('statHolidays').innerText = data.stats.holidays;

        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';

        const dayNames = ['रवि', 'सोम', 'मंगल', 'बुध', 'गुरु', 'शुक्र', 'शनि'];
        dayNames.forEach((name, idx) => {
            let el = document.createElement('div');
            el.className = `cal-day-name ${idx === 0 ? 'sunday-header' : ''}`;
            el.innerText = name;
            grid.appendChild(el);
        });

        for(let i = 0; i < data.first_day_of_week; i++) {
            let empty = document.createElement('div');
            empty.className = 'cal-cell empty';
            grid.appendChild(empty);
        }

        data.calendar.forEach(item => {
            let cell = document.createElement('div');
            let todayClass = item.is_today ? 'today-cell' : '';
            cell.className = `cal-cell ${item.status} ${todayClass}`;
            
            let labelHtml = item.label ? `<span class="holiday-tag">${item.label}</span>` : '';
            cell.innerHTML = `<span>${item.day}</span>${labelHtml}`;
            
            cell.onclick = () => showDateDetails(item);

            grid.appendChild(cell);
        });
    }

    function showDateDetails(item) {
        document.getElementById('popDateTitle').innerText = `तारीख: ${item.date}`;
        let statusBadge = '';
        
        if(item.status === 'present') {
            statusBadge = `<span style="background:#dcfce7; color:#15803d; padding:6px 12px; border-radius:14px; font-weight:800; font-size:14px;">उपस्थित (Present)</span>`;
        } else if(item.status === 'absent') {
            statusBadge = `<span style="background:#fee2e2; color:#b91c1c; padding:6px 12px; border-radius:14px; font-weight:800; font-size:14px;">अनुपस्थित (Absent)</span>`;
        } else if(item.status === 'sunday-holiday' || item.status === 'holiday') {
            statusBadge = `<span style="background:#e0e7ff; color:#4338ca; padding:6px 12px; border-radius:14px; font-weight:800; font-size:14px;">छुट्टी (Holiday)</span>`;
        } else {
            statusBadge = `<span style="background:#f1f5f9; color:#64748b; padding:6px 12px; border-radius:14px; font-weight:800; font-size:14px;">कोई रिकॉर्ड नहीं</span>`;
        }

        let photoHtml = item.photo ? `<div style="margin-top:12px;"><p style="font-weight:700; margin-bottom:6px;">दर्ज फोटो:</p><img src="${item.photo}" style="width:110px; height:110px; object-fit:cover; border-radius:12px; border:1px solid #cbd5e1;"></div>` : '';
        let labelHtml = item.label ? `<p style="margin-top:8px;"><b>विशेष / त्योहार:</b> ${item.label}</p>` : '';

        document.getElementById('popBodyContent').innerHTML = `
            <p style="margin-bottom:10px; font-size:17px;"><b>विद्यार्थी का नाम:</b> ${globalStudentName}</p>
            <div style="margin-bottom:14px;"><b>स्थिति (Status):</b> ${statusBadge}</div>
            <p><b>In Time (आगमन):</b> ${item.time}</p>
            <p><b>Out Time (प्रस्थान):</b> ${item.out_time || '--:--'}</p>
            <p><b>लोकेशन:</b> TC ACADEMY CENTER, Tendukheda</p>
            ${labelHtml}
            ${photoHtml}
        `;

        document.getElementById('dateDetailModal').style.display = 'flex';
    }

    function closeDateModal() {
        document.getElementById('dateDetailModal').style.display = 'none';
    }
</script>

</body>
</html>