<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID absensi tidak ditemukan!'); window.location.href='riwayat_absensi.php';</script>";
    exit();
}

$id = $_GET['id'];

// Ambil data absensi berdasarkan ID
$query = "SELECT * FROM absensi_siswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Data absensi tidak ditemukan!'); window.location.href='riwayat_absensi.php';</script>";
    exit();
}

$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kelas = $_POST['kelas'];
    $mapel = $_POST['mapel'];
    $sekolah = !empty($_POST['sekolah']) ? $_POST['sekolah'] : null;
    $status = $_POST['status'];
    $alasan = !empty($_POST['alasan']) ? $_POST['alasan'] : null;

    $update = "UPDATE absensi_siswa SET kelas = ?, mapel = ?, sekolah = ?, status = ?, alasan = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update);
    $stmt_update->bind_param("sssssi", $kelas, $mapel, $sekolah, $status, $alasan, $id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Data absensi berhasil diperbarui!'); window.location.href='riwayat_absensi.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data absensi!'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
                * {
            box-sizing: border-box;
        }
        .container {
            margin-top: 100px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #145375;
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
            width: 100%;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #45a049;
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
        <h2>Edit Absensi</h2>
        <form method="post">
            <label for="kelas">Kelas:</label>
            <input type="text" id="kelas" name="kelas" value="<?= htmlspecialchars($data['kelas']) ?>" required>

            <label for="mapel">Mapel:</label>
            <input type="text" id="mapel" name="mapel" value="<?= htmlspecialchars($data['mapel']) ?>" required>

            <label for="sekolah">Sekolah (Opsional):</label>
            <input type="text" id="sekolah" name="sekolah" value="<?= htmlspecialchars($data['sekolah']) ?>">

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Hadir" <?= $data['status'] === 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                <option value="Izin" <?= $data['status'] === 'Izin' ? 'selected' : '' ?>>Izin</option>
                <option value="Sakit" <?= $data['status'] === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
            </select>

            <label for="alasan">Alasan:</label>
            <textarea id="alasan" name="alasan" rows="3"><?= htmlspecialchars($data['alasan']) ?></textarea>

            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
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