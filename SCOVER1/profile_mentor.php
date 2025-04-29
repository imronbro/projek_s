<?php
session_start();
include 'koneksi.php';


$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT pengajar_id, full_name, email, alamat, gambar, ttl, mapel, nohp FROM mentor WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

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
    <title>Dashboard Mentor</title>
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
        max-width: 900px;
        margin: 0 auto;
        padding: 40px;
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }
    }

    .card {
        background-color: #145375;
        color: white;
        border: 2px solid white;
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 20px;
        border-radius: 10px;
        gap: 20px;
    }

    .profile-section {
        text-align: center;
        margin-right: 20px;
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

    .profile-info {
        flex-grow: 1;
    }

    .rating {
        font-size: 18px;
        color: #ffd700;
        margin-top: 10px;
    }

    h2 {
        color: #145375;
        text-align: center;
    }

    .btn-primary,
    .btn-secondary {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        margin: 10px 5px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #145375;
        color: white;
        
    }

    .btn-primary:hover {
        background-color: #e6c200;
        
    }

    .btn-secondary {
        background-color: #e6c200;
        color: #145375;
    }

    .btn-secondary:hover {
        background-color: #145375;
        color: #fff;
    }

    .star {
        font-size: 24px;
        display: inline-block;
    }

    .star.full {
        color: #ffd700;
    }

    .star.empty {
        color: #d3d3d3;
    }

    .star.partial {
        background: linear-gradient(to right, #ffd700 var(--fill), #d3d3d3 var(--fill));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
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
            <li><a href="proses_presensi.php">Presensi Siswa</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php" class="active">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
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
            <div class="rating">
                <?php
    $fullStars = floor($average_rating); 
    $decimal = $average_rating - $fullStars;
    $emptyStars = 5 - ceil($average_rating); 
    for ($i = 0; $i < $fullStars; $i++) {
        echo '<span class="star full">★</span>';
    }
    if ($decimal > 0) {
        $percentage = $decimal * 100; 
        echo '<span class="star partial" style="--fill:' . $percentage . '%;">★</span>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        echo '<span class="star empty">★</span>';
    }
    ?>
                <?= number_format($average_rating, 1); ?>/5
            </div>


            <div class="profile-info">
                <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['full_name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
                <p><strong>Tutor:</strong> <?= htmlspecialchars($data['mapel'] ?? '-'); ?></p>
                <p><strong>TTL:</strong> <?= htmlspecialchars($data['ttl'] ?? '-'); ?></p>
                <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat'] ?? '-'); ?></p>
                <p><strong>No HP:</strong> <?= htmlspecialchars($data['nohp'] ?? '-'); ?></p>
            </div>
        </div>
        <a href="edit_profile_mentor.php" class="btn btn-secondary">Edit Profil</a>
        <a href="riwayat_rating.php" class="btn btn-secondary">Riwayat Rating</a>
    </div>
    <script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
</body>

</html>