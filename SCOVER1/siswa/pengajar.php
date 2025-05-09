<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
$user_email = $_SESSION['user_email'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "SELECT pengajar_id, full_name, gambar, mapel, nohp FROM mentor";
if (!empty($search)) {
    $query .= " WHERE full_name LIKE '%$search%'";
}

$result = mysqli_query($conn, $query);


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <style>
        /* Global Styles */
                * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 80px;
            /* Memberikan ruang di bawah navbar */
            padding: 20px;
        }

        /* Header */
        h2 {
            text-align: center;
            color: #145375;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        /* Kartu Pengajar */
        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Bayangan lembut */
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Animasi hover */
            animation: fadeInUp 0.5s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
            /* Efek zoom saat hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            /* Bayangan lebih besar saat hover */
        }

        /* Gambar pada Kartu Pengajar */
        .card img {
            width: 150px; /* Lebar gambar tetap */
            height: 150px; /* Tinggi gambar tetap */
            border-radius: 50%; /* Membuat gambar menjadi bulat */
            object-fit: cover; /* Memastikan gambar tetap proporsional */
            margin: 0 auto; /* Pusatkan gambar di dalam kartu */
            display: block; /* Pastikan gambar menjadi elemen blok */
            border: 3px solid #145375; /* Tambahkan border untuk estetika */
        }

        .card h4 {
            font-size: 1.2em;
            color: #145375;
            margin: 10px 0;
        }

        .card p {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 15px;
        }

        .card .btn-group-vertical {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .card .btn {
            display: block;
            width: 90%;
            /* Lebar tombol hampir penuh */
            padding: 10px;
            margin: 5px auto;
            background-color: #faaf1d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .card .btn:hover {
            background-color: #fabe49;
            transform: scale(1.05);
            /* Efek zoom saat hover */
        }

        .card .badge {
            display: inline-block;
            background-color: #145375;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-top: 5px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Daftar Pengajar */
        .row {
            display: flex;
            flex-wrap: wrap; /* Pastikan elemen menyesuaikan layar */
            gap: 30px; /* Jarak antar kartu */
            justify-content: center; /* Pusatkan elemen */
            margin-top: 20px;
        }

        .col-md-4 {
            flex: 1 1 calc(25% - 30px); /* Lebar kartu di layar besar */
            max-width: calc(25% - 30px); /* Maksimal 4 kartu per baris */
            box-sizing: border-box; /* Sertakan padding dan border dalam ukuran */
        }

        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Animasi hover */
            padding: 20px; /* Tambahkan padding di dalam kartu */
        }

        .card:hover {
            transform: scale(1.05); /* Efek zoom saat hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Bayangan lebih besar saat hover */
        }

        .card img {
            width: 120px; /* Lebar gambar tetap */
            height: 120px; /* Tinggi gambar tetap */
            border-radius: 50%; /* Membuat gambar menjadi bulat */
            object-fit: cover; /* Memastikan gambar tetap proporsional */
            margin: 0 auto 15px; /* Pusatkan gambar di dalam kartu dan beri jarak bawah */
            display: block; /* Pastikan gambar menjadi elemen blok */
            border: 3px solid #145375; /* Tambahkan border untuk estetika */
        }

        .card h4 {
            font-size: 1.2em;
            color: #145375;
            margin: 10px 0;
            text-align: center; /* Teks rata tengah */
        }

        .card p {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 15px;
            text-align: center; /* Teks rata tengah */
        }

        .card .btn-group-vertical {
            display: flex;
            flex-direction: column; /* Tombol vertikal */
            align-items: center;
            gap: 10px; /* Jarak antar tombol */
            margin-top: 10px;
        }

        .card .btn {
            display: block;
            width: 90%; /* Lebar tombol hampir penuh */
            padding: 10px;
            background-color: #faaf1d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .card .btn:hover {
            background-color: #fabe49;
            transform: scale(1.05); /* Efek zoom saat hover */
        }

        /* Form Pencarian */
        .search-form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px; /* Jarak antara input dan tombol */
            width: 100%;
            max-width: 600px; /* Lebar maksimum form pencarian */
        }

        .search-input {
            flex: 1; /* Input memenuhi ruang yang tersedia */
            padding: 12px;
            border: 1px solid #ccc; /* Border abu-abu */
            border-radius: 5px; /* Sudut membulat */
            font-size: 16px;
            box-sizing: border-box; /* Sertakan padding dalam lebar total */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-input:focus {
            border-color: #faaf1d; /* Border kuning saat fokus */
            box-shadow: 0 0 5px rgba(250, 175, 29, 0.5); /* Efek cahaya kuning */
            outline: none; /* Hilangkan outline default */
        }

        /* Tombol Cari */
        .button {
            padding: 12px 20px;
            background-color: #faaf1d; /* Warna tombol kuning */
            color: white;
            border: none;
            border-radius: 5px; /* Sudut membulat */
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .button:hover {
            background-color: #fabe49; /* Warna kuning lebih terang saat hover */
            transform: scale(1.05); /* Efek zoom saat hover */
        }

        /* Responsif untuk Layar Kecil */
        @media (max-width: 768px) {
            .search-box {
                flex-direction: column; /* Susun input dan tombol secara vertikal */
                gap: 10px; /* Jarak antar elemen */
            }

            .button {
                width: 100%; /* Tombol memenuhi lebar layar */
                font-size: 14px; /* Ukuran font lebih kecil */
                padding: 10px; /* Kurangi padding untuk tombol */
            }
        }

        /* Responsif untuk Layar Sangat Kecil */
        @media (max-width: 480px) {
            .search-input {
                font-size: 14px; /* Ukuran font lebih kecil di layar kecil */
                padding: 8px; /* Kurangi padding */
            }

            .button {
                font-size: 12px; /* Ukuran font lebih kecil */
                padding: 8px 10px; /* Kurangi padding tombol */
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .card {
                margin: 10px auto;
                width: 100%;
                /* Kartu memenuhi lebar layar */
            }

            .card img {
                width: 120px; /* Lebar gambar lebih kecil di layar kecil */
                height: 120px; /* Tinggi gambar lebih kecil di layar kecil */
            }

            .card h4 {
                font-size: 1em;
                /* Ukuran teks lebih kecil */
            }

            .card p {
                font-size: 0.8em;
                /* Ukuran teks lebih kecil */
            }

            .card .btn {
                font-size: 0.8em;
                /* Ukuran tombol lebih kecil */
                padding: 8px;
            }

            .col-md-4 {
                flex: 1 1 100%;
                /* Kartu memenuhi lebar layar */
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .card img {
                width: 100px; /* Lebar gambar lebih kecil di layar sangat kecil */
                height: 100px; /* Tinggi gambar lebih kecil di layar sangat kecil */
            }

            .card h4 {
                font-size: 0.9em;
                /* Ukuran teks lebih kecil */
            }

            .card p {
                font-size: 0.75em;
                /* Ukuran teks lebih kecil */
            }

            .card .btn {
                font-size: 0.75em;
                /* Ukuran tombol lebih kecil */
                padding: 6px;
            }
        }
    </style>
</head>
<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    function toggleDropdown(event) {
        event.preventDefault(); // supaya gak reload atau pergi ke #
        const dropdown = event.currentTarget.nextElementSibling;
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function toggleDropdown(event) {
        event.preventDefault();
        const link = event.currentTarget;
        const dropdown = link.nextElementSibling;
        const arrow = link.querySelector('#arrow');

        const isOpen = dropdown.style.display === 'block';
        dropdown.style.display = isOpen ? 'none' : 'block';
        arrow.innerHTML = isOpen ? '&#9660;' : '&#9650;'; // ▼ / ▲
    }

    // Tutup dropdown kalau klik di luar menu
    document.addEventListener('click', function(event) {
        const dropdownMenus = document.querySelectorAll('.dropdown-menu');
        dropdownMenus.forEach(menu => {
            if (!menu.parentElement.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    });
</script>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">

            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php" class="active">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
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
        <!-- Form Pencarian -->
        <form action="pengajar.php" method="get" class="search-form" style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
            <div class="search-box" style="display: flex; align-items: center; gap: 10px; width: 100%; max-width: 600px;">
                <input type="text" name="search" placeholder="Cari Nama Pengajar..." class="search-input" style="flex: 1; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; box-sizing: border-box;">
                <button type="submit" class="button" style="padding: 12px 20px; background-color: #faaf1d; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer;">Cari</button>
            </div>
        </form>
        <div class="row mt-4">
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) {
                    $imagePath = "../uploads/" . basename(htmlspecialchars($row['gambar']));
                    $defaultImage = "../uploads1/default.jpg";
                    $finalImage = (!empty($row['gambar']) && file_exists($imagePath)) ? $imagePath : $defaultImage;
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center p-3">
                            <img src="<?= $finalImage; ?>" alt="Foto Pengajar" class="profile-img mb-3">
                            <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                            <p><strong>TUTOR <?= htmlspecialchars($row['mapel']); ?></strong></p>
                            <p class="badge"><?= htmlspecialchars($row['mapel']); ?></p>
                            <div class="btn-group-vertical">
                                <a href="https://wa.me/<?= htmlspecialchars($row['nohp']); ?>" target="_blank" class="btn btn-whatsapp">Hubungi via WhatsApp</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12 text-center">
                    <p class="text-danger">Pengajar tidak ditemukan.</p>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
    <script src="js/menu.js" defer></script>
</body>

</html>