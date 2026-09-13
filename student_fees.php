<?php include 'db_config.php'; 
$stu_id = $_SESSION['student']['id'];
?>
<h3>Your Fee Payment History</h3>
<table border="1" width="100%">
    <tr style="background:#eee"><th>Date</th><th>Amount Paid</th></tr>
    <?php
    $fees = mysqli_query($conn, "SELECT * FROM fees WHERE student_id = $stu_id");
    while($f = mysqli_fetch_assoc($fees)){
        echo "<tr><td>{$f['payment_date']}</td><td>₹ {$f['amount_paid']}</td></tr>";
    }
    ?>
</table>