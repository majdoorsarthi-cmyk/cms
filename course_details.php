<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php';

if (!isset($_GET['id'])) {
    header("Location: courses.php");
    exit();
}

$course_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM courses WHERE id = '$course_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: courses.php");
    exit();
}

$course = mysqli_fetch_assoc($result);

$course_name = $course['course_name'] ?? "प्रोफेशनल डिप्लोमा कोर्स";
$check_name = strtolower(trim($course_name));

// --- 🏢 सेंटर एवं यूनिवर्सिटी मास्टर डेटा ---
$center_name = "TC ACADEMY COMPUTER CENTER";
$center_address = "तेंदूखेड़ा, जिला दमोह (म.प्र.) – 470880";
$center_email = "info@vacancyportal.co.in";
$center_phone = "8120751922";
$university_name = "रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU), भोपाल";
$university_badge = "UGC, AICTE & M.P. Govt. Approved University";

// Dynamic variables engine
$duration = "1 वर्ष (2 सेमेस्टर)";
$eligibility = "12वीं (10+2) पास";
$course_fees = $course['fees'] ?? "12,000";
$exam_mode = "ऑनलाइन मोड (CBT Online Exam)";
$header_img = "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1600";

$sem1_syllabus = [];
$sem2_syllabus = [];
$career_opportunities = [];
$course_overview = "";
$course_highlights = [];

// 1️⃣ DCA Course Specific Logic
if (strpos($check_name, 'dca') !== false && strpos($check_name, 'pg') === false) {
    $course_fees = "12,000";
    $eligibility = "10+2 हायर सेकेंडरी (किसी भी विषय में 12वीं पास)";
    $header_img = "https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=1600";
    
    $course_overview = "DCA (Diploma in Computer Applications) रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) भोपाल से संबद्ध 1 वर्षीय मान्यता प्राप्त डिप्लोमा कोर्स है। यह कोर्स उन सभी विद्यार्थियों के लिए अति आवश्यक है जो सरकारी परीक्षाओं (जैसे MP CPCT, Vyapam/ESB, SSC, रेलवे, पटवारी, पुलिस, हाई कोर्ट) और प्राइवेट सेक्टर में ऑफिस ऑपरेटर, डेटा एंट्री या बिलिंग एग्जीक्यूटिव बनना चाहते हैं। भले ही कंप्यूटर की मुख्य शब्दावली इंग्लिश में होती है, लेकिन TC Academy तेंदूखेड़ा में हमारे अनुभवी शिक्षकों द्वारा बेहद सरल हिंदी भाषा में 100% प्रैक्टिकल के साथ समझाया जाता है।";
    
    $sem1_syllabus = [
        "Fundamentals of Computers & Information Technology (कंप्यूटर मूल बातें)",
        "Operating Systems - Windows 10/11 & Basic MS-DOS Concepts",
        "PC Package - MS Word, MS Excel, MS PowerPoint (2021 Advanced)",
        "Database Management Systems & FoxPro / MS Access"
    ];
    $sem2_syllabus = [
        "IT Trends and Technologies (AI, Cloud Computing, IoT & Cyber Security)",
        "Internet & Web Page Designing (HTML5, CSS3 & Web Architecture)",
        "Financial Accounting with Tally Prime & GST Billing Basics",
        "Multimedia & Desktop Publishing (Photoshop & PageMaker)"
    ];
    $career_opportunities = [
        "मध्य प्रदेश शासन की सभी सरकारी नौकरियों (CPCT आधारित) हेतु मान्य",
        "कंप्यूटर ऑपरेटर एवं डेटा एंट्री विशेषज्ञ (Data Entry Operator)",
        "ऑफिस ऑटोमेशन एवं एडमिनिस्ट्रेटिव असिस्टेंट",
        "बैंक, स्कूल, हॉस्पिटल एवं प्राइवेट कंपनियों में फ्रंट ऑफिस एग्जीक्यूटिव",
        "स्वयं का डिजिटल सेवा केंद्र / एमपी ऑनलाइन / कंप्यूटर इंस्टिट्यूट संचालक"
    ];
} 
// 2️⃣ PGDCA Course Specific Logic
elseif (strpos($check_name, 'pgdca') !== false) {
    $course_fees = "13,000";
    $eligibility = "स्नातक पास (Graduation - BA, B.Com, B.Sc, BCA, B.Tech आदि)";
    $header_img = "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1600";
    
    $course_overview = "PGDCA (Post Graduate Diploma in Computer Applications) स्नातक उत्तीर्ण विद्यार्थियों के लिए 1 वर्षीय पोस्ट ग्रेजुएट डिप्लोमा है, जो रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) भोपाल द्वारा संबद्ध है। यह डिप्लोमा आपको कंप्यूटर साइंस, सॉफ्टवेयर डेवलपमेंट, एडवांस्ड प्रोग्रामिंग, डेटाबेस मैनेजमेंट और आईटी नेटवर्किंग में निपुण बनाता है। यह डिप्लोमा उच्च स्तरीय सरकारी नौकरियों (जैसे कंप्यूटर शिक्षक, सहायक प्रोग्रामर, सिस्टम एडमिन) और बहुराष्ट्रीय (MNC) आईटी कंपनियों में करियर के दरवाजे खोलता है।";
    
    $sem1_syllabus = [
        "Fundamentals of Information Technology & Software Engineering Concepts",
        "Operating Systems & Advanced Office Automation Suite",
        "Programming in C / C++ with Object Oriented Concepts",
        "Relational Database Management Systems (RDBMS & SQL Access)"
    ];
    $sem2_syllabus = [
        "System Analysis and Design & Software Testing Fundamentals",
        "Object Oriented Programming with Java / Python Basics",
        "Web Development Technologies (HTML, CSS, JavaScript & PHP)",
        "Tally Prime ERP with Advanced Corporate GST & Payroll Systems"
    ];
    $career_opportunities = [
        "सहायक प्रोग्रामर (Assistant Programmer) एवं जूनियर डेवलपर",
        "उच्चतर माध्यमिक विद्यालयों / कॉलेजों में कंप्यूटर शिक्षक (Computer Teacher)",
        "डेटाबेस एडमिनिस्ट्रेटर एवं सिस्टम एनालिस्ट",
        "सरकारी आईटी प्रोजेक्ट्स, ई-गवर्नेंस एवं एनआईसी सहायक",
        "आईटी ऑपरेशंस मैनेजर एवं सीनियर कंप्यूटर फैकल्टी"
    ];
} 
// 3️⃣ Other Courses Fallback Logic
else {
    $course_fees = $course['fees'] ?? "8,000";
    $duration = "3 से 6 महीने";
    $eligibility = "10वीं या 12वीं उत्तीर्ण";
    $course_overview = "TC Academy तेंदूखेड़ा का यह विशेष सर्टिफिकेशन कोर्स विद्यार्थियों को आईटी और कंप्यूटर उद्योग की आधुनिक आवश्यकताओं के अनुसार व्यावहारिक दक्षता (Skill Training) प्रदान करने के लिए डिज़ाइन किया गया है।";
    $sem1_syllabus = ["मॉड्यूल 1: कंप्यूटर फंडामेंटल्स एवं बेसिक सॉफ्टवेयर", "मॉड्यूल 2: कोर थ्योरी, शॉर्टकट्स और टाइपिंग अभ्यास"];
    $sem2_syllabus = ["मॉड्यूल 3: एडवांस्ड प्रैक्टिकल लैब असाइनमेंट", "मॉड्यूल 4: लाइव प्रोजेक्ट, ऑनलाइन परीक्षा एवं इंटरव्यू तैयारी"];
    $career_opportunities = ["संबंधित क्षेत्र में रोजगार अवसर", "फ्रीलांसिंग, पार्ट-टाइम जॉब एवं सेल्फ-एम्प्लॉयमेंट"];
}

$course_img = !empty($course['image']) ? $course['image'] : $header_img;
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course_name); ?> - TC Academy Tendukheda | RNTU Bhopal</title>
    
    <!-- Bootstrap 5, FontAwesome, Bootstrap Icons, Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- AOS (Animate On Scroll) Library CSS for Live Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-navy: #0f172a;
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --gold-accent: #d97706;
            --green-accent: #15803d;
            --bg-soft: #f8fafc;
            --border-color: #e2e8f0;
        }

        body { 
            font-family: 'Mukta', 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-soft); 
            color: #334155; 
            line-height: 1.7;
            overflow-x: hidden;
        }

        /* Top Info Announcement Bar */
        .top-info-bar {
            background: #0f172a;
            color: #cbd5e1;
            font-size: 13px;
            padding: 8px 0;
            font-weight: 600;
        }
        .top-info-bar a { color: #f59e0b; text-decoration: none; }

        /* Main Navigation */
        .main-navbar {
            background: #ffffff;
            border-bottom: 3px solid var(--phonepe-purple);
            padding: 14px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-navy);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo span { color: var(--phonepe-purple); }

        /* Live Animated Hero Banner */
        .hero-banner { 
            position: relative;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.93), rgba(63, 29, 112, 0.92)), url('<?php echo $course_img; ?>');
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed;
            padding: 80px 0 100px 0; 
            color: #ffffff; 
        }

        .uni-badge-pill {
            background: rgba(217, 119, 6, 0.25);
            border: 1.5px solid #f59e0b;
            color: #fbbf24;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { transform: scale(1.02); box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        /* Content Sections */
        .main-container { margin-top: -50px; position: relative; z-index: 30; }

        .content-card { 
            background: #ffffff; 
            border-radius: 24px; 
            padding: 35px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            transition: 0.3s ease;
        }
        .content-card:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        }

        .section-title {
            font-size: 23px;
            font-weight: 800;
            color: var(--primary-navy);
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 12px;
        }
        .section-title i { color: var(--phonepe-purple); font-size: 26px; }

        /* Quick Specs Cards */
        .spec-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            border-left: 4px solid var(--phonepe-purple);
            height: 100%;
            transition: 0.3s;
        }
        .spec-box:hover { background: #f1f5f9; transform: translateY(-3px); }
        .spec-box small { color: #64748b; font-size: 13px; font-weight: 700; display: block; text-transform: uppercase; }
        .spec-box span { color: var(--primary-navy); font-size: 18px; font-weight: 800; }

        /* Timings & Batch Feature Box */
        .timing-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #6ee7b7;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .timing-badge-live {
            background: #047857;
            color: #ffffff;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .batch-highlight-box {
            background: #ffffff;
            padding: 18px;
            border-radius: 16px;
            border: 1px solid #a7f3d0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            height: 100%;
        }

        /* Syllabus Accordion / Cards */
        .syllabus-item {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: 0.3s;
        }
        .syllabus-item:hover {
            border-color: var(--phonepe-purple);
            background: #fdf4ff;
            transform: translateX(5px);
        }
        .syllabus-item i { color: var(--phonepe-purple); font-size: 20px; }

        /* Sticky Admission Sidebar */
        .sidebar-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(95, 37, 159, 0.12);
            border: 2px solid var(--phonepe-purple);
            position: sticky;
            top: 100px;
        }

        .price-display {
            font-size: 42px;
            font-weight: 800;
            color: var(--green-accent);
            margin: 8px 0;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .price-display small { font-size: 15px; color: #64748b; font-weight: 600; }

        .installment-badge {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
            font-weight: 800;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 22px;
        }

        .btn-apply-now {
            background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%);
            color: #ffffff;
            font-size: 18px;
            font-weight: 800;
            padding: 16px;
            border-radius: 16px;
            border: none;
            width: 100%;
            display: block;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(95, 37, 159, 0.35);
            transition: 0.3s ease;
        }
        .btn-apply-now:hover { 
            color: #ffffff; 
            transform: translateY(-3px); 
            box-shadow: 0 12px 30px rgba(95, 37, 159, 0.45); 
        }

        .feature-check { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            margin-bottom: 14px; 
            font-weight: 700; 
            font-size: 15px; 
            color: #334155;
        }
        .feature-check i { color: #16a34a; font-size: 20px; }

        /* Address & Contact Footer Card */
        .contact-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 24px;
            padding: 35px;
            margin-top: 30px;
        }

        /* Floating Action Buttons */
        .float-call-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #22c55e;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
            z-index: 999;
            text-decoration: none;
            transition: 0.3s;
        }
        .float-call-btn:hover { color: white; transform: scale(1.1); }

        @media (max-width: 768px) {
            .hero-banner { padding: 50px 0 70px 0; }
            .content-card { padding: 22px; }
            .price-display { font-size: 34px; }
            .sidebar-card { padding: 24px; margin-top: 20px; }
        }
    </style>
</head>
<body>

<!-- TOP ANNOUNCEMENT BAR -->
<div class="top-info-bar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <i class="bi bi-geo-alt-fill text-warning me-1"></i> <?php echo $center_address; ?>
        </div>
        <div class="d-none d-md-block">
            <i class="bi bi-envelope-fill text-warning me-1"></i> <?php echo $center_email; ?> | 
            <i class="bi bi-telephone-fill text-warning ms-2 me-1"></i> Call: <?php echo $center_phone; ?>
        </div>
    </div>
</div>

<!-- MAIN NAVBAR -->
<nav class="main-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="courses.php" class="brand-logo">
            <i class="fa-solid fa-graduation-cap text-warning fs-2"></i>
            <div>
                TC <span>ACADEMY</span>
                <small class="d-block text-muted style-sub" style="font-size: 11px; font-weight: 700; line-height: 1;">Computer Center Tendukheda</small>
            </div>
        </a>
        <a href="courses.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-bold border-2">
            <i class="bi bi-arrow-left"></i> सभी कोर्सेस देखें
        </a>
    </div>
</nav>

<!-- HERO BANNER SECTION -->
<header class="hero-banner text-center text-md-start">
    <div class="container">
        <div class="col-lg-9" data-aos="fade-up" data-aos-duration="1000">
            <span class="uni-badge-pill">
                <i class="fa-solid fa-building-columns me-2"></i> <?php echo $university_name; ?> से संबद्ध
            </span>
            <h1 class="display-4 fw-bold text-white mb-3"><?php echo htmlspecialchars($course_name); ?></h1>
            <p class="fs-5 opacity-90 mb-4">
                100% प्रैक्टिकल आधारित शिक्षण, डिजिटल कंप्यूटर लैब, अनुभवी शिक्षकों द्वारा मार्गदर्शन एवं शासकीय नौकरियों हेतु 100% मान्य डिप्लोमा।
            </p>
            <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold"><i class="bi bi-shield-check text-success me-1"></i> UGC & M.P. Govt Valid</span>
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold"><i class="bi bi-clock-history text-primary me-1"></i> Fleixble Batch Timings</span>
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold"><i class="bi bi-laptop text-danger me-1"></i> Online CBT Examination</span>
            </div>
        </div>
    </div>
</header>

<!-- MAIN CONTENT WRAPPER -->
<div class="container main-container pb-5">
    <div class="row g-4">
        
        <!-- LEFT COLUMN: DETAILED COURSE INFO -->
        <div class="col-lg-8">
            
            <!-- Quick Specifications Grid -->
            <div class="content-card" data-aos="fade-up" data-aos-duration="1000">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="spec-box">
                            <small>कुल अवधि</small>
                            <span><?php echo $duration; ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spec-box">
                            <small>संबद्ध यूनिवर्सिटी</small>
                            <span>RNTU भोपाल</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spec-box">
                            <small>अनिवार्य योग्यता</small>
                            <span><?php echo $eligibility; ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spec-box">
                            <small>परीक्षा मोड</small>
                            <span>ऑनलाइन CBT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Affiliation Highlight Card -->
            <div class="content-card style-uni-box" data-aos="fade-up" data-aos-duration="1000" style="border-left: 6px solid #d97706; background: #fffbeb;">
                <div class="d-flex align-items-start gap-3">
                    <i class="fa-solid fa-award text-warning display-5 mt-1"></i>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) भोपाल मान्यता</h4>
                        <p class="text-secondary mb-0 fs-6">
                            TC Academy तेंदूखेड़ा में संचालित DCA और PGDCA कोर्सेस **रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) भोपाल** से पूर्णतः संबद्ध हैं। RNTU एक UGC, AICTE एवं मध्य प्रदेश शासन द्वारा अधिकृत यूनिवर्सिटी है। इस डिप्लोमा का उपयोग आप **MP CPCT, Vyapam/ESB, SSC, रेलवे, बैंकिंग, पटवारी, पुलिस एवं समस्त राज्य व केंद्र सरकार की भर्तियों** में 100% निसंकोच कर सकते हैं।
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detailed Overview Section -->
            <div class="content-card" data-aos="fade-up" data-aos-duration="1000">
                <h3 class="section-title"><i class="bi bi-file-text-fill"></i> कोर्स का संपूर्ण विवरण (Course Overview)</h3>
                <p class="fs-6 text-dark lh-base mb-4">
                    <?php echo $course_overview; ?>
                </p>
                
                <div class="p-3 rounded-3 border bg-light" style="border-left: 4px solid var(--phonepe-purple) !important;">
                    <h5 class="fw-bold text-primary mb-2"><i class="bi bi-translate me-2"></i> सरल भाषा में शिक्षण (Easy Language Teaching):</h5>
                    <p class="mb-0 text-secondary fs-6">
                        अधिकतर ग्रामीण व शहरी छात्र कंप्यूटर की कठिन इंग्लिश शब्दावली से डरते हैं। हमारे सेंटर पर अनुभवी फैकल्टी पूरे सिलेबस को **बहुत ही आसान हिंदी भाषा में** स्टेप-बाय-स्टेप समझाती है। हर छात्र के लिए अलग कंप्यूटर सिस्टम उपलब्ध रहता है ताकि जो भी थ्योरी में पढ़ाया जाए, उसका तुरंत **100% प्रैक्टिकल** हो सके।
                    </p>
                </div>
            </div>

            <!-- Interactive Batch Timings Card -->
            <div class="timing-card" data-aos="fade-up" data-aos-duration="1000">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h4 class="fw-bold text-success m-0"><i class="bi bi-clock-history me-2"></i> बैच टाइमिंग एवं फ्लेक्सिबिलिटी</h4>
                    <span class="timing-badge-live"><i class="bi bi-circle-fill text-danger fs-6"></i> सुबह 06:00 AM से शाम 06:00 PM तक खुला</span>
                </div>
                <p class="mb-4 text-dark font-medium">
                    विद्यार्थियों, कॉलेज स्टूडेंट्स एवं नौकरीपेशा लोगों की सुविधा के लिए हमारे सेंटर पर दिनभर नियमित बैचेस लगते हैं। **विद्यार्थी अपने सुविधानुसार कोई भी बैच चुन सकता है:**
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="batch-highlight-box border-danger">
                            <span class="badge bg-danger mb-2 px-3 py-1">पहला विशेष बैच (First Batch)</span>
                            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-alarm-fill text-danger me-2"></i> सुबह 06:00 AM से 08:00 AM</h5>
                            <p class="small text-muted mt-2 mb-0">यह पहला बैच **पूरे 2 घंटे** का होता है, जो सुबह जल्दी पढ़ाई करने वाले व जॉब/कॉलेज जाने वाले छात्रों के लिए विशेष उपयुक्त है।</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="batch-highlight-box border-primary">
                            <span class="badge bg-primary mb-2 px-3 py-1">दिनभर नियमित बैचेस</span>
                            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-calendar-check-fill text-primary me-2"></i> सुबह 08:00 AM से शाम 06:00 PM</h5>
                            <p class="small text-muted mt-2 mb-0">सुबह 8 बजे से शाम 6 बजे तक लगातार बैचेस उपलब्ध हैं। छात्र अपनी सुविधा अनुसार अपना समय कभी भी बदल सकते हैं।</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Semester Syllabus -->
            <div class="content-card" data-aos="fade-up" data-aos-duration="1000">
                <h3 class="section-title"><i class="bi bi-journal-code"></i> मुख्य पाठ्यक्रम एवं विषय (Semester Syllabus)</h3>
                
                <h5 class="fw-bold text-primary mt-4 mb-3"><i class="bi bi-1-circle-fill me-2 text-warning"></i> प्रथम सेमेस्टर (Semester 1 Syllabus)</h5>
                <?php foreach($sem1_syllabus as $item) { ?>
                    <div class="syllabus-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo htmlspecialchars($item); ?></span>
                    </div>
                <?php } ?>

                <h5 class="fw-bold text-primary mt-5 mb-3"><i class="bi bi-2-circle-fill me-2 text-warning"></i> द्वितीय सेमेस्टर (Semester 2 Syllabus)</h5>
                <?php foreach($sem2_syllabus as $item) { ?>
                    <div class="syllabus-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo htmlspecialchars($item); ?></span>
                    </div>
                <?php } ?>
            </div>

            <!-- Online Examination & CBT System -->
            <div class="content-card" data-aos="fade-up" data-aos-duration="1000">
                <h3 class="section-title"><i class="bi bi-laptop-fill"></i> ऑनलाइन परीक्षा प्रणाली (Online CBT Exam)</h3>
                <p class="fs-6">
                    रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) के नियमानुसार सत्र समाप्त होने पर परीक्षा **ऑनलाइन मोड (Computer Based Test)** में होती है:
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border text-center h-100">
                            <i class="bi bi-ui-checks-grid text-primary fs-2 mb-2 d-block"></i>
                            <h6 class="fw-bold">ऑब्जेक्टिव प्रश्न (MCQs)</h6>
                            <small class="text-muted">परीक्षा में बहुविकल्पीय प्रश्न पूछे जाते हैं।</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border text-center h-100">
                            <i class="bi bi-display text-success fs-2 mb-2 d-block"></i>
                            <h6 class="fw-bold">मॉक टेस्ट प्रैक्टिस</h6>
                            <small class="text-muted">सेंटर पर कंप्यूटर पर परीक्षा की तैयारी कराई जाती है।</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border text-center h-100">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2 mb-2 d-block"></i>
                            <h6 class="fw-bold">ओरिजिनल मार्कशीट</h6>
                            <small class="text-muted">यूनिवर्सिटी द्वारा अधिकृत मूल मार्कशीट व डिप्लोमा।</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Career & Job Opportunities Matrix -->
            <div class="content-card" data-aos="fade-up" data-aos-duration="1000">
                <h3 class="section-title"><i class="bi bi-briefcase-fill"></i> करियर एवं जॉब के अवसर (Career Scope)</h3>
                <div class="row g-3 mt-1">
                    <?php foreach($career_opportunities as $job) { ?>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                                <i class="bi bi-patch-check-fill text-success fs-4"></i>
                                <span class="fw-bold text-dark" style="font-size: 15px;"><?php echo htmlspecialchars($job); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Frequently Asked Questions (FAQs) -->
            <div class="content-card" data-aos="fade-up" data-aos-duration="1000">
                <h3 class="section-title"><i class="bi bi-question-circle-fill"></i> अक्सर पूछे जाने वाले प्रश्न (FAQs)</h3>
                
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                क्या RNTU यूनिवर्सिटी का DCA/PGDCA सरकारी नौकरी में मान्य है?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                जी हाँ, बिल्कुल! रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) भोपाल UGC और मध्य प्रदेश सरकार से पूर्ण मान्यता प्राप्त है। इस डिप्लोमा की मार्कशीट MP CPCT, Vyapam/ESB, रेलवे, पटवारी, पुलिस एवं समस्त सरकारी व प्राइवेट नौकरियों में 100% मान्य है।
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                क्या फीस आसान किश्तों (Installments) में जमा कर सकते हैं?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                जी हाँ, छात्रों की सुविधा हेतु फीस को आसान मासिक किश्तों में जमा करने की पूरी व्यवस्था है। एडमिशन के समय एक छोटी सी अग्रिम राशि देकर पढ़ाई शुरू की जा सकती है।
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                क्या मुझे इंग्लिश कमजोर होने पर कंप्यूटर समझने में दिक्कत आएगी?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                बिल्कुल नहीं! TC Academy तेंदूखेड़ा में हमारे शिक्षक बहुत ही सरल हिंदी भाषा में 100% प्रैक्टिकल के साथ सिखाते हैं, जिससे हिंदी मीडियम के छात्र भी आसानी से समझ जाते हैं।
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Official Contact Details Card -->
            <div class="contact-box" data-aos="fade-up" data-aos-duration="1000">
                <h4 class="fw-bold mb-3 text-warning"><i class="bi bi-building me-2"></i> <?php echo $center_name; ?></h4>
                <p class="mb-2"><i class="bi bi-geo-alt-fill text-warning me-2"></i> <strong>पता:</strong> <?php echo $center_address; ?></p>
                <p class="mb-2"><i class="bi bi-envelope-fill text-warning me-2"></i> <strong>ईमेल:</strong> <?php echo $center_email; ?></p>
                <p class="mb-0"><i class="bi bi-telephone-fill text-warning me-2"></i> <strong>मोबाइल / व्हाट्सएप:</strong> <?php echo $center_phone; ?></p>
            </div>

        </div>


        <!-- RIGHT COLUMN: STICKY ADMISSION SIDEBAR -->
        <div class="col-lg-4">
            <div class="sidebar-card" data-aos="fade-left" data-aos-duration="1000">
                
                <span class="badge bg-primary text-white mb-2 px-3 py-1 rounded-pill fw-bold">एडमिशन फॉर्म चालू है 2024-25</span>
                
                <h3 class="fw-bold text-dark m-0"><?php echo htmlspecialchars($course_name); ?></h3>
                <small class="text-muted d-block mb-3 fw-bold">RNTU भोपाल यूनिवर्सिटी एफिलिएटेड</small>

                <div class="price-display">
                    ₹<?php echo number_format((float)str_replace(',', '', $course_fees)); ?>
                    <small>/ कुल फ़ीस (1 वर्ष)</small>
                </div>

                <div class="installment-badge">
                    <i class="bi bi-credit-card-2-front-fill me-1"></i> आसान किश्तों (Installments) की सुविधा
                </div>

                <hr class="my-3">

                <div class="mb-4">
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> RNTU यूनिवर्सिटी की मूल मार्कशीट</div>
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> थ्योरी के साथ 100% प्रैक्टिकल लैब</div>
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> हिंदी-इंग्लिश में आसान क्लास</div>
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> सुबह 6 से शाम 6 मनपसंद समय</div>
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> पहला बैच: सुबह 6 से 8 (2 घंटे)</div>
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> ऑनलाइन CBT परीक्षा की पूरी तैयारी</div>
                    <div class="feature-check"><i class="bi bi-check-circle-fill"></i> फ्री स्टडी मटेरियल व नोट्स</div>
                </div>

                <a href="register.php?course_id=<?php echo $course_id; ?>" class="btn-apply-now mb-3">
                    <i class="bi bi-rocket-takeoff-fill me-2"></i> अभी ऑनलाइन एडमिशन फॉर्म भरें
                </a>

                <div class="text-center pt-2 border-top">
                    <p class="small text-muted mb-1">सीधे संपर्क या जानकारी हेतु कॉल करें:</p>
                    <a href="tel:<?php echo $center_phone; ?>" class="fw-bold text-decoration-none fs-4" style="color: var(--phonepe-purple);">
                        <i class="bi bi-telephone-fill me-1"></i> <?php echo $center_phone; ?>
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- FLOATING WHATSAPP / CALL ACTION BUTTON -->
<a href="tel:<?php echo $center_phone; ?>" class="float-call-btn" title="Call Us Now">
    <i class="bi bi-telephone-fill"></i>
</a>

<!-- FOOTER -->
<footer class="bg-dark text-white py-4 mt-5 border-top border-secondary">
    <div class="container text-center">
        <p class="mb-1 opacity-75">&copy; 2024 <?php echo $center_name; ?> - Tendukheda, Damoh (M.P.)</p>
        <small class="text-muted">संबद्ध: रविंद्रनाथ टैगोर यूनिवर्सिटी (RNTU) भोपाल | सर्वाधिकार सुरक्षित।</small>
    </div>
</footer>

<!-- JS LIBRARIES -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- INITIALIZE ANIMATIONS ON SCROLL -->
<script>
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-in-out'
    });
</script>
</body>
</html>
<?php 
ob_end_flush(); 
?>