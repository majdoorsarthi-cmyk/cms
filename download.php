<?php
// सीधे फाइल का नाम दें क्योंकि यह download.php के साथ ही रखी होगी
$file = 'TCCMS.apk';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="TCCMS.apk"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    echo "Error: APK file not found. Please place TCCMS.apk in the same folder as download.php";
}
?>