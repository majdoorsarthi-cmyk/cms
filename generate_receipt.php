<?php
// 1. Output Buffering Start (Reload / Header Sent Error रोकने के लिए)
ob_start();

include 'db_config.php';

if(!isset($_GET['id'])) { 
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:red;'>Invalid Access</h2>
            <p>No receipt ID provided.</p>
         </div>"); 
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

$query = "SELECT f.*, u.name, s.roll_no, s.course, u.email, s.father_name 
          FROM fees f 
          JOIN students s ON f.student_id = s.id 
          JOIN users u ON s.user_id = u.id 
          WHERE f.id = '$id'";

$res = mysqli_query($conn, $query);

if(mysqli_num_rows($res) == 0) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:red;'>Record Not Found</h2>
            <p>The requested receipt does not exist in our database.</p>
         </div>");
}

$data = mysqli_fetch_assoc($res);

/**
 * Manual Number to Words Function (Indian Numbering: Lakhs, Crores)
 */
function moneyToWords($number) {
    $no = (int)floor($number);
    $point = (int)round(($number - $no) * 100);
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty',
        30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
        60 => 'Sixty', 70 => 'Seventy',
        80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_1 ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($point > 0) ? "And " . ($words[$point / 10] . " " . $words[$point % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise . " Only";
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Receipt_<?php echo htmlspecialchars($data['receipt_no']); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root { 
            --primary-color: #1a237e; 
            --text-dark: #2c3e50; 
            --border-color: #dee2e6;
            
            /* PhonePe Mobile UI Colors */
            --phonepe-purple: #5f259f;
            --phonepe-dark: #3d1668;
            --phonepe-green: #0f9d58;
            --phonepe-bg: #f4f5f9;
        }

        * { box-sizing: border-box; -webkit-print-color-adjust: exact; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #f4f7f9; color: var(--text-dark); min-height: 100vh; }

        /* 🖥️ ---------------- DESKTOP STYLES (SHANDAR A4 PRINT FORMAT) ---------------- */
        .desktop-receipt-wrapper { padding: 30px; }
        .no-print-zone { max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .btn-action { padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-print { background: var(--primary-color); color: white; }
        .btn-back { background: #e2e8f0; color: #475569; }

        .receipt-container { 
            background: white; 
            max-width: 800px; 
            margin: auto; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            position: relative;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .receipt-container::before {
            content: "OFFICIAL";
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 110px;
            color: rgba(0,0,0,0.02);
            font-weight: 900;
            z-index: 0;
            white-space: nowrap;
        }

        .header-grid { display: grid; grid-template-columns: 100px 1fr 130px; gap: 20px; align-items: center; border-bottom: 3px solid var(--primary-color); padding-bottom: 20px; }
        .logo img { width: 85px; height: auto; }
        .school-info h1 { margin: 0; font-size: 22px; color: var(--primary-color); font-weight: 800; letter-spacing: -0.5px; }
        .school-info p { margin: 5px 0 0; font-size: 11px; color: #64748b; line-height: 1.4; font-weight: 500; }
        
        .receipt-label { background: var(--primary-color); color: white; text-align: center; padding: 8px; margin: 20px 0; font-weight: 800; border-radius: 6px; letter-spacing: 2px; font-size: 14px; }

        .meta-data { display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 14px; font-weight: 600; }
        .meta-data b { color: var(--primary-color); }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; position: relative; z-index: 1; }
        .info-table td { padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .label { width: 150px; color: #64748b; font-weight: 600; }
        .value { font-weight: 700; color: #1e293b; }

        .payment-summary { background: #f8fafc; padding: 22px; border-radius: 10px; border-left: 5px solid var(--primary-color); margin: 25px 0; }
        .amount-row { display: flex; justify-content: space-between; align-items: center; }
        .big-amount { font-size: 26px; font-weight: 800; color: var(--primary-color); }
        .words { font-size: 13px; font-style: italic; color: #64748b; margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-weight: 600; }

        .footer-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 40px; margin-top: 50px; text-align: center; }
        .sig-box { border-top: 1px solid #cbd5e1; padding-top: 8px; font-size: 12px; font-weight: 700; color: #475569; }

        .qr-section { text-align: right; }
        .qr-section img { width: 90px; border: 1px solid #e2e8f0; padding: 5px; background: #fff; border-radius: 6px; }

        /* Hidden Mobile View on Desktop */
        .mobile-phonepe-receipt { display: none; }


        /* 📱 ---------------- PHONEPE MOBILE APP VIEW (BIG FONTS & APP UI) ---------------- */
        @media (max-width: 768px) {
            body { background: var(--phonepe-bg) !important; font-family: 'Poppins', sans-serif !important; padding-bottom: 30px; }
            .desktop-receipt-wrapper { display: none !important; }

            .mobile-phonepe-receipt { display: block !important; }

            /* PhonePe Purple Header Bar */
            .m-app-header {
                background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%);
                color: white; padding: 18px 16px; display: flex; align-items: center; justify-content: space-between;
                position: sticky; top: 0; z-index: 999; box-shadow: 0 4px 15px rgba(95, 37, 159, 0.25);
            }
            .m-app-header-left { display: flex; align-items: center; gap: 14px; }
            .m-back-btn { color: white; font-size: 20px; text-decoration: none; width: 40px; height: 40px; background: rgba(255,255,255,0.18); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
            .m-app-header h3 { font-size: 19px !important; font-weight: 700; margin: 0; }
            .m-app-header small { font-size: 12.5px !important; opacity: 0.85; display: block; }

            .m-content-container { padding: 16px; }

            /* Big Green Success Card */
            .m-success-card {
                background: #ffffff; border-radius: 24px; padding: 24px 20px; text-align: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 16px; border: 1px solid #edf2f7;
            }
            .m-success-icon {
                width: 68px; height: 68px; background: #e6f4ea; color: var(--phonepe-green);
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                font-size: 38px; margin: 0 auto 12px auto;
            }
            .m-success-title { font-size: 20px !important; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
            .m-success-subtitle { font-size: 14px !important; color: #64748b; font-weight: 600; }
            .m-big-amount-text { font-size: 34px !important; font-weight: 800; color: #0f172a; margin: 15px 0 5px 0; }
            .m-amount-words { font-size: 13px !important; font-weight: 600; color: #64748b; background: #f8fafc; padding: 8px 12px; border-radius: 10px; display: inline-block; margin-top: 5px; }

            /* PhonePe Section Detail Cards (Bigger Fonts) */
            .m-detail-card {
                background: #ffffff; border-radius: 20px; padding: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 16px; border: 1px solid #edf2f7;
            }
            .m-card-heading {
                font-size: 16px !important; font-weight: 800; color: var(--phonepe-purple);
                margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;
            }

            .m-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
            .m-row:last-child { margin-bottom: 0; }
            .m-row-label { font-size: 14.5px !important; font-weight: 600; color: #64748b; width: 40%; }
            .m-row-val { font-size: 15.5px !important; font-weight: 700; color: #0f172a; width: 60%; text-align: right; word-break: break-word; }

            /* Action Buttons (Big & Bold) */
            .m-btn-group { display: flex; gap: 12px; margin-top: 20px; }
            .m-btn {
                flex: 1; padding: 16px; border-radius: 16px; font-size: 16px !important; font-weight: 700;
                text-align: center; text-decoration: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            }
            .m-btn-primary { background: linear-gradient(135deg, var(--phonepe-purple) 0%, var(--phonepe-dark) 100%); color: white; box-shadow: 0 4px 15px rgba(95, 37, 159, 0.3); }
            .m-btn-secondary { background: #ffffff; color: #334155; border: 1.5px solid #cbd5e1; }
        }

        /* 🖨️ PRINT MEDIA STYLES */
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print-zone, .mobile-phonepe-receipt { display: none !important; }
            .desktop-receipt-wrapper { display: block !important; padding: 0 !important; }
            .receipt-container { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body>

<!-- 📱 PHONEPE MOBILE APP DIGITAL RECEIPT VIEW -->
<div class="mobile-phonepe-receipt">
    <div class="m-app-header">
        <div class="m-app-header-left">
            <a href="collect_fees.php" class="m-back-btn"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h3>भुगतान रसीद (Receipt)</h3>
                <small>TC ACADEMY COMPUTER CENTER</small>
            </div>
        </div>
        <div style="font-size:22px; color:#fff;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
    </div>

    <div class="m-content-container">
        <!-- Success Banner -->
        <div class="m-success-card">
            <div class="m-success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="m-success-title">शुल्क सफलतापूर्वक प्राप्त हुआ!</div>
            <div class="m-success-subtitle">Payment Received Successfully</div>
            
            <div class="m-big-amount-text">₹ <?php echo number_format($data['amount_paid'], 2); ?></div>
            <div class="m-amount-words"><i class="fa-solid fa-quote-left me-1"></i> <?php echo moneyToWords($data['amount_paid']); ?></div>
        </div>

        <!-- Payment Info Card -->
        <div class="m-detail-card">
            <div class="m-card-heading">
                <i class="fa-solid fa-receipt"></i> ट्रांजेक्शन विवरण (Payment Details)
            </div>
            <div class="m-row">
                <span class="m-row-label">रसीद संख्या</span>
                <span class="m-row-val" style="color:var(--phonepe-purple);">#<?php echo htmlspecialchars($data['receipt_no']); ?></span>
            </div>
            <div class="m-row">
                <span class="m-row-label">भुगतान तिथि</span>
                <span class="m-row-val"><?php echo date('d M Y, h:i A', strtotime($data['payment_date'])); ?></span>
            </div>
            <div class="m-row">
                <span class="m-row-label">भुगतान स्थिति</span>
                <span class="m-row-val" style="color:var(--phonepe-green);"><i class="fa-solid fa-circle-check"></i> SUCCESSFUL</span>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="m-detail-card">
            <div class="m-card-heading">
                <i class="fa-solid fa-user-graduate"></i> छात्र की जानकारी (Student Details)
            </div>
            <div class="m-row">
                <span class="m-row-label">छात्र का नाम</span>
                <span class="m-row-val"><?php echo strtoupper(htmlspecialchars($data['name'])); ?></span>
            </div>
            <div class="m-row">
                <span class="m-row-label">रोल नंबर</span>
                <span class="m-row-val"><?php echo htmlspecialchars($data['roll_no']); ?></span>
            </div>
            <div class="m-row">
                <span class="m-row-label">पिता का नाम</span>
                <span class="m-row-val"><?php echo strtoupper(htmlspecialchars($data['father_name'] ?? 'N/A')); ?></span>
            </div>
            <div class="m-row">
                <span class="m-row-label">कोर्स (Course)</span>
                <span class="m-row-val"><?php echo strtoupper(htmlspecialchars($data['course'])); ?></span>
            </div>
            <div class="m-row">
                <span class="m-row-label">ईमेल ID</span>
                <span class="m-row-val" style="font-size:13.5px !important;"><?php echo htmlspecialchars($data['email']); ?></span>
            </div>
        </div>

        <!-- QR Verification Card -->
        <div class="m-detail-card" style="text-align: center;">
            <div class="m-card-heading" style="justify-content: center;">
                <i class="fa-solid fa-qrcode"></i> QR सत्यापन (Verify Receipt)
            </div>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=<?php echo urlencode("Receipt: ".$data['receipt_no']." | Student: ".$data['name']." | Amount: ".$data['amount_paid']); ?>" alt="Verify QR" style="border:1px solid #e2e8f0; padding:6px; border-radius:12px; margin-top:5px;">
            <p style="font-size:12px; color:#64748b; margin-top:8px; font-weight:600;">डिजिटल रूप से सत्यापित आधिकारिक रसीद</p>
        </div>

        <!-- Action Buttons -->
        <div class="m-btn-group">
            <button onclick="window.print()" class="m-btn m-btn-primary">
                <i class="fa-solid fa-file-pdf"></i> रसीद डाउनलोड करें
            </button>
            <a href="collect_fees.php" class="m-btn m-btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> वापस जाएं
            </a>
        </div>
    </div>
</div>


<!-- 🖥️ DESKTOP PRINTABLE RECEIPT VIEW (FORMAL A4 FORMAT) -->
<div class="desktop-receipt-wrapper">
    <div class="no-print-zone">
        <a href="collect_fees.php" class="btn-action btn-back">← Back to Portal</a>
        <button onclick="window.print()" class="btn-action btn-print"><i class="fa-solid fa-print me-1"></i> Download as PDF / Print</button>
    </div>

    <div class="receipt-container">
        <div class="header-grid">
            <div class="logo">
                <img src="https://cdn-icons-png.flaticon.com/512/2991/2991148.png" alt="Logo">
            </div>
            <div class="school-info">
                <h1>TC ACADEMY COMPUTER CENTER</h1>
                <p>Regd. No: 12345/MP/2024 • ISO 9001:2015 Certified<br>
                   Ward No 09, Khakariya Road, Tendukheda, Dist. Damoh (M.P.) - 470880<br>
                   <strong>Contact:</strong> +91 8120751922 | <strong>Email:</strong> info@tcacademy.com</p>
            </div>
            <div class="qr-section">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode("Receipt: ".$data['receipt_no']." | Student: ".$data['name']." | Amount: ".$data['amount_paid']); ?>" alt="Verify QR">
            </div>
        </div>

        <div class="receipt-label">FEES PAYMENT RECEIPT</div>

        <div class="meta-data">
            <span>Receipt No: <b>#<?php echo htmlspecialchars($data['receipt_no']); ?></b></span>
            <span>Payment Date: <b><?php echo date('d M, Y', strtotime($data['payment_date'])); ?></b></span>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Student Name</td>
                <td class="value"><?php echo strtoupper(htmlspecialchars($data['name'])); ?></td>
                <td class="label">Registration No.</td>
                <td class="value"><?php echo htmlspecialchars($data['roll_no']); ?></td>
            </tr>
            <tr>
                <td class="label">Father's Name</td>
                <td class="value"><?php echo strtoupper(htmlspecialchars($data['father_name'] ?? 'N/A')); ?></td>
                <td class="label">Course / Trade</td>
                <td class="value"><?php echo strtoupper(htmlspecialchars($data['course'])); ?></td>
            </tr>
            <tr>
                <td class="label">Academic Email</td>
                <td class="value"><?php echo htmlspecialchars($data['email']); ?></td>
                <td class="label">Payment Status</td>
                <td class="value"><span style="color:#0f9d58; font-weight:bold;">● SUCCESSFUL</span></td>
            </tr>
        </table>

        <div class="payment-summary">
            <div class="amount-row">
                <span style="font-weight: 700; color: #64748b;">TOTAL AMOUNT RECEIVED</span>
                <span class="big-amount">₹ <?php echo number_format($data['amount_paid'], 2); ?></span>
            </div>
            <div class="words">
                <strong>Amount in Words:</strong> <?php echo moneyToWords($data['amount_paid']); ?>
            </div>
        </div>

        <div style="font-size: 11px; color: #94a3b8; margin-top: 20px; line-height: 1.4; font-weight: 500;">
            * Note: This is an electronically generated official document. Any unauthorized alteration or tampering with this receipt will be considered a legal offense. 
            Please keep this copy for your records and future clearance.
        </div>

        <div class="footer-grid">
            <div class="sig-box">Depositor's Signature</div>
            <div></div>
            <div class="sig-box">
                <div style="height: 35px;"></div>
                Authorized Signatory
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to prevent page reload loops -->
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