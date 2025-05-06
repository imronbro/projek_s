<?php
session_start();
include '../koneksi.php';

// Ambil data user berdasarkan email dari session
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT full_name, email, sekolah, alamat, gambar, kelas, ttl, nohp FROM siswa WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #145375;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            display: block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            color: #145375;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .btn {
            background-color: #e6c200;
            color: #145375;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            text-align: center;
            margin: 10px 5px;
            display: inline-block;
        }

        .btn:hover {
            background-color: #145375;
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }

        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        .notification-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .btn-secondary {
            background-color: #ccc;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #bbb;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
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
            <li><a href="profile.php" class="active">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Profil Pengguna</h2>
        <div class="card text-center">
            <?php
            $imagePath = "uploads/" . basename(htmlspecialchars($data['gambar']));
            if (!empty($data['gambar']) && file_exists($imagePath)) {
                echo "<img src='$imagePath' alt='Foto Profil' class='profile-img'>";
            } else {
                echo "<img src='uploads/default.jpg' alt='Foto Profil Default' class='profile-img'>";
            }
            ?>
        </div>
        <div class="card">
            <div class="info-row"><strong>Nama Lengkap</strong><span>: <?= htmlspecialchars($data['full_name']); ?></span></div>
            <div class="info-row"><strong>Email</strong><span>: <?= htmlspecialchars($data['email']); ?></span></div>
            <div class="info-row"><strong>Sekolah</strong><span>: <?= htmlspecialchars($data['sekolah'] ?? '-'); ?></span></div>
            <div class="info-row"><strong>Kelas</strong><span>: <?= htmlspecialchars($data['kelas'] ?? '-'); ?></span></div>
            <div class="info-row"><strong>TTL</strong><span>: <?= htmlspecialchars($data['ttl'] ?? '-'); ?></span></div>
            <div class="info-row"><strong>Alamat</strong><span>: <?= htmlspecialchars($data['alamat'] ?? '-'); ?></span></div>
            <div class="info-row"><strong>No HP</strong><span>: <?= htmlspecialchars($data['nohp'] ?? '-'); ?></span></div>
        </div>
        <div class="text-center">
            <a href="edit_profile.php" class="btn">Edit Profil</a>
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
    <script src="js/logout.js" defer></script>
</body>

</html>
