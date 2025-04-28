<?php
session_start();
include 'koneksi.php';

// Pastikan pengajar sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data pengajar berdasarkan email
$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$pengajar = $result->fetch_assoc();

if (!$pengajar) {
    die("Akun pengajar tidak ditemukan. Pastikan Anda login dengan akun pengajar yang benar.");
}

$pengajar_id   = $pengajar['pengajar_id'];
$pengajar_name = $pengajar['full_name'];

// Ambil data riwayat penilaian berdasarkan pengajar_id, dan join dengan tabel siswa untuk mendapatkan nama siswa
$query = "SELECT n.nilai, n.nama_kuis, n.waktu, s.full_name AS siswa_name
          FROM nilai_siswa n
          JOIN siswa s ON n.siswa_id = s.siswa_id
          WHERE n.pengajar_id = ?
          ORDER BY n.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penilaian</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        color: #145375;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 90%;
        max-width: 1000px;
        margin: 30px auto;
        background-color: #fff;
        padding: 60px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.8s ease-in-out;
    }
    h2 {
        text-align: center;
        margin-bottom: 25px;
        font-size: 28px;
        color: #145375;
    }
    p {
        text-align: center;
        font-size: 18px;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        color: #145375;
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        padding: 14px 18px;
        border-bottom: 1px solid #e0e0e0;
        text-align: center;
        font-size: 16px;
    }
    th {
        background-color: #145375;
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    .back-button {
        display: inline-block;
        background-color: #145375;
        color: #fff;
        font-weight: bold;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 8px;
        margin-top: 25px;
        transition: background-color 0.3s, transform 0.3s;
    }
    .back-button:hover {
        background-color: #e6c200;
        transform: scale(1.05);
    }
    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
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
        <li><a href="home_mentor.php">Presensi</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php" class="active">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
        <li><a href="kontak_mentor.php">Kontak</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
<div class="container">
    <h2>Riwayat Penilaian</h2>
    <p>Selamat datang, <?php echo htmlspecialchars($pengajar_name); ?></p>
    <table>
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th>Nama Kuis</th>
                <th>Nilai</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $tanggal = date("d-m-Y H:i:s", strtotime($row['waktu']));
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['siswa_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_kuis']); ?></td>
                        <td><?php echo htmlspecialchars($row['nilai']); ?></td>
                        <td><?php echo $tanggal; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Belum ada riwayat penilaian yang tersedia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div style="text-align: center;">
        <a href="home_mentor.php" class="back-button">Kembali</a>
    </div>
</div>
<script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
</body>
</html>
<?php
$stmt->close();
mysqli_close($conn);
?>
