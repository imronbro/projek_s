<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "Pengajar tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Ambil data rating pengajar
$ratingQuery = mysqli_query($conn, "
    SELECT r.rating, r.komentar, r.created_at, s.full_name 
    FROM rating_pengajar r
    JOIN siswa s ON r.siswa_id = s.siswa_id
    WHERE r.pengajar_id = $id
    ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Rating dan Komentar</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        * {

            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin-top: 140px;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #0b3c5d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #0b3c5d;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        p {
            text-align: center;
            font-style: italic;
            color: gray;
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

        .btn-kembali {
            position: absolute;
            top: 140px;
            /* sesuaikan dengan margin atas body dan container */
            left: 50px;
            background-color: #083d6e;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            z-index: 10;
            text-decoration: none;
        }

        .btn-kembali:hover {
            background-color: #0a4d8c;
        }
    </style>
</head>
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
            <a href="home.php">
                <img src="images/foto4.png" alt="Logo" class="logo-image">
            </a>
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

            <li><a href="pengajar.php" class="active">Pengajar</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="kontak.php">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container">
        <h2>Riwayat Rating dan Komentar</h2>

        <!-- Tombol Kembali -->
        <button onclick="history.back()" class="btn-kembali">← Kembali</button>

        <div class="rating-section">
            <?php if (mysqli_num_rows($ratingQuery) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rating = mysqli_fetch_assoc($ratingQuery)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($rating['full_name']) ?></td>
                                <td><?= htmlspecialchars($rating['rating']) ?> / 5</td>
                                <td><?= htmlspecialchars($rating['komentar']) ?></td>
                                <td><?= date("d M Y, H:i", strtotime($rating['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada rating untuk pengajar ini.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>