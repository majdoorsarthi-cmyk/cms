<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "your_db_name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $course = $_POST['course_id'];
    $tid = $_POST['transaction_id'];

    $sql = "INSERT INTO enrollments (student_name, course_id, transaction_id) VALUES ('$name', '$course', '$tid')";
    
    if ($conn->query($sql)) {
        $success = "धन्यवाद! आपकी जानकारी मिल गई है। हम 30 मिनट में पेमेंट वेरीफाई करके आपका कोर्स अनलॉक कर देंगे।";
    } else {
        $error = "यह Transaction ID पहले ही उपयोग की जा चुकी है।";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fa; font-family: 'Outfit', sans-serif; }
        .confirm-card { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="confirm-card text-center">
        <h3 class="fw-bold text-primary mb-4">पेमेंट की पुष्टि करें</h3>
        
        <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST">
            <input type="hidden" name="course_id" value="<?php echo $_GET['id'] ?? 'web-dev'; ?>">
            <div class="mb-3 text-start">
                <label class="form-label fw-bold">आपका पूरा नाम</label>
                <input type="text" name="name" class="form-control" required placeholder="Jaisa Payment App mein hai">
            </div>
            <div class="mb-3 text-start">
                <label class="form-label fw-bold">Transaction ID / UTR No.</label>
                <input type="text" name="transaction_id" class="form-control" required placeholder="12 digit number">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow">जानकारी सबमिट करें</button>
        </form>
    </div>
</body>
</html>