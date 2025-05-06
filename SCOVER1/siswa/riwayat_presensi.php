<?php
session_start();
include '../koneksi.php'; 

date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu Anda

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

// Ambil tanggal dan bulan yang dipilih dari filter (jika ada)
$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d'); // Default ke hari ini
$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m'); // Default ke bulan ini

// Query untuk mengambil data presensi berdasarkan siswa_id, tanggal, atau bulan
if (isset($_GET['tanggal'])) {
    // Filter berdasarkan tanggal
    $sql = "SELECT id, tanggal, sesi, status, komentar, waktu_presensi 
            FROM presensi_siswa 
            WHERE siswa_id = ? AND tanggal = ?
            ORDER BY waktu_presensi DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $siswa_id, $selected_date);
} elseif (isset($_GET['bulan'])) {
    // Filter berdasarkan bulan
    $sql = "SELECT id, tanggal, sesi, status, komentar, waktu_presensi 
            FROM presensi_siswa 
            WHERE siswa_id = ? AND DATE_FORMAT(tanggal, '%Y-%m') = ?
            ORDER BY waktu_presensi DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $siswa_id, $selected_month);
} else {
    // Default: Tampilkan data hari ini
    $sql = "SELECT id, tanggal, sesi, status, komentar, waktu_presensi 
            FROM presensi_siswa 
            WHERE siswa_id = ? AND tanggal = ?
            ORDER BY waktu_presensi DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $siswa_id, $selected_date);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Presensi</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/navbar.css">
  <style>
    body {
      font-family: 'Poppins';
      /* Gunakan font yang sama dengan navbar */
      color: white;
      /* Warna teks default */
      background-color: #fff;
      /* Warna latar belakang */
      margin: 0;
      padding: 0;
    }


    .content {
      margin-top: 100px;
      /* Tambahkan jarak agar tidak tertutup navbar */
      padding: 20px;
      background-color: #f4f4f4 ;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      color: #145375;
      /* Warna teks */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      background-color: #fff;
      /* Warna latar belakang tabel */
      color: #fff;
      /* Warna teks */
    }

    table th,
    table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
      background-color: #fff;
      color :rgb(0, 0, 0);
    }

    table th {
      background-color: #145375;
      /* Warna kuning cerah */
      color: #fff;
      /* Warna teks gelap */
    }

    table tr:nth-child(even) {
      background-color: #0271ab;
      /* Warna biru terang untuk baris genap */
    }

    table tr:nth-child(odd) {
      background-color: #145375;
      /* Warna biru gelap untuk baris ganjil */
    }

    .back-button {
      font-family: Arial, sans-serif;
      /* Gunakan font yang sama */
      font-size: 16px;
      /* Ukuran font tombol */
      background-color: #145375;
      /* Warna tombol */
      color: white;
      /* Warna teks tombol */
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .back-button:hover {
      background-color: #145375;
      /* Warna hover */
      transform: scale(1.05);
      /* Efek zoom */
    }

    .btn.edit-btn {
        display: inline-block;
        padding: 5px 10px;
        background-color: #faaf1d;
        color: #003049;
        text-decoration: none;
        border-radius: 5px;
        font-size: 0.9em;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn.edit-btn:hover {
        background-color: #fabe49;
    }

    .filter-form {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-form label {
        font-weight: bold;
        color: #fabe49;
    }

    .filter-form input[type="date"],
    .filter-form input[type="month"] {
        padding: 5px 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .filter-form button {
        padding: 5px 15px;
        background-color: #faaf1d;
        color: #003049;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .filter-form button:hover {
        background-color: #fabe49;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
      .content {
        margin-top: 120px;
        /* Sesuaikan jarak untuk layar kecil */
        padding: 15px;
        /* Kurangi padding */
        font-size: 14px;
        /* Ukuran font lebih kecil */
      }

      h2 {
        font-size: 1.5em;
        /* Sesuaikan ukuran judul */
        text-align: center;
      }

      table {
        font-size: 12px;
        /* Ukuran font tabel lebih kecil */
      }

      table th,
      table td {
        padding: 8px;
        /* Kurangi padding tabel */
      }

      .back-button {
        font-size: 14px;
        /* Ukuran font tombol lebih kecil */
        padding: 8px 15px;
        /* Sesuaikan padding tombol */
      }
    }

    @media (max-width: 480px) {
      .content {
        margin-top: 100px;
        /* Sesuaikan jarak untuk layar HP */
        padding: 10px;
        /* Kurangi padding lebih jauh */
        font-size: 12px;
        /* Ukuran font lebih kecil */
      }

      h2 {
        font-size: 1.2em;
        /* Ukuran judul lebih kecil */
      }

      table {
        font-size: 10px;
        /* Ukuran font tabel lebih kecil */
      }

      table th,
      table td {
        padding: 5px;
        /* Kurangi padding tabel lebih jauh */
      }

      .back-button {
        font-size: 12px;
        /* Ukuran font tombol lebih kecil */
        padding: 6px 10px;
        /* Sesuaikan padding tombol */
        width: 100%;
        /* Tombol memenuhi lebar layar */
        text-align: center;
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
      <li><a href="home.php" class="active">Jurnal</a></li>
      <li><a href="pengajar.php">Pengajar</a></li>
      <li><a href="rating.php">Rating</a></li>
      <li><a href="jadwal1.php">Jadwal</a></li>
      <li><a href="nilai_siswa.php">Nilai</a></li>
      <li><a href="profile.php">Profil</a></li>
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

    <!-- Filter Tanggal dan Bulan -->
    <form method="GET" class="filter-form">
        <label for="tanggal">Pilih Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($selected_date); ?>">

        <label for="bulan">Pilih Bulan:</label>
        <input type="month" id="bulan" name="bulan" value="<?php echo htmlspecialchars($selected_month); ?>">

        <button type="submit">Tampilkan</button>
    </form>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Status</th>
                <th>Komentar</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    $waktu_presensi = new DateTime($row['waktu_presensi']);
                    $current_time = new DateTime();
                    $time_difference = $current_time->getTimestamp() - $waktu_presensi->getTimestamp();

                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sesi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . (!empty($row['komentar']) ? htmlspecialchars($row['komentar']) : '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row['waktu_presensi']) . "</td>";
                    echo "<td>";

                    if ($time_difference >= 0 && $time_difference <= 1800) { // 1800 detik = 30 menit
                        echo "<a href='edit_riwayat.php?id=" . $row['id'] . "' class='btn edit-btn'>Edit</a>";
                    } else {
                        echo "-";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada data presensi untuk filter ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="button-container">
        <button class="back-button" onclick="goBack()">Kembali</button>
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