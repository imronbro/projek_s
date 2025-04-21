<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['mentor_id'])) {
    header("Location: login_mentor.php");
    exit();
}

$mentor_id = $_SESSION['mentor_id'];

$query = "
    SELECT k.id, k.nama AS nama_kuis, k.file_kuis, k.tanggal, s.full_name 
    FROM kuis k
    JOIN siswa s ON k.siswa_id = s.siswa_id
    WHERE k.pengajar_id = ?
    ORDER BY k.tanggal DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Kuis</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #145375;
            margin: 0;
            padding-top: 100px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            color: #145375;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #145375;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        a.download-btn {
            background-color: #faaf1d;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        a.download-btn:hover {
            background-color: #145375;
        }

        .back-button {
            margin-top: 20px;
            display: inline-block;
            background-color: #faaf1d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #145375;
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
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php" class="active">Kuis</a></li>
        <li><a href="nilai.php">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>

<div class="container">
    <h2>Riwayat Kuis yang Diunggah</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th>Nama Kuis</th>
                <th>Tanggal</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kuis']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td>
                        <?php if (!empty($row['file_kuis'])): ?>
                            <a href="download.php?file=<?= urlencode($row['file_kuis']) ?>" target="_blank">Unduh</a>
                        <?php else: ?>
                            Tidak Ada File
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="kuis.php" class="back-button">Kembali</a>
</div>

</body>
</html>
