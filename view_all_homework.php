<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php'; 

// Student Login Check
if(!isset($_SESSION['student'])) { 
    header("Location: index.php"); 
    exit(); 
}

$stu = $_SESSION['student'];

// Search Query handle karna
$search = "";
if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Homework/Study Material fetch karna
$sql = "SELECT * FROM homework WHERE title LIKE '%$search%' ORDER BY id DESC";
$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Study Materials | Student Portal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; }
        .header { background: #2c3e50; color: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; }
        .container { padding: 40px; max-width: 1000px; margin: auto; }
        
        /* Search Bar */
        .search-box { margin-bottom: 30px; display: flex; gap: 10px; }
        .search-box input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        .btn-search { background: #3498db; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        
        /* Materials List */
        .hw-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; border-left: 5px solid #3498db; }
        .hw-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .hw-info h3 { margin: 0; color: #2c3e50; font-size: 18px; }
        .hw-info p { margin: 5px 0 0; color: #7f8c8d; font-size: 14px; }
        
        .btn-download { background: #2ecc71; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; display: flex; align-items: center; gap: 8px; }
        .btn-download:hover { background: #27ae60; }
        .no-data { text-align: center; padding: 50px; color: #95a5a6; background: white; border-radius: 12px; }
        .btn-back { color: white; text-decoration: none; font-size: 14px; opacity: 0.8; }
        .btn-back:hover { opacity: 1; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <a href="student_dashboard.php" class="btn-back">← Back to Dashboard</a>
        <h2 style="margin:5px 0 0;">📚 All Study Materials</h2>
    </div>
    <div style="text-align: right;">
        <small>Portal: <?php echo htmlspecialchars($stu['name']); ?></small>
    </div>
</div>

<div class="container">
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by topic or title..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-search">🔍 Search</button>
    </form>

    <?php if(mysqli_num_rows($res) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($res)): ?>
            <div class="hw-card">
                <div class="hw-info">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>📅 Uploaded on: <?php echo isset($row['upload_date']) ? date('d M, Y', strtotime($row['upload_date'])) : 'N/A'; ?></p>
                    <?php if(isset($row['description'])): ?>
                        <p style="color:#95a5a6; margin-top:5px;"><?php echo htmlspecialchars($row['description']); ?></p>
                    <?php endif; ?>
                </div>
                <a href="uploads/<?php echo $row['file_path']; ?>" target="_blank" class="btn-download">
                    📥 Download PDF
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-data">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.3; margin-bottom: 20px;">
            <h3>No Materials Found</h3>
            <p>Admin ne abhi tak koi study material upload nahi kiya hai.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>