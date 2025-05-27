<?php
if (!isset($_GET['file'])) {
    echo "File tidak ditentukan.";
    exit;
}

$filename = basename($_GET['file']); // Hindari directory traversal
$filepath = '../uploads1/' . $filename; // Ganti sesuai direktori file disimpan

if (file_exists($filepath)) {
    // Set header agar browser mendownload file
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    flush(); // Bersihkan buffer
    readfile($filepath);
    exit;
} else {
    echo "File tidak ditemukan.";
}
?>
