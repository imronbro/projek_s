<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['mentor_id'])) {
    header("Location: login_mentor.php");
    exit();
}

$mentor_id = $_SESSION['mentor_id'];

$query = "SELECT js.id, js.tanggal, js.sesi, js.mata_pelajaran, s.full_name as nama_siswa 
          FROM jadwal_siswa js
          JOIN siswa s ON js.siswa_id = s.siswa_id
          WHERE js.pengajar_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mentor</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #145375;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #145375;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            table {
                font-size: 14px;
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
            <li><a href="home_mentor.php">Jurnal</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php" class="active">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Jadwal Anda</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Mata Pelajaran</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                    <td><?= htmlspecialchars($row['sesi']); ?></td>
                    <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="js/logout.js" defer></script>
    <script src="js/menu.js" defer></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>