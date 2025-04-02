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
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #003049; /* Dark background */
            color: #fabe49; /* Light text */
            margin: 0;
            padding: 0;
        }

        /* Animasi untuk kontainer */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px); /* Mulai dari posisi sedikit ke bawah */
            }
            to {
                opacity: 1;
                transform: translateY(0); /* Berakhir di posisi normal */
            }
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #0271ab; /* Light blue background */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-in-out; /* Tambahkan animasi fade-in */
            margin-top: 100px; /* Tambahkan margin untuk memberi ruang di bawah navbar */
        }

        h2 {
            text-align: center;
            color: #faaf1d; /* Dark yellow */
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        p {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        /* Tabel */
        table {
            width: 100%; /* Tabel memenuhi lebar kontainer */
            border-collapse: collapse; /* Hilangkan jarak antar border */
            margin-top: 20px;
            background-color: #fabe49; /* Latar belakang tabel */
            color: #003049; /* Warna teks */
            border-radius: 10px; /* Membuat sudut tabel membulat */
            overflow: hidden; /* Pastikan border-radius terlihat */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Tambahkan bayangan */
        }

        th, td {
            padding: 15px; /* Tambahkan ruang di dalam sel */
            border: 1px solid #003049; /* Border gelap */
            text-align: center; /* Teks di tengah */
            font-size: 1em; /* Ukuran font */
        }

        th {
            background-color: #faaf1d; /* Latar belakang header tabel */
            color: #003049; /* Warna teks header */
            font-weight: bold; /* Teks tebal */
        }

        tr:nth-child(even) {
            background-color: #e0e0e0; /* Warna abu-abu terang untuk baris genap */
        }

        tr:nth-child(odd) {
            background-color: #ffffff; /* Warna putih untuk baris ganjil */
        }

        tr:hover {
            background-color: #faaf1d; /* Warna kuning gelap saat di-hover */
            color: #003049; /* Warna teks saat di-hover */
        }

        /* Tombol Kembali */
        .back-button {
            display: inline-block;
            background-color: #faaf1d; /* Dark yellow */
            color: #003049; /* Dark text */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1em;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #fabe49; /* Light yellow */
            transform: scale(1.05); /* Efek zoom saat hover */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 2em;
            }

            p {
                font-size: 1em;
            }

            th, td {
                font-size: 0.9em;
                padding: 10px;
            }

            .back-button {
                font-size: 0.9em;
                padding: 8px 15px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            h2 {
                font-size: 1.8em;
            }

            p {
                font-size: 0.9em;
            }

            th, td {
                font-size: 0.8em;
                padding: 8px;
            }

            .back-button {
                font-size: 0.8em;
                padding: 6px 10px;
            }
        }
    </style>
        <link rel="stylesheet" href="css/navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
            <span class="logo-text">Scover Center</span>
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
    <div class="menu-icon" onclick="toggleMenu()"
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
