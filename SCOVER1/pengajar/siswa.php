<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Siswa</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="css/pengajar.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
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
            margin-top: 90px;
            padding: 50px 20px;
            text-align: center;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            color: #145375;
            margin-bottom: 30px;
        }

        /* Search Form */
        .search-form {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 40px;
        }

        .search-form input[type="text"] {
            padding: 10px 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 200px;
            max-width: 100%;
        }

        .search-form button {
            background-color: #f1c40f;
            color: #0b3c5d;
            border: none;
            border-radius: 8px;
            padding: 10px 35px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-form button:hover {
            background-color: #d4ac0d;
        }

        /* Card Container */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        /* Card Column */
        .col-md-4 {
            flex: 1 1 calc(20% - 30px);
            /* 5 kolom horizontal */
            max-width: calc(20% - 30px);
            display: flex;
            justify-content: center;
        }

        /* Responsive adjustments tetap boleh kamu sesuaikan jika ingin */

        /* Card Styles */
        .card {
            background-color: #145375;
            /* warna biru */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            /* warna teks putih agar kontras */
        }

        /* Ganti warna heading di card */
        .card h4 {
            margin: 0 0 10px;
            color: #fff;
            font-weight: 600;
            word-wrap: break-word;
        }

        /* Tombol Detail (ubah warna background dan border agar cocok dengan card biru) */
        .btn-detail {
            background-color: #f1c40f;
            /* kuning cerah */
            color: #145375;
            border: 2px solid #f1c40f;
        }

        .btn-detail:hover {
            background-color: #d4ac0d;
            color: #fff;
        }

        /* Tombol WhatsApp (ubah supaya kontras) */
        .btn-whatsapp {
            background-color: #e6c200;
            color: #145375;
            border: 2px solid #e6c200;
        }

        .btn-whatsapp:hover {
            background-color: #fff;
            color: #145375;
            border-color: #145375;
        }


        /* Responsive Column Adjustments */
        @media (max-width: 992px) {
            .col-md-4 {
                flex: 1 1 calc(50% - 30px);
                max-width: calc(50% - 30px);
            }
        }

        @media (max-width: 576px) {
            .col-md-4 {
                flex: 1 1 100%;
                max-width: 100%;
            }
        }

        /* Card Styles */
       

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .card h4 {
            margin: 0 0 10px;
            color: #fff;
            font-weight: 600;
            word-wrap: break-word;
        }

        /* Button Group */
        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
            width: 100%;
            max-width: 200px;
        }

        .btn-detail,
        .btn-whatsapp {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 900;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .btn-detail {
            background-color: #fff;
            color: #145375;
            border: 2px solid #145375;
        }

        .btn-detail:hover {
            background-color: #145375;
            color: #fff;
        }

        .btn-whatsapp {
            background-color: #e6c200;
            color: #145375;
            border: 2px solid #e6c200;
        }

        .btn-whatsapp:hover {
            background-color: #fff;
            color: #145375;
            border-color: #145375;
        }

        /* Alert Not Found */
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
            <img src="images/foto4.png" alt="Logo" />
        </div>
        <h1 class="title">Dashboard Mentor</h1>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Jurnal</a></li>
            <li><a href="proses_presensi.php">Presensi Siswa</a></li>
            <li><a href="siswa.php" class="active">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

   <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Siswa</h2>

        <!-- Form Pencarian -->
        <form action="siswa.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Cari Nama Siswa..." />
            <button type="submit">Cari</button>
        </form>

        <div class="card-container">
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) {
                    $imagePath = "../uploads/" . basename(htmlspecialchars($row['gambar']));
                    $defaultImage = "../uploads1/default.jpg";
                    $finalImage = (!empty($row['gambar']) && file_exists($imagePath)) ? $imagePath : $defaultImage;
                ?>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <img src="<?= $finalImage; ?>" alt="Foto Siswa" class="profile-img" />
                            <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                            <div class="btn-group-vertical">
                                <a href="detail_siswa.php?id=<?= $row['siswa_id']; ?>" class="btn-detail">Lihat Detail</a>
                                <a href="https://wa.me/<?= htmlspecialchars($row['nohp']); ?>" target="_blank" class="btn-whatsapp">Hubungi via WhatsApp</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert-notfound">
                    <p>😢 Maaf, siswa tidak ditemukan. Silakan coba kata kunci lain.</p>
                </div>
            <?php } ?>
        </div>
    </div>

     <script src="js/menu.js" defer>
        

        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>

</html>