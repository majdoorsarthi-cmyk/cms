<?php 
include 'db_config.php';
if(isset($_POST['q1'])){
    $score = 0;
    if($_POST['q1'] == "CPU") { $score++; }
    if($_POST['q2'] == "Random Access Memory") { $score++; }

    $student_id = $_SESSION['student']['id'];
    $exam_id = $_POST['exam_id'];

    mysqli_query($conn, "INSERT INTO results (student_id, exam_id, marks_obtained) VALUES ($student_id, $exam_id, $score)");
    echo "<h1>Exam Submitted! Your Score: $score</h1>";
    echo "<a href='student_panel.php'>Back to Dashboard</a>";
}
?>