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
        width: 95%;
        max-width: 900px;
        margin: auto;
        margin-top: 70px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .table-wrapper {
        overflow-x: auto;
        margin-bottom: 15px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #145375;
        font-size: 1.8rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }

    td:nth-child(4) {
        white-space: normal; /* Allow comment text to wrap */
        min-width: 200px;
    }

    th {
        background-color: #e6c200;
        color: #145375;
        position: sticky;
        top: 0;
    }

    .btn {
        background-color: #e6c200;
        color: #145375;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        margin-top: 20px;
        display: inline-block;
        width: auto;
    }

    .scroll-hint {
        display: none;
        text-align: center;
        color: #666;
        font-size: 0.9rem;
        margin: 10px 0;
    }

    @media (max-width: 768px) {
        body {
            padding-top: 80px;
        }

        .container {
            width: 100%;
            padding: 6px;
            border-radius: 0;
        }

        h2 {
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .scroll-hint {
            display: block;
        }

        table, th, td {
            font-size: 14px;
        }

        th, td {
            padding: 10px;
        }

        .btn {
            width: 100%;
            margin-top: 15px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding-top: 70px;
        }

        h2 {
            font-size: 1.2rem;
        }

        table, th, td {
            font-size: 13px;
        }

        th, td {
            padding: 8px;
        }

        td:nth-child(4) {
            min-width: 150px;
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
      <h2>Riwayat Rating Mentor</h2>

    <div class="scroll-hint">
        ← Geser untuk melihat selengkapnya →
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Rating</th>
            <th>Komentar</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result_rating->num_rows > 0) {
              $no = 1; // Initialize counter
              while ($row = $result_rating->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $no++ . "</td>"; // Display and increment counter
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
    </div>

    <a href="profile_mentor.php" class="btn">Kembali</a>
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
