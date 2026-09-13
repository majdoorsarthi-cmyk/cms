<?php include 'db_config.php'; ?>
<div style="max-width:500px; margin:auto; padding:20px; border:1px solid #ddd; border-radius:10px;">
    <h2>New Admission Enquiry</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Your Name" required style="width:100%; padding:10px; margin:5px 0;"><br>
        <input type="text" name="phone" placeholder="Phone Number" required style="width:100%; padding:10px; margin:5px 0;"><br>
        <input type="text" name="course" placeholder="Course Interested In" style="width:100%; padding:10px; margin:5px 0;"><br>
        <textarea name="msg" placeholder="Your Message" style="width:100%; padding:10px; margin:5px 0;"></textarea><br>
        <button type="submit" name="send" style="background:#28a745; color:white; padding:10px 20px; border:none; cursor:pointer; width:100%;">Submit Inquiry</button>
    </form>
</div>

<?php
if(isset($_POST['send'])){
    $name = $_POST['name']; $phone = $_POST['phone'];
    $course = $_POST['course']; $msg = $_POST['msg'];
    $q = "INSERT INTO enquiries (name, phone, course_interested, message) VALUES ('$name', '$phone', '$course', '$msg')";
    if(mysqli_query($conn, $q)){ echo "<script>alert('Thank you! We will contact you soon.');</script>"; }
}
?>