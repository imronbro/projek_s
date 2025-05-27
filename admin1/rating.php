<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
// Ambil keyword pencarian jika ada
$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : "";

// Query gabung 3 tabel: rating_pengajar + mentor + siswa
$sql = "
    SELECT 
        rp.*, 
        m.full_name AS nama_pengajar, 
        s.full_name AS nama_siswa
    FROM rating_pengajar rp
    JOIN mentor m ON rp.pengajar_id = m.pengajar_id
    JOIN siswa s ON rp.siswa_id = s.siswa_id
    WHERE m.full_name LIKE '%$keyword%'
    ORDER BY rp.created_at DESC
";


$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins',sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
        }

        .content {
            padding: 100px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h3 {
            text-align: center;
            color: #145375;
            margin: 30px 0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input,
        select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            max-width: 960px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            padding: 9px 12px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        td {
            padding: 8px 10px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        table th {
            background-color: #145375;
            color: white;
            font-size: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        td {
            font-size: 15px;
            color: #444;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        .btn-detail {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: rgb(13, 78, 135);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-detail:hover {
            background-color: rgb(2, 65, 131);
        }

        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }

        .bintang {
            color: gold;
            font-size: 20px;
        }

        .bintang-kosong {
            color: #ccc;
            font-size: 20px;
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .filter-bar form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        button {
            background-color: #e6c200;
            color: #145375;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        button:hover {
            background-color: #145375;
            color: #fff;
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

        * {
            box-sizing: border-box;
        }
            .notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f9f9f9;
        color: #145375;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        /* Default: disembunyikan */
        text-align: center;
        width: 300px;
    }

    .notification p {
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
    }

    /* Tombol di dalam notifikasi */
    .notification-buttons {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .notification-buttons .btn {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
        text-decoration: none;
        /* Hilangkan garis bawah */
    }

    /* Tombol Batal */
    .notification-buttons .btn-secondary {
        background-color: #145375;
        color: white;
    }

    .notification-buttons .btn-secondary:hover {
        background-color: #e6c200;
        color: #145375;
    }

    /* Tombol Keluar */
    .notification-buttons .btn-danger {
        background-color: #e74c3c;
        /* Warna merah */
        color: white;
        border: none;
    }

    .notification-buttons .btn-danger:hover {
        background-color: #c0392b;
        /* Warna merah lebih gelap saat hover */
        transform: scale(1.05);
        /* Efek zoom saat hover */
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
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php" class="active">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container">
        <h3>Data Rating Pengajar</h3>

        <div class="filter-bar">
            <form method="GET" action="">
                <input type="text" name="keyword" placeholder="Cari Nama Pengajar..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Pengajar</th>
                    <th>Nama Siswa</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_pengajar']) ?></td>
                            <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td>
                                <?php
                                $rating = (int)$row['rating'];
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<span class="bintang">★</span>';
                                    } else {
                                        echo '<span class="bintang-kosong">☆</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['komentar']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Tidak ada data ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
        <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
</body>
<script src="js/menu.js" defer></script>

</html>
<?php $conn->close(); ?>