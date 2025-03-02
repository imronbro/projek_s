<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scover";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/logout.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
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
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['full_name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
            <p><strong>Sekolah:</strong> <?= htmlspecialchars($data['sekolah'] ?? '-'); ?></p>
            <p><strong>Kelas:</strong> <?= htmlspecialchars($data['kelas'] ?? '-'); ?></p>
            <p><strong>TTL:</strong> <?= htmlspecialchars($data['ttl'] ?? '-'); ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat'] ?? '-'); ?></p>
            <p><strong>No HP:</strong> <?= htmlspecialchars($data['nohp'] ?? '-'); ?></p>
        </div>
        <a href="home.php" class="btn btn-primary">Kembali</a>
        <a href="edit_profile.php" class="btn btn-secondary">Edit Profil</a>
    </div>
</body>
<script src="js/menu.js" defer></script>
<script src="js/logout.js" defer></script> 
</html>
</html>
