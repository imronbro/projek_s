<?php
session_start();
include 'koneksi.php'; 
// Koneksi database

$email = $_SESSION['user_email'];

// Inisialisasi variabel filter
$kelas = isset($_POST['kelas']) ? $_POST['kelas'] : '';
$hari = isset($_POST['hari']) ? $_POST['hari'] : '';
$sesi = isset($_POST['sesi']) ? $_POST['sesi'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="css/home.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #145375;
            color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            list-style: none;
            padding: 0;
            display: flex;
        }
        .nav-links li {
            margin: 0 10px;
        }
        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
        h2 {
            color: #e6c200;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #145375;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #e6c200;
            color: #145375;
        }
        button {
            background-color: #e6c200;
            color: #145375;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #145375;
            color: #fff;
        }
    </style>
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
            <li><a href="rating.php">Rating</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
        </ul>
    </nav>
    <div class="content">
        <h2>Daftar Siswa Hadir</h2>
        <form method="POST" action="">
            <label for="kelas">Pilih Kelas:</label>
            <select name="kelas" id="kelas">
                <option value="">Semua</option>
                <option value="Aspira">Aspira</option>
                <option value="Ignite">Ignite</option>
                <option value="Neptunus">Neptunus</option>
                <option value="Free Fire">Free Fire</option>
            </select>

            <label for="hari">Pilih Hari:</label>
            <input type="date" name="hari" id="hari">

            <label for="sesi">Pilih Sesi:</label>
            <select name="sesi" id="sesi">
                <option value="">Semua</option>
                <option value="Sesi 1">Sesi 1</option>
                <option value="Sesi 2">Sesi 2</option>
                <option value="Sesi 3">Sesi 3</option>
            </select>
            
            <button type="submit">Filter</button>
        </form>
        <p>Data siswa tidak tersedia karena koneksi ke database telah dihapus.</p>
    </div>
</body>
</html>
