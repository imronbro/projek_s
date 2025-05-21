<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data pengajar berdasarkan sesi
$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login_mentor.php';</script>";
    exit();
}

$stmt->close();

// Ambil data presensi siswa berdasarkan pengajar
$query = "SELECT a.id, s.full_name AS nama_siswa, a.kelas, a.mapel, a.sekolah, a.status, a.alasan, a.waktu_presensi 
          FROM absensi_siswa a
          JOIN siswa s ON a.siswa_id = s.siswa_id
          WHERE a.pengajar_id = ?
          ORDER BY a.waktu_presensi DESC";
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
    <title>Riwayat Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
                * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
        }

        h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600; /* Gunakan berat font yang sesuai */
        }

        label, input, select, textarea, .btn {
            font-family: 'Poppins', sans-serif;
            font-weight: 400; /* Berat font normal */
        }

        .container {
        margin-top: 35px;
        padding: 50px;
        width: 90%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 1s ease-in-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        text-align: center;
        color: #145375;
        margin-bottom: 20px;
        font-size: 2.5em;
    }

    p {
        text-align: center;
        font-size: 1.2em;
        margin-bottom: 30px;
    }


    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fabe49;
        color: #003049;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    th,
    td {
        padding: 15px;
        border: 1px solid #003049;
        text-align: center;
        font-size: 1em;
    }

    th {
        background-color: #faaf1d;
        color: #003049;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    tr:nth-child(odd) {
        background-color: #ffffff;
    }

    tr:hover {
        background-color: #faaf1d;
        color: #003049;
    }

    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-form input,
        .filter-form button {
            width: 100%;
        }

        h2 {
            font-size: 2em;
        }

        th,
        td {
            font-size: 0.9em;
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.8em;
        }

        th,
        td {
            font-size: 0.8em;
            padding: 8px;
        }

        .filter-form input,
        .filter-form button {
            font-size: 0.9em;
            padding: 8px;
        }
    }

        .btn-edit, .btn-delete {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            transition: 0.3s ease;
            text-align: center;
        }

        .btn-edit {
            background-color: #4CAF50; /* Warna hijau untuk tombol Edit */
        }

        .btn-edit:hover {
            background-color: #45a049; /* Warna hijau lebih gelap saat hover */
        }

        .btn-delete {
            background-color: #e74c3c; /* Warna merah untuk tombol Hapus */
        }

        .btn-delete:hover {
            background-color: #c0392b; /* Warna merah lebih gelap saat hover */
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 18px;
            }

            table th, table td {
                font-size: 12px;
                padding: 8px;
            }

            .btn-edit, .btn-delete {
                font-size: 12px; /* Ukuran font lebih kecil */
                padding: 6px 10px; /* Padding lebih kecil */
            }

            td small {
                font-size: 10px; /* Ukuran font untuk sisa waktu */
            }
        }

        /* Responsif untuk layar sangat kecil */
        @media (max-width: 480px) {
            h2 {
                font-size: 16px;
            }

            table th, table td {
                font-size: 10px;
                padding: 6px;
            }

            .btn-edit, .btn-delete {
                font-size: 10px; /* Ukuran font lebih kecil lagi */
                padding: 5px 8px; /* Padding lebih kecil lagi */
            }

            td small {
                font-size: 9px; /* Ukuran font untuk sisa waktu */
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
        <li><a href="home_mentor.php" >Jurnal</a></li>
        <li><a href="proses_presensi.php"class="active">Presensi Siswa</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
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
        <h2>Riwayat Absensi</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Mapel</th>
                    <th>Sekolah</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th> <!-- Tambahkan kolom Aksi -->
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        // Hitung selisih waktu antara waktu presensi dan waktu sekarang
                        $waktu_presensi = strtotime($row['waktu_presensi']);
                        $batas_edit = 30 * 60; // 30 menit dalam detik
                        $waktu_sekarang = time();
                        $dapat_diedit = ($waktu_sekarang - $waktu_presensi) <= $batas_edit;
                        ?>

                        <tr>
                            <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><?= htmlspecialchars($row['mapel']) ?></td>
                            <td><?= htmlspecialchars($row['sekolah']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['alasan']) ?></td>
                            <td><?= date("d-m-Y H:i:s", strtotime($row['waktu_presensi'])) ?></td>
                            <td>
                                <?php if ($dapat_diedit): ?>
                                    <a href="edit_absensi.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="hapus_absensi.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                    <br>
                                    <small style="color: #555;">Sisa waktu: 
                                        <?php
                                        $sisa_waktu = $batas_edit - ($waktu_sekarang - $waktu_presensi);
                                        echo gmdate("i menit s detik", $sisa_waktu);
                                        ?>
                                    </small>
                                <?php else: ?>
                                    <span>Tidak Dapat Diedit atau Dihapus</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">Tidak ada data presensi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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