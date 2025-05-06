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

    /* Container */
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

    /* Card */
    .card {
        background-color: #145375;
        color: white;
        border: 2px solid white;
        display: flex;
        flex-direction: row; /* Default: Foto dan info dalam satu baris */
        align-items: center;
        padding: 20px;
        border-radius: 10px;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .card {
            flex-direction: column; /* Ubah menjadi kolom pada layar kecil */
            text-align: center; /* Pusatkan konten */
        }
    }

    /* Foto Profil */
    .profile-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        margin: 0 auto 20px;
        border: 3px solid #faaf1d;
    }

    /* Rating */
    .rating {
        font-size: 18px;
        color: #ffd700;
        margin-top: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }

    @media (max-width: 768px) {
        .rating {
            margin-top: 20px; /* Tambahkan jarak di bawah foto profil */
            font-size: 16px; /* Ukuran font lebih kecil */
        }
    }

    /* Bintang */
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

    h2 {
        color: #145375;
        text-align: center;
    }

    .notification {
  display: none; /* Sembunyikan elemen secara default */
  position: fixed; /* Tetap di posisi layar */
  top: 50%; /* Posisikan di tengah vertikal */
  left: 50%; /* Posisikan di tengah horizontal */
  transform: translate(-50%, -50%); /* Pastikan elemen benar-benar di tengah */
  background-color: #f9f9f9; /* Warna latar belakang */
  color: #145375; /* Warna teks */
  padding: 20px; /* Ruang di dalam elemen */
  border-radius: 10px; /* Sudut melengkung */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Bayangan untuk efek melayang */
  z-index: 1000; /* Pastikan elemen berada di atas elemen lainnya */
  text-align: center; /* Teks rata tengah */
  width: 300px; /* Lebar tetap */
  animation: slideIn 0.3s ease forwards; /* Animasi muncul */
}

.notification.hide {
  animation: slideOut 0.3s ease forwards; /* Animasi menghilang */
}

.notification p {
  margin-bottom: 20px; /* Jarak bawah untuk teks */
  font-size: 16px; /* Ukuran font */
  font-weight: bold; /* Teks tebal */
}

/* Tombol di dalam notifikasi */
.notification-buttons {
  display: flex; /* Tampilkan tombol secara horizontal */
  justify-content: space-between; /* Jarak antar tombol */
  gap: 10px; /* Tambahkan jarak antar tombol */
}

.notification-buttons .btn {
  flex: 1; /* Tombol mengambil ruang yang sama */
  text-align: center; /* Teks rata tengah */
  padding: 10px; /* Ruang di dalam tombol */
  border-radius: 6px; /* Sudut melengkung */
  font-weight: bold; /* Teks tebal */
  cursor: pointer; /* Ubah kursor menjadi pointer */
  transition: 0.3s ease; /* Animasi transisi */
  text-decoration: none; /* Hilangkan garis bawah */
}

/* Tombol Batal */
.notification-buttons .btn-secondary {
  background-color: #145375; /* Warna biru */
  color: white; /* Warna teks */
}

.notification-buttons .btn-secondary:hover {
  background-color: #e6c200; /* Warna kuning saat hover */
  color: #145375; /* Warna teks saat hover */
}

/* Tombol Keluar */
.notification-buttons .btn-danger {
  background-color: #e74c3c; /* Warna merah */
  color: white; /* Warna teks */
  border: none; /* Hilangkan border */
}

.notification-buttons .btn-danger:hover {
  background-color: #c0392b; /* Warna merah lebih gelap saat hover */
  transform: scale(1.05); /* Efek zoom saat hover */
}

/* Tombol Edit Profil */
.edit-profile-btn {
  background-color: #e6c200; /* Warna latar belakang */
  color: #145375; /* Warna teks */
  padding: 10px 20px; /* Ruang di dalam tombol */
  border-radius: 5px; /* Sudut melengkung */
  font-weight: bold; /* Teks tebal */
  text-decoration: none; /* Hilangkan garis bawah */
  display: inline-block; /* Tampilkan sebagai tombol */
  transition: background-color 0.3s ease, color 0.3s ease; /* Animasi transisi */
}

.edit-profile-btn:hover {
  background-color: #145375; /* Warna latar belakang saat hover */
  color: #fff; /* Warna teks saat hover */
}

/* Tombol Riwayat Rating */
.riwayat-rating-btn {
  background-color: #faaf1d; /* Warna latar belakang */
  color: #145375; /* Warna teks */
  padding: 10px 20px; /* Ruang di dalam tombol */
  border-radius: 5px; /* Sudut melengkung */
  font-weight: bold; /* Teks tebal */
  text-decoration: none; /* Hilangkan garis bawah */
  display: inline-block; /* Tampilkan sebagai tombol */
  transition: background-color 0.3s ease, color 0.3s ease; /* Animasi transisi */
}

.riwayat-rating-btn:hover {
  background-color: #145375; /* Warna latar belakang saat hover */
  color: #fff; /* Warna teks saat hover */
}

/* Animasi untuk notifikasi pop-up */
@keyframes slideIn {
  0% {
    opacity: 0; /* Tidak terlihat */
    transform: translate(-50%, -60%); /* Mulai sedikit di atas posisi tengah */
  }
  100% {
    opacity: 1; /* Terlihat */
    transform: translate(-50%, -50%); /* Berakhir di posisi tengah */
  }
}

@keyframes slideOut {
  0% {
    opacity: 1; /* Terlihat */
    transform: translate(-50%, -50%); /* Mulai di posisi tengah */
  }
  100% {
    opacity: 0; /* Tidak terlihat */
    transform: translate(
      -50%,
      -60%
    ); /* Menghilang sedikit di atas posisi tengah */
  }
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
            <img src="uploads1/default.jpg" alt="Foto Profil Default" class="profile-img">
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
        <a href="edit_profile_mentor.php" class="btn btn-secondary edit-profile-btn">Edit Profil</a>
        <a href="riwayat_rating.php" class="btn btn-secondary riwayat-rating-btn">Riwayat Rating</a>
    </div>
    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
    <script src="js/menu.js" defer></script>
</body>

</html>