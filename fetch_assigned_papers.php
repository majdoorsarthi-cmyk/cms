<?php
/**
 * AJAX Helper: Fetch already entered marks for a student
 * Purpose: To hide already filled subjects from the entry dropdown.
 */

// Database connection include karein
include 'db_config.php';

// JSON response ke liye header set karein
header('Content-Type: application/json');

// Result array initialize karein
$filled = [];

// Check karein ki student_id mili hai ya nahi
if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
    
    // Sanitize input to prevent SQL Injection
    $student_id = mysqli_real_escape_string($conn, $_GET['student_id']);

    // Query: Sirf wahi subject names nikalein jo marks table mein is student ke liye hain
    $query = "SELECT subject_name FROM marks WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Har subject name ko array mein store karein
            $filled[] = $row['subject_name'];
        }
    } else {
        // Agar query fail ho jaye (Optionally error log kar sakte hain)
        // echo json_encode(["error" => mysqli_error($conn)]); exit;
    }
}

// Data ko JSON format mein return karein
// Agar koi marks nahi mile, toh ye khali array [] return karega
echo json_encode($filled);

// Connection close karein (Optional but recommended)
mysqli_close($conn);
?>