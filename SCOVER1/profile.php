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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #003049;
            color: #fabe49;
        }
        .card {
            background-color: #145375;
            color:rgb(255, 255, 255);
            border: 2px solid rgb(255, 255, 255);
        }

        .card p {
            text-align:justify;
            padding-left:500px;
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
        .content {
  width: 80%;
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin: 20px 0;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #003049;
  padding: 15px;
  color: white;
  width: 100%;
}

.navbar .logo img {
  width: 70px;
  height: auto;
}

.navbar .nav-links {
  list-style: none;
  display: flex;
  padding: 0;
}

.navbar .nav-links li {
  margin: 10px 15px;
  position: relative;
}

.navbar .nav-links a {
  text-decoration: none;
  color: white;
  padding-bottom: 5px;
  transition: all 0.3s;
  position: relative;
}

.navbar .nav-links a::after {
  content: "";
  display: block;
  width: 0;
  height: 2px;
  background-color: #fabe49;
  transition: width 0.3s ease-in-out;
  position: absolute;
  bottom: -2px;
  left: 0;
}

.navbar .nav-links a:hover::after {
  width: 100%;
}

.navbar .nav-links a.active::after {
  width: 100%;
  background-color: #fabe49;
}

.menu-icon {
  display: none;
  flex-direction: column;
  cursor: pointer;
}

.menu-icon span {
  width: 30px;
  height: 4px;
  background-color: white;
  margin: 4px 0;
}

@media (max-width: 768px) {
  .navbar .nav-links {
    display: none;
    flex-direction: column;
    position: absolute;
    top: 60px;
    left: 0;
    background-color: #003049;
    width: 100%;
    padding: 10px 0;
  }
  .navbar .nav-links.active {
    display: flex;
  }
  .navbar .nav-links li {
    margin: 10px 0;
    text-align: center;
  }
  .menu-icon {
    display: flex;
  }
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
            <li><a href="home.php" class="active">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Profil Pengguna</h2>
        <div class="card p-3 shadow mb-4 text-center">
            <?php 
            $imagePath = "uploads/" . basename(htmlspecialchars($data['gambar']));
            if (!empty($data['gambar']) && file_exists($imagePath)) {
            ?>
                <img src="<?= $imagePath; ?>" alt="Foto Profil" class="profile-img">
            <?php 
            } else { 
            ?>
                <img src="uploads/default.png" alt="Foto Profil Default" class="profile-img">
            <?php 
            } 
            ?>
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['full_name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
            <p><strong>Sekolah:</strong> <?= htmlspecialchars($data['sekolah'] ?? '-'); ?></p>
            <p><strong>Kelas:</strong> <?= htmlspecialchars($data['kelas'] ?? '-'); ?></p>
            <p><strong>TTL:</strong> <?= htmlspecialchars($data['ttl'] ?? '-'); ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat'] ?? '-'); ?></p>
            <p><strong>No HP:</strong> <?= htmlspecialchars($data['nohp'] ?? '-'); ?></p>
        </div>
        <a href="home.php" class="btn btn-primary">Kembali</a>
        <a href="edit_profile.php" class="btn btn-secondary">Edit Profil</a>
    </div>
</body>
<script>
        document.addEventListener("DOMContentLoaded", function () {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("tanggal").value = today;
        });

        function toggleKomentar() {
            let kehadiran = document.getElementById("kehadiran").value;
            let komentarContainer = document.getElementById("komentar-container");

            if (kehadiran === "Izin" || kehadiran === "Sakit") {
                komentarContainer.style.display = "block";
            } else {
                komentarContainer.style.display = "none";
            }
        }
        
        function toggleMenu() {
            document.querySelector(".nav-links").classList.toggle("active");
        }
</html>
