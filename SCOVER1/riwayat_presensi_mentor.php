<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data mentor
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

// Ambil riwayat presensi mentor
$sql = "SELECT id, tanggal, sesi, status, mapel, materi, kelas, jumlah_siswa, keterangan, note, gambar, waktu_presensi 
        FROM presensi_pengajar 
        WHERE pengajar_id = ? 
        ORDER BY waktu_presensi DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Presensi Mentor</title>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        /* Container */
        .container {
            max-width: 100%;
            margin: auto;
            padding: 20px;
            padding-top: 120px; /* Tambahkan padding atas untuk menghindari tumpang tindih dengan navbar */
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            overflow-x: auto; /* Tambahkan scroll horizontal jika tabel terlalu lebar */
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        table th {
            background-color: #145375;
            color: white;
        }

        /* Tombol */
        .btn-edit, .btn-view, .btn-delete, .btn-action {
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s ease;
            text-align: center;
            display: inline-block;
        }

        .btn-edit {
            background-color: #e6c200;
            color: #145375;
            border: none;
        }

        .btn-edit:hover {
            background-color: #145375;
            color: white;
        }

        .btn-view {
            background-color: #4CAF50;
            color: white;
            border: none;
        }

        .btn-view:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-action {
            background-color: #145375;
            color: white;
            border: none;
        }

        .btn-action:hover {
            background-color: #e6c200;
            color: #145375;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            border: 5px solid white;
            border-radius: 10px;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
                padding: 8px;
            }

            .btn-edit, .btn-view, .btn-delete, .btn-action {
                font-size: 12px;
                padding: 6px 10px;
            }

            .container {
                padding: 15px;
            }


        /* Responsif untuk layar sangat kecil */
        @media (max-width: 480px) {
            table th, table td {
                font-size: 10px;
                padding: 5px;
            }

            .btn-edit, .btn-view, .btn-delete, .btn-action {
                font-size: 10px;
                padding: 5px 8px;
            }

            .container {
                padding: 10px;
                padding-top:40px;
            }


        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">
        <img src="images/foto4.png" alt="Logo">
    </div>
    <h1 class="title">Riwayat Presensi Mentor</h1>
    <ul class="nav-links">
        <li><a href="home_mentor.php" class="active">Jurnal</a></li>
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
    <h2>Riwayat Presensi Mentor</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Status</th>
                <th>Gambar</th>
                <th>Waktu Presensi</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Jumlah Siswa</th>
                <th>Materi</th>
                <th>Keterangan</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $waktu_presensi = strtotime($row['waktu_presensi']);
                $batas_edit = 30 * 60; // 30 menit dalam detik
                $waktu_sekarang = time();
                $dapat_diedit = ($waktu_sekarang - $waktu_presensi) <= $batas_edit;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['sesi']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <button class="btn-view" onclick="showImageModal('<?= htmlspecialchars($row['gambar']) ?>')">Lihat</button>
                        <?php else: ?>
                            Tidak Ada Gambar
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($row['waktu_presensi']))) ?></td>
                    <td><?= htmlspecialchars($row['mapel']) ?></td>
                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['materi']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td><?= htmlspecialchars($row['note']) ?></td>
                    <td>
                        <?php if ($dapat_diedit): ?>
                            <button class="btn-action" onclick="toggleActionMenu(this)">Aksi</button>
                            <div class="action-menu" style="display: none;">
                                <a href="edit_presensi.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                <a href="hapus_presensi.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus presensi ini?')">Hapus</a>
                            </div>
                        <?php else: ?>
                            <span>Tidak Dapat Diedit</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div id="imageModal" class="modal">
    <span class="close" onclick="closeImageModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script src="js/logout.js" defer></script>
<script src="js/menu.js" defer></script>
<script>
    function showImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modal.style.display = 'block';
        modalImage.src = imageSrc;
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }

    function toggleActionMenu(button) {
        const actionMenu = button.nextElementSibling;
        button.style.display = "none"; // Sembunyikan tombol Aksi
        actionMenu.style.display = "block"; // Tampilkan menu Edit dan Hapus
    }
</script>
</body>
</html>