<?php
// Output Buffering to prevent header redirect / reload loops
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
include 'db_config.php'; 

// Database se courses fetch karna safely
$db_courses = null;
if (isset($conn) && $conn) {
    $db_courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY id ASC");
}

// Course Card Render Function (Safely Defined)
if (!function_exists('renderCourseCard')) {
    function renderCourseCard($id, $title, $cat, $price, $img, $dur) {
        // Price Formatting (e.g. 12000 -> ₹12,000)
        $formatted_price = is_numeric($price) ? '₹' . number_format($price) : $price;
        if(empty($price)) { $formatted_price = "₹0"; }

        // Category Tag Selection
        $category_name = $cat;
        if (empty($cat)) {
            $lower_title = strtolower($title);
            if (strpos($lower_title, 'dca') !== false || strpos($lower_title, 'diploma') !== false) {
                $category_name = "Diploma";
            } elseif (strpos($lower_title, 'tally') !== false) {
                $category_name = "Accounting";
            } elseif (strpos($lower_title, 'web') !== false || strpos($lower_title, 'stack') !== false) {
                $category_name = "Technology";
            } else {
                $category_name = "Certification";
            }
        }

        echo '
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card course-card">
                <div class="image-container">
                    <div class="price-badge">'.$formatted_price.'</div>
                    <img src="'.htmlspecialchars($img).'" class="course-image" alt="'.htmlspecialchars($title).'">
                </div>
                <div class="card-body">
                    <span class="badge-category">'.htmlspecialchars($category_name).'</span>
                    <h5 class="course-title">'.htmlspecialchars($title).'</h5>
                    
                    <div class="course-meta">
                        <span><i class="bi bi-clock-history"></i> '.htmlspecialchars($dur).'</span>
                        <span><i class="bi bi-patch-check-fill text-primary"></i> प्रमाणित</span>
                    </div>

                    <div class="btn-group-action">
                        <a href="course_details.php?id='.$id.'" class="btn-enroll"><i class="bi bi-info-circle"></i> कोर्स विवरण</a>
                        <a href="register.php?course_id='.$id.'" class="btn-reg"><i class="bi bi-rocket-takeoff"></i> अभी आवेदन करें</a>
                    </div>
                </div>
            </div>
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Digital Courses | TC Academy</title>
    
    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@500;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #4f46e5;
            --bg-soft: #f1f5f9;
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3f1d70;
            --phonepe-bg: #f4f5f9;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        body { 
            font-family: 'Mukta', 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-soft); 
            color: #1e293b; 
            -webkit-font-smoothing: antialiased;
        }

        /* 🖥️ ---------------- DESKTOP STYLES ---------------- */
        .top-nav { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(12px);
            padding: 15px 0; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
        }

        .hero-section { 
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070');
            background-size: cover;
            background-position: center;
            padding: 80px 0; 
            color: white; 
            border-radius: 0 0 40px 40px; 
            margin-bottom: 40px;
            overflow: hidden;
        }

        .hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(4px);
            z-index: 1;
        }

        .hero-content { position: relative; z-index: 2; }

        .course-card { 
            border: none; 
            border-radius: 20px; 
            transition: 0.3s ease; 
            overflow: hidden; 
            background: #ffffff;
            height: 100%; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
        }

        .course-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 35px rgba(0,0,0,0.08); 
        }
        
        .image-container { position: relative; height: 190px; overflow: hidden; background: #e2e8f0; }
        .course-image { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .course-card:hover .course-image { transform: scale(1.08); }
        
        .price-badge { 
            position: absolute; 
            top: 15px; 
            right: 15px; 
            background: rgba(255, 255, 255, 0.96); 
            backdrop-filter: blur(5px);
            color: #15803d; 
            padding: 6px 16px; 
            border-radius: 50px; 
            font-weight: 800; 
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .card-body { padding: 22px; display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; }
        .badge-category { 
            background: #e0e7ff; 
            color: var(--accent-color); 
            font-weight: 700; 
            font-size: 12px; 
            text-transform: uppercase; 
            padding: 5px 12px; 
            border-radius: 50px; 
            margin-bottom: 10px; 
            display: inline-block; 
            width: fit-content;
        }
        
        .course-title { font-size: 19px; font-weight: 800; color: #0f172a; margin-bottom: 10px; line-height: 1.3; }

        .btn-enroll { 
            background: var(--primary-color); 
            color: white; 
            border-radius: 12px; 
            padding: 12px; 
            font-weight: 700; 
            border: none; 
            width: 100%; 
            transition: 0.3s; 
            text-decoration: none; 
            display: block; 
            text-align: center; 
            margin-bottom: 8px; 
            font-size: 15px;
        }
        .btn-enroll:hover { background: var(--accent-color); color: white; }
        
        .btn-reg { 
            background: #f8fafc; 
            color: var(--primary-color); 
            border: 1.5px solid #cbd5e1; 
            border-radius: 12px; 
            padding: 11px; 
            font-weight: 700; 
            width: 100%; 
            text-decoration: none; 
            display: block; 
            text-align: center; 
            transition: 0.3s; 
            font-size: 15px;
        }
        .btn-reg:hover { background: var(--primary-color); color: white; border-color: var(--primary-color); }

        .course-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; font-size: 14px; color: #64748b; font-weight: 700; }

        /* Hidden Mobile App Navigation Elements on Desktop */
        .mobile-header, .mobile-bottom-nav { display: none; }


        /* 📱 ---------------- PHONEPE MOBILE APP VIEW (<768px) ---------------- */
        @media (max-width: 768px) {
            body { 
                background: var(--phonepe-bg) !important; 
                padding-top: 70px; 
                padding-bottom: 85px; 
                font-family: 'Poppins', 'Mukta', sans-serif !important;
            }

            /* Hide Desktop Header & Hero Banner on Mobile */
            .top-nav, .hero-section { display: none !important; }

            /* PhonePe Style App Top Bar */
            .mobile-header {
                display: flex !important;
                position: fixed;
                top: 0; left: 0; right: 0;
                height: 68px;
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%);
                z-index: 9999;
                padding: 0 16px;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3);
            }

            .mobile-header-left { display: flex; align-items: center; gap: 14px; }
            .mobile-back-btn { 
                color: #ffffff; 
                font-size: 20px; 
                width: 40px; 
                height: 40px; 
                background: rgba(255,255,255,0.2); 
                border-radius: 50%; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                text-decoration: none; 
            }
            .mobile-header h3 { color: #ffffff !important; font-size: 20px !important; font-weight: 800; margin: 0; }
            .mobile-header small { color: rgba(255,255,255,0.85); font-size: 13px !important; font-weight: 600; display: block; }

            /* PhonePe Style Cards on Mobile with LARGE BOLD FONTS */
            .course-card {
                border-radius: 22px !important;
                margin-bottom: 12px;
                box-shadow: 0 6px 20px rgba(0,0,0,0.05) !important;
                border: 1px solid #e2e8f0 !important;
                background: #ffffff !important;
            }

            .image-container { height: 185px !important; }

            .price-badge {
                font-size: 1.3rem !important; /* Extremely Readable Price */
                padding: 8px 18px !important;
                font-weight: 800 !important;
                color: #15803d !important;
                box-shadow: 0 4px 14px rgba(0,0,0,0.18) !important;
            }

            .card-body { padding: 20px !important; }

            .badge-category {
                font-size: 13px !important; 
                padding: 6px 14px !important;
                margin-bottom: 10px !important;
                font-weight: 800 !important;
            }

            .course-title {
                font-size: 22px !important; /* Big PhonePe Header Title */
                font-weight: 800 !important;
                color: #0f172a !important;
                line-height: 1.3 !important;
                margin-bottom: 14px !important;
            }

            .course-meta {
                font-size: 15px !important; 
                margin-bottom: 18px !important;
                background: #f8fafc;
                padding: 12px 16px;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
            }

            .btn-enroll {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%) !important;
                font-size: 17px !important; /* Touch Friendly Big Text */
                padding: 14px !important;
                border-radius: 14px !important;
                font-weight: 800 !important;
                box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3) !important;
                margin-bottom: 10px !important;
            }

            .btn-reg {
                font-size: 16px !important;
                padding: 13px !important;
                border-radius: 14px !important;
                font-weight: 800 !important;
                background: #ffffff !important;
                color: var(--phonepe-purple) !important;
                border: 2px solid var(--phonepe-purple) !important;
            }

            /* PhonePe Bottom Navigation Bar */
            .mobile-bottom-nav {
                display: flex !important; 
                position: fixed; 
                bottom: 0; left: 0; right: 0;
                height: 72px; 
                background: #ffffff; 
                border-top: 1px solid #e2e8f0; 
                z-index: 9998;
                justify-content: space-around; 
                align-items: center; 
                box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            }
            
            .phonepe-nav-item { 
                display: flex; 
                flex-direction: column; 
                align-items: center; 
                justify-content: center; 
                text-decoration: none; 
                color: #64748b; 
                font-size: 12px !important; 
                font-weight: 700; 
                width: 25%; 
            }
            
            .phonepe-nav-item i { font-size: 22px; margin-bottom: 4px; }
            .phonepe-nav-item.active { color: var(--phonepe-purple) !important; font-weight: 800; }
        }
    </style>
</head>
<body>

<!-- 📱 PHONEPE MOBILE APP HEADER -->
<div class="mobile-header">
    <div class="mobile-header-left">
        <a href="index.php" class="mobile-back-btn"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h3>डिजिटल कोर्सेस</h3>
            <small>TC Academy Official</small>
        </div>
    </div>
    <div style="color:#fff; font-size:22px;">
        <i class="fa-solid fa-graduation-cap"></i>
    </div>
</div>

<!-- 🖥️ DESKTOP TOP NAV -->
<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-bold border-2"><i class="bi bi-arrow-left"></i> होम</a>
        <h4 class="m-0 fw-bold" style="color: var(--primary-color);"><i class="bi bi-lightning-charge-fill text-warning"></i> TC <span style="color: var(--accent-color);">Academy</span></h4>
    </div>
</nav>

<!-- 🖥️ DESKTOP HERO SECTION -->
<header class="hero-section text-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1 class="display-4 fw-bold mb-3">TC Academy <span style="color: #ffd600;">डिजिटल कोर्सेस</span></h1>
        <p class="opacity-100 fs-5 mx-auto mb-0" style="max-width: 750px;">DCA, PGDCA, Tally Prime से लेकर Full Stack Web Dev तक - 100% प्रैक्टिकल ट्रेनिंग।</p>
        <div class="mt-4">
            <span class="badge rounded-pill bg-light text-dark px-3 py-2 mx-1 shadow-sm"><i class="bi bi-award text-warning"></i> #1 कंप्यूटर एकेडमी</span>
            <span class="badge rounded-pill bg-light text-dark px-3 py-2 mx-1 shadow-sm"><i class="bi bi-shield-check text-success"></i> ISO 9001:2015 प्रमाणित</span>
        </div>
    </div>
</header>

<!-- MAIN CONTENT WRAPPER -->
<div class="container pb-5">
    
    <div class="row g-4">
        <?php
        // Pure Dynamic Fetching from Database
        if ($db_courses && mysqli_num_rows($db_courses) > 0) {
            while ($c = mysqli_fetch_assoc($db_courses)) {
                
                // Fallback Image Engine
                $c_img = !empty($c['image']) ? $c['image'] : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600';
                
                // Duration Fallback
                $c_duration = !empty($c['duration']) ? $c['duration'] : '6 Months';

                renderCourseCard(
                    $c['id'], 
                    $c['course_name'], 
                    "", 
                    $c['fees'], 
                    $c_img, 
                    $c_duration
                );
            }
        } else {
            // Database Khali hone par Alert
            echo '
            <div class="col-12 text-center py-5">
                <i class="bi bi-folder-x fs-1 text-muted"></i>
                <h4 class="mt-3 text-muted">कोई भी कोर्स उपलब्ध नहीं है।</h4>
                <p class="text-secondary">कृपया एडमिना पैनल से डेटाबेस में नए कोर्स जोड़ें।</p>
            </div>';
        }
        ?>
    </div>
</div>

<!-- 📱 PHONEPE BOTTOM NAVIGATION BAR (Mobile Only) -->
<div class="mobile-bottom-nav">
    <a href="index.php" class="phonepe-nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>होम</span>
    </a>
    <a href="courses.php" class="phonepe-nav-item active">
        <i class="bi bi-book-fill"></i>
        <span>कोर्स</span>
    </a>
    <a href="fees_history.php" class="phonepe-nav-item">
        <i class="bi bi-wallet2"></i>
        <span>फीस</span>
    </a>
    <a href="profile.php" class="phonepe-nav-item">
        <i class="bi bi-person-fill"></i>
        <span>प्रोफाइल</span>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Prevent Unwanted Reload Loops -->
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</body>
</html>
<?php 
ob_end_flush(); 
?>