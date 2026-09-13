<?php
session_name("STUDENT_SESSION");
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <title>Online Exam | AISECT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-5 rounded shadow">
        <h3>Information Technology Tools - Online Exam</h3>
        <hr>
        <form id="quizForm">
            <?php 
            // यहाँ PHP लूप का उपयोग करके 50 प्रश्न दिखाएं
            for($i=1; $i<=50; $i++) {
                echo "<div class='mb-4'>
                        <p><strong>प्रश्न $i:</strong> आईटी टूल्स और नेटवर्क बेसिक्स का यह प्रश्न नंबर $i है?</p>
                        <input type='radio' name='q$i' value='a'> विकल्प A <br>
                        <input type='radio' name='q$i' value='b'> विकल्प B
                      </div>";
            }
            ?>
            <button type="button" onclick="submitExam()" class="btn btn-success">Submit Exam</button>
        </form>

        <div id="resultArea" style="display:none;" class="alert alert-info mt-4">
            <h4>परीक्षा सबमिट हो गई!</h4>
            <p>आपका रेफरेंस नंबर: <strong id="refNumber"></strong></p>
        </div>
    </div>

    <script>
    function submitExam() {
        // रेफरेंस नंबर जनरेशन लॉजिक
        var rollNo = "<?php echo $_SESSION['user']; ?>";
        var randomCode = Math.floor(100000 + Math.random() * 900000);
        var ref = "AGU-" + rollNo + "-" + randomCode;
        
        document.getElementById('quizForm').style.display = 'none';
        document.getElementById('resultArea').style.display = 'block';
        document.getElementById('refNumber').innerText = ref;
    }
    </script>
</body>
</html>