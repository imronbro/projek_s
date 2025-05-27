<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
    echo "Siswa tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM siswa WHERE siswa_id = $id");
$siswa = mysqli_fetch_assoc($query);

if (!$siswa) {
    echo "Siswa tidak ditemukan.";
    exit;
}

$gambar = htmlspecialchars($siswa['gambar']);
$imagePath = "" . $gambar;
$defaultImage = "../uploads/default.png";
$displayImage = (!empty($gambar) && file_exists($imagePath)) ? $imagePath : $defaultImage;

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $sekolah = $_POST['sekolah'];
    $kelas = $_POST['kelas'];
    $ttl = $_POST['ttl'];
    $alamat = $_POST['alamat'];
    $nohp = $_POST['nohp'];

    // Upload gambar jika ada
    $gambarBaru = $gambar; // default: tetap pakai gambar lama
    if (!empty($_FILES['gambar']['name'])) {
        $uploadDir = '../uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['gambar']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
            // Hapus gambar lama jika bukan default
            if ($gambar !== '../uploads/default.png' && file_exists("" . $gambar)) {
                unlink("" . $gambar);
            }
            $gambarBaru = '../uploads/' . $fileName;
        }
    }

    // Update data ke database
    $update = mysqli_query($conn, "UPDATE siswa SET 
        full_name = '$full_name', 
        email = '$email', 
        sekolah = '$sekolah', 
        kelas = '$kelas', 
        ttl = '$ttl', 
        alamat = '$alamat', 
        nohp = '$nohp', 
        gambar = '$gambarBaru'
        WHERE siswa_id = $id");

}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * {

        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #f4f4f4;
        color: #145375;
        margin: 0;
        padding: 0;
        padding-top: 100px;
        overflow-x: hidden;

    }

    .content {
        padding: 100px;
    }

    @media (max-width: 768px) {

        table,
        th,
        td {
            font-size: 12px;
            width: 97%;
        }

        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
    }

    button {
        background-color: #e6c200;
        color: #145375;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        border-radius: 6px;
    }

    button:hover {
        background-color: #145375;
        color: #fff;
    }

    /* Dropdown styles */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #0271ab;
        min-width: 180px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        padding: 0;
        margin: 5px;
        left: -35px;
        list-style: none;

        /* <- tambahkan border */
    }

    .dropdown-menu li a {
        color: #fff !important;
        /* pastikan warnanya terlihat */
        padding: 12px 16px;
        text-decoration: none;
        display: block;

        font-weight: bold;
        /* opsional biar lebih terlihat */
    }

    .dropdown-menu li a:hover {
        background-color: #e6c200;
        color: #145375;
    }


    .arrow {
        font-size: 12px;
        margin-left: 5px;
    }

    .container {
        background-color: #ffffff;
        max-width: 600px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        color: #145375;
    }

    .container h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 24px;
        color: #145375;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        font-size: 14px;
    }

    input[type="text"],
    input[type="email"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
    }

    .btn-back {
        display: inline-block;
        margin-bottom: 20px;
        color: #145375;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.2s ease;
    }

    .btn-back:hover {
        color: #e6c200;
    }
        .notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f9f9f9;
        color: #145375;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        /* Default: disembunyikan */
        text-align: center;
        width: 300px;
    }

    .notification p {
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
    }

    /* Tombol di dalam notifikasi */
    .notification-buttons {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .notification-buttons .btn {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
        text-decoration: none;
        /* Hilangkan garis bawah */
    }

    /* Tombol Batal */
    .notification-buttons .btn-secondary {
        background-color: #145375;
        color: white;
    }

    .notification-buttons .btn-secondary:hover {
        background-color: #e6c200;
        color: #145375;
    }

    /* Tombol Keluar */
    .notification-buttons .btn-danger {
        background-color: #e74c3c;
        /* Warna merah */
        color: white;
        border: none;
    }

    .notification-buttons .btn-danger:hover {
        background-color: #c0392b;
        /* Warna merah lebih gelap saat hover */
        transform: scale(1.05);
        /* Efek zoom saat hover */
    }
    </style>
</head>

<div>
    <nav class="navbar">

        <div class="logo">
            <a href="home.php">
                <img src="images/foto4.png" alt="Logo" class="logo-image">
            </a>
        </div>
        <h1 class="title">Dashboard Admin</h1>
        <ul class="nav-links">
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown(event)">Presensi <span id="arrow" class="arrow">&#9660;</span></a>
                <ul class="dropdown-menu">
                    <li><a href="home.php">Presensi Siswa</a></li>
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
                </ul>
            </li>

            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="siswa.php" class="active">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>

        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <body>
        <div class="container">
            <a href="detail_siswa.php?id=<?= $siswa['siswa_id'] ?>" class="btn-back">&larr; Kembali ke Profil</a>
            <h2>Edit Profil Siswa</h2>
            <form method="POST" enctype="multipart/form-data">
                <div style="text-align: center;">
                    <img src="<?= $displayImage ?>" alt="Foto Siswa"
                        style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #ccc; margin-bottom: 15px;">
                </div>

                <label>Ganti Foto:</label>
                <input type="file" name="gambar" accept="image/*">
                
                <label>Nama Lengkap:</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($siswa['full_name']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($siswa['email']) ?>" required>

                <label>Sekolah:</label>
                <input type="text" name="sekolah" value="<?= htmlspecialchars($siswa['sekolah']) ?>" required>

                <label>Kelas:</label>
                <input type="text" name="kelas" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>

                <label>Tanggal Lahir:</label>
                <input type="text" name="ttl" value="<?= htmlspecialchars($siswa['ttl']) ?>" required>

                <label>Alamat:</label>
                <textarea name="alamat" rows="3" required><?= htmlspecialchars($siswa['alamat']) ?></textarea>

                <label>No HP:</label>
                <input type="text" name="nohp" value="<?= htmlspecialchars($siswa['nohp']) ?>" required>

                <button type="submit">Simpan Perubahan</button>
            </form>
        </div>
<div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
</body>
<script src="js/menu.js" defer></script>

</html>