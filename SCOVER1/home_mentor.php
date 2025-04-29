<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

$user_email = $_SESSION['user_email'];

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

$sql_cek = "SELECT UNIX_TIMESTAMP(waktu_presensi) as last_presensi FROM presensi_pengajar WHERE pengajar_id = ? ORDER BY waktu_presensi DESC LIMIT 1";
$stmt = $conn->prepare($sql_cek);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$last_presensi = 0;
if ($row = $result->fetch_assoc()) {
    $last_presensi = $row['last_presensi'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'] ?? null;
    $sesi = htmlspecialchars($_POST['sesi'] ?? '');
    $status = htmlspecialchars($_POST['kehadiran'] ?? '');
    $komentar = htmlspecialchars($_POST['komentar'] ?? '');
    $mapel = htmlspecialchars($_POST['mapel'] ?? '');
    $materi = htmlspecialchars($_POST['materi'] ?? '');
    $kelas = htmlspecialchars($_POST['kelas'] ?? '');
    $jumlah_siswa = htmlspecialchars($_POST['jumlah_siswa'] ?? '');
    $keterangan = htmlspecialchars($_POST['keterangan'] ?? '');
    $note = htmlspecialchars($_POST['note'] ?? '');

    if (!$tanggal || !$sesi || !$status || !$mapel || !$materi || !$kelas || !$jumlah_siswa || !$keterangan) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit();
    }

    $current_time = time();
    if ($last_presensi > 0 && ($current_time - $last_presensi) < 5400) {
        echo "<script>alert('Anda hanya bisa mengisi presensi sekali dalam 90 menit!'); window.history.back();</script>";
        exit();
    }

    $image_path = null;
    if ($status == "Hadir" && isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $image_path);
    }

    $sql = "INSERT INTO presensi_pengajar (pengajar_id, full_name, tanggal, sesi, status, komentar, gambar, mapel, materi, kelas, jumlah_siswa, keterangan, note, waktu_presensi)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssiss", $pengajar_id, $full_name, $tanggal, $sesi, $status, $komentar, $image_path, $mapel, $materi, $kelas, $jumlah_siswa, $keterangan, $note);

    if ($stmt->execute()) {
        echo "<script>alert('Presensi berhasil disimpan!'); window.location.href='home_mentor.php';</script>";
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
    <title>Dashboard Mentor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
            overflow-x: hidden;
        }

        .container {
            max-width: 800px;
            margin: auto;
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

        select, input[type="date"], input[type="text"], input[type="number"], textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .btn {
            background-color: #e6c200;
            color: #145375;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn:hover {
            background-color: #145375;
            color: white;
        }

        #komentar-container,
        #upload-container {
            display: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }

        .btn-secondary {
            background-color: #145375;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            margin-left: 10px;
        }

        .btn-secondary:hover {
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
    <h2>Presensi Mentor</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="sesi">Sesi:</label>
        <select id="sesi" name="sesi">
            <option value="Sesi 1">Sesi 1 (09.00-10.30)</option>
            <option value="Sesi 2">Sesi 2 (10.30-12.00)</option>
            <option value="Sesi 3">Sesi 3 (13.00-14.30)</option>
            <option value="Sesi 4">Sesi 4 (14.30-16.00)</option>
            <option value="Sesi 5">Sesi 5 (16.00-17.30)</option>
            <option value="Sesi 6">Sesi 6 (18.00-19.30)</option>
            <option value="Sesi 7">Sesi 7 (19.30-21.00)</option>
        </select>

        <label for="tanggal">Pilih Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" required>

        <label for="kehadiran">Status Kehadiran:</label>
        <select id="kehadiran" name="kehadiran" required onchange="toggleUpload()">
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
        </select>

        <div id="komentar-container">
            <label for="komentar">Alasan Izin/Sakit:</label>
            <textarea id="komentar" name="komentar" rows="3"></textarea>
        </div>

        <label for="mapel">Mata Pelajaran:</label>
        <input type="text" id="mapel" name="mapel" required>

        <label for="materi">Materi/Soal yang Disampaikan:</label>
        <textarea id="materi" name="materi" rows="3" required></textarea>

        <label for="kelas">Kelas:</label>
        <input type="text" id="kelas" name="kelas" required>

        <label for="jumlah_siswa">Jumlah Siswa:</label>
        <input type="number" id="jumlah_siswa" name="jumlah_siswa" required>

        <label for="keterangan">Keterangan:</label>
        <select id="keterangan" name="keterangan" required>
            <option value="AL-IZZAH">AL-IZZAH</option>
            <option value="AL-UMM">AL-UMM</option>   
            <option value="AR-ROHMAH">AR-ROHMAH</option>
            <option value="MENGAJAR LUAR KOTA">MENGAJAR LUAR KOTA</option>
            <option value="MENGAJAR POWER HOUR">MENGAJAR POWER HOUR</option>
            <option value="MENGAJAR TETAP">MENGAJAR TETAP</option>
            <option value="ONLINE CLASS">ONLINE CLASS</option> 
            <option value="OLIMPIADE">OLIMPIADE</option>
            <option value="TELKOM">TELKOM</option>   
            <option value="THURSINA">THURSINA</option>         
            <option value="SOSIALISASI">SOSIALISASI</option>
        </select>

        <label for="note">Catatan:</label>
        <textarea id="note" name="note" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        <div id="upload-container">
            <label for="gambar">Upload Bukti Kehadiran:</label>
            <input type="file" id="gambar" name="gambar" accept="image/*">
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn">Kirim</button>
            <a href="riwayat_presensi_mentor.php" class="btn btn-secondary">Riwayat Presensi</a>
        </div>
    </form>
</div>

<script src="js/logout.js" defer></script>
<script src="js/menu.js" defer></script>
<script>
    function toggleUpload() {
        var status = document.getElementById("kehadiran").value;
        document.getElementById("upload-container").style.display = (status === "Hadir") ? "block" : "none";
        document.getElementById("komentar-container").style.display = (status !== "Hadir") ? "block" : "none";
    }

    // Panggil saat halaman pertama kali dimuat
    window.onload = toggleUpload;
</script>

</body>
</html>
