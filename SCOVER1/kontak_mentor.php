<?php
    $whatsapp = "https://wa.me/+6289697053591";
    $email = "mailto:scover@gmail.com";
    $gallery = [
        "images/foto1.jpg",
        "images/foto2.jpg",
        "images/foto3.jpg",
        "images/foto4.png",
    ];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <link rel="stylesheet" href="css/kontak.css">
    <link rel="stylesheet" href="css/logout.css" />

</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Presensi</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="jurnal.php">Jurnal</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><a href="kontak_mentor.php" class="active">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container">
        <header>
            <h1>Tentang Kami</h1>
        </header>
        <section class="contact">
            <a href="<?php echo $whatsapp; ?>" class="btn">WhatsApp</a>
            <a href="<?php echo $email; ?>" class="btn">Email</a>
        </section>
        <section class="gallery">
            <h2>Galeri</h2>
            <div class="gallery-grid">
                <?php foreach ($gallery as $img) : ?>
                    <div class="gallery-item">
                        <img src="<?php echo $img; ?>" alt="Gallery Image">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
</body>
</html>
