<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data siswa_id dari email
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

// Ambil data presensi
$query = "SELECT tanggal, sesi, status, komentar, waktu_presensi FROM presensi_siswa WHERE siswa_id = ? ORDER BY tanggal DESC, waktu_presensi DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();

$presensi = [];
while ($row = $result->fetch_assoc()) {
    $presensi[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Presensi</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f0f0;
            padding-top: 100px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #145375;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #145375;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #e6c200;
            color: #145375;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-back:hover {
            background-color: #145375;
            color: white;
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
            <li><a href="home.php"class="active"></a>>Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Riwayat Presensi - <?= htmlspecialchars($full_name) ?></h2>

        <?php if (empty($presensi)) : ?>
            <p style="text-align: center;">Belum ada data presensi.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Sesi</th>
                        <th>Status</th>
                        <th>Komentar</th>
                        <th>Waktu Presensi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presensi as $p) : ?>
                        <tr>
                            <td><?= htmlspecialchars($p['tanggal']) ?></td>
                            <td><?= htmlspecialchars($p['sesi']) ?></td>
                            <td><?= htmlspecialchars($p['status']) ?></td>
                            <td><?= htmlspecialchars($p['komentar']) ?></td>
                            <td><?= htmlspecialchars($p['waktu_presensi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
