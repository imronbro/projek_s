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
$sql = "SELECT tanggal, sesi, status, komentar, created_at 
        FROM presensi_siswa 
        WHERE siswa_id = ? 
        ORDER BY tanggal DESC, created_at DESC";

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
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-circle">LOGO</div>
        </div>
        <h1 class="title">Riwayat Presensi</h1>
        <ul class="nav-links">
            <li><a href="home.php">Kembali</a></li>
        </ul>
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
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data presensi</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Tutup statement & koneksi database
$stmt->close();
$conn->close();
?>
