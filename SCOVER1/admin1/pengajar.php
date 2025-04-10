<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 100px; /* Memberikan ruang di bawah navbar */
            padding: 20px;
            text-align: center;
        }

        h2 {
            text-align: center;
            color: #007bff;
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

        input, select {
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
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-detail {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color:rgb(13, 78, 135);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-detail:hover {
            background-color:rgb(2, 65, 131);
        }
        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
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
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php"class="active">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
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
        <form action="pengajar.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Cari Nama Pengajar..." class="search-input">
            <button type="submit" class="btn">Cari</button>
        </form>
        <div class="container mt-5">
    <div class="row mt-4">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $imagePath = "../uploads/" . basename(htmlspecialchars($row['gambar']));
                $defaultImage = "../uploads1/default.png";
                $finalImage = (!empty($row['gambar']) && file_exists($imagePath)) ? $imagePath : $defaultImage;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-3">
                        <img src="<?= $finalImage; ?>" alt="Foto Pengajar" class="profile-img mb-3">

                        <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                        <p><strong>TUTOR <?= htmlspecialchars($row['mapel']); ?></strong></p>

                        <div class="btn-group-vertical">
                            <a href="detail_pengajar.php?id=<?= $row['pengajar_id']; ?>" class="btn btn-detail">Lihat Detail</a>
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
</body>
</html>