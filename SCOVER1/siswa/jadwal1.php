<?php
session_start();
include '../koneksi.php';

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
if ($row = $result->fetch_assoc()) {
    $siswa_id = $row['siswa_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

// Ambil tanggal hari ini
$tanggal_sekarang = date('Y-m-d');

// Ambil jadwal siswa dengan join ke tabel mentor untuk mendapatkan nama pengajar
$sql = "SELECT j.tanggal, j.sesi, j.mata_pelajaran, j.pengajar_id, m.full_name AS pengajar 
        FROM jadwal_siswa j
        LEFT JOIN mentor m ON j.pengajar_id = m.pengajar_id
        WHERE j.siswa_id = ? AND j.tanggal >= ?
        ORDER BY j.tanggal, j.sesi";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $siswa_id, $tanggal_sekarang); // Gunakan tanggal sekarang sebagai parameter
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css" />
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

    .unduh {
        display: inline-block;
        background-color: #faaf1d;
        color: #145375;
        padding: 10px 20px;
        text-decoration: none;
        font-weight: bold;
        border-radius: 8px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 1em;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .unduh:hover {
        background-color: #145375;
        color: #fff;
        transform: scale(1.05);
    }

    .unduh:active {
        transform: scale(0.98);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
</head>

<>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php" class="active">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li>
                <button class="logout-btn" onclick="confirmLogout()">Keluar</button>
            </li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Jadwal Siswa</h2>
        <?php if ($result->num_rows > 0): ?>
        <?php
            $current_date = null; // Variabel untuk melacak tanggal saat ini
            while ($row = $result->fetch_assoc()):
                // Jika tanggal berubah, tampilkan header tanggal baru
                if ($current_date !== $row['tanggal']):
                    if ($current_date !== null): ?>
        </table>
        <?php endif; ?>
        <?php $current_date = $row['tanggal']; ?>
        <h3><?= htmlspecialchars($current_date) ?></h3>
        <table border="1">
            <tr>
                <th>Sesi</th>
                <th>Mata Pelajaran</th>
                <th>Pengajar</th>
            </tr>
            <?php endif; ?>
            <tr>
                <td><?= htmlspecialchars($row['sesi']) ?></td>
                <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
                <td><?= htmlspecialchars('Kak ' . ($row['pengajar'] ?? 'Pengajar Tidak Ditemukan')) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>Belum ada jadwal yang tersedia.</p>
        <?php endif; ?>
    </div>

    <!-- Bagian Kuis dari Mentor -->
    <div class="container">
        <h2 style="text-align: center; margin-top: 40px;">Kuis dari Mentor</h2>
        <form action="jadwal1.php" method="get" style="text-align: center; margin-bottom: 20px;">
            <label for="filter_tanggal">Filter Tanggal:</label>
            <input type="date" name="filter_tanggal" id="filter_tanggal"
                value="<?= htmlspecialchars($_GET['filter_tanggal'] ?? '') ?>">
            <button type="submit" class="button">Tampilkan</button>
        </form>
        <?php
    include '../koneksi.php'; // Pastikan koneksi dibuka ulang jika sebelumnya sudah ditutup

    // Periksa apakah filter tanggal diisi
    if (isset($_GET['filter_tanggal']) && !empty($_GET['filter_tanggal'])) {
        $filter_tanggal = $_GET['filter_tanggal'];

        $sql_kuis = "SELECT k.nama, k.kelas, k.tanggal, k.nilai, m.full_name AS pengajar, k.file_kuis
                     FROM kuis k
                     LEFT JOIN mentor m ON k.pengajar_id = m.pengajar_id
                     WHERE k.siswa_id = $siswa_id AND k.tanggal = '$filter_tanggal'
                     ORDER BY k.tanggal DESC";

        $result_kuis = $conn->query($sql_kuis);
    } else {
        // Query default untuk menampilkan kuis mulai dari hari ini
        $sql_kuis = "SELECT k.nama, k.kelas, k.tanggal, k.nilai, m.full_name AS pengajar, k.file_kuis
                     FROM kuis k
                     LEFT JOIN mentor m ON k.pengajar_id = m.pengajar_id
                     WHERE k.siswa_id = ? AND k.tanggal >= ?
                     ORDER BY k.tanggal ASC";
        $stmt_kuis = $conn->prepare($sql_kuis);
        $stmt_kuis->bind_param("is", $siswa_id, $tanggal_sekarang);
        $stmt_kuis->execute();
        $result_kuis = $stmt_kuis->get_result();
    }

    if ($result_kuis->num_rows > 0): ?>
        <table border="1" style="margin: 0 auto; margin-top: 20px;">
            <tr>
                <th>Nama Kuis</th>
                <th>Pengajar</th>
                <th>Tanggal</th>
                <th>File</th>
            </tr>
            <?php while ($row = $result_kuis->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars('Kak ' . $row['pengajar']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td>
                    <?php if (!empty($row['file_kuis'])): ?>
                    <a href="download_kuis.php?file=<?= urlencode($row['file_kuis']) ?>" target="_blank"
                        class="unduh">Unduh</a>
                    <?php else: ?>
                    Tidak Ada File
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p style="text-align: center;">Belum ada kuis yang tersedia.</p>
        <?php endif; ?>
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
// Pastikan file yang diminta ada di folder uploads
if (isset($_GET['file'])) {
    $file_name = basename($_GET['file']);
    $file_path = __DIR__ . '/../uploads1/' . $file_name;

    echo "Parameter file: " . htmlspecialchars($file_name) . "<br>";
    echo "Path file: " . htmlspecialchars($file_path) . "<br>";

    if (file_exists($file_path)) {
        echo "File ditemukan.";
    } else {
        echo "File tidak ditemukan.";
    }
    exit();
}
?>