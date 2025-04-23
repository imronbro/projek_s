<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT * FROM siswa";
if (!empty($search)) {
    $query .= " WHERE full_name LIKE '%$search%'";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 100px;
            padding: 20px;
        }

        h2 {
            color: #145375;
            text-align: center;
        }

        .search-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-form button {
            background-color: #f1c40f;
            color: #0b3c5d;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #d4ac0d;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #145375;
            margin-bottom: 10px;
            background-color: #ccc;
            /* fallback warna abu-abu kalau default.png transparan */
        }


        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-detail {
            background-color: #145375;
            color: #fff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .btn-whatsapp {
            background-color: #e6c200;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-whatsapp:hover,
        .btn-detail:hover {
            opacity: 0.9;
        }

        .alert-notfound {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 20px;
            border-radius: 10px;
            font-weight: 600;
            max-width: 500px;
            margin: 40px auto;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dropdown styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #0271ab;
            min-width: 180px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            padding: 0;
            margin: 5px;
            left: -35px;
            list-style: none;

            /* <- tambahkan border */
        }

        .dropdown-menu li a {
            color: #fff !important;
            /* pastikan warnanya terlihat */
            padding: 12px 16px;
            text-decoration: none;
            display: block;

            font-weight: bold;
            /* opsional biar lebih terlihat */
        }

        .dropdown-menu li a:hover {
            background-color: #e6c200;
            color: #145375;
        }


        .arrow {
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Admin</h1>
        <ul class="nav-links">
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown(event)">Presensi <span id="arrow" class="arrow">&#9660;</span></a>
                <ul class="dropdown-menu">
                    <li><a href="home.php">Presensi Siswa</a></li>
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
                </ul>
            </li>

            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="siswa.php" class="active">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </nav>

    <div class="container">
        <h2>Daftar Siswa</h2>

        <form action="siswa.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Cari Nama Siswa...">
            <button type="submit">Cari</button>
        </form>

        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $gambar = htmlspecialchars($row['gambar']);
                    $imagePath = "../uploads/" . basename($gambar);
                    $defaultImage = "../uploads1/default.png";
                    $finalImage = (!empty($gambar) && file_exists($imagePath)) ? $imagePath : $defaultImage;

            ?>
                    <div class="card">
                        <img src="<?= $finalImage; ?>" alt="Foto Siswa" class="profile-img">
                        <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                        <p><?= htmlspecialchars($row['sekolah']); ?> - Kelas <?= htmlspecialchars($row['kelas']); ?></p>
                        <div class="btn-group-vertical">
                            <a href="detail_siswa.php?id=<?= $row['siswa_id']; ?>" class="btn-detail">Lihat Detail</a>
                            <a href="https://wa.me/<?= htmlspecialchars($row['nohp']); ?>" target="_blank" class="btn-whatsapp">Hubungi via WhatsApp</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='alert-notfound'>😢 Maaf, siswa tidak ditemukan. Silakan coba kata kunci lain.</div>";
            }
            ?>
        </div>
    </div>
    <script>
        function confirmLogout() {
            if (confirm("Apakah kamu yakin ingin keluar?")) {
                window.location.href = "logout.php"; // ganti sesuai nama file logout-mu
            }
        }

        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');
            const arrow = dropdown.querySelector('.arrow');

            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            arrow.innerHTML = menu.style.display === 'block' ? '&#9650;' : '&#9660;'; // ▲ dan ▼
        }

        // Tutup dropdown jika klik di luar
        window.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.dropdown');
            if (!dropdown.contains(e.target)) {
                dropdown.querySelector('.dropdown-menu').style.display = 'none';
                dropdown.querySelector('.arrow').innerHTML = '&#9660;';
            }
        });
    </script>
</body>

</html>