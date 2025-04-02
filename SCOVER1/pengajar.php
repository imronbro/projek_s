<?php
session_start();
include 'koneksi.php';

$query = "SELECT full_name, gambar, mapel, nohp FROM mentor";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajar</title>
    <link rel="stylesheet" href="css/pengajar.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php" class="active">Pengajar</a></li>
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
    <div class="container mt-5">
        <h2 class="text-center">Daftar Pengajar</h2>
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
                        <p><strong></strong> TUTOR <?= htmlspecialchars($row['mapel']); ?></p>
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
