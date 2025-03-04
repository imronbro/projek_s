<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scover";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data user berdasarkan email dari session
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT pengajar_id, full_name, email, alamat, gambar, ttl, mapel, nohp FROM mentor WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Ambil rata-rata rating dari tabel rating_pengajar
    $pengajar_id = $data['pengajar_id'];
    $rating_query = "SELECT AVG(rating) as avg_rating FROM rating_pengajar WHERE pengajar_id = ?";
    $stmt_rating = mysqli_prepare($conn, $rating_query);
    mysqli_stmt_bind_param($stmt_rating, "i", $pengajar_id);
    mysqli_stmt_execute($stmt_rating);
    $rating_result = mysqli_stmt_get_result($stmt_rating);
    $rating_data = mysqli_fetch_assoc($rating_result);
    mysqli_stmt_close($stmt_rating);

    $average_rating = round($rating_data['avg_rating'], 1) ?? 0;
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login_mentor.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/logout.css">
    <link rel="stylesheet" href="css/home.css">
    <style>
              body {
            background-color: #003049;
            color: #fabe49;
        }
        .card {
            background-color: #145375;
            color: white;
            border: 2px solid white;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 20px;
        }
        .profile-section {
            text-align: center;
            margin-right: 20px;
        }

        .rating {
            font-size: 18px;
            color: #ffd700;
            margin-top: 10px;
        }
        .profile-info {
            flex-grow: 1;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 20px;
            border: 3px solid #faaf1d;
        }
        .btn-primary {
            background-color: #0271ab;
            border-color: #0271ab;
        }
        .btn-secondary {
            background-color: #faaf1d;
            border-color:rgb(88, 79, 59);
            color: #003049;
        }
        .btn-primary:hover {
            background-color: #145375;
            border-color: #145375;
        }
        .btn-secondary:hover {
            background-color: #fabe49;
            border-color: #fabe49;
        }
        h2 {
            color: #faaf1d;
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Presensi</a></li>
            <li><a href="siswa.php" >Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="jurnal.php">Jurnal</a></li>
            <li><a href="profile_mentor.php" class="active">Profil</a></li>
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
        <h2 class="text-center">Profil Pengguna Pengajar</h2>
        <div class="card p-3 shadow mb-4 text-center">
            <?php 
            $imagePath = "uploads/" . basename(htmlspecialchars($data['gambar']));
            if (!empty($data['gambar']) && file_exists($imagePath)) {
            ?>
                <img src="<?= $imagePath; ?>" alt="Foto Profil" class="profile-img">
            <?php 
            } else { 
            ?>
                <img src="uploads1/default.png" alt="Foto Profil Default" class="profile-img">
            <?php 
            } 
            ?>
            <div class="rating">⭐ <?= $average_rating; ?>/5</div>
            <a href="riwayat_rating.php">Riwayat Rating</a>
            <div class="profile-info">
                <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['full_name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
                <p><strong>Mata Pelajaran:</strong> <?= htmlspecialchars($data['mapel'] ?? '-'); ?></p>
                <p><strong>TTL:</strong> <?= htmlspecialchars($data['ttl'] ?? '-'); ?></p>
                <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat'] ?? '-'); ?></p>
                <p><strong>No HP:</strong> <?= htmlspecialchars($data['nohp'] ?? '-'); ?></p>
            </div>
        </div>
        <a href="home_mentor.php" class="btn btn-primary">Kembali</a>
        <a href="edit_profile_mentor.php" class="btn btn-secondary">Edit Profil</a>
    </div>
    <script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
</body>
</html>
