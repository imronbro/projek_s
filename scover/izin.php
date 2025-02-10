<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>izin</title>
    <link rel="stylesheet" href="style2.css"/>
</head>
<body>
    <h1>Halaman Daftar izin</h1>
    <p>Ini adalah halaman izin.</p>
    <a href="beranda.php">Kembali ke Beranda</a>
</body>
</html>
