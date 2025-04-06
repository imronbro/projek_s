<?php
session_start();
include 'koneksi.php';

// Ambil data user berdasarkan email dari session
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT full_name, email, sekolah, alamat, gambar, kelas, ttl, nohp FROM siswa WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/navbar.css">

</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
            <span class="logo-text">Scover Center</span>
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php" class="active">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Profil Pengguna</h2>
        <div class="card p-3 shadow mb-4 text-center">
            <?php 
            $imagePath = "uploads/" . basename(htmlspecialchars($data['gambar']));
            if (!empty($data['gambar']) && file_exists($imagePath)) {
            ?>
                <img src="<?= $imagePath; ?>" alt="Foto Profil" class="profile-img">
            <?php 
            } else { 
            ?>
                <img src="uploads/default.png" alt="Foto Profil Default" class="profile-img">
            <?php 
            } 
            ?>
        </div>
        <div class="card p-3 shadow mb-4 text-left">
            <div class="info-row">
                <strong>Nama Lengkap</strong>
                <span>: <?= htmlspecialchars($data['full_name']); ?></span>
            </div>
            <div class="info-row">
                <strong>Email</strong>
                <span>: <?= htmlspecialchars($data['email']); ?></span>
            </div>
            <div class="info-row">
                <strong>Sekolah</strong>
                <span>: <?= htmlspecialchars($data['sekolah'] ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <strong>Kelas</strong>
                <span>: <?= htmlspecialchars($data['kelas'] ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <strong>TTL</strong>
                <span>: <?= htmlspecialchars($data['ttl'] ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <strong>Alamat</strong>
                <span>: <?= htmlspecialchars($data['alamat'] ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <strong>No HP</strong>
                <span>: <?= htmlspecialchars($data['nohp'] ?? '-'); ?></span>
            </div>
        </div>
        <a href="home.php" class="btn btn-primary">Kembali</a>
        <a href="edit_profile.php" class="btn btn-secondary">Edit Profil</a>
    </div>
</body>
<script src="js/menu.js" defer></script>
<script src="js/logout.js" defer></script> 
</html>
</html>
