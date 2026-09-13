<?php
include 'db_config.php';
session_start();
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$status = "";
if(isset($_POST['update_biz'])){
    $name = mysqli_real_escape_string($conn, $_POST['inst_name']);
    $addr = mysqli_real_escape_string($conn, $_POST['inst_address']);
    $ph = mysqli_real_escape_string($conn, $_POST['inst_phone']);
    $em = mysqli_real_escape_string($conn, $_POST['inst_email']);

    $sql = "UPDATE business_settings SET inst_name='$name', inst_address='$addr', inst_phone='$ph', inst_email='$em' WHERE id=1";
    if(mysqli_query($conn, $sql)){
        $status = "<div style='color: #27ae60; margin-bottom:15px;'>✅ Profile Updated Successfully!</div>";
    }
}

$biz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM business_settings WHERE id=1"));
?>