<?php
session_start();
include 'db_config.php';

// Check Admin Session
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$status = "";

// Save Marks Logic
if(isset($_POST['save_marks'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['paper']); 
    $obtained_marks = mysqli_real_escape_string($conn, $_POST['marks']);
    $total_marks = mysqli_real_escape_string($conn, $_POST['total_marks']);

    // Re-verify if already exists
    $check = mysqli_query($conn, "SELECT id FROM marks WHERE student_id='$student_id' AND subject_name='$subject_name'");
    if(mysqli_num_rows($check) > 0) {
        $status = "<div class='alert error-box'>❌ Error: Is subject ke marks pehle hi bhare ja chuke hain!</div>";
    } else {
        $query = "INSERT INTO marks (student_id, subject_name, total_marks, obtained_marks) 
                  VALUES ('$student_id', '$subject_name', '$total_marks', '$obtained_marks')";
        
        if(mysqli_query($conn, $query)) {
            $status = "<div class='alert success-box'>✅ Success: Marks successfully saved!</div>";
        } else {
            $status = "<div class='alert error-box'>❌ Database Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marks Entry Portal | Smart CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --brand-color: #1a237e; --accent: #ffd600; --bg: #f4f7fe; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
        /* Navbar Design */
        .navbar { background: white !important; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 800; color: var(--brand-color) !important; text-decoration: none; }
        .back-link { color: var(--brand-color); text-decoration: none; font-weight: 600; transition: 0.3s; }
        .back-link:hover { color: #000; transform: translateX(-5px); }

        /* Split Layout Design */
        .page-wrapper { display: flex; min-height: calc(100vh - 70px); }
        
        /* Left Side: Instructions */
        .side-info { 
            background: var(--brand-color); color: white; width: 30%; padding: 40px; 
            display: flex; flex-direction: column; position: sticky; top: 70px; height: calc(100vh - 70px);
        }
        .instruction-item { display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-start; }
        .instruction-item i { color: var(--accent); font-size: 1.2rem; margin-top: 3px; }

        .btn-back-side {
            background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);
            padding: 10px 20px; border-radius: 10px; text-decoration: none; display: inline-block;
            margin-bottom: 30px; transition: 0.3s; font-size: 14px;
        }
        .btn-back-side:hover { background: white; color: var(--brand-color); }

        /* Right Side: Content Area */
        .content-area { width: 70%; padding: 40px; }
        
        .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; overflow: hidden; }
        .card-header { background: #f8faff; border-bottom: 1px solid #edf2f7; padding: 20px; font-weight: 700; color: var(--brand-color); }
        
        .form-label { font-weight: 600; font-size: 13px; color: #555; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #ddd; background: #fafafa; }
        .form-control:focus { border-color: var(--brand-color); box-shadow: none; background: #fff; }

        .btn-save { background: var(--brand-color); color: white; width: 100%; padding: 14px; border-radius: 10px; font-weight: 700; border: none; transition: 0.3s; }
        .btn-save:hover { background: #0d144d; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3); }

        /* Status Messages */
        .success-box { background: #e8f6ef; color: #27ae60; border-left: 5px solid #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .error-box { background: #fff5f5; color: #c0392b; border-left: 5px solid #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 20px; }

        /* Stylish Table */
        .table-container { background: white; border-radius: 20px; padding: 25px; margin-top: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .search-wrapper { position: relative; width: 300px; }
        .search-wrapper input { border-radius: 30px; padding-left: 40px; background: #f1f3f9; border: none; }
        .search-wrapper i { position: absolute; left: 15px; top: 12px; color: #888; }

        @media (max-width: 1100px) {
            .side-info { display: none; }
            .content-area { width: 100%; padding: 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="back-link me-4" href="admin_dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
        <a class="navbar-brand" href="#">SMART<span>CMS</span></a>
        <div class="ms-auto">
            <span class="badge bg-light text-dark p-2 rounded-pill"><i class="fas fa-user-shield me-1"></i> Admin Portal</span>
        </div>
    </div>
</nav>

<div class="page-wrapper">
    <div class="side-info">
        <a href="admin_dashboard.php" class="btn-back-side">
            <i class="fas fa-chevron-left me-2"></i> Go Back Home
        </a>

        <h3 class="fw-bold mb-4">Marks Management</h3>
        <p class="opacity-75 mb-5 small">अंक प्रविष्टि (Marks Entry) करने से पहले इन निर्देशों को ध्यान से पढ़ें:</p>
        
        <div class="instruction-item">
            <i class="fas fa-user-check"></i>
            <div>
                <h6 class="mb-1 fw-bold">Select Student</h6>
                <p class="small opacity-75">सूची से छात्र का चयन करें, जिसके अंक आपको जोड़ने हैं।</p>
            </div>
        </div>
        <div class="instruction-item">
            <i class="fas fa-eye-slash"></i>
            <div>
                <h6 class="mb-1 fw-bold">Smart Filter</h6>
                <p class="small opacity-75">एक बार किसी पेपर के अंक भरने के बाद, वह पेपर सूची से अपने आप हट जाएगा।</p>
            </div>
        </div>
        <div class="instruction-item">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <h6 class="mb-1 fw-bold">Double Check</h6>
                <p class="small opacity-75">प्राप्तांक (Obtained Marks) और कुल अंक (Total) की पुष्टि अवश्य करें।</p>
            </div>
        </div>
        
        <div class="mt-auto text-center opacity-50 small">
            &copy; 2025 Smart Education System
        </div>
    </div>

    <div class="content-area">
        <div class="row">
            <div class="col-xl-5 col-lg-6">
                <div class="main-card card">
                    <div class="card-header"><i class="fas fa-edit me-2"></i> Add Student Marks</div>
                    <div class="card-body p-4">
                        <?php echo $status; ?>
                        
                        <form method="POST" id="marksForm">
                            <div class="mb-3">
                                <label class="form-label">Select Student</label>
                                <select name="student_id" id="studentSelect" class="form-select" onchange="loadAvailablePapers()" required>
                                    <option value="">-- Choose Student --</option>
                                    <?php
                                    $students = mysqli_query($conn, "SELECT s.id, u.name, s.roll_no FROM students s JOIN users u ON s.user_id = u.id ORDER BY u.name ASC");
                                    while($st = mysqli_fetch_assoc($students)) {
                                        echo "<option value='{$st['id']}'>{$st['name']} (Roll: #{$st['roll_no']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Course Category</label>
                                <select id="mainCourse" class="form-select" onchange="loadAvailablePapers()" required>
                                    <option value="">-- Select Course --</option>
                                    <option value="DCA">DCA (Diploma Course)</option>
                                    <option value="PGDCA">PGDCA (Post Graduate)</option>
                                    <option value="TALLY">Tally Prime with GST</option>
                                    <option value="SCHOOL">School Subjects</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subject / Paper</label>
                                <select id="paperSelect" name="paper" class="form-select" required>
                                    <option value="">-- Select Student & Course --</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Obtained Marks</label>
                                    <input type="number" name="marks" class="form-control" placeholder="00" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Total Marks</label>
                                    <input type="number" name="total_marks" class="form-control" value="100" required>
                                </div>
                            </div>

                            <button type="submit" name="save_marks" class="btn-save mt-3">
                                SAVE RECORD <i class="fas fa-save ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 text-dark">Recent Mark Entries</h5>
                        <div class="search-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="tableSearch" class="form-control" placeholder="Search by name...">
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 480px;">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 13px;">Student</th>
                                    <th style="font-size: 13px;">Subject</th>
                                    <th style="font-size: 13px;">Score</th>
                                    <th style="font-size: 13px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="marksTable">
                                <?php
                                $list = mysqli_query($conn, "SELECT m.*, u.name FROM marks m JOIN students s ON m.student_id = s.id JOIN users u ON s.user_id = u.id ORDER BY m.id DESC");
                                while($row = mysqli_fetch_assoc($list)) {
                                    echo "<tr>
                                        <td><div class='fw-bold' style='font-size: 14px;'>{$row['name']}</div></td>
                                        <td><span class='text-muted' style='font-size: 13px;'>{$row['subject_name']}</span></td>
                                        <td><span class='badge bg-success rounded-pill px-3'>{$row['obtained_marks']} / {$row['total_marks']}</span></td>
                                        <td>
                                            <a href='delete_mark.php?id={$row['id']}' class='btn btn-sm btn-outline-danger border-0' onclick=\"return confirm('Delete this record?');\">
                                                <i class='fas fa-trash-alt'></i>
                                            </a>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// (Previous JS Code remains same)
const courseData = {
    "DCA": ["Fundamental of Computers", "PC Package (Word, Excel, PPT)", "MS Access / FoxPro", "IT Trends", "Internet & E-Commerce"],
    "PGDCA": ["Fundamentals of IT", "PC Package", "Database Using MS Access", "Fundamentals of Multimedia", "Programming with VB.Net"],
    "TALLY": ["Basics of Accounting", "Voucher Entry", "GST & Taxation", "Payroll Management", "Tally Final Exam"],
    "SCHOOL": ["Mathematics", "Science", "English", "Hindi", "Social Science", "Sanskrit"]
};

function loadAvailablePapers() {
    const studentId = document.getElementById("studentSelect").value;
    const course = document.getElementById("mainCourse").value;
    const paperSelect = document.getElementById("paperSelect");

    if(!studentId || !course) {
        paperSelect.innerHTML = '<option value="">-- Select Student & Course --</option>';
        return;
    }

    fetch('fetch_filled_papers.php?student_id=' + studentId)
    .then(response => response.json())
    .then(filledPapers => {
        paperSelect.innerHTML = '<option value="">-- Choose Paper --</option>';
        const allPapers = courseData[course];
        
        let foundAny = false;
        allPapers.forEach(paper => {
            if(!filledPapers.includes(paper)) {
                let opt = document.createElement("option");
                opt.value = paper;
                opt.text = paper;
                paperSelect.appendChild(opt);
                foundAny = true;
            }
        });
        
        if(!foundAny) {
            paperSelect.innerHTML = '<option value="">✅ All papers completed for this course!</option>';
        }
    });
}

document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#marksTable tr');
    rows.forEach(row => {
        row.style.display = (row.innerText.toLowerCase().indexOf(value) > -1) ? '' : 'none';
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>