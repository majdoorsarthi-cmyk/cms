<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// 1. Auth Check (Admin Only)
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

// --- NEW: UPDATE FEES LOGIC ---
if(isset($_POST['update_fee'])) {
    $fee_id = mysqli_real_escape_string($conn, $_POST['fee_id']);
    $new_amount = mysqli_real_escape_string($conn, $_POST['new_amount']);
    
    $update_q = "UPDATE fees SET amount_paid = '$new_amount' WHERE id = '$fee_id'";
    if(mysqli_query($conn, $update_q)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
        exit();
    }
}

// --- FILTER & SEARCH LOGIC ---
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_course = isset($_GET['course']) ? $_GET['course'] : 'All';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where_clause = "WHERE MONTH(f.payment_date) = '$selected_month' AND YEAR(f.payment_date) = '$selected_year'";

if($selected_course != 'All') {
    $where_clause .= " AND s.course = '$selected_course'";
}
if(!empty($search_query)) {
    $where_clause .= " AND (u.name LIKE '%$search_query%' OR f.receipt_no LIKE '%$search_query%' OR s.roll_no LIKE '%$search_query%')";
}

// --- EXCEL EXPORT LOGIC ---
if(isset($_GET['export'])) {
    $filename = "Revenue_Report_" . $selected_month . "_" . $selected_year . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Date', 'Roll No', 'Student Name', 'Course', 'Receipt No', 'Amount Paid'));
    
    $export_q = mysqli_query($conn, "SELECT f.payment_date, s.roll_no, u.name, s.course, f.receipt_no, f.amount_paid 
                                     FROM fees f 
                                     JOIN students s ON f.student_id = s.id 
                                     JOIN users u ON s.user_id = u.id $where_clause");
    
    while($row = mysqli_fetch_assoc($export_q)) { 
        fputcsv($output, $row); 
    }
    fclose($output);
    exit();
}

// 2. Monthly Collection Total
$month_q = mysqli_query($conn, "SELECT SUM(f.amount_paid) as total FROM fees f 
                                JOIN students s ON f.student_id = s.id 
                                JOIN users u ON s.user_id = u.id $where_clause");
$monthly_collection = mysqli_fetch_assoc($month_q)['total'] ?? 0;

// 3. Transactions List
$query = "SELECT f.*, u.name, u.mobile, s.roll_no, s.course FROM fees f 
          JOIN students s ON f.student_id = s.id 
          JOIN users u ON s.user_id = u.id $where_clause ORDER BY f.payment_date DESC";
$transactions = mysqli_query($conn, $query);

$courses_list = mysqli_query($conn, "SELECT DISTINCT course FROM students ORDER BY course ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* (Puraane Styles Sem rahenge) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f1f5f9; display: flex; color: #1e293b; min-height: 100vh; }
        .sidebar { width: 260px; background: #0f172a; color: #fff; position: fixed; height: 100vh; padding: 25px; transition: 0.3s; }
        .sidebar h2 { color: #3b82f6; margin-bottom: 35px; font-size: 22px; }
        .sidebar a { color: #94a3b8; text-decoration: none; display: block; padding: 12px 15px; margin: 8px 0; border-radius: 10px; font-size: 14px; }
        .sidebar a.active { background: #3b82f6; color: white; }
        .main-content { margin-left: 260px; padding: 35px; width: calc(100% - 260px); }
        .revenue-banner { background: linear-gradient(135deg, #3b82f6, #1e40af); color: white; padding: 35px; border-radius: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .filters-card { background: white; padding: 25px; border-radius: 15px; margin-bottom: 25px; }
        .filter-form { display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end; }
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group input, .input-group select { padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; background: #f8fafc; }
        .btn { padding: 12px 20px; border-radius: 10px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-excel { background: #10b981; color: white; }
        .btn-edit { background: #6366f1; color: white; padding: 6px 12px; font-size: 11px; margin-right: 5px; }
        .btn-wa { background: #25d366; color: white; padding: 6px 12px; font-size: 11px; }
        .btn-id { background: #f59e0b; color: white; padding: 6px 12px; font-size: 11px; margin-right: 5px; }
        .table-container { background: white; border-radius: 20px; padding: 15px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 18px; text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase; }
        td { padding: 18px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }

        /* Modal Styles */
        #idModal, #editModal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); }
        .modal-card { background: white; width: 400px; margin: 100px auto; border-radius: 20px; overflow: hidden; padding: 30px; }
        
        .id-card { background: white; width: 350px; margin: 80px auto; border-radius: 20px; overflow: hidden; position: relative; }
        .id-card-header { background: #0f172a; color: white; padding: 25px; text-align: center; }
        .id-card-body { padding: 30px; text-align: center; }
        .photo-circle { width: 100px; height: 100px; background: #f1f5f9; border-radius: 50%; margin: 0 auto 15px; border: 4px solid #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 40px; }

        @media print {
            .no-print, .sidebar { display: none !important; }
            .main-content { margin: 0; padding: 0; width: 100%; }
            #idModal { display: block !important; position: relative; background: none; }
        }
    </style>
</head>
<body>

<div class="sidebar no-print">
    <h2>CMS<span>PRO</span></h2>
    <a href="admin_dashboard.php">📊 <span>Dashboard</span></a>
    <a href="students.php">👥 <span>Students</span></a>
    <a href="collect_fees.php">💰 <span>Collect Fees</span></a>
    <a href="fee_report.php" class="active">📈 <span>Revenue Report</span></a>
    <a href="logout.php" style="margin-top: 50px; color: #ef4444;">🚪 <span>Logout</span></a>
</div>

<div class="main-content">
    
    <div class="revenue-banner no-print">
        <div>
            <p>Total Revenue Filtered</p>
            <h1>₹<?= number_format($monthly_collection, 2) ?></h1>
            <p>📅 Period: <?= date('F Y', mktime(0,0,0,$selected_month, 1, $selected_year)) ?></p>
        </div>
        <div style="display:flex; gap:12px;">
            <a href="?export=1&month=<?=$selected_month?>&year=<?=$selected_year?>&course=<?=$selected_course?>&search=<?=$search_query?>" class="btn btn-excel">📥 Export Excel</a>
            <button onclick="window.print()" class="btn" style="background:rgba(255,255,255,0.2); color:white;">🖨️ Print Report</button>
        </div>
    </div>

    <div class="filters-card no-print">
        <form method="GET" class="filter-form">
            <div class="input-group">
                <label>Quick Search</label>
                <input type="text" name="search" placeholder="Name / Roll / Receipt" value="<?= htmlspecialchars($search_query) ?>">
            </div>
            <div class="input-group">
                <label>Month</label>
                <select name="month">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= sprintf('%02d', $m) ?>" <?= $selected_month == $m ? 'selected' : '' ?>>
                            <?= date('F', mktime(0,0,0,$m,1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Course</label>
                <select name="course">
                    <option value="All">All Courses</option>
                    <?php while($c = mysqli_fetch_assoc($courses_list)): ?>
                        <option value="<?= $c['course'] ?>" <?= $selected_course == $c['course'] ? 'selected' : '' ?>>
                            <?= $c['course'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Student Info</th>
                    <th>Course</th>
                    <th>Receipt</th>
                    <th>Amount Paid</th>
                    <th class="no-print">Quick Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($transactions) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($transactions)): 
                        $wa_msg = "Success Academy: Hello " . $row['name'] . ", we have received your fee of ₹" . $row['amount_paid'] . ". Receipt No: " . $row['receipt_no'];
                        $wa_url = "https://wa.me/91" . $row['mobile'] . "?text=" . urlencode($wa_msg);
                    ?>
                    <tr>
                        <td style="color:#64748b;"><?= date('d M, Y', strtotime($row['payment_date'])) ?></td>
                        <td>
                            <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($row['name']) ?></div>
                            <div style="font-size:11px; color:#3b82f6;">ROLL: <?= $row['roll_no'] ?></div>
                        </td>
                        <td><span style="background:#eff6ff; color:#3b82f6; padding:5px 10px; border-radius:6px; font-size:12px;"><?= $row['course'] ?></span></td>
                        <td><code style="background:#f1f5f9; padding:3px 6px; border-radius:4px;"><?= $row['receipt_no'] ?></code></td>
                        <td style="font-weight:bold; color:#059669;">₹<?= number_format($row['amount_paid'], 2) ?></td>
                        <td class="no-print">
                            <button class="btn btn-edit" onclick="openEditModal('<?= $row['id'] ?>', '<?= $row['amount_paid'] ?>', '<?= addslashes($row['name']) ?>')">✏️ Edit</button>
                            
                            <button class="btn btn-id" onclick="openIDCard('<?= addslashes($row['name']) ?>', '<?= $row['roll_no'] ?>', '<?= $row['course'] ?>')">🆔 ID</button>
                            <a href="<?= $wa_url ?>" target="_blank" class="btn btn-wa">💬 WA</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:50px;">No transactions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal">
    <div class="modal-card">
        <h3 style="margin-bottom: 20px;">Edit Fee Amount</h3>
        <p id="editStudentName" style="font-size: 14px; color: #64748b; margin-bottom: 15px;"></p>
        <form method="POST">
            <input type="hidden" name="fee_id" id="editFeeId">
            <div class="input-group" style="margin-bottom: 20px;">
                <label>Amount Paid (₹)</label>
                <input type="number" name="new_amount" id="editAmount" required style="width: 100%;">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" name="update_fee" class="btn btn-primary" style="flex: 1;">Update Amount</button>
                <button type="button" onclick="closeEditModal()" class="btn" style="background: #ef4444; color: white; flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="idModal">
    <div class="id-card">
        <div class="id-card-header">
            <h3>SUCCESS ACADEMY</h3>
        </div>
        <div class="id-card-body">
            <div class="photo-circle">👤</div>
            <h2 id="idName">--</h2>
            <p id="idCourse">--</p>
            <p id="idRoll">--</p>
        </div>
        <div class="no-print" style="padding:20px; display:flex; gap:10px;">
            <button onclick="window.print()" class="btn btn-primary" style="flex:1;">Print</button>
            <button onclick="document.getElementById('idModal').style.display='none'" class="btn" style="background:#ef4444; color:white; flex:1;">Close</button>
        </div>
    </div>
</div>

<script>
    // Edit Modal Functions
    function openEditModal(id, amount, name) {
        document.getElementById('editFeeId').value = id;
        document.getElementById('editAmount').value = amount;
        document.getElementById('editStudentName').innerText = "Student: " + name;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // ID Card Function
    function openIDCard(name, roll, course) {
        document.getElementById('idName').innerText = name;
        document.getElementById('idRoll').innerText = "ROLL: " + roll;
        document.getElementById('idCourse').innerText = course;
        document.getElementById('idModal').style.display = 'block';
    }

    window.onclick = function(event) {
        if (event.target.id == 'idModal') document.getElementById('idModal').style.display = "none";
        if (event.target.id == 'editModal') document.getElementById('editModal').style.display = "none";
    }
</script>

</body>
</html>