<?php
session_start();
include 'db_config.php';

// 1. Admin Auth Check
if(!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// 2. Action Logic (Verify/Reject)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action == 'approve') ? 'Verified' : 'Rejected';
    
    $update_q = "UPDATE students SET status = '$new_status' WHERE user_id = '$id'";
    if(mysqli_query($conn, $update_q)) {
        $msg = "Student status updated to $new_status successfully!";
        $msg_class = "alert-success";
    }
}

// 3. Fetch Pending Students
$pending_students = mysqli_query($conn, "SELECT s.*, u.email, u.name FROM students s JOIN users u ON s.user_id = u.id WHERE s.status = 'Pending' ORDER BY s.id DESC");
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Student Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --admin-dark: #0f172a; --brand-blue: #3498db; }
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        
        .admin-sidebar { width: 260px; background: var(--admin-dark); height: 100vh; position: fixed; color: white; padding: 20px; }
        .main-body { margin-left: 260px; padding: 30px; }
        
        .nav-link { color: #94a3b8; padding: 12px; border-radius: 8px; margin-bottom: 5px; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        
        .stat-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid var(--brand-blue); }
        .student-table { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        
        .badge-pending { background: #fef3c7; color: #d97706; padding: 5px 12px; border-radius: 20px; font-size: 12px; }
        .btn-verify { background: #10b981; color: white; border: none; padding: 6px 15px; border-radius: 6px; font-size: 13px; transition: 0.3s; }
        .btn-reject { background: #ef4444; color: white; border: none; padding: 6px 15px; border-radius: 6px; font-size: 13px; }
        .btn-verify:hover { background: #059669; }
    </style>
</head>
<body>

<div class="admin-sidebar">
    <h4 class="mb-5 text-center text-info fw-bold">ADMIN PANEL</h4>
    <a href="admin_dashboard.php" class="nav-link"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
    <a href="admin_verify.php" class="nav-link active"><i class="fas fa-user-check me-2"></i> Verify Students</a>
    <a href="manage_courses.php" class="nav-link"><i class="fas fa-book me-2"></i> Manage Courses</a>
    <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="main-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Student Verification Portal</h2>
        <div class="text-muted small"><?php echo date('D, d M Y'); ?></div>
    </div>

    <?php if(isset($msg)) echo "<div class='alert $msg_class'>$msg</div>"; ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted text-uppercase">Pending Requests</h6>
                <h2 class="fw-bold"><?php echo mysqli_num_rows($pending_students); ?></h2>
            </div>
        </div>
    </div>

    <div class="student-table">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Application No</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($pending_students) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($pending_students)): ?>
                    <tr>
                        <td class="ps-4 fw-bold">#<?php echo $row['roll_no']; ?></td>
                        <td>
                            <div><?php echo $row['name']; ?></div>
                            <small class="text-muted"><?php echo $row['email']; ?></small>
                        </td>
                        <td><span class="badge bg-info text-dark"><?php echo $row['course']; ?></span></td>
                        <td><span class="badge-pending">Pending</span></td>
                        <td class="text-center">
                            <a href="?action=approve&id=<?php echo $row['user_id']; ?>" class="btn-verify me-2" onclick="return confirm('Verify this student?')">
                                <i class="fas fa-check me-1"></i> Verify
                            </a>
                            <a href="?action=reject&id=<?php echo $row['user_id']; ?>" class="btn-reject" onclick="return confirm('Reject this student?')">
                                <i class="fas fa-times me-1"></i> Reject
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No pending verification requests.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>