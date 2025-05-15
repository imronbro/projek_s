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
    <link rel="stylesheet" href="css/jadwal.css" />
    <style>
        * {

            box-sizing: border-box;
        }
        body{
            font-family: 'Poppins';
        }

        /* Form Filter Tanggal */
        form {
          display: flex;
          justify-content: center;
          align-items: center;
          gap: 10px; /* Jarak antara elemen input dan tombol */
          margin-bottom: 20px;
        }

        form label {
          font-size: 16px;
          font-weight: bold;
          color: #333;
        }

        form input[type="date"] {
          padding: 10px;
          border: 1px solid #ccc; /* Border abu-abu */
          border-radius: 5px; /* Sudut membulat */
          font-size: 14px;
          box-sizing: border-box; /* Sertakan padding dalam lebar total */
          transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        form input[type="date"]:focus {
          border-color: #faaf1d; /* Border kuning saat fokus */
          box-shadow: 0 0 5px rgba(250, 175, 29, 0.5); /* Efek cahaya kuning */
          outline: none; /* Hilangkan outline default */
        }

        form .button {
          padding: 10px 20px;
          background-color: #faaf1d; /* Warna tombol kuning */
          color: white;
          border: none;
          border-radius: 5px; /* Sudut membulat */
          font-size: 14px;
          font-weight: bold;
          cursor: pointer;
          transition: background-color 0.3s ease, transform 0.2s ease;
        }

        form .button:hover {
          background-color: #fabe49; /* Warna kuning lebih terang saat hover */
          transform: scale(1.05); /* Efek zoom saat hover */
        }

        /* Responsif untuk Layar Kecil */
        @media (max-width: 768px) {
          form {
            flex-direction: column; /* Susun elemen secara vertikal */
            gap: 15px; /* Jarak antar elemen */
          }

          form label {
            font-size: 14px; /* Ukuran font lebih kecil */
          }

          form input[type="date"] {
            width: 100%; /* Input memenuhi lebar layar */
            font-size: 14px; /* Ukuran font lebih kecil */
          }

          form .button {
            width: 100%; /* Tombol memenuhi lebar layar */
            font-size: 14px; /* Ukuran font lebih kecil */
            padding: 10px; /* Kurangi padding */
          }
        }

        /* Responsif untuk Layar Sangat Kecil */
        @media (max-width: 480px) {
          form label {
            font-size: 12px; /* Ukuran font lebih kecil */
          }

          form input[type="date"] {
            font-size: 12px; /* Ukuran font lebih kecil */
            padding: 8px; /* Kurangi padding */
          }

          form .button {
            font-size: 12px; /* Ukuran font lebih kecil */
            padding: 8px 10px; /* Kurangi padding tombol */
          }
        }
    </style>
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

    <div class="content">
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
    <h2 style="text-align: center; margin-top: 40px;">Kuis dari Mentor</h2>
    <form action="jadwal1.php" method="get" style="text-align: center; margin-bottom: 20px;">
        <label for="filter_tanggal">Filter Tanggal:</label>
        <input type="date" name="filter_tanggal" id="filter_tanggal" value="<?= htmlspecialchars($_GET['filter_tanggal'] ?? '') ?>">
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
                            <a href="download.php?file=<?= urlencode($row['file_kuis']) ?>" target="_blank">Unduh</a>
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