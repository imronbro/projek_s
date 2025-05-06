<?php
session_start();
include 'koneksi.php'; 

date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu Anda

// Periksa apakah user sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah ID presensi ada di URL
if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='riwayat_presensi.php';</script>";
    exit();
}

$presensi_id = $_GET['id'];

// Ambil data presensi berdasarkan ID
$query = "SELECT * FROM presensi_siswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $presensi_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $tanggal = $row['tanggal'];
    $sesi = $row['sesi'];
    $status = $row['status'];
    $komentar = $row['komentar'];
} else {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='riwayat_presensi.php';</script>";
    exit();
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $tanggal = $_POST['tanggal'];
        $sesi = $_POST['sesi'];
        $status = $_POST['status'];
        $komentar = $_POST['komentar'];

        // Update data termasuk waktu_presensi
        $update_query = "UPDATE presensi_siswa SET tanggal = ?, sesi = ?, status = ?, komentar = ?, waktu_presensi = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $tanggal, $sesi, $status, $komentar, $presensi_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Data berhasil diperbarui!'); window.location.href='riwayat_presensi.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Riwayat Presensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: 'Poppins';
            background-color: #f4f4f4;
            color: #fabe49;
            margin: 0;
            padding: 0;
        }
        .container {
    margin-top: 120px; /* Tambahkan jarak agar tidak tertutupi navbar */
    max-width: 600px;
    margin: 120px auto; /* Sesuaikan margin */
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

        h2 {
            text-align: center;
            color: #faaf1d;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input, select, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
        }

        button {
            padding: 10px 15px;
            background-color: #faaf1d;
            color: #003049;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #fabe49;
        }

        .back-button {
            text-align: center;
            margin-top: 20px;
        }

        .back-button a {
            color: #faaf1d;
            text-decoration: none;
            font-weight: bold;
        }

        .back-button a:hover {
            text-decoration: underline;
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
            <li><a href="home.php" class="active">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>

    <div class="container">
        <h2>Edit Riwayat Presensi</h2>
        <form method="POST">
            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" required>

            <label for="sesi">Sesi:</label>
            <select id="sesi" name="sesi" required>
                <option value="1" <?php echo $sesi == '1' ? 'selected' : ''; ?>>Sesi 1 (09.00-10.30)</option>
                <option value="2" <?php echo $sesi == '2' ? 'selected' : ''; ?>>Sesi 2 (10.30-12.00)</option>
                <option value="3" <?php echo $sesi == '3' ? 'selected' : ''; ?>>Sesi 3 (13.00-14.30)</option>
                <option value="4" <?php echo $sesi == '4' ? 'selected' : ''; ?>>Sesi 4 (14.30-16.00)</option>
                <option value="5" <?php echo $sesi == '5' ? 'selected' : ''; ?>>Sesi 5 (16.00-17.30)</option>      
                <option value="6" <?php echo $sesi == '6' ? 'selected' : ''; ?>>Sesi 6 (18.00-19.30)</option>   
                <option value="7" <?php echo $sesi == '7' ? 'selected' : ''; ?>>Sesi 7 (19.30-21.00)</option>       
            </select>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Hadir" <?php echo $status == 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
                <option value="Izin" <?php echo $status == 'Izin' ? 'selected' : ''; ?>>Izin</option>
                <option value="Sakit" <?php echo $status == 'Sakit' ? 'selected' : ''; ?>>Sakit</option>
            </select>

            <label for="komentar">Komentar:</label>
            <textarea id="komentar" name="komentar" rows="4"><?php echo htmlspecialchars($komentar); ?></textarea>

            <button type="submit" name="update">Perbarui</button>
        </form>
        <div class="back-button">
            <a href="riwayat_presensi.php">Kembali ke Riwayat Presensi</a>
        </div>
    </div>
</body>
</html>