<?php 
include 'db_config.php'; 

// Check agar admin login hai
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

$message = "";

// 1. Teacher Add Karne ka Logic
if(isset($_POST['add_teacher'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Check Duplicate Email
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        $message = "<div style='color:red; background:#ffdce0; padding:10px; border-radius:5px;'>❌ Error: Email pehle se registered hai!</div>";
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'teacher')";
        if(mysqli_query($conn, $sql)){
            $message = "<div style='color:green; background:#e6ffed; padding:10px; border-radius:5px;'>✅ Teacher Successfully Add ho gaye hain!</div>";
        }
    }
}

// 2. Teacher Delete Karne ka Logic
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id' AND role='teacher'");
    header("Location: teachers.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Teachers | Admin</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 230px; height: 100vh; background: #2c3e50; color: #fff; position: fixed; padding: 20px; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 10px; margin: 5px 0; }
        .main-content { margin-left: 270px; padding: 40px; width: 100%; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 30px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn { background: #1a73e8; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #333; color: white; }
        .del-btn { color: #d93025; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Panel</h3>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="add_student.php">➕ Add Student</a>
    <a href="teachers.php" style="background:#34495e; color:white;">👨‍🏫 Teachers</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="card">
        <h2>Add New Teacher</h2>
        <?php echo $message; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Teacher Name" required>
            <input type="email" name="email" placeholder="Email (Login ID)" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_teacher" class="btn">Add Teacher</button>
        </form>
    </div>

    <div class="card">
        <h2>Teacher List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM users WHERE role='teacher' ORDER BY id DESC");
            while($row = mysqli_fetch_assoc($res)){
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td><a href='teachers.php?delete={$row['id']}' class='del-btn' onclick='return confirm(\"Kya aap waqai delete karna chahte hain?\")'>Delete</a></td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</div>

</body>
</html>