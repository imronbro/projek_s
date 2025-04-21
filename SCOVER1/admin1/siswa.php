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
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Admin</h1>
        <ul class="nav-links">
            <li><a href="home.php">Presensi Siswa</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="siswa.php"class="active">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
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
</body>

</html>