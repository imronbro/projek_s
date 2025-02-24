<?php
session_start();
include 'koneksi.php'; // File koneksi ke database

// Periksa apakah user sudah login (menggunakan email dalam session)
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email']; // Ambil email dari session

// Ambil siswa_id dan full_name berdasarkan email
$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $siswa_id = $row['siswa_id'];
    $full_name = $row['full_name']; // Ambil nama lengkap siswa
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dengan validasi sederhana
    $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : null;
    $sesi = !empty($_POST['sesi']) ? htmlspecialchars($_POST['sesi']) : null;
    $status = !empty($_POST['kehadiran']) ? htmlspecialchars($_POST['kehadiran']) : null;
    $komentar = !empty($_POST['komentar']) ? htmlspecialchars($_POST['komentar']) : null;

    // Validasi input
    if (!$tanggal || !$sesi || !$status) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit();
    }

    // Simpan presensi ke database dengan full_name
    $sql = "INSERT INTO presensi_siswa (siswa_id, full_name, tanggal, sesi, status, komentar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $siswa_id, $full_name, $tanggal, $sesi, $status, $komentar);

    if ($stmt->execute()) {
        echo "<script>alert('Presensi berhasil disimpan!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Tutup koneksi database
$conn->close();
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
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
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

                <button type="submit">Kirim</button>
            </form>
        </div>
        <div class="riwayat-presensi">
    <a href="riwayat_presensi.php" class="btn">Lihat Riwayat Presensi</a>
</div>
    </div>
    <div class="riwayat-presensi">
    <a href="riwayat_presensi.php" class="btn">Lihat Riwayat Presensi</a>
</div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("tanggal").value = today;
        });

        function toggleKomentar() {
            let kehadiran = document.getElementById("kehadiran").value;
            let komentarContainer = document.getElementById("komentar-container");

            if (kehadiran === "Izin" || kehadiran === "Sakit") {
                komentarContainer.style.display = "block";
            } else {
                komentarContainer.style.display = "none";
            }
        }
    </script>
</body>
</html>
