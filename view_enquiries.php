<?php include 'db_config.php'; ?>
<h2>Student Enquiries</h2>
<table border="1" width="100%" cellpadding="10" style="border-collapse: collapse;">
    <tr style="background:#f4f4f4;"><th>Name</th><th>Phone</th><th>Course</th><th>Message</th><th>Action</th></tr>
    <?php
    $res = mysqli_query($conn, "SELECT * FROM enquiries ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($res)){
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['course_interested']}</td>
                <td>{$row['message']}</td>
                <td><a href='https://wa.me/{$row['phone']}' target='_blank' style='color:green;'>WhatsApp</a></td>
              </tr>";
    }
    ?>
</table>