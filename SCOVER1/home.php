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
    <title>Dashboard Siswa</title>
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
            <li><a href="profil.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
        </ul>
        <div class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="content">
    <div class="form-presensi">
        <form action="#" method="post">
        </form>
        <label for="sesi">Sesi:</label>
            <select id="sesi" name="sesi">
                <option value="Sesi 1">Sesi 1 (09.00-10.30)</option>
                <option value="Sesi 2">Sesi 2 (10.30-12.00)</option>
                <option value="Sesi 3">Sesi 3 (13.00-14.30)</option>
                <option value="Sesi 4">Sesi 4 (14.30-16.00)</option>
                <option value="Sesi 5">Sesi 5 (16.00-17.30)</option>
                <option value="Sesi 6">Sesi 6 (18.00-19.30)</option>
                <option value="Sesi 7">Sesi 7 (19.30-21.00)</option>
            </select>
            <label for="tanggal">Pilih Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" required>
            <select id="sesi" name="sesi">
                <option value="Hadir">JHadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
            </select>
            <button type="submit">Kirim</button>
            <script>
        // JavaScript untuk mengatur tanggal default ke hari ini
        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
            document.getElementById("tanggal").value = today;
        });
    </script>
    </div>
    </div>
</body>
</html>
