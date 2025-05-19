<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data siswa_id dari email
$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $siswa_id = $row['siswa_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

// Cek filter tanggal
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

if ($tanggal_filter) {
    $query = "SELECT a.status, a.alasan AS komentar, a.waktu_presensi,
                     DATE(a.waktu_presensi) AS tanggal, 
                     TIME(a.waktu_presensi) AS sesi
              FROM absensi_siswa a
              WHERE a.siswa_id = ? AND DATE(a.waktu_presensi) = ?
              ORDER BY a.waktu_presensi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $siswa_id, $tanggal_filter);
} else {
    $query = "SELECT a.status, a.alasan AS komentar, a.waktu_presensi,
                     DATE(a.waktu_presensi) AS tanggal, 
                     TIME(a.waktu_presensi) AS sesi
              FROM absensi_siswa a
              WHERE a.siswa_id = ?
              ORDER BY a.waktu_presensi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $siswa_id);
}

$stmt->execute();
$result = $stmt->get_result();

$presensi = [];
while ($row = $result->fetch_assoc()) {
    $presensi[] = $row;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        color: #003049;
        padding-top: 110px;
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
</style>

</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">
            <li><a href="home.php" class="active">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Riwayat Presensi - <?= htmlspecialchars($full_name) ?></h2>
        <form method="GET" class="filter-form">
            <label for="tanggal">Filter Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>">
            <button type="submit">Tampilkan</button>
        </form>

        <?php if (empty($presensi)) : ?>
        <p style="text-align: center;">Belum ada data presensi.</p>
        <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Status</th>
                    <th>Waktu Presensi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presensi as $p) : ?>
                    <?php
                     $waktu = date('H:i', strtotime($p['waktu_presensi']));
                        $sesi = 'Tidak Diketahui';
                        if ($waktu >= '09:00' && $waktu < '10:30') {
                            $sesi = 'Sesi 1 (09.00-10.30)';
                        } elseif ($waktu >= '10:30' && $waktu < '12:00') {
                            $sesi = 'Sesi 2 (10.30-12.00)';
                        } elseif ($waktu >= '13:00' && $waktu < '14:30') {
                            $sesi = 'Sesi 3 (13.00-14.30)';
                        } elseif ($waktu >= '14:30' && $waktu < '16:00') {
                            $sesi = 'Sesi 4 (14.30-16.00)';
                        } elseif ($waktu >= '16:00' && $waktu < '17:30') {
                            $sesi = 'Sesi 5 (16.00-17.30)';
                        } elseif ($waktu >= '18:00' && $waktu < '19:30') {
                            $sesi = 'Sesi 6 (18.00-19.30)';
                        } elseif ($waktu >= '19:30' && $waktu < '21:00') {
                            $sesi = 'Sesi 7 (19.30-21.00)';
                        }
                        ?>
                <tr>
                    <td><?= htmlspecialchars($p['tanggal']) ?></td>
                    <td><?= $sesi ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td><?= htmlspecialchars($p['waktu_presensi']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>

    <script src="js/menu.js" defer></script>
    <script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    function toggleKomentar() {
        const status = document.getElementById('kehadiran').value;
        const komentar = document.getElementById('komentar-container');
        komentar.style.display = (status === 'Izin' || status === 'Sakit') ? 'block' : 'none';
    }

    function confirmLogout() {
        const notification = document.getElementById('logout-notification');
        notification.style.display = 'block';
    }

    function cancelLogout() {
        const notification = document.getElementById('logout-notification');
        notification.style.display = 'none';
    }
    </script>
</body>

</html>