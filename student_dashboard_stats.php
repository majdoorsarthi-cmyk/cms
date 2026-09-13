<?php
// Attendance Percentage Calculation
$att_res = mysqli_query($conn, "SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present 
    FROM attendance WHERE student_id = '$stu_id'");
$att_data = mysqli_fetch_assoc($att_res);
$total_days = $att_data['total'] ?: 1; // Avoid division by zero
$percentage = round(($att_data['present'] / $total_days) * 100, 1);

// Fees Calculation
$fee_res = mysqli_query($conn, "SELECT SUM(amount_paid) as paid, MAX(total_amount) as total FROM fees WHERE student_id = '$stu_id'");
$fee_data = mysqli_fetch_assoc($fee_res);
$paid = $fee_data['paid'] ?: 0;
$total_fee = $fee_data['total'] ?: 0;
$pending = $total_fee - $paid;
?>