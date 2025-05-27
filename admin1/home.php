<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi.php';
// Koneksi database
if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$tanggal_hari_ini = date('Y-m-d'); // format harus YYYY-mm-dd untuk filter DATE
$siswa_id = isset($_GET['siswa_id']) ? $_GET['siswa_id'] : '';
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : $tanggal_hari_ini;

$query = "SELECT s.full_name, a.status, a.alasan AS komentar, a.tanggal, TIME(a.tanggal) AS waktu_presensi
          FROM absensi_siswa a
          JOIN siswa s ON a.siswa_id = s.siswa_id
          WHERE DATE(a.tanggal) = ?";

if (!empty($siswa_id)) {
    $query .= " AND s.siswa_id = ?";
}

$query .= " ORDER BY a.tanggal DESC";

// Siapkan statement
$stmt = $conn->prepare($query);

if (!empty($siswa_id)) {
    $stmt->bind_param("ss", $tanggal_filter, $siswa_id);
} else {
    $stmt->bind_param("s", $tanggal_filter);
}

$stmt->execute();
$result = $stmt->get_result();


$presensi = [];
while ($row = $result->fetch_assoc()) {
    $waktu = $row['waktu_presensi'];

    // Konversi waktu ke sesi
    if ($waktu >= '09:00:00' && $waktu < '10:30:00') {
        $row['sesi'] = 'Sesi 1 (09.00-10.30)';
    } elseif ($waktu >= '10:30:00' && $waktu < '12:00:00') {
        $row['sesi'] = 'Sesi 2 (10.30-12.00)';
    } elseif ($waktu >= '13:00:00' && $waktu < '14:30:00') {
        $row['sesi'] = 'Sesi 3 (13.00-14.30)';
    } elseif ($waktu >= '14:30:00' && $waktu < '16:00:00') {
        $row['sesi'] = 'Sesi 4 (14.30-16.00)';
    } elseif ($waktu >= '16:00:00' && $waktu < '17:30:00') {
        $row['sesi'] = 'Sesi 5 (16.00-17.30)';
    } elseif ($waktu >= '18:00:00' && $waktu < '19:30:00') {
        $row['sesi'] = 'Sesi 6 (18.00-19.30)';
    } elseif ($waktu >= '19:30:00' && $waktu < '21:00:00') {
        $row['sesi'] = 'Sesi 7 (19.30-21.00)';
    } else {
        $row['sesi'] = 'Di luar sesi';
    }

    $presensi[] = $row;
}
// Ambil daftar semua siswa
$siswa_list = [];
$siswa_result = $conn->query("SELECT siswa_id, full_name FROM siswa ORDER BY full_name");
while ($row = $siswa_result->fetch_assoc()) {
    $siswa_list[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * {

        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #f4f4f4;
        color: #145375;
        margin: 0;
        padding: 0;
        padding-top: 100px;
        overflow-x: hidden;

    }

    .container {
        margin-top: 35px;
        padding: 20px;
        width: 90%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 1s ease-in-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        text-align: center;
        color: #145375;
        margin-bottom: 20px;
        font-size: 2.5em;
    }

    p {
        text-align: center;
        font-size: 1.2em;
        margin-bottom: 30px;
    }

    .filter-form {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        justify-content: center;
    }

    .filter-form label {
        font-weight: bold;
        color: #003049;
    }

    .filter-form select#siswa_id {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1em;
        width: 200px;
    }


    .filter-form input[type="date"] {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1em;
        width: 200px;
    }

    .filter-form button {
        padding: 10px 20px;
        background-color: #faaf1d;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 1em;
    }

    .filter-form button:hover {
        background-color: #fabe49;
        transform: scale(1.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fabe49;
        color: #003049;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    th,
    td {
        padding: 15px;
        border: 1px solid #003049;
        text-align: center;
        font-size: 1em;
    }

    th {
        background-color: #faaf1d;
        color: #003049;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    tr:nth-child(odd) {
        background-color: #ffffff;
    }

    tr:hover {
        background-color: #faaf1d;
        color: #003049;
    }

    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-form input,
        .filter-form button {
            width: 100%;
        }

        h2 {
            font-size: 2em;
        }

        th,
        td {
            font-size: 0.9em;
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.8em;
        }

        th,
        td {
            font-size: 0.8em;
            padding: 8px;
        }

        .filter-form input,
        .filter-form button {
            font-size: 0.9em;
            padding: 8px;
        }
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

    /* Notifikasi Pop-up */
    .notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f9f9f9;
        color: #145375;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        /* Default: disembunyikan */
        text-align: center;
        width: 300px;
    }

    .notification p {
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
    }

    /* Tombol di dalam notifikasi */
    .notification-buttons {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .notification-buttons .btn {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
        text-decoration: none;
        /* Hilangkan garis bawah */
    }

    /* Tombol Batal */
    .notification-buttons .btn-secondary {
        background-color: #145375;
        color: white;
    }

    .notification-buttons .btn-secondary:hover {
        background-color: #e6c200;
        color: #145375;
    }

    /* Tombol Keluar */
    .notification-buttons .btn-danger {
        background-color: #e74c3c;
        /* Warna merah */
        color: white;
        border: none;
    }

    .notification-buttons .btn-danger:hover {
        background-color: #c0392b;
        /* Warna merah lebih gelap saat hover */
        transform: scale(1.05);
        /* Efek zoom saat hover */
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
                <a href="#" onclick="toggleDropdown(event)" class="active">Presensi <span id="arrow"
                        class="arrow">&#9660;</span></a>
                <ul class="dropdown-menu">
                    <li><a href="home.php">Presensi Siswa</a></li>
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
                </ul>
            </li>

            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>

        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="container">
    

        <form method="GET" class="filter-form">
            <label for="siswa_id">Pilih Siswa:</label>
            <select name="siswa_id" id="siswa_id">
                <option value="">-- Semua Siswa --</option>
                <?php foreach ($siswa_list as $siswa): ?>
                <option value="<?= $siswa['siswa_id'] ?>" <?= ($siswa['siswa_id'] == $siswa_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($siswa['full_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>">
            <button type="submit">Tampilkan</button>
        </form>

        <h2>Daftar Presensi</h2>

    <?php if (empty($presensi)) : ?>
        <p style="text-align:center;">Belum ada siswa yang presensi hari ini.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Status</th>
                    <th>Waktu Presensi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presensi as $p) : ?>
                <tr>
                    <td><?= htmlspecialchars($p['full_name']) ?></td>
                    <td><?= date('Y-m-d', strtotime($p['tanggal'])) ?></td>
                    <td><?= $p['sesi'] ?></td>
                    <td><?= $p['status'] ?></td>
                    <td><?= date('H:i:s', strtotime($p['tanggal'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
<script>
        function confirmLogout() {
            if (confirm("Apakah kamu yakin ingin keluar?")) {
                window.location.href = "logout.php"; // ganti sesuai nama file logout-mu
            }
        }

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
    <script src="js/menu.js" defer></script>

</html>