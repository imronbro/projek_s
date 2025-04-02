<?php
session_start();
include 'koneksi.php';

$query = "SELECT full_name, kelas, alamat, gambar, sekolah, nohp FROM siswa";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/pengajar.css">
        <link rel="stylesheet" href="css/navbar.css">


</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
            <span class="logo-text">Scover Center</span>
        </div>
        <h1 class="title">Dashboard Mentor</h1>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Presensi</a></li>
            <li><a href="siswa.php" class="active">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="jurnal.php">Jurnal</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><a href="kontak_mentor.php">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Daftar Siswa</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-3">
                        <?php 
                        $imagePath = "uploads/" . basename(htmlspecialchars($row['gambar']));
                        if (!empty($row['gambar']) && file_exists($imagePath)) {
                        ?>
                            <img src="<?= $imagePath; ?>" alt="Foto Pengajar" class="profile-img">
                        <?php } else { ?>
                            <img src="uploads1/default.png" alt="Foto Default" class="profile-img">
                        <?php } ?>
                        <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                        <p><strong></strong> <?= htmlspecialchars($row['sekolah']); ?></p>
                        <p><strong></strong>Kelas: <?= htmlspecialchars($row['kelas']); ?></p>
                        <p><strong></strong> <?= htmlspecialchars($row['alamat']); ?></p>
                        <a href="https://wa.me/<?= htmlspecialchars($row['nohp']); ?>" target="_blank" class="btn btn-whatsapp">Hubungi via WhatsApp</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script src="js/menu.js" defer></script>
    <script src="js/logout.js" defer></script>
</body>
</html>

<?php mysqli_close($conn); ?>
