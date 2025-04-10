<?php
session_start();
include 'koneksi.php';
// Koneksi database

// Ambil filter dan search dari GET request
$tanggal = $_GET['tanggal'] ?? '';
$kelas = $_GET['kelas'] ?? '';
$search = $_GET['search'] ?? '';

// Query dasar
$query = "SELECT full_name, tanggal, sesi, status, komentar, waktu_presensi FROM presensi_siswa WHERE 1";

// Filter tanggal
if (!empty($tanggal)) {
    $query .= " AND tanggal = '$tanggal'";
}

// Filter search nama
if (!empty($search)) {
    $query .= " AND full_name LIKE '%$search%'";
}

$query .= " ORDER BY tanggal DESC, waktu_presensi DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;

        }

        .content {
            padding: 100px;
        }

        h2 {
            text-align: center;
            margin: 30px 0;
            color: #333;
        }

        .content {
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .filter-bar form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        input[type="text"],
        input[type="date"],
        select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            min-width: 150px;
        }


        /* Tabel */
        table {
            width: 100%;
            max-width: 1000px;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 60px 20px rgba(2, 58, 104, 0.05);
            border-radius: 15px;
            overflow: hidden;
        }

        th,
        td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background-color: #145375;
            color: white;
            font-size: 13px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        td {
            font-size: 14px;
            color: #444;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        @media (max-width: 768px) {

            table,
            th,
            td {
                font-size: 12px;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }

        button {
            background-color: #e6c200;
            color: #145375;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 6px;
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
    </style>
</head>

<div>
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
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
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

    <div class="container">

        <h2>Presensi Siswa</h2>

        <div class="filter-bar">
            <form method="GET" action="">
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                <input type="text" name="search" placeholder="Cari Nama..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Filter</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Status</th>
                    <th>Komentar</th>
                    <th>Waktu Presensi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= $row['tanggal'] ?></td>
                            <td><?= $row['sesi'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td><?= $row['komentar'] ?? '-' ?></td>
                            <td><?= $row['waktu_presensi'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Tidak ada data presensi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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