<?php
session_start();
include 'koneksi.php'; // Koneksi database

$email = $_SESSION['user_email'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mentor</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-circle">LOGO</div>
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">
            <li><a href="presensi.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
        </ul>
        <div class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="content">
        <!-- Konten Utama -->
    </div>
</body>
</html>
