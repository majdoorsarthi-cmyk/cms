<?php
// delete_process.php
session_start();
include 'db_config.php';

// 1. Admin Authentication Check
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// 2. Sirf POST request accept karein (Security ke liye)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    
    // ID ko sanitize karein
    $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);
    $admin_user = $_SESSION['admin']; // Admin ka naam track karne ke liye (agar column ho)

    /* SOFT DELETE LOGIC: 
       Hum row ko DELETE nahi kar rahe, bas is_deleted ko 1 kar rahe hain 
       aur status ko 'Archived' taaki lifetime record bana rahe.
    */
    $sql = "UPDATE service_requests 
            SET is_deleted = 1, 
                status = 'Archived' 
            WHERE id = '$delete_id' LIMIT 1";

    if (mysqli_query($conn, $sql)) {
        // Success: Redirect back with success message
        $_SESSION['msg'] = "Record archived successfully!";
        header("Location: generate_docs.php?status=success");
    } else {
        // Error handling
        $_SESSION['msg'] = "Error: Could not process request.";
        header("Location: generate_docs.php?status=error");
    }
} else {
    // Direct access block karein
    header("Location: generate_docs.php");
}

mysqli_close($conn);
?>