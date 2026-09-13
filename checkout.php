<?php
$id = $_GET['id'] ?? 'web-dev';
$courses_db = [
    "web-dev" => ["title" => "Modern Web Development", "price" => "3999", "upi_id" => "YOURNAME@okicici"], // <--- APNI REAL UPI ID YAHAN DALO
    "uiux" => ["title" => "Professional Graphic Design", "price" => "2499", "upi_id" => "YOURNAME@okicici"]
];

if(!isset($courses_db[$id])) { $id = "web-dev"; }
$c = $courses_db[$id];
$total = $c['price'];

// QR Code Logic
$upi_id = $c['upi_id'];
$payee_name = "EduPlus Education";
$qr_text = "upi://pay?pa=" . $upi_id . "&pn=" . urlencode($payee_name) . "&am=" . $total . "&cu=INR";
$qr_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($qr_text);
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | EduPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f4f7f6; }
        .checkout-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: none; }
        .order-sidebar { background: #1e293b; color: white; padding: 30px; }
        
        /* Payment Selection Styles */
        .pay-box { border: 2px solid #eee; padding: 15px; border-radius: 15px; cursor: pointer; transition: 0.3s; margin-bottom: 15px; }
        .pay-box:hover { border-color: #6366f1; }
        .pay-box.active { border-color: #6366f1; background: #f5f6ff; }
        
        /* Form Styling */
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #ddd; }
        .btn-pay { background: #6366f1; color: white; width: 100%; padding: 15px; border-radius: 12px; font-weight: 700; border: none; font-size: 1.1rem; }
        
        #payment-loader { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; }
        .spinner-border { width: 3rem; height: 3rem; color: #6366f1; }
    </style>
</head>
<body>

<div id="payment-loader">
    <div class="spinner-border mb-3"></div>
    <h5 class="fw-bold">Processing Your Payment...</h5>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="checkout-card shadow">
                <div class="row g-0">
                    <div class="col-md-7 p-4 p-md-5">
                        <h3 class="fw-bold mb-4"><i class="bi bi-shield-check text-success"></i> सुरक्षित पेमेंट</h3>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">विद्यार्थी का नाम</label>
                            <input type="text" id="cust_name" class="form-control" placeholder="Full Name">
                        </div>

                        <h6 class="fw-bold mb-3">पेमेंट का तरीका चुनें</h6>
                        
                        <div class="pay-box active" onclick="togglePayment('upi')">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-qr-code fs-3 me-3 text-primary"></i>
                                <div>
                                    <span class="d-block fw-bold">UPI (PhonePe, Google Pay, Paytm)</span>
                                    <small class="text-muted">Scan QR to pay instantly</small>
                                </div>
                                <i class="bi bi-check-circle-fill ms-auto text-primary" id="upi-check"></i>
                            </div>
                        </div>

                        <div class="pay-box" onclick="togglePayment('card')">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-credit-card fs-3 me-3 text-dark"></i>
                                <div>
                                    <span class="d-block fw-bold">Credit / Debit Card</span>
                                    <small class="text-muted">Visa, Mastercard, RuPay</small>
                                </div>
                                <i class="bi bi-circle ms-auto text-muted" id="card-check"></i>
                            </div>
                        </div>

                        <div id="payment-view" class="mt-4 p-3 border rounded-3 bg-light">
                            <div id="upi-view" class="text-center">
                                <p class="small text-muted mb-2">नीचे दिए गए QR कोड को किसी भी App से स्कैन करें</p>
                                <img src="<?php echo $qr_url; ?>" class="img-fluid border p-2 bg-white rounded shadow-sm" style="max-width: 200px;">
                                <p class="mt-2 fw-bold text-primary"><?php echo $c['upi_id']; ?></p>
                            </div>

                            <div id="card-view" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label small">कार्ड नंबर</label>
                                    <input type="text" class="form-control" placeholder="0000 0000 0000 0000">
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label small">Expiry Date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">CVV</label>
                                        <input type="password" class="form-control" placeholder="***">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn-pay mt-4" onclick="handleFinalPay()">पेमेंट पूरा करें - ₹<?php echo number_format($total); ?></button>
                    </div>

                    <div class="col-md-5 order-sidebar text-center">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        <div class="p-3 rounded bg-white bg-opacity-10 mb-4">
                            <h5 class="m-0"><?php echo $c['title']; ?></h5>
                            <hr class="bg-white">
                            <div class="d-flex justify-content-between">
                                <span>Course Fee</span>
                                <span>₹<?php echo number_format($total); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mt-2 text-warning">
                                <span>GST (Incl.)</span>
                                <span>₹0.00</span>
                            </div>
                        </div>
                        <h2 class="fw-bold text-info">Total: ₹<?php echo number_format($total); ?></h2>
                        <div class="mt-5 p-3 border border-secondary rounded text-start small">
                            <p class="mb-1"><i class="bi bi-patch-check text-success me-2"></i> Lifetime Access</p>
                            <p class="mb-1"><i class="bi bi-patch-check text-success me-2"></i> Certification Ready</p>
                            <p class="mb-0"><i class="bi bi-patch-check text-success me-2"></i> Direct Mentor Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentMethod = 'upi';

function togglePayment(method) {
    currentMethod = method;
    const upiView = document.getElementById('upi-view');
    const cardView = document.getElementById('card-view');
    const boxes = document.querySelectorAll('.pay-box');
    
    // UI Update
    boxes.forEach(b => b.classList.remove('active'));
    document.getElementById('upi-check').className = 'bi bi-circle ms-auto text-muted';
    document.getElementById('card-check').className = 'bi bi-circle ms-auto text-muted';

    if(method === 'upi') {
        upiView.style.display = 'block';
        cardView.style.display = 'none';
        boxes[0].classList.add('active');
        document.getElementById('upi-check').className = 'bi bi-check-circle-fill ms-auto text-primary';
    } else {
        upiView.style.display = 'none';
        cardView.style.display = 'block';
        boxes[1].classList.add('active');
        document.getElementById('card-check').className = 'bi bi-check-circle-fill ms-auto text-primary';
    }
}

function handleFinalPay() {
    const name = document.getElementById('cust_name').value;
    if(!name) { alert("कृपया अपना नाम दर्ज करें!"); return; }
    
    document.getElementById('payment-loader').style.display = 'flex';
    
    setTimeout(() => {
        document.getElementById('payment-loader').style.display = 'none';
        if(currentMethod === 'upi') {
            alert("महत्वपूर्ण: कृपया ऊपर दिए गए QR कोड को स्कैन करके ₹" + <?php echo $total; ?> + " का पेमेंट पूरा करें। पेमेंट के बाद आपका कोर्स 30 मिनट में एक्टिवेट हो जाएगा।");
        } else {
            alert("धन्यवाद " + name + "! आपका कार्ड विवरण सुरक्षित रूप से प्रोसेस कर लिया गया है। (यह एक डेमो है, रियल पेमेंट के लिए Razorpay API कनेक्ट करें)");
        }
    }, 2500);
}
</script>

</body>
</html>