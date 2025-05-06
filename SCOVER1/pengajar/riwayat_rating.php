<?php
session_start();
include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta'); 

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Mendapatkan email pengajar yang sedang login
$pengajar_email = $_SESSION['user_email'];

// Mendapatkan pengajar_id berdasarkan email
$query_pengajar = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt_pengajar = $conn->prepare($query_pengajar);
$stmt_pengajar->bind_param("s", $pengajar_email);
$stmt_pengajar->execute();
$result_pengajar = $stmt_pengajar->get_result();

if ($result_pengajar->num_rows > 0) {
    $pengajar = $result_pengajar->fetch_assoc();
    $pengajar_id = $pengajar['pengajar_id'];
    $pengajar_name = $pengajar['full_name'];

    // Mengambil data rating untuk pengajar yang sedang login
    $query_rating = "
        SELECT rp.id, s.full_name AS nama_siswa, rp.rating, rp.komentar, rp.created_at
        FROM rating_pengajar rp
        INNER JOIN siswa s ON rp.siswa_id = s.siswa_id
        WHERE rp.pengajar_id = ?
        ORDER BY rp.created_at DESC
    ";
    $stmt_rating = $conn->prepare($query_rating);
    $stmt_rating->bind_param("i", $pengajar_id);
    $stmt_rating->execute();
    $result_rating = $stmt_rating->get_result();
} else {
    echo "Pengajar tidak ditemukan.";
    exit();
}
?>
<!-- Setelah kode PHP di atas -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Rating - <?php echo htmlspecialchars($pengajar_name); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/navbar.css">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins';
      background-color: #fff;
      color: #145375;
      margin: 0;
      padding: 0;
      padding-top: 100px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      padding: 30px;
      background-color: #f9f9f9;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #145375;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #e6c200;
      color: #145375;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .btn {
      background-color: #e6c200;
      color: #145375;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s ease;
      text-decoration: none;
      text-align: center;
      margin-top: 20px;
      display: inline-block;
    }

    .btn:hover {
      background-color: #145375;
      color: white;
    }

    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }

      table, th, td {
        font-size: 14px;
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
      <span></span>
      <span></span>
      <span></span>
    </div>
  </nav>

  <div class="container">
    <h2>Riwayat Rating untuk <?php echo htmlspecialchars($pengajar_name); ?></h2>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Siswa</th>
          <th>Rating</th>
          <th>Komentar</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result_rating->num_rows > 0) {
          while ($row = $result_rating->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
            echo "<td>" . str_repeat("⭐", $row['rating']) . "</td>";
            echo "<td>" . htmlspecialchars($row['komentar']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='5' style='text-align:center;'>Belum ada rating yang diberikan.</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <a href="javascript:history.back()" class="btn">Kembali</a>
  </div>

  <div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
  </div>

  <script src="js/logout.js" defer></script>
  <script src="js/menu.js" defer></script>
</body>
</html>
