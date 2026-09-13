<?php
// 1. Session and Database Connection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_config.php';

if(!isset($_SESSION['student'])) {
    die("Unauthorized access!");
}

// 2. Parameters Get Karein
$type = $_GET['type'] ?? '';
$student_id = $_GET['id'] ?? 0;

// 3. Security Check: Status 'Verified' hona chahiye
$check_q = mysqli_query($conn, "SELECT * FROM service_requests 
                                WHERE student_id = '$student_id' 
                                AND doc_type = '$type' 
                                AND status = 'Verified'");

if(mysqli_num_rows($check_q) > 0) {
    
    // 4. Sahi File Redirect Logic (Sudhara hua)
    if($type == 'Marksheet' || $type == 'Report Card') {
        // Marksheet ke liye sahi page
        header("Location: print_marksheet.php?id=" . $student_id);
        exit();
    } 
    elseif($type == 'Certificate') {
        // Sudhara hua: print_general_doc ki jagah print_certificate.php use karein
        header("Location: print_certificate.php?id=" . $student_id);
        exit();
    }
    elseif($type == 'ID Card') {
        // ID Card ke liye page (Check karein agar aapne print_id_card.php banaya hai)
        header("Location: print_id_card.php?id=" . $student_id);
        exit();
    }
    else {
        die("Error: Is document type ke liye koi print page set nahi hai.");
    }
} else {
    echo "<script>alert('Document Verified nahi hai ya Admin ne approval nahi di.'); window.history.back();</script>";
}
?>