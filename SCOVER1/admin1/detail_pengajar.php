<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "Pengajar tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Ambil data pengajar
$mentorQuery = mysqli_query($conn, "SELECT * FROM mentor WHERE pengajar_id = $id");
$mentor = mysqli_fetch_assoc($mentorQuery);

if (!$mentor) {
    echo "Pengajar tidak ditemukan.";
    exit;
}

// Ambil rating pengajar
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <style>
    * {

        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        margin-top: 0px;
        /* Memberikan ruang di bawah navbar */
        padding: 20px;
        text-align: center;
    }

    h2 {
        text-align: center;
        color: rgb(255, 255, 255);
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

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 45px;
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #fff;
        color: #333;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 35px;
        color: #333;
        font-size: 14px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px;
        width: 40px;
    }

    button {
        padding: 10px 15px;
        background-color: rgb(7, 52, 100);
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: rgb(14, 54, 98);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th,
    table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    table th {
        background-color: rgb(16, 59, 105);
        color: #fff;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .btn-detail {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 12px;
        background-color: #e6c200;
        color: #145375;
        text-decoration: none;
        border-radius: 5px;
    }

    .btn-detail:hover {
        background-color: #fff;

    }

    .btn-group-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
        margin-top: 10px;
    }

    .btn-grid1{
        display: center flex;
        grid-template-columns: repeat(2, 1fr);
        /* 2 tombol per baris */
        gap: 15 px;
        justify-items: center;
        margin-top: 0px;
    }
    .btn-grid2{
        padding-left: 14px;
        display: center flex;
        grid-template-columns: repeat(2, 1fr);
        /* 2 tombol per baris */
        gap: 15 px;
        justify-items: center;
        margin-top: 0px;
    }


    .profile-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        margin-top: 120px;
        /* tambahkan ini */
    }


    .card {
        background-color: #145375;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(14, 63, 148, 0.1);
        width: 100%;
        max-width: 500px;
        text-align: center;
    }

    .mentor-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 20px;
        border: 3px solid rgb(12, 54, 99);
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

    function confirmLogout() {
            if (confirm("Apakah kamu yakin ingin keluar?")) {
                window.location.href = "logout.php"; // ganti sesuai nama file logout-mu
            }
        }
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
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="profile-container">
        <div class="card">
            <?php
            $gambar = htmlspecialchars($mentor['gambar']);
            $imagePath = "../" . $gambar; // gambar sudah termasuk 'uploads/namafile' dari DB
            $defaultImage = "../uploads1/default.png";

            if (!empty($gambar) && file_exists($imagePath)) {
                $displayImage = $imagePath;
            } else {
                $displayImage = $defaultImage;
            }
            ?>
            <img src="<?= $displayImage; ?>" alt="Foto Pengajar" class="mentor-img">

            <h2><?= htmlspecialchars($mentor['full_name']); ?></h2>
            <p><strong>Mata Pelajaran:</strong> <?= htmlspecialchars($mentor['mapel']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($mentor['email']); ?></p>
            <p><strong>No HP:</strong> <?= htmlspecialchars($mentor['nohp']); ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($mentor['alamat']); ?></p>
            <p><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($mentor['ttl']); ?></p>
            <!-- Tombol navigasi -->
            <div class="btn-grid1">
                <a href="detailriwayatrating.php?id=<?= $id ?>" class="btn-detail">Riwayat Rating</a>
                <a href="riwayat_presensi.php?id=<?= $id ?>" class="btn-detail">Riwayat Presensi</a>
            </div>
                <div class="btn-grid2">
                <a href="edit_pengajar.php?id=<?= $id ?>" class="btn-detail">Edit</a>
                <a href="pengajar.php?id=<?= $id ?>" class="btn-detail">Kembali</a>
            </div>
        </div>
    </div>
</body>

</html>