<!DOCTYPE html>
<html lang="hi">
<head>
  <meta property="og:title" content="Ravindranath Tagour University - Admit Card" />
    <meta property="og:description" content="अपना एडमिट कार्ड डाउनलोड करने के लिए यहाँ क्लिक करें और परीक्षा विवरण देखें।" />
    <meta property="og:image" content="https://cms.vacancyportal.co.in/uploads/ll.png" />
    <meta property="og:url" content="https://cms.vacancyportal.co.in/search_admit.php" />
    <meta property="og:type" content="website" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card Portal | RAVINDRANATH TEGOUR UNIVERSITY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary: #2563eb; --secondary: #0f172a; --accent: #f59e0b; }
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; color: #334155; }
        
        /* Glassmorphism Effect */
        .glass-card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); 
        }

        .hero-section { background: linear-gradient(135deg, var(--secondary), var(--primary)); color: white; padding: 60px 0; border-bottom-left-radius: 50px; border-bottom-right-radius: 50px; }
        
        .btn-search { background: var(--accent); color: white; border: none; padding: 15px; border-radius: 12px; transition: 0.3s; }
        .btn-search:hover { background: #d97706; transform: translateY(-3px); }

        .feature-icon { font-size: 24px; color: var(--primary); margin-bottom: 15px; }
        .card-hover:hover { transform: translateY(-10px); transition: 0.4s; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-graduation-cap"></i> RAVINDRANATH TEGOUR UNIVERSITY</a>
    </div>
</nav>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">परीक्षा एडमिट कार्ड पोर्टल</h1>
        <p class="lead opacity-75">अपने परीक्षा विवरण और एडमिट कार्ड के लिए अपना रोल नंबर दर्ज करें</p>
    </div>
</div>

<div class="container" style="margin-top: -50px;">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="glass-card p-5 card-hover">
                <h3 class="fw-bold mb-4"><i class="fas fa-search me-2 text-primary"></i> एडमिट कार्ड खोजें</h3>
                <form action="find_admit.php" method="GET">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">रोल नंबर या एप्लीकेशन आईडी</label>
                        <input type="text" name="roll_no" class="form-control form-control-lg border-2" placeholder="उदाहरण: APP-2026-XXXX" required>
                    </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">कोर्स का चयन करें</label>
                    <select name="course" class="form-select form-select-lg border-2" required>
                        <option value="">-- कोर्स चुनें --</option>
                        <option value="DCA">DCA</option>
                       <option value="Diploma in Computer Application">Diploma in Computer Application</option>
                        <option value="PGDCA">PGDCA</option>
                      <option value="BA">BA</option>
                    </select>
                </div>
                  
                    <button type="submit" class="btn btn-search w-100 fw-bold fs-5"><i class="fas fa-download me-2"></i> एडमिट कार्ड प्राप्त करें</button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-clipboard-list me-2"></i> महत्वपूर्ण निर्देश</h5>
                <ul class="list-unstyled">
                    <li class="mb-3 border-bottom pb-2"><i class="fas fa-arrow-right text-warning me-2"></i> केवल अधिकृत रोल नंबर ही स्वीकार्य है।</li>
                    <li class="mb-3 border-bottom pb-2"><i class="fas fa-arrow-right text-warning me-2"></i> यदि कार्ड नहीं मिल रहा, तो संस्था से संपर्क करे !</li>
                    <li class="mb-3 border-bottom pb-2"><i class="fas fa-arrow-right text-warning me-2"></i> डाउनलोड के बाद विवरण क्रॉस-चेक करें।</li>
                </ul>
                <div class="alert alert-info mt-4">
                    <i class="fas fa-headset"></i> <b>हेल्पलाइन:</b> तकनीकी समस्या हेतु कार्यालय में संपर्क करें।
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-4 card-hover">
            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
            <h5>सुरक्षित पोर्टल</h5>
        </div>
        <div class="col-md-4 card-hover">
            <div class="feature-icon"><i class="fas fa-print"></i></div>
            <h5>त्वरित प्रिंट</h5>
        </div>
        <div class="col-md-4 card-hover">
            <div class="feature-icon"><i class="fas fa-sync"></i></div>
            <h5>डिजिटल अपडेट्स</h5>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-secondary border-top">
    &copy; 2008 University Management System. All Rights Reserved.
</footer>

</body>
</html>