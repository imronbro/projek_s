<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$user_email = $_SESSION['user_email'];

// Fungsi untuk mendapatkan nama hari dalam bahasa Indonesia
function getHari($tanggal) {
    $hari = date('l', strtotime($tanggal));
    $hariIndonesia = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];
    return $hariIndonesia[$hari];
}

// Proses tambah jadwal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['siswa_id'])) {
    $siswa_id = $_POST['siswa_id'];
    $tanggal = $_POST['tanggal'];
    $sesi = $_POST['sesi'];
    $mata_pelajaran = $_POST['mata_pelajaran'];
    $pengajar_id = $_POST['pengajar_id'];

    $query = "INSERT INTO jadwal_siswa (siswa_id, tanggal, sesi, mata_pelajaran, pengajar_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi", $siswa_id, $tanggal, $sesi, $mata_pelajaran, $pengajar_id);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal!');</script>";
    }
    $stmt->close();
}

// Ambil daftar siswa
$siswa_result = $conn->query("SELECT siswa_id, full_name FROM siswa");

// Ambil daftar pengajar
$pengajar_result = $conn->query("SELECT pengajar_id, full_name FROM mentor");

// Ambil jadwal
$jadwal_query = "SELECT j.id, s.full_name AS siswa_name, j.tanggal, j.sesi, j.mata_pelajaran, m.full_name AS pengajar_name 
                 FROM jadwal_siswa j 
                 JOIN siswa s ON j.siswa_id = s.siswa_id 
                 JOIN mentor m ON j.pengajar_id = m.pengajar_id
                 ORDER BY j.tanggal, j.sesi";
$jadwal_result = $conn->query($jadwal_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007bff;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #fff;
            color: #333;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
            color: #333;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
            width: 40px;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
<nav class="navbar">
        <div class="logo">
            <div class="logo-circle">LOGO</div>
        </div>
        <h1 class="title">Dashboard Siswa</h1>
        <ul class="nav-links">
            <li><a href="presensi.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container">
        <h2>Atur Jadwal Siswa</h2>

        <!-- Form Tambah Jadwal -->
        <form action="" method="post">
            <label for="siswa_id">Siswa:</label>
            <select name="siswa_id" id="siswa_id" class="select2" required>
                <option value="" disabled selected>Pilih Siswa</option>
                <?php while ($row = $siswa_result->fetch_assoc()) { ?>
                    <option value="<?= $row['siswa_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
                <?php } ?>
            </select>

            <label for="tanggal">Tanggal:</label>
            <input type="date" name="tanggal" required>

            <label for="sesi">Sesi:</label>
            <select name="sesi" required>
                <option value="Sesi 1 (09:00-10:30)">Sesi 1 (09:00-10:30)</option>
                <option value="Sesi 2 (10:30-12:00)">Sesi 2 (10:30-12:00)</option>
                <option value="Sesi 3 (13:00-14:30)">Sesi 3 (13:00-14:30)</option>
                <option value="Sesi 4 (14:30-16:00)">Sesi 4 (14:30-16:00)</option>
                <option value="Sesi 5 (16:00-17:30)">Sesi 5 (16:00-17:30)</option>
                <option value="Sesi 6 (18:00-19:30)">Sesi 6 (18:00-19:30)</option>
                <option value="Sesi 7 (19:30-21:00)">Sesi 7 (19:30-21:00)</option>
            </select>

            <label for="mata_pelajaran">Mata Pelajaran:</label>
            <input type="text" name="mata_pelajaran" required>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
            <label for="pengajar_id">Pengajar:</label\><select name="pengajar_id" id="pengajar_id" class="select2" required>
                <option value="" disabled selected>Pilih Pengajar</option>
                <?php while ($row = $pengajar_result->fetch_assoc()) { ?>
                    <option value="<?= $row['pengajar_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
                <?php } ?>
            </select>

            <button type="submit">Tambah Jadwal</button>
        </form>

        <!-- Tabel Jadwal -->
        <h3>Jadwal yang Telah Diatur</h3>
        <table>
            <tr>
                <th>Siswa</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Sesi</th>
                <th>Mata Pelajaran</th>
                <th>Pengajar</th>
                <th>Aksi</th>
            </tr>
            <?php if ($jadwal_result->num_rows > 0) {
                while ($row = $jadwal_result->fetch_assoc()) {
                    $hari = getHari($row['tanggal']); // Dapatkan nama hari
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['siswa_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                    echo "<td>" . htmlspecialchars($hari) . "</td>"; // Tampilkan nama hari
                    echo "<td>" . htmlspecialchars($row['sesi']) . "</td>"; // Tampilkan sesi lengkap
                    echo "<td>" . htmlspecialchars($row['mata_pelajaran']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pengajar_name']) . "</td>";
                    echo "<td><a href='edit_jadwal.php?id=" . $row['id'] . "'>Edit</a> | ";
                    echo "<a href='hapus_jadwal.php?id=" . $row['id'] . "' onclick='return confirm(\"Hapus jadwal ini?\")'>Hapus</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Tidak ada data jadwal.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2({
                placeholder: "Pilih...",
                allowClear: true
            });
        });
    </script>
</body>

</html>
