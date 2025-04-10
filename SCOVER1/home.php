<?php
session_start();
include 'koneksi.php';

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

    <style>
        * {
            
            box-sizing: border-box;
        }

        body {
            background-color: #003049;
            color: #fabe49;
        }

        .card {
            background-color: #145375;
            color: white;
            border: 2px solid white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #faaf1d;
            display: block;
            margin: 0 auto;
        }

        .btn-whatsapp {
            background-color: #faaf1d;
            color: #003049;
            border: none;
        }

        .btn-whatsapp:hover {
            background-color: #fabe49;
        }
    </style>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/navbar.css">

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
        <h2 class="form-title">Presensi Siswa</h2>
        <div class="form-presensi">
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

                <div id="komentar-container" style="display: none;">
                    <label for="komentar">Alasan Izin/Sakit:</label>
                    <textarea id="komentar" name="komentar" rows="3" placeholder="Jelaskan alasan izin atau sakit..."></textarea>
                </div>

                <div style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
                    <button type="submit" class="btn">Kirim</button>
                    <a href="riwayat_presensi.php" class="btn">Riwayat Presensi</a>
                </div>
            </form>
        </div>
    </div>
    <script src="js/home.js" defer></script>
</body>
</html>