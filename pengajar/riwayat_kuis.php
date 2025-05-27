<?php
session_start();
include '../koneksi.php';

// Ganti pengecekan session
if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Ambil email dari session
$user_email = $_SESSION['user_email'];

// Ambil pengajar_id berdasarkan email
$query = "SELECT pengajar_id FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result_pengajar = $stmt->get_result();
if ($row = $result_pengajar->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
} else {
    // Jika tidak ditemukan, paksa logout
    header("Location: logout.php");
    exit();
}
$stmt->close();

$query = "
    SELECT k.id, k.nama AS nama_kuis, k.file_kuis, k.tanggal, s.full_name 
    FROM kuis k
    JOIN siswa s ON k.siswa_id = s.siswa_id
    WHERE k.pengajar_id = ?
    ORDER BY k.tanggal DESC
";

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
    <title>Riwayat Kuis</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins';
            background-color: #fff;
            color: #fabe49;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 150px auto 20px;
            background-color: #145375;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .table-wrapper {
            overflow-x: auto;
            margin-bottom: 15px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            color: #145375;
            border-radius: 5px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #145375;
            text-align: center;
            white-space: nowrap;
        }

        th {
            background-color: #e6c200;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #e0e0e0;
        }
        .unduh{
            background-color: #145375;
            color: #ffffff;
            padding: 6px 12px;
            font-size: bold;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .back-button {
            display: inline-block;
            background-color: #e6c200;
            color: #145375;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }

        .scroll-hint {
            display: none;
            text-align: center;
            margin: 10px 0;
            font-size: 0.9rem;
        }

        @media screen and (max-width: 768px) {
            .container {
                width: 95%;
                margin-top: 90px;
                padding: 15px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .scroll-hint {
                display: block;
            }

            th, td {
                padding: 8px;
                font-size: 0.9rem;
            }

            .back-button {
                width: 100%;
                padding: 12px;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                width: 100%;
                margin-top: 70px;
                padding: 10px;
                border-radius: 0;
            }

            h2 {
                font-size: 1.3rem;
            }

            th, td {
                padding: 6px;
                font-size: 0.85rem;
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
        <li><a href="home_mentor.php">Presensi</a></li>
        <li><a href="proses_presensi.php">Presensi Siswa</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php"class="active">Kuis</a></li>
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
    
    <div class="scroll-hint">
        ← Geser untuk melihat selengkapnya →
    </div>

    <div class="table-wrapper">
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
                                <a href="download.php?file=<?= urlencode($row['file_kuis']) ?>" target="_blank"class="unduh">Unduh</a>
                            <?php else: ?>
                                Tidak Ada File
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <a href="kuis.php" class="back-button">Kembali</a>
</div>

<div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
</div>
    <script src="js/menu.js" defer></script>
</body>
</html>
