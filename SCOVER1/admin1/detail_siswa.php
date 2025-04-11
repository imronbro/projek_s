<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "Siswa tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Ambil data siswa
$siswaQuery = mysqli_query($conn, "SELECT * FROM siswa WHERE siswa_id = $id");
$siswa = mysqli_fetch_assoc($siswaQuery);

if (!$siswa) {
    echo "Siswa tidak ditemukan.";
    exit;
}

$gambar = htmlspecialchars($siswa['gambar']);
$imagePath = "../" . $gambar;
$defaultImage = "../uploads1/default.png";
$displayImage = (!empty($gambar) && file_exists($imagePath)) ? $imagePath : $defaultImage;
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
            font-family: Arial, sans-serif;
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

        .profile-container {
             display: flex;
             justify-content: center;
             align-items: center;
             padding: 40px 20px;
             margin-top: 120px; /* tambahkan ini */
        }


        .card {
            background-color: rgb(2, 65, 131);
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
            background-color: #145375;
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

        .profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    margin-top: 120px;
}

.card {
    background-color: #145375;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(14, 63, 148, 0.1);
    width: 100%;
    max-width: 500px;
    text-align: center;
    color: white;
}

.siswa-img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 20px;
    border: 3px solid rgb(12, 54, 99);
}

.card p {
    margin: 8px 0;
    font-size: 16px;
}

.btn-group {
    margin-top: 20px;
}

.btn {
    padding: 10px 15px;
    background-color: #ffc107;
    color: #145375;
    text-decoration: none;
    border-radius: 5px;
    margin: 0 5px;
    font-weight: bold;
}

.btn:hover {
    background-color: #e6c200;
}

        
    </style>
</head>
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

            <li><a href="pengajar.php" >Pengajar</a></li>
            <li><a href="siswa.php" class="active">Siswa</a></li>
            <li><a href="jadwal.php" >Jadwal</a></li>
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
    <div class="profile-container">
        <div class="card">
            <img src="<?= $displayImage ?>" alt="Foto Siswa" class="siswa-img">
            <h2><?= htmlspecialchars($siswa['full_name']) ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($siswa['email']) ?></p>
            <p><strong>Sekolah:</strong> <?= htmlspecialchars($siswa['sekolah']) ?></p>
            <p><strong>Kelas:</strong> <?= htmlspecialchars($siswa['kelas']) ?></p>
            <p><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($siswa['ttl']) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($siswa['alamat']) ?></p>
            <p><strong>No HP:</strong> <?= htmlspecialchars($siswa['nohp']) ?></p>
            <p><strong>Dibuat pada:</strong> <?= htmlspecialchars($siswa['created_at']) ?></p>

            <div class="btn-group">
                <a href="siswa.php" class="btn">Kembali</a>
                <a href="edit_siswa.php?id=<?= $siswa['siswa_id'] ?>" class="btn">Edit</a>
            </div>
        </div>
    </div>
</body>
</html>