<?php
session_start();
include '../koneksi.php';

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
        $target_dir = "../uploads/";
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
        $notification = [
            'type' => 'success',
            'title' => 'Presensi Berhasil!',
            'message' => 'Data presensi Anda telah berhasil diedit.'
        ];
    } else {
        $notification = [
            'type' => 'error',
            'title' => 'Gagal Menyimpan',
            'message' => 'Terjadi kesalahan: ' . $stmt->error
        ];
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
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins';
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
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.form-buttons .btn {
    padding: 10px 20px;
    border: none;
    background-color: #145375;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}

.form-buttons .btn:hover {
    background-color: #e6c200;
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

        .notification {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .notification-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .notification-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .notification-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            width: 320px;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            text-align: center;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .notification-overlay.show {
            opacity: 1;
            display: block;
        }

        .notification-popup.show {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            display: block;
        }

        .notification-icon {
            font-size: 52px;
            margin-bottom: 20px;
            display: inline-block;
        }

        .notification-popup.success .notification-icon {
            color: #28a745;
        }

        .notification-popup.error .notification-icon {
            color: #dc3545;
        }

        .notification-popup.success {
            border-top: 5px solid #28a745;
        }

        .notification-popup.error {
            border-top: 5px solid #dc3545;
        }

        .notification-title {
            font-size: 20px;
            font-weight: 600;
            color: #145375;
            margin-bottom: 12px;
        }

        .notification-message {
            font-size: 15px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .notification-button {
            background: #145375;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notification-button:hover {
            background: #0e3e5a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(20, 83, 117, 0.3);
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
        <li><a href="home_mentor" class="active">Jurnal</a></li>
        <li><a href="proses_presensi">Presensi Siswa</a></li>
        <li><a href="siswa">Siswa</a></li>
        <li><a href="jadwal">Jadwal</a></li>
        <li><a href="kuis">Kuis</a></li>
        <li><a href="nilai">Nilai</a></li>
        <li><a href="profile_mentor">Profil</a></li>
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
        <a href="logout" class="btn btn-danger">Keluar</a>
    </div>
</div>
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
            
            <a href="riwayat_presensi_mentor" class="btn">Batal</a>
        </div>
    </form>
</div>
<div class="notification-overlay" id="notificationOverlay"></div>
<div class="notification-popup" id="notificationPopup">
    <div class="notification-icon">
        <i class="fas fa-check-circle" id="notificationIcon"></i>
    </div>
    <div class="notification-title" id="notificationTitle"></div>
    <div class="notification-message" id="notificationMessage"></div>
    <button class="notification-button" id="notificationButton">OK</button>
</div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('notificationOverlay');
    const popup = document.getElementById('notificationPopup');
    const title = document.getElementById('notificationTitle');
    const message = document.getElementById('notificationMessage');
    const button = document.getElementById('notificationButton');
    const icon = document.getElementById('notificationIcon');

    function showNotification(type, titleText, messageText) {
        overlay.style.display = 'block';
        popup.style.display = 'block';
        
        title.innerText = titleText;
        message.innerText = messageText;
        
        popup.className = `notification-popup ${type}`;
        icon.className = type === 'success' ? 
            'fas fa-check-circle' : 
            'fas fa-exclamation-circle';
        
        setTimeout(() => {
            overlay.classList.add('show');
            popup.classList.add('show');
        }, 10);
    }

    button.onclick = function() {
        overlay.classList.remove('show');
        popup.classList.remove('show');
        
        setTimeout(() => {
            overlay.style.display = 'none';
            popup.style.display = 'none';
            if (popup.classList.contains('success')) {
                // Change redirect to riwayat_presensi_mentor.php
                window.location.href = 'riwayat_presensi_mentor.php';
            }
        }, 300);
    };

    <?php if (isset($notification)): ?>
    showNotification(
        '<?php echo $notification['type']; ?>', 
        '<?php echo $notification['title']; ?>', 
        '<?php echo $notification['message']; ?>'
    );
    <?php endif; ?>
});
</script>
</body>
</html>