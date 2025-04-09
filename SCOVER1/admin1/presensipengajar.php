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
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
        }

        .content {
            padding: 100px;
        }

        h2 {
            color: #e6c200;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #145375;
        }

        th,
        td {
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

        /* Dropdown styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #0271ab;
            min-width: 180px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            padding: 0;
            margin: 5px;
            left: -35px;
            list-style: none;
           
            /* <- tambahkan border */
        }

        .dropdown-menu li a {
            color: #fff !important;
            /* pastikan warnanya terlihat */
            padding: 12px 16px;
            text-decoration: none;
            display: block;
           
            font-weight: bold;
            /* opsional biar lebih terlihat */
        }

        .dropdown-menu li a:hover {
            background-color: #e6c200;
            color: #145375;
        }


        .arrow {
            font-size: 12px;
            margin-left: 5px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 ;
            width: 100%;
        }
    </style>
</head>

<body>
    <nav class="navbar">
       
            <div class="logo">
                <a href="home.php">
                    <img src="images/foto4.png" alt="Logo" class="logo-image">
                </a>
            </div>
            <h1 class="title">Dashboard Admin</h1>
            <ul class="nav-links">
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown(event)" class="active">Presensi <span id="arrow" class="arrow">&#9660;</span></a>
                    <ul class="dropdown-menu">
                        <li><a href="home.php">Presensi Siswa</a></li>
                        <li><a href="presensi_pengajar.php">Presensi Pengajar</a></li>
                    </ul>
                </li>

                <li><a href="pengajar.php">Pengajar</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="nilai.php">Nilai</a></li>
                <li><a href="rating.php">Rating</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="kontak.php">Kontak</a></li>
                <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
            </ul>
        
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="content">
        <h2>Daftar Pengajar Hadir</h2>
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
        <p>Data pengajar tidak tersedia karena koneksi ke database telah dihapus.</p>
    </div>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');
            const arrow = dropdown.querySelector('.arrow');

            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            arrow.innerHTML = menu.style.display === 'block' ? '&#9650;' : '&#9660;'; // ▲ dan ▼
        }

        // Tutup dropdown jika klik di luar
        window.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.dropdown');
            if (!dropdown.contains(e.target)) {
                dropdown.querySelector('.dropdown-menu').style.display = 'none';
                dropdown.querySelector('.arrow').innerHTML = '&#9660;';
            }
        });
    </script>
</body>

</html>