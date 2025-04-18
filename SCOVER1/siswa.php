<?php
session_start();
include 'koneksi.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT full_name, kelas, alamat, gambar, sekolah, nohp FROM siswa";
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
    <title>Dashboard Mentor - Siswa</title>
    <link rel="stylesheet" href="css/navbar.css">
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
            margin-top: 100px;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #145375;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box input[type="text"] {
            padding: 10px 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 250px;
        }

        .search-box button {
            background-color: #f1c40f;
            color: #0b3c5d;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: #d4ac0d;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .col-md-4 {
            width: 40%;
            display: flex;
            justify-content: center;
        }

        .card {
            background-color: #fff;
            height: 350px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ccc;
            margin: 0 auto 15px;
        }

        .card h4 {
            margin: 0;
            font-weight: bold;
            color: #145375;
        }

        .card p {
            margin: 4px 0;
            font-size: 14px;
        }

        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }

        .btn-whatsapp {
            background-color: #e6c200;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-whatsapp:hover {
            background-color: #145375;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Mentor</h1>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Jurnal</a></li>
            <li><a href="siswa.php" class="active">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Daftar Siswa</h2>

        <!-- Form Pencarian -->
        <form action="siswa.php" method="get" class="search-form">
            <div class="search-box">
                <input type="text" name="search" placeholder="Cari Nama Siswa..." class="search-input" value="<?= htmlspecialchars($search); ?>">
                <button type="submit">Cari</button>
            </div>
        </form>

        <div class="row">
            <?php if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $imagePath = "uploads/" . basename(htmlspecialchars($row['gambar']));
                    $defaultImage = "uploads1/default.png";
                    $finalImage = (!empty($row['gambar']) && file_exists($imagePath)) ? $imagePath : $defaultImage;
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="<?= $finalImage; ?>" alt="Foto Siswa" class="profile-img">
                            <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                            <p><?= htmlspecialchars($row['sekolah']); ?></p>
                            <p>Kelas: <?= htmlspecialchars($row['kelas']); ?></p>
                            <p><?= htmlspecialchars($row['alamat']); ?></p>
                            <div class="btn-group-vertical">
                                <a href="https://wa.me/<?= htmlspecialchars($row['nohp']); ?>" target="_blank" class="btn-whatsapp">Hubungi via WhatsApp</a>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="col-12 text-center">
                    <p class="text-danger">Siswa tidak ditemukan.</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="js/menu.js" defer></script>
    <script src="js/logout.js" defer></script>
</body>

</html>

<?php mysqli_close($conn); ?>
