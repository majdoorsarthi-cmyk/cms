<?php
// update_date.php
session_start();
include 'db_config.php'; // यह आपकी डेटाबेस फाइल को कनेक्ट करेगा

if(isset($_POST['subject_id'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
    $new_end = mysqli_real_escape_string($conn, $_POST['end_date']);

    // 1. छात्र की एक्सेस एक्सपायरी डेट बढ़ाएं
    mysqli_query($conn, "UPDATE students SET lms_access_expiry = '$new_end' WHERE id = '$student_id'");

    // 2. क्विज़ दोबारा खुलवाने के लिए पुरानी अटेंप्ट डिलीट करें
    // नोट: यहाँ हम subject_id (जो छात्र की आईडी है) का उपयोग कर रहे हैं
    mysqli_query($conn, "DELETE FROM quiz_attempts WHERE subject_id = '$student_id'"); 

    // वापस डैशबोर्ड पर भेजें
    header("Location: admin_dashboard.php?page=lms_master&msg=success");
    exit();
} else {
    echo "Invalid Request";
}
?>