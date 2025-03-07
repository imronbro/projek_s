<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

$user_email = $_SESSION['user_email']; 


$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
    $full_name = $row['full_name']; 
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login_mentor.php';</script>";
    exit();
}
$stmt->close();


$sql = "SELECT tanggal, sesi, status, komentar, waktu_presensi 
        FROM presensi_pengajar 
        WHERE pengajar_id = ? 
        ORDER BY tanggal DESC, waktu_presensi DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Presensi</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/logout.css">
    <style>
    .button-container {
        margin-top: 20px;
        text-align: center;
    }

    .back-button {
        background-color: #3498db;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .back-button:hover {
        background-color: #2980b9;
    }
</style>

</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Pengajar</h1>
        <ul class="nav-links">
            <li><a href="home.php" class="active">Presensi</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="jurnal.php">Jurnal</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li><a href="kontak_mentor.php">Kontak</a></li>
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
