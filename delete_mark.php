<?php
session_start();
include 'db_config.php';

// Check karein ki kya Admin login hai
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Check karein ki ID mili hai ya nahi
if(isset($_GET['id']) && !empty($_GET['id'])) {
    
    // ID ko sanitize karein security ke liye
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Marks delete karne ki query
    $delete_query = "DELETE FROM marks WHERE id = '$id'";

    if(mysqli_query($conn, $delete_query)) {
        // Success message ke sath redirect karein
        // Hum JavaScript ka use kar rahe hain taaki alert dikha sakein
        echo "<script>
                alert('✅ Marks Record Deleted Successfully!');
                window.location.href = 'marks_entry.php';
              </script>";
    } else {
        // Agar error aaye
        echo "<script>
                alert('❌ Error: Record delete nahi ho paya.');
                window.location.href = 'marks_entry.php';
              </script>";
    }

} else {
    // Agar bina ID ke page access kiya jaye
    header("Location: marks_entry.php");
    exit();
}

mysqli_close($conn);
?>