<?php
session_start();
include 'koneksi.php'; // File koneksi database

// Periksa apakah user sudah login (menggunakan email dalam session)
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email']; // Ambil email dari session

// Ambil siswa_id berdasarkan email
$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $siswa_id = $row['siswa_id'];
    $full_name = $row['full_name']; // Ambil nama lengkap siswa
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

// Ambil data presensi siswa berdasarkan siswa_id
$sql = "SELECT tanggal, sesi, status, komentar, waktu_presensi 
        FROM presensi_siswa 
        WHERE siswa_id = ? 
        ORDER BY tanggal DESC, waktu_presensi DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Presensi</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    body {
      font-family: Arial, sans-serif; /* Gunakan font yang sama dengan navbar */
      color: white; /* Warna teks default */
      background-color: #003049; /* Warna latar belakang */
      margin: 0;
      padding: 0;
    }

    .navbar {
      position: fixed; /* Navbar tetap di atas */
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    .content {
      margin-top: 100px; /* Tambahkan jarak agar tidak tertutup navbar */
      padding: 20px;
      background-color: #145375; /* Warna latar belakang konten */
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      color: #fabe49; /* Warna teks */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      background-color: #145375; /* Warna latar belakang tabel */
      color: #fabe49; /* Warna teks */
    }

    table th, table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    table th {
      background-color: #faaf1d; /* Warna kuning cerah */
      color: #003049; /* Warna teks gelap */
    }

    table tr:nth-child(even) {
      background-color: #0271ab; /* Warna biru terang untuk baris genap */
    }

    table tr:nth-child(odd) {
      background-color: #145375; /* Warna biru gelap untuk baris ganjil */
    }

    .back-button {
      font-family: Arial, sans-serif; /* Gunakan font yang sama */
      font-size: 16px; /* Ukuran font tombol */
      background-color: #3498db; /* Warna tombol */
      color: white; /* Warna teks tombol */
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .back-button:hover {
      background-color: #2980b9; /* Warna hover */
      transform: scale(1.05); /* Efek zoom */
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
      .content {
        margin-top: 120px; /* Sesuaikan jarak untuk layar kecil */
        padding: 15px; /* Kurangi padding */
        font-size: 14px; /* Ukuran font lebih kecil */
      }

      h2 {
        font-size: 1.5em; /* Sesuaikan ukuran judul */
        text-align: center;
      }

      table {
        font-size: 12px; /* Ukuran font tabel lebih kecil */
      }

      table th, table td {
        padding: 8px; /* Kurangi padding tabel */
      }

      .back-button {
        font-size: 14px; /* Ukuran font tombol lebih kecil */
        padding: 8px 15px; /* Sesuaikan padding tombol */
      }
    }

    @media (max-width: 480px) {
      .content {
        margin-top: 100px; /* Sesuaikan jarak untuk layar HP */
        padding: 10px; /* Kurangi padding lebih jauh */
        font-size: 12px; /* Ukuran font lebih kecil */
      }

      h2 {
        font-size: 1.2em; /* Ukuran judul lebih kecil */
      }

      table {
        font-size: 10px; /* Ukuran font tabel lebih kecil */
      }

      table th, table td {
        padding: 5px; /* Kurangi padding tabel lebih jauh */
      }

      .back-button {
        font-size: 12px; /* Ukuran font tombol lebih kecil */
        padding: 6px 10px; /* Sesuaikan padding tombol */
        width: 100%; /* Tombol memenuhi lebar layar */
        text-align: center;
      }

      .navbar .nav-links {
        flex-direction: column; /* Atur link navbar menjadi kolom */
        gap: 10px; /* Tambahkan jarak antar link */
      }

      .navbar .nav-links a {
        font-size: 14px; /* Ukuran font link lebih kecil */
        padding: 8px; /* Sesuaikan padding link */
      }

      .navbar .menu-icon {
        display: flex; /* Tampilkan menu hamburger */
      }

      .navbar .nav-links {
        display: none; /* Sembunyikan link navbar secara default */
        flex-direction: column;
        position: absolute;
        top: 60px;
        right: 0;
        width: 100%;
        background-color: #003049;
        padding: 20px 0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        z-index: 1000;
      }

      .navbar .nav-links.active {
        display: flex; /* Tampilkan link navbar saat menu aktif */
      }
    }
    </style>

</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
            <span class="logo-text">Scover Center</span>
        </div>
        <h1 class="title">Dashboard Siswa</h1>
    <ul class="nav-links">
        <li><a href="home.php" class="active">Presensi</a></li>
        <li><a href="pengajar.php">Pengajar</a></li>
        <li><a href="rating.php">Rating</a></li>
        <li><a href="jadwal1.php">Jadwal</a></li>
        <li><a href="nilai_siswa.php">Nilai</a></li>
        <li><a href="profile.php">Profil</a></li>
        <li><a href="kontak.php">Kontak</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>

    <div class="content">
        <h2>Riwayat Presensi - <?php echo htmlspecialchars($full_name); ?></h2>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Status</th>
                    <th>Komentar</th>
                    <th>Dibuat Pada</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sesi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>" . (!empty($row['komentar']) ? htmlspecialchars($row['komentar']) : '-') . "</td>";
                        echo "<td>" . htmlspecialchars($row['waktu_presensi']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data presensi</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="button-container">
    <button class="back-button" onclick="goBack()">Kembali</button>
</div>
    </div>
    <script src="js/menu.js" defer></script>
    <script src="js/logout.js" defer></script>
    <script>        function goBack() {
        window.history.back();
    }
    </script>
</body>



</html>

<?php
// Tutup statement & koneksi database
$stmt->close();
$conn->close();
?>
