<?php include 'db_config.php'; ?>
<style>
    .receipt { width: 400px; border: 1px solid #ccc; padding: 20px; margin: auto; font-family: 'Courier New', Courier, monospace; }
    .center { text-align: center; }
    .line { border-bottom: 1px dashed #000; margin: 10px 0; }
</style>

<?php
if(isset($_GET['fee_id'])){
    $fid = $_GET['fee_id'];
    $sql = "SELECT fees.*, students.name, students.course FROM fees 
            JOIN students ON fees.student_id = students.id 
            WHERE fees.fee_id = $fid";
    $data = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>
    <div class="receipt">
        <h2 class="center">COMPUTER COACHING CENTER</h2>
        <p class="center">Main Road, Jabalpur (M.P.)</p>
        <div class="line"></div>
        <p><strong>Receipt No:</strong> #<?php echo $data['fee_id']; ?></p>
        <p><strong>Date:</strong> <?php echo $data['payment_date']; ?></p>
        <p><strong>Student:</strong> <?php echo $data['name']; ?> (ID: <?php echo $data['student_id']; ?>)</p>
        <p><strong>Course:</strong> <?php echo $data['course']; ?></p>
        <div class="line"></div>
        <h3 class="center">Amount Received: ₹<?php echo $data['amount_paid']; ?></h3>
        <div class="line"></div>
        <p class="center"><i>Thank you for your payment!</i></p>
        <br>
        <button onclick="window.print()" id="pb">Print Now</button>
    </div>
    <style> @media print { #pb { display:none; } } </style>
<?php } ?>