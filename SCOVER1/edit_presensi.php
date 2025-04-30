<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID presensi tidak ditemukan!'); window.location.href='riwayat_presensi_mentor.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Ambil data presensi berdasarkan ID
$sql = "SELECT * FROM presensi_pengajar WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Data presensi tidak ditemukan!'); window.location.href='riwayat_presensi_mentor.php';</script>";
    exit();
}

$data = $result->fetch_assoc();
$stmt->close();

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'] ?? null;
    $sesi = htmlspecialchars($_POST['sesi'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? '');
    $mapel = htmlspecialchars($_POST['mapel'] ?? '');
    $materi = htmlspecialchars($_POST['materi'] ?? '');
    $kelas = htmlspecialchars($_POST['kelas'] ?? '');
    $jumlah_siswa = htmlspecialchars($_POST['jumlah_siswa'] ?? '');
    $keterangan = htmlspecialchars($_POST['keterangan'] ?? '');
    $note = htmlspecialchars($_POST['note'] ?? '');

    // Proses upload gambar baru jika ada
    $gambar = $data['gambar']; // Gambar lama
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        $gambar = $target_dir . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar);
    }

    if (!$tanggal || !$sesi || !$status || !$mapel || !$materi || !$kelas || !$jumlah_siswa || !$keterangan) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit();
    }

    $sql = "UPDATE presensi_pengajar 
            SET tanggal = ?, sesi = ?, status = ?, mapel = ?, materi = ?, kelas = ?, jumlah_siswa = ?, keterangan = ?, note = ?, gambar = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssisssi", $tanggal, $sesi, $status, $mapel, $materi, $kelas, $jumlah_siswa, $keterangan, $note, $gambar, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Presensi berhasil diperbarui!'); window.location.href='riwayat_presensi_mentor.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Presensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
                * {
            box-sizing: border-box;
        }
        .container {
            margin-top: 120px;
            max-width: 600px;
            margin-left:auto ;
            margin-right: auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input, select, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            background-color: #145375;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn:hover {
            background-color: #e6c200;
            color: #145375;
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
        <li><a href="home_mentor.php" class="active">Jurnal</a></li>
        <li><a href="proses_presensi.php">Presensi Siswa</a></li>
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
    <h2>Edit Presensi Mentor</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required>

        <label for="sesi">Sesi:</label>
        <select id="sesi" name="sesi" required>
            <option value="Sesi 1" <?= $data['sesi'] == 'Sesi 1' ? 'selected' : '' ?>>Sesi 1 (09.00-10.30)</option>
            <option value="Sesi 2" <?= $data['sesi'] == 'Sesi 2' ? 'selected' : '' ?>>Sesi 2 (10.30-12.00)</option>
            <option value="Sesi 3" <?= $data['sesi'] == 'Sesi 3' ? 'selected' : '' ?>>Sesi 3 (13.00-14.30)</option>
            <option value="Sesi 4" <?= $data['sesi'] == 'Sesi 4' ? 'selected' : '' ?>>Sesi 4 (14.30-16.00)</option>
            <option value="Sesi 5" <?= $data['sesi'] == 'Sesi 5' ? 'selected' : '' ?>>Sesi 5 (16.00-17.30)</option>
            <option value="Sesi 6" <?= $data['sesi'] == 'Sesi 6' ? 'selected' : '' ?>>Sesi 6 (18.00-19.30)</option>
            <option value="Sesi 7" <?= $data['sesi'] == 'Sesi 7' ? 'selected' : '' ?>>Sesi 7 (19.30-21.00)</option>
        </select>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Hadir" <?= $data['status'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
            <option value="Izin" <?= $data['status'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
            <option value="Sakit" <?= $data['status'] == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
        </select>

        <label for="mapel">Mata Pelajaran:</label>
        <input type="text" id="mapel" name="mapel" value="<?= htmlspecialchars($data['mapel']) ?>" required>

        <label for="materi">Materi/Soal yang Disampaikan:</label>
        <textarea id="materi" name="materi" rows="3" required><?= htmlspecialchars($data['materi']) ?></textarea>

        <label for="kelas">Kelas:</label>
        <input type="text" id="kelas" name="kelas" value="<?= htmlspecialchars($data['kelas']) ?>" required>

        <label for="jumlah_siswa">Jumlah Siswa:</label>
        <input type="number" id="jumlah_siswa" name="jumlah_siswa" value="<?= htmlspecialchars($data['jumlah_siswa']) ?>" required>

        <label for="keterangan">Keterangan:</label>
        <select id="keterangan" name="keterangan" required>
            <option value="AL-IZZAH" <?= $data['keterangan'] == 'AL-IZZAH' ? 'selected' : '' ?>>AL-IZZAH</option>
            <option value="AL-UMM" <?= $data['keterangan'] == 'AL-UMM' ? 'selected' : '' ?>>AL-UMM</option>
            <option value="AR-ROHMAH" <?= $data['keterangan'] == 'AR-ROHMAH' ? 'selected' : '' ?>>AR-ROHMAH</option>
            <option value="MENGAJAR LUAR KOTA" <?= $data['keterangan'] == 'MENGAJAR LUAR KOTA' ? 'selected' : '' ?>>MENGAJAR LUAR KOTA</option>
            <option value="MENGAJAR POWER HOUR" <?= $data['keterangan'] == 'MENGAJAR POWER HOUR' ? 'selected' : '' ?>>MENGAJAR POWER HOUR</option>
            <option value="MENGAJAR TETAP" <?= $data['keterangan'] == 'MENGAJAR TETAP' ? 'selected' : '' ?>>MENGAJAR TETAP</option>
            <option value="ONLINE CLASS" <?= $data['keterangan'] == 'ONLINE CLASS' ? 'selected' : '' ?>>ONLINE CLASS</option>
            <option value="OLIMPIADE" <?= $data['keterangan'] == 'OLIMPIADE' ? 'selected' : '' ?>>OLIMPIADE</option>
            <option value="TELKOM" <?= $data['keterangan'] == 'TELKOM' ? 'selected' : '' ?>>TELKOM</option>
            <option value="THURSINA" <?= $data['keterangan'] == 'THURSINA' ? 'selected' : '' ?>>THURSINA</option>
            <option value="SOSIALISASI" <?= $data['keterangan'] == 'SOSIALISASI' ? 'selected' : '' ?>>SOSIALISASI</option>
        </select>

        <label for="note">Catatan:</label>
        <textarea id="note" name="note" rows="3"><?= htmlspecialchars($data['note']) ?></textarea>

        <label for="gambar">Upload Gambar:</label>
        <input type="file" id="gambar" name="gambar">

        <div class="form-buttons">
            <button type="submit" class="btn">Simpan</button>
            <a href="riwayat_presensi_mentor.php" class="btn">Batal</a>
        </div>
    </form>
</div>
</body>
</html>