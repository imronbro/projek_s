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
    <link rel="stylesheet" href="css/home.css">
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
        function goBack() {
        window.history.back();
    }
    </script>
</html>

<?php
// Tutup statement & koneksi database
$stmt->close();
$conn->close();
?>
