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
    die("Akun pengajar tidak ditemukan.");
}

$pengajar_id = $pengajar['pengajar_id'];
$pengajar_name = $pengajar['full_name'];

// Ambil bulan dan tahun yang dipilih
$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Daftar tarif per kelas/mapel
$tarif_mapel = [
    'AL-IZZAH' => 200000,
    'AL-UMM' => 80000,
    'AR-ROHMAH' => 150000,
    'MENGAJAR LUAR KOTA' => 100000,
    'MENGAJAR POWER HOUR' => 40000,
    'MENGAJAR TETAP' => 80000,
    'ONLINE CLASS' => 80000,
    'OLIMPIADE' => 120000,
    'TELKOM' => 80000,
    'THURSINA' => 100000,
    'SOSIALISASI' => 80000
];

// Query data presensi
$query = "SELECT tanggal, sesi, mapel, kelas, keterangan, status 
          FROM presensi_pengajar
          WHERE pengajar_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? 
          AND status = 'Hadir'";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $pengajar_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

$data_presensi = [];
$total_gaji = 0;

while ($row = $result->fetch_assoc()) {
    $mapel = strtoupper(trim($row['mapel']));
    $kelas = strtoupper(trim($row['kelas']));
    $keterangan = strtoupper(trim($row['keterangan'] ?? '')); // ambil dari kolom 'keterangan'

    // Ambil tarif berdasarkan keterangan tempat
    $tarif = $tarif_mapel[$keterangan] ?? 0;
    $total_gaji += $tarif;

    $data_presensi[] = [
        'tanggal' => $row['tanggal'],
        'sesi' => $row['sesi'],
        'kelas' => $kelas,
        'mapel' => $mapel,
        'keterangan'=> $keterangan,
        'tarif' => $tarif
    ];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mentor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
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
        color: #faaf1d;
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
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php" class="active">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>
    <div class="container">
        <h2>Slip Gaji Pengajar</h2>
        <p>Selamat datang, <?php echo htmlspecialchars($pengajar_name); ?></p>

        <form method="get" class="filter-form">
            <label for="bulan">Bulan:</label>
            <select name="bulan" required>
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $selected = ($i == $selected_month) ? 'selected' : '';
                    echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                }
                ?>
            </select>
            <label for="tahun">Tahun:</label>
            <select name="tahun" required>
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                    $selected = ($y == $selected_year) ? 'selected' : '';
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>
            <button type="submit">Tampilkan</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Kelas</th>
                    <th>Mapel</th>
                    <th>Tempat</th> <!-- Tambahan -->
                    <th>Jumlah HR (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($data_presensi) > 0) {
                    foreach ($data_presensi as $row) {
                        echo "<tr>
                                <td>{$row['tanggal']}</td>
                                <td>{$row['sesi']}</td>
                                <td>{$row['kelas']}</td>
                                <td>{$row['mapel']}</td>
                                <td>{$row['keterangan']}</td> <!-- Tempat sama dengan mapel saat ini -->
                                <td>" . number_format($row['tarif'], 0, ',', '.') . "</td>
                              </tr>";
                    }
                    
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data presensi untuk bulan dan tahun ini.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="total">
            Total Gaji: Rp <?= number_format($total_gaji, 0, ',', '.') ?>
        </div>


        <br>
        <a class="back-button"
            href="slip_gaji_pdf.php?bulan=<?= $selected_month ?>&tahun=<?= $selected_year ?>&format=pdf">Unduh PDF</a>

    </div>

    <script src="js/logout.js" defer></script>
</body>

</html>

<?php $stmt->close(); $conn->close(); ?>