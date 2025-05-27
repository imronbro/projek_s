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

    /* Table Container */
    .table-wrapper {
        overflow-x: auto;
        margin: 20px 0;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        -webkit-overflow-scrolling: touch;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fabe49;
        color: #003049;
        min-width: 800px; /* Ensures table doesn't get too squeezed */
    }

    th, td {
        padding: 12px 15px;
        border: 1px solid #003049;
        text-align: left;
        white-space: nowrap;
    }

    th {
        background-color: #faaf1d;
        color: #003049;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    td {
        background-color: #fff;
        
    }
    

    /* Scroll Hint */
    .scroll-hint {
        display: none;
        text-align: center;
        color: #003049;
        margin: 10px 0;
        font-size: 0.9em;
    }

    /* Total Section */
    .total {
        margin-top: 20px;
        padding: 15px;
        background-color: #faaf1d;
        color: #003049;
        border-radius: 5px;
        font-weight: bold;
        text-align: right;
    }

    /* Form and Button Styles */
    .filter-form {
        margin-bottom: 20px;
        text-align: center;
    }

    .filter-form label {
        margin-right: 10px;
        font-weight: 500;
    }

    .filter-form select {
        padding: 8px 15px;
        margin-right: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: 'Poppins';
    }

    .filter-form button,
    .back-button {
        background-color: #faaf1d;
        color: #003049;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        font-family: 'Poppins';
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
    }

    .filter-form button:hover,
    .back-button:hover {
        background-color: #e6c200;
    }

    .back-button {
        margin-top: 20px;
        text-align: center;
        display: block;
        width: fit-content;
    }
    /* Tombol Keluar */
.notification-buttons .btn-danger {
  background-color: #e74c3c; /* Warna merah */
  color: white; /* Warna teks */
  border: none; /* Hilangkan border */
}

.notification-buttons .btn-danger:hover {
  background-color: #c0392b; /* Warna merah lebih gelap saat hover */
  transform: scale(1.05); /* Efek zoom saat hover */
}


    /* Responsive Adjustments */
    @media screen and (max-width: 768px) {
        .container {
            width: 95%;
            padding: 15px;
            margin-top: 10px;
        }

        .scroll-hint {
            display: block;
        }

        table {
            font-size: 14px;
        }

        th, td {
            padding: 10px;
        }

        .total {
            font-size: 14px;
            padding: 12px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .filter-form select,
        .filter-form button,
        .back-button {
            width: 100%;
            margin: 5px 0;
            padding: 12px;
        }

        .back-button {
            margin: 20px auto;
        }
    }

    @media screen and (max-width: 480px) {
        .container {
            width: 100%;
            padding: 10px;
            border-radius: 0;
        }

        table {
            font-size: 13px;
        }

        th, td {
            padding: 8px;
        }

        .total {
            font-size: 13px;
            padding: 10px;
        }

        .filter-form select,
        .filter-form button,
        .back-button {
            font-size: 14px;
            padding: 10px;
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

        <div class="scroll-hint">
            ← Geser tabel ke kanan/kiri untuk melihat selengkapnya →
        </div>

        <div class="table-wrapper">
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
        </div>

        <div class="total">
            Total Gaji: Rp <?= number_format($total_gaji, 0, ',', '.') ?>
        </div>


        <br>
        
       <div style="display: flex; gap: 10px;">
    <a class="back-button"
        href="slip_gaji_pdf.php?bulan=<?= $selected_month ?>&tahun=<?= $selected_year ?>&format=pdf">Unduh PDF</a>

    <a class="back-button"
        href="profile_mentor.php">Kembali</a>
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

<?php $stmt->close(); $conn->close(); ?>