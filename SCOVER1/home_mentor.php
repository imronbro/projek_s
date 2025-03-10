<?php
session_start();
include 'koneksi.php'; 

// Periksa apakah user sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil pengajar_id dan full_name berdasarkan email
$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

// Cek waktu presensi terakhir
$sql_cek = "SELECT UNIX_TIMESTAMP(waktu_presensi) as last_presensi FROM presensi_pengajar WHERE pengajar_id = ? ORDER BY waktu_presensi DESC LIMIT 1";
$stmt = $conn->prepare($sql_cek);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$last_presensi = 0;
if ($row = $result->fetch_assoc()) {
    $last_presensi = $row['last_presensi'];
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'] ?? null;
    $sesi = htmlspecialchars($_POST['sesi'] ?? '');
    $status = htmlspecialchars($_POST['kehadiran'] ?? '');
    $tempat = htmlspecialchars($_POST['tempat'] ?? '');
    $komentar = htmlspecialchars($_POST['komentar'] ?? '');

    if (!$tanggal || !$sesi || !$status || empty($tempat)) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit();
    }

    $current_time = time();
    if ($last_presensi > 0 && ($current_time - $last_presensi) < 5400) {
        echo "<script>alert('Anda hanya bisa mengisi presensi sekali dalam 90 menit!'); window.history.back();</script>";
        exit();
    }

    $image_path = null;
    if ($status == "Hadir" && isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $image_path);
    }

    // Simpan presensi ke database
    $sql = "INSERT INTO presensi_pengajar (pengajar_id, full_name, tanggal, sesi, status, tempat, komentar, gambar, waktu_presensi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $pengajar_id, $full_name, $tanggal, $sesi, $status, $tempat, $komentar, $image_path);
    
    if ($stmt->execute()) {
        echo "<script>alert('Presensi berhasil disimpan!'); window.location.href='home_mentor.php';</script>";
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
    <title>Dashboard Mentor</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/logout.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">
        <img src="images/foto4.png" alt="Logo">
    </div>
    <ul class="nav-links">
        <li><a href="home_mentor.php" class="active">Presensi</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
        <li><a href="kontak_mentor.php">Kontak</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
</nav>

<div class="content">
    <div class="form-presensi">
        <form action="" method="post" enctype="multipart/form-data">
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
            <select id="kehadiran" name="kehadiran" required onchange="toggleUpload()">
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
            </select>

            <label for="tempat">Tempat Mengajar:</label>
            <input type="text" id="tempat" name="tempat" required>

            <div id="komentar-container" style="display: none;">
                <label for="komentar">Alasan Izin/Sakit:</label>
                <textarea id="komentar" name="komentar" rows="3"></textarea>
            </div>

            <div id="upload-container">
                <label for="gambar">Upload Bukti Kehadiran:</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
            </div>

            <button type="submit">Kirim</button>
        </form>
    </div>
</div>
<script src="js/logout.js" defer></script>
<script src="js/home.js" defer></script>
<script src="js/menu.js" defer></script>
<script>
    function toggleUpload() {
        var status = document.getElementById("kehadiran").value;
        document.getElementById("upload-container").style.display = (status === "Hadir") ? "block" : "none";
        document.getElementById("komentar-container").style.display = (status !== "Hadir") ? "block" : "none";
    }
</script>
</body>
</html>