<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | EduPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Noto+Sans+Devanagari:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Poppins', 'Noto Sans Devanagari', sans-serif;
            background-color: #ffffff;
            color: #2d3436;
        }

        /* Hero Section */
        .about-hero {
            background: linear-gradient(rgba(108, 99, 255, 0.9), rgba(108, 99, 255, 0.8)), 
                        url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        /* Back Button */
        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 8px 20px;
            border-radius: 50px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-back:hover {
            background: white;
            color: #6c63ff;
        }

        /* Info Section */
        .info-card {
            border: none;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: #fff;
            transition: 0.3s;
        }
        .info-card:hover {
            transform: translateY(-5px);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: #6c63ff;
            color: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Team Section */
        .team-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #f8f9fa;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #6c63ff;
        }
    </style>
</head>
<body>

<section class="about-hero position-relative">
    <a href="dashboard.php" class="btn-back"><i class="bi bi-arrow-left"></i> डेशबोर्ड</a>
    <div class="container">
        <h1 class="fw-bold display-4">हमारे बारे में जानें</h1>
        <p class="lead">हम शिक्षा को हर किसी के लिए आसान और सुलभ बना रहे हैं।</p>
    </div>
</section>

<div class="container my-5 py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4">
            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow" alt="Story">
        </div>
        <div class="col-lg-6 ps-lg-5">
            <h6 class="text-uppercase fw-bold text-primary mb-3">हमारी कहानी</h6>
            <h2 class="fw-bold mb-4">शिक्षा के क्षेत्र में एक नई क्रांति</h2>
            <p class="text-muted">EduPlus की शुरुआत एक छोटे से विजन के साथ हुई थी - भारत के हर छात्र को बेहतरीन और किफायती शिक्षा प्रदान करना। हम केवल सिखाते नहीं हैं, हम भविष्य का निर्माण करते हैं।</p>
            <div class="row g-4 mt-2">
                <div class="col-6">
                    <h5 class="fw-bold mb-0">10k+</h5>
                    <small class="text-muted">संतुष्ट छात्र</small>
                </div>
                <div class="col-6">
                    <h5 class="fw-bold mb-0">50+</h5>
                    <small class="text-muted">विशेषज्ञ शिक्षक</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="info-card h-100">
                    <div class="icon-box mx-auto"><i class="bi bi-eye"></i></div>
                    <h4 class="fw-bold">हमारा लक्ष्य (Vision)</h4>
                    <p class="text-muted">दुनिया भर में डिजिटल शिक्षा का विस्तार करना और तकनीकी कौशल को बढ़ावा देना।</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card h-100">
                    <div class="icon-box mx-auto"><i class="bi bi-rocket-takeoff"></i></div>
                    <h4 class="fw-bold">हमारा मिशन</h4>
                    <p class="text-muted">किफायती दामों में इंडस्ट्री लेवल के कोर्सेस उपलब्ध कराना ताकि हर युवा आत्मनिर्भर बने।</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card h-100">
                    <div class="icon-box mx-auto"><i class="bi bi-shield-check"></i></div>
                    <h4 class="fw-bold">हमारा भरोसा</h4>
                    <p class="text-muted">क्वालिटी कंटेंट और 24/7 सपोर्ट के साथ हम अपने छात्रों की सफलता सुनिश्चित करते हैं।</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">हमारी <span class="text-primary">टीम</span></h2>
        <p class="text-muted">मिलिए उन विशेषज्ञों से जो आपकी सफलता के पीछे हैं।</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="p-4 border rounded-4 bg-white shadow-sm">
                <img src="https://i.pravatar.cc/150?u=1" class="team-img" alt="Founder">
                <h5 class="fw-bold mb-1">अमित कुमार</h5>
                <p class="text-primary small mb-3">Founder & CEO</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border rounded-4 bg-white shadow-sm">
                <img src="https://i.pravatar.cc/150?u=2" class="team-img" alt="Instructor">
                <h5 class="fw-bold mb-1">नेहा सिंह</h5>
                <p class="text-primary small mb-3">Lead Instructor</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 border rounded-4 bg-white shadow-sm">
                <img src="https://i.pravatar.cc/150?u=3" class="team-img" alt="Tech Head">
                <h5 class="fw-bold mb-1">राहुल शर्मा</h5>
                <p class="text-primary small mb-3">CTO</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-github"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 bg-dark text-white">
    <p class="mb-0">&copy; 2025 EduPlus | Education for Everyone</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>