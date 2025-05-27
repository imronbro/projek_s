<?php
session_start();
include '../koneksi.php';

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
            margin: 120px auto 20px;
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
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php" class="active">Nilai</a></li>
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
    <h2>Riwayat Penilaian</h2>
    <div class="scroll-hint">
        ← Geser tabel ke kanan/kiri →
    </div>
    <div class="table-wrapper">
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
    </div>
    <div style="text-align: center;">
        <a href="nilai.php" class="back-button">Kembali</a>
    </div>
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
<?php
$stmt->close();
mysqli_close($conn);
?>
