<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
$user_email = $_SESSION['user_email'];

// Ambil data siswa berdasarkan email
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

// Ambil tanggal hari ini
$tanggal_sekarang = date('Y-m-d');

// Ambil jadwal siswa dengan join ke tabel mentor untuk mendapatkan nama pengajar
$sql = "SELECT j.tanggal, j.sesi, j.mata_pelajaran, j.pengajar_id, m.full_name AS pengajar 
        FROM jadwal_siswa j
        LEFT JOIN mentor m ON j.pengajar_id = m.pengajar_id
        WHERE j.siswa_id = ? AND j.tanggal >= ?
        ORDER BY j.tanggal, j.sesi";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $siswa_id, $tanggal_sekarang); // Gunakan tanggal sekarang sebagai parameter
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="css/jadwal.css" />
  </head>
  <body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
            <span class="logo-text">Scover Center</span>
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
        <h2>Jadwal Siswa</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php
            $current_date = null; // Variabel untuk melacak tanggal saat ini
            while ($row = $result->fetch_assoc()):
                // Jika tanggal berubah, tampilkan header tanggal baru
                if ($current_date !== $row['tanggal']):
                    if ($current_date !== null): ?>
                        </table>
                    <?php endif; ?>
                    <?php $current_date = $row['tanggal']; ?>
                    <h3><?= htmlspecialchars($current_date) ?></h3>
                    <table border="1">
                        <tr>
                            <th>Sesi</th>
                            <th>Mata Pelajaran</th>
                            <th>Pengajar</th>
                        </tr>
                <?php endif; ?>
                        <tr>
                            <td><?= htmlspecialchars($row['sesi']) ?></td>
                            <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
                            <td><?= htmlspecialchars('Kak ' . ($row['pengajar'] ?? 'Pengajar Tidak Ditemukan')) ?></td>
                        </tr>
            <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Belum ada jadwal yang tersedia.</p>
        <?php endif; ?>
    </div>
    <script src="js/menu.js" defer></script>
  </body>
</html>
