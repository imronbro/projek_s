<?php
session_start();
include 'koneksi.php';

// Pastikan siswa sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data siswa berdasarkan email
$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
    die("Akun siswa tidak ditemukan.");
}

$siswa_id   = $siswa['siswa_id'];
$siswa_name = $siswa['full_name'];

// Ambil data nilai siswa beserta nama pengajar menggunakan JOIN
$query = "SELECT n.nilai, n.nama_kuis, n.waktu, p.full_name AS pengajar_name 
          FROM nilai_siswa n 
          JOIN mentor p ON n.pengajar_id = p.pengajar_id 
          WHERE n.siswa_id = ?
          ORDER BY n.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #003049;
            color: #fabe49;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #0271ab;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
        }
        p {
            text-align: center;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fabe49;
            color: #003049;
        }
        th, td {
            padding: 12px;
            border: 1px solid #003049;
            text-align: center;
        }
        th {
            background-color: #faaf1d;
        }
        tr:nth-child(even) {
            background-color: #e0e0e0;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }
        .back-button {
            display: inline-block;
            background-color: #faaf1d;
            color: #003049;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #fabe49;
        }

    </style>
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/logout.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">
        <img src="images/foto4.png" alt="Logo">
    </div>
    <h1 class="title">Dashboard Siswa</h1>
    <ul class="nav-links">
        <li><a href="home.php">Presensi</a></li>
        <li><a href="pengajar.php">Pengajar</a></li>
        <li><a href="rating.php">Rating</a></li>
        <li><a href="jadwal1.php">Jadwal</a></li>
        <li><a href="nilai_siswa.php" class="active">Nilai</a></li>
        <li><a href="profile.php">Profil</a></li>
        <li><a href="kontak.php">Kontak</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>
    <div class="container">
        <h2>Daftar Nilai Siswa</h2>
        <p>Selamat datang, <?php echo htmlspecialchars($siswa_name); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Nama Kuis</th>
                    <th>Nilai</th>
                    <th>Pengajar</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Format tanggal untuk tampil dengan format dd-mm-YYYY HH:MM:SS
                        $tanggal = date("d-m-Y H:i:s", strtotime($row['waktu']));
                        echo "<tr>
                                <td>" . htmlspecialchars($row['nama_kuis']) . "</td>
                                <td>" . htmlspecialchars($row['nilai']) . "</td>
                                <td>" . htmlspecialchars($row['pengajar_name']) . "</td>
                                <td>" . $tanggal . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Belum ada nilai yang ditampilkan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="home.php" class="back-button">Kembali</a>
    </div>
</body>
    <script src="js/logout.js" defer></script>
    <script src="js/menu.js" defer></script>
</html>
<?php
$stmt->close();
mysqli_close($conn);
?>
