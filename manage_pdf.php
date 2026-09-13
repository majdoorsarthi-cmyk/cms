<?php include 'db_config.php'; ?>
<h2>Manage Homework/PDF</h2>
<table border="1" cellpadding="10">
    <tr><th>Title</th><th>File</th><th>Action</th></tr>
    <?php
    $res = mysqli_query($conn, "SELECT * FROM homework");
    while($row = mysqli_fetch_assoc($res)){
        echo "<tr>
                <td>{$row['title']}</td>
                <td><a href='uploads/{$row['file_path']}'>View</a></td>
                <td><a href='delete_pdf.php?id={$row['hw_id']}' style='color:red'>Delete</a></td>
              </tr>";
    }
    ?>
</table>