<?php
session_start();
include 'koneksi.php';
include 'logout_notification.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
$user_email = $_SESSION['user_email'];

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

$sql_cek = "SELECT UNIX_TIMESTAMP(waktu_presensi) as last_presensi FROM presensi_siswa WHERE siswa_id = ? ORDER BY waktu_presensi DESC LIMIT 1";
$stmt = $conn->prepare($sql_cek);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$last_presensi = 0;
if ($row = $result->fetch_assoc()) {
    $last_presensi = $row['last_presensi'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : null;
    $sesi = !empty($_POST['sesi']) ? htmlspecialchars($_POST['sesi']) : null;
    $status = !empty($_POST['kehadiran']) ? htmlspecialchars($_POST['kehadiran']) : null;
    $komentar = !empty($_POST['komentar']) ? htmlspecialchars($_POST['komentar']) : null;

    if (!$tanggal || !$sesi || !$status) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit();
    }

    $current_time = time();
    if ($last_presensi > 0 && ($current_time - $last_presensi) < 5400) {
        echo "<script>alert('Anda hanya bisa mengisi presensi sekali dalam 90 menit!'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO presensi_siswa (siswa_id, full_name, tanggal, sesi, status, komentar, waktu_presensi) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $siswa_id, $full_name, $tanggal, $sesi, $status, $komentar);

    if ($stmt->execute()) {
        echo "<script>alert('Presensi berhasil disimpan!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

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
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #145375;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        select,
        input[type="date"],
        textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            background-color: #e6c200;
            color: #145375;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn:hover {
            background-color: #145375;
            color: white;
        }

        #komentar-container {
            display: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
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
        <h2>Presensi Siswa</h2>
        <form action="" method="post">
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

            <label for="kehadiran">Status Kehadiran:</label>
            <select id="kehadiran" name="kehadiran" required onchange="toggleKomentar()">
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
            </select>

            <div id="komentar-container">
                <label for="komentar">Alasan Izin/Sakit:</label>
                <textarea id="komentar" name="komentar" rows="3" placeholder="Jelaskan alasan izin atau sakit..."></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn">Kirim</button>
                <a href="riwayat_presensi.php" class="btn">Riwayat Presensi</a>
            </div>
        </form>
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
    </script>
</body>

</html>
