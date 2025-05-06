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
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    * {

        box-sizing: border-box;
    }

    body {
        background: #f4f4f4;
        color: #fabe49;
        font-family: "Poppins";
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .container {

        margin: 80px auto;
        padding: 30px;
        background: #f4f4f4;
        border-radius: 15px;
        animation: fadeIn 1s ease-in-out;
    }

    header h2 { 
        color: #145375;
        font-size: 2em;
        animation: slideDown 1s ease-in-out;
        margin-top: 20px;
    }

    .contact {
        margin: 30px 0;
    }

    .contact .btn {
        display: inline-block;
        padding: 14px 30px;
        margin: 12px;
        color: #145375;
        background: #e6c200;
        text-decoration: none;
        font-weight: bold;
        border-radius: 8px;
        transition: transform 0.3s ease-in-out, background 0.3s;
    }

    .contact .btn:hover {
        transform: scale(1.15);
        color: #fff;
        background: #145375;
    }

    .gallery {
        margin-top: 0px;
    }

    .gallery h2 {
        color: #145375;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 25px;
    }

    .gallery-item img {
        width: 100%;
        height: auto;
        border-radius: 12px;
        opacity: 0;
        transform: scale(0.8);
        animation: imgFadeIn 0.8s ease-out forwards;
    }

    /* Saat hover */
    .gallery-item img:hover {
        transform: scale(1.05) translateY(-5px);
        filter: brightness(1.1);
        transition: transform 0.4s ease, filter 0.4s ease;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
        transition: transform 0.4s ease;
    }


    /* Tambahkan animasi untuk delay per item */
    .gallery-item:nth-child(1) img {
        animation-delay: 0.2s;
    }

    .gallery-item:nth-child(2) img {
        animation-delay: 0.4s;
    }

    .gallery-item:nth-child(3) img {
        animation-delay: 0.6s;
    }

    .gallery-item:nth-child(4) img {
        animation-delay: 0.8s;
    }

    /* Animasi */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes imgFadeIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
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
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li><a href="kontak.php" class="active">Kontak</a></li>
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
            <h2>Tentang Kami</h2>
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
    <script src="js/menu.js" defer></script>
</body>

</html>