<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
$user_email = $_SESSION['user_email'];

$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $siswa_id = $row['siswa_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

$sql = "SELECT * FROM jadwal_siswa WHERE siswa_id = ? ORDER BY tanggal, sesi";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Siswa</title>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/jadwal.css">
</head>
<body>
<nav class="navbar">
      <div class="logo">
        <img src="images/foto4.png" alt="Logo" />
      </div>
      <h1 class="title">Dashboard Siswa</h1>
      <ul class="nav-links">
        <li><a href="home.php">Presensi</a></li>
        <li><a href="pengajar.php">Pengajar</a></li>
        <li><a href="rating.php">Rating</a></li>
        <li><a href="jadwal1.php" class="active">Jadwal</a></li>
        <li><a href="nilai_siswa.php">Nilai</a></li>
        <li><a href="profile.php">Profil</a></li>
        <li><a href="kontak.php">Kontak</a></li>
        <li>
          <button class="logout-btn" onclick="confirmLogout()">Keluar</button>
        </li>
      </ul>
      <div class="menu-icon" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </nav>

    <div class="content">
        <table border="1">
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Mata Pelajaran</th>
                <th>Pengajar</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['sesi']) ?></td>
                    <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
                    <td><?= htmlspecialchars($row['pengajar']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
