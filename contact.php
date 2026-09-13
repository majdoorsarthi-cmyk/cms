<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | EduPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Noto+Sans+Devanagari:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Poppins', 'Noto Sans Devanagari', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Back Button */
        .btn-back {
            background: white;
            color: #6c63ff;
            border-radius: 50px;
            padding: 8px 20px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .btn-back:hover {
            background: #6c63ff;
            color: white;
            transform: translateX(-5px);
        }

        /* Contact Card Styling */
        .contact-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .contact-info-sidebar {
            background: #6c63ff;
            color: white;
            padding: 50px;
            height: 100%;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .info-item i {
            font-size: 24px;
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 12px;
        }

        /* Form Styling */
        .form-control {
            border-radius: 12px;
            padding: 12px 20px;
            border: 1px solid #eee;
            background: #f9f9f9;
        }
        .form-control:focus {
            box-shadow: 0 0 10px rgba(108, 99, 255, 0.2);
            border-color: #6c63ff;
        }

        .btn-send {
            background: #6c63ff;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }
        .btn-send:hover {
            background: #5148d8;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(108, 99, 255, 0.3);
        }

        /* Social Icons */
        .social-links a {
            color: white;
            font-size: 20px;
            margin-right: 15px;
            transition: 0.3s;
        }
        .social-links a:hover {
            color: #ffda79;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="mb-4">
        <a href="dashboard.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> डेशबोर्ड पर वापस जाएं
        </a>
    </div>

    <div class="contact-container">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="contact-info-sidebar">
                    <h2 class="fw-bold mb-4">संपर्क जानकारी</h2>
                    <p class="mb-5 opacity-75">क्या आपके पास कोई सवाल है? हमें संदेश भेजें, हमारी टीम जल्द ही आपसे संपर्क करेगी।</p>
                    
                    <div class="info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">हमारा पता</h6>
                            <small class="opacity-75">सेक्टर 15, नोएडा, उत्तर प्रदेश, भारत</small>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">फ़ोन करें</h6>
                            <small class="opacity-75">+91 98765 43210</small>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-envelope"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">ईमेल करें</h6>
                            <small class="opacity-75">support@eduplus.com</small>
                        </div>
                    </div>

                    <div class="social-links mt-5 pt-4 border-top border-white border-opacity-25">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 bg-white p-5">
                <h3 class="fw-bold mb-4 text-dark">हमें संदेश भेजें</h3>
                
                <form action="process_contact.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label small fw-bold">आपका नाम</label>
                            <input type="text" name="name" class="form-control" placeholder="नाम लिखें..." required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label small fw-bold">ईमेल एड्रेस</label>
                            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">विषय (Subject)</label>
                        <input type="text" name="subject" class="form-control" placeholder="किस बारे में बात करनी है?">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">संदेश (Message)</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="अपना संदेश यहाँ विस्तार से लिखें..." required></textarea>
                    </div>

                    <button type="submit" class="btn-send">
                        संदेश भेजें <i class="bi bi-send ms-2"></i>
                    </button>
                </form>

                <div class="mt-5 rounded-4 overflow-hidden border">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3503.581896195537!2d77.310707!3d28.582302!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce45ed69424f1%3A0xc3f60893074d2b38!2sNoida%20Sector%2015!5e0!3m2!1sen!2sin!4v1690000000000" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>