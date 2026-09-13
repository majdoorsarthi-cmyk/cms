<?php
// admin_requests.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_config.php';

// 1. Admin Auth Check
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

$table_name = "service_requests"; 

// 2. Action Logic: Status Update
if(isset($_GET['action']) && isset($_GET['id'])) {
    $req_id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    
    $update_query = "UPDATE $table_name SET status = '$status' WHERE id = '$req_id'";
    if(mysqli_query($conn, $update_query)) {
        header("Location: admin_requests.php?filter=".$_GET['filter']."&msg=success");
        exit();
    }
}

// 3. Fetch Filtered Data (Sudhari hui Query)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Pending';

// Humne 'u.id' aur 's.user_id' ko check kiya hai. 
// Agar aapki table mein 'student_id' ki jagah sirf 'user_id' hai toh niche query update karein.
$query = "SELECT r.*, u.name as student_name, s.roll_no, s.course 
          FROM $table_name r 
          LEFT JOIN users u ON r.student_id = u.id 
          LEFT JOIN students s ON u.id = s.user_id 
          WHERE r.status = '$filter' 
          ORDER BY r.id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Request Center | Fixed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4361ee; --success: #2ec4b6; --danger: #e71d36; --bg: #f4f7fe; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); padding: 20px; color: #2b3674; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0px 10px 30px rgba(0,0,0,0.05); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .tab-group { background: #f4f7fe; padding: 5px; border-radius: 12px; display: flex; }
        .tab { padding: 10px 25px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; color: #a3aed0; }
        .tab.active { background: white; color: var(--primary); box-shadow: 0px 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; color: #a3aed0; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f4f7fe; font-size: 14px; }
        .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .pending { background: #fff4e5; color: #ff9f1c; }
        .approved { background: #e7faf3; color: #2ec4b6; }
        .rejected { background: #ffebee; color: #e71d36; }
        .btn-approve { background: var(--success); color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 12px; margin-right: 5px; }
        .btn-reject { background: #f4f7fe; color: var(--danger); padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <div>
            <h2>Admin Request Center</h2>
            <p>Manage and Verify Document Submissions</p>
        </div>
        <div class="tab-group">
            <a href="?filter=Pending" class="tab <?= $filter=='Pending'?'active':'' ?>">Pending</a>
            <a href="?filter=Approved" class="tab <?= $filter=='Approved'?'active':'' ?>">Approved</a>
            <a href="?filter=Rejected" class="tab <?= $filter=='Rejected'?'active':'' ?>">Rejected</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Details</th>
                <th>Request Details</th>
                <th>Status</th>
                <th>Date</th>
                <?php if($filter == 'Pending'): ?><th>Actions</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td>
                    <strong><?= $row['student_name'] ?? 'Unknown User' ?></strong><br>
                    <small style="color:#a3aed0">Roll: <?= $row['roll_no'] ?? 'N/A' ?> | <?= $row['course'] ?? 'N/A' ?></small>
                </td>
                <td>
                    <b><?= $row['request_type'] ?? ($row['type'] ?? 'General Request') ?></b><br>
                    <small><?= $row['description'] ?? ($row['message'] ?? 'No details provided') ?></small>
                </td>
                <td><span class="status-pill <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                <td><?= date('d M, Y', strtotime($row['request_date'] ?? $row['created_at'])) ?></td>
                <?php if($filter == 'Pending'): ?>
                <td>
                    <a href="?action=approve&id=<?= $row['id'] ?>&filter=Pending" class="btn-approve">Approve</a>
                    <a href="?action=reject&id=<?= $row['id'] ?>&filter=Pending" class="btn-reject">Reject</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <p style="margin-top:30px;"><a href="admin_dashboard.php">← Return to Dashboard</a></p>
</div>

</body>
</html>