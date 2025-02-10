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
    <title>Daftar Hadir</title>
    <link rel="stylesheet" href="style2.css"/>
</head>
<body>
    <h1>Halaman Daftar Hadir</h1>
    <p>Ini adalah halaman daftar hadir.</p>
    <a href="beranda.html"></a>
</body>
</html>
