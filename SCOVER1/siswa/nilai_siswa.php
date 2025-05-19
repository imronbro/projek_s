<?php
session_start();
include '../koneksi.php';


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

// Ambil tahun saat ini
$current_year = date('Y');

// Ambil bulan dan tahun yang dipilih dari filter (jika ada)
$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Buat array tahun mulai dari 2025 hingga tahun berikutnya
$years = range(2025, $current_year + 1);

// Query untuk mengambil data nilai berdasarkan bulan dan tahun yang dipilih
$query = "SELECT n.nilai, n.nama_kuis, n.waktu, p.full_name AS pengajar_name 
          FROM nilai_siswa n 
          JOIN mentor p ON n.pengajar_id = p.pengajar_id 
          WHERE n.siswa_id = ? AND MONTH(n.waktu) = ? AND YEAR(n.waktu) = ?
          ORDER BY n.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $siswa_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

// Query untuk mendapatkan daftar tahun yang tersedia
$tahun_query = "SELECT DISTINCT YEAR(waktu) AS tahun FROM nilai_siswa ORDER BY tahun DESC";
$tahun_result = $conn->query($tahun_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */

        * {

            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins';
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            /* Latar belakang abu-abu terang */
            color: #003049;
            /* Teks biru gelap */
            padding-top: 110px;
            /* Sesuaikan dengan tinggi navbar, tambahkan lebih banyak ruang */
        }

        .container {
            margin-top: 35px;
            /* Tambahkan jarak tambahan untuk memastikan konten tidak tertutupi */
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            background-color: #ffffff;
            /* Latar belakang putih */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Bayangan lembut */
        }

        /* Animasi untuk kontainer */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
                /* Mulai dari posisi sedikit ke bawah */
            }

            to {
                opacity: 1;
                transform: translateY(0);
                /* Berakhir di posisi normal */
            }
        }

        .container {
            animation: fadeInUp 1s ease-in-out;
            /* Tambahkan animasi fade-in */
        }

        h2 {
            text-align: center;
            color: #145375;
            /* Dark yellow */
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
            width: 100%;
            /* Tabel memenuhi lebar kontainer */
            border-collapse: collapse;
            /* Hilangkan jarak antar border */
            margin-top: 20px;
            background-color: #fabe49;
            /* Latar belakang tabel */
            color: #003049;
            /* Warna teks */
            border-radius: 10px;
            /* Membuat sudut tabel membulat */
            overflow: hidden;
            /* Pastikan border-radius terlihat */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            /* Tambahkan bayangan */
        }

        th,
        td {
            padding: 15px;
            /* Tambahkan ruang di dalam sel */
            border: 1px solid #003049;
            /* Border gelap */
            text-align: center;
            /* Teks di tengah */
            font-size: 1em;
            /* Ukuran font */
        }

        th {
            background-color: #faaf1d;
            /* Latar belakang header tabel */
            color: #003049;
            /* Warna teks header */
            font-weight: bold;
            /* Teks tebal */
        }

        tr:nth-child(even) {
            background-color: #e0e0e0;
            /* Warna abu-abu terang untuk baris genap */
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
            /* Warna putih untuk baris ganjil */
        }

        tr:hover {
            background-color: #faaf1d;
            /* Warna kuning gelap saat di-hover */
            color: #003049;
            /* Warna teks saat di-hover */
        }

        /* Tombol Kembali */
        .back-button {
            display: inline-block;
            background-color: #faaf1d;
            /* Dark yellow */
            color: #003049;
            /* Dark text */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1em;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #fabe49;
            /* Light yellow */
            transform: scale(1.05);
            /* Efek zoom saat hover */
        }

        /* Filter Form */
        .filter-form {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        .filter-form label {
            font-weight: bold;
            color: #003049;
            margin-bottom: 5px;
        }

        .filter-form select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
            width: 200px;
            box-sizing: border-box;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #faaf1d;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1em;
        }

        .filter-form button:hover {
            background-color: #fabe49;
            transform: scale(1.05);
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-form select {
                width: 100%;
            }

            .filter-form button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .filter-form select {
                font-size: 0.9em;
                padding: 8px;
            }

            .filter-form button {
                font-size: 0.9em;
                padding: 8px 15px;
            }
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

            th,
            td {
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
                padding: 12px;
                margin-top: 15px;
            }

            h2 {
                font-size: 1.8em;
            }

            p {
                font-size: 0.9em;
            }

            th,
            td {
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
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php" class="active">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
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

        <!-- Filter Pilihan Bulan dan Tahun -->
        <form method="get" action="nilai_siswa.php" class="filter-form">
            <label for="bulan">Pilih Bulan:</label>
            <select id="bulan" name="bulan" required>
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $selected = (isset($_GET['bulan']) && $_GET['bulan'] == $i) ? 'selected' : '';
                    echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                }
                ?>
            </select>

            <label for="tahun">Pilih Tahun:</label>
            <select id="tahun" name="tahun" required>
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                    $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $i) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>

            <button type="submit">Tampilkan</button>
        </form>

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
                    echo "<tr><td colspan='4'>Tidak ada nilai untuk bulan dan tahun ini.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="download_nilai.php?format=pdf&bulan=<?= isset($_GET['bulan']) ? $_GET['bulan'] : date('m'); ?>&tahun=<?= isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>" class="back-button">Unduh Nilai</a>
    </div>

    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
</body>
<script src="js/logout.js" defer></script>
<script src="js/menu.js" defer></script>

</html>
<?php
$stmt->close();
mysqli_close($conn);
?>
``` 