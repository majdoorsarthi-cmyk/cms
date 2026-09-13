<?php
session_start();
include 'db_config.php';

// Student login check
if(!isset($_SESSION['student'])) {
    header("Location: index.php");
    exit();
}
$student = $_SESSION['student'];
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Portal | University Digital Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --exam-primary: #4338ca;
            --exam-secondary: #1e293b;
            --exam-accent: #f59e0b;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #334155; }
        
        /* Header Section */
        .exam-header { background: linear-gradient(135deg, var(--exam-primary) 0%, #6366f1 100%); color: white; padding: 50px 0; border-radius: 0 0 40px 40px; }
        
        /* Glassmorphism Tabs */
        .nav-pills .nav-link { color: #64748b; font-weight: 700; border-radius: 12px; padding: 12px 25px; margin: 0 5px; transition: 0.3s; }
        .nav-pills .nav-link.active { background-color: white !important; color: var(--exam-primary) !important; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        /* Card Designs */
        .exam-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; transition: 0.3s; }
        .exam-card:hover { transform: translateY(-5px); }
        
        .status-badge { padding: 5px 15px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }

        .btn-download { background: var(--exam-primary); color: white; border-radius: 10px; padding: 10px 20px; font-weight: 600; border: none; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-download:hover { background: #312e81; color: white; transform: scale(1.05); }
        
        .table thead { background: #f1f5f9; border-radius: 10px; }
        .table th { border: none; font-size: 13px; text-transform: uppercase; color: #64748b; padding: 15px; }
        .table td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="student_dashboard.php" style="color: var(--exam-primary);">
            <i class="bi bi-shield-check me-2"></i>EXAM<span>PORTAL</span>
        </a>
        <div class="ms-auto">
            <span class="text-muted small me-2">छात्र:</span>
            <span class="fw-bold"><?= $student['name'] ?></span>
        </div>
    </div>
</nav>

<header class="exam-header text-center">
    <div class="container">
        <h1 class="fw-800 mb-2">परीक्षा नियंत्रण केंद्र</h1>
        <p class="opacity-75">अपने एडमिट कार्ड, डेट-शीट और परीक्षा परिणाम यहाँ देखें</p>
    </div>
</header>

<div class="container mt-n4">
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-5 bg-white">
        <ul class="nav nav-pills nav-justified" id="examTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#schedule"><i class="bi bi-calendar-event me-2"></i>डेट-शीट</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#admitcard"><i class="bi bi-person-badge me-2"></i>एडमिट कार्ड</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#results"><i class="bi bi-trophy me-2"></i>परिणाम</button>
            </li>
        </ul>
    </div>

    <div class="tab-content mt-4">
        <div class="tab-pane fade show active" id="schedule">
            <div class="exam-card p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-info-circle text-primary me-2"></i>आगामी परीक्षा समय-सारणी (2025)</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>तारीख</th>
                                <th>विषय (Subject)</th>
                                <th>समय</th>
                                <th>कोड</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="fw-bold">15 Jan 2025</span></td>
                                <td>Web Technologies</td>
                                <td>10:00 AM - 01:00 PM</td>
                                <td><span class="badge bg-light text-dark">WT-401</span></td>
                            </tr>
                            <tr>
                                <td><span class="fw-bold">18 Jan 2025</span></td>
                                <td>Database Management</td>
                                <td>10:00 AM - 01:00 PM</td>
                                <td><span class="badge bg-light text-dark">DB-402</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="admitcard">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="exam-card p-5 text-center">
                        <i class="bi bi-file-earmark-pdf text-danger display-3 mb-3"></i>
                        <h4 class="fw-bold">Admit Card (Semester IV)</h4>
                        <p class="text-muted small mb-4">आपकी परीक्षा की अनुमति के लिए एडमिट कार्ड तैयार है। कृपया इसे डाउनलोड करके प्रिंट कर लें।</p>
                        <div class="alert alert-warning text-start small">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> परीक्षा केंद्र पर आधार कार्ड और एडमिट कार्ड ले जाना अनिवार्य है।
                        </div>
                        <a href="generate_admit_card.php" class="btn-download w-100">
                            <i class="bi bi-download me-2"></i> डाउनलोड एडमिट कार्ड (PDF)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="results">
            <div class="exam-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">पिछली परीक्षाओं के परिणाम</h5>
                    <span class="status-badge status-active">Current CGPA: 8.5</span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>सेमेस्टर</th>
                                <th>परिणाम स्थिति</th>
                                <th>अंक / ग्रेड</th>
                                <th>एक्शन</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Semester III (Nov 2024)</td>
                                <td><span class="status-badge status-active">Pass</span></td>
                                <td>A+ (85%)</td>
                                <td><a href="#" class="text-primary fw-bold text-decoration-none small">मार्कशीट देखें</a></td>
                            </tr>
                            <tr>
                                <td>Semester II (May 2024)</td>
                                <td><span class="status-badge status-active">Pass</span></td>
                                <td>A (78%)</td>
                                <td><a href="#" class="text-primary fw-bold text-decoration-none small">मार्कशीट देखें</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5 mb-5">
        <p class="text-muted small">परीक्षा से संबंधित किसी भी समस्या के लिए <a href="contact.php" class="text-primary fw-bold">हेल्पलाइन</a> पर संपर्क करें।</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>