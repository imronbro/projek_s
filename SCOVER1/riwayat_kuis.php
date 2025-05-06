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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: 'Poppins';
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
            background-color: #e6c200;
            color: #145375;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #145375;
            color: #fff;
        }

        .notification {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            z-index: 1000;
        }

        .notification-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-secondary {
            background-color: #e6e6e6;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #d4d4d4;
        }

        .btn-danger {
            background-color: #d9534f;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c9302c;
        }
    </style>
    <script>
        function confirmLogout() {
            document.getElementById('logout-notification').style.display = 'block';
        }

        function cancelLogout() {
            document.getElementById('logout-notification').style.display = 'none';
        }
    </script>
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
        <li><a href="kuis.php" calss="active">Kuis</a></li>
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

<div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
</div>

</body>
</html>
