<?php
session_start();
include '../koneksi.php';


$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
if ($email === null) {
    die("Anda belum login. Harap login terlebih dahulu.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sekolah = mysqli_real_escape_string($conn, $_POST['sekolah']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $ttl = mysqli_real_escape_string($conn, $_POST['ttl']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    
    $uploadOk = 1;
    $gambar = '';
    if (!empty($_FILES["gambar"]["name"])) {
        $target_dir = "uploads/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check === false) {
            echo "File bukan gambar.";
            $uploadOk = 0;
        }
        if ($_FILES["gambar"]["size"] > 2000000) {
            echo "Ukuran file terlalu besar.";
            $uploadOk = 0;
        }
        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            echo "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
            $uploadOk = 0;
        }
        if ($uploadOk && move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $target_file;
        } else {
            echo "Terjadi kesalahan saat mengupload gambar.";
            $uploadOk = 0;
        }
    }
    
    $query = "UPDATE siswa SET sekolah='$sekolah', kelas='$kelas', ttl='$ttl', alamat='$alamat', nohp='$nohp'";
    if ($gambar) {
        $query .= ", gambar='$gambar'";
    }
    $query .= " WHERE email='$email'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href = 'profile.php';</script>";
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

function formatNoHP($nohp) {
    $nohp = preg_replace('/[^0-9]/', '', $nohp);
    if (substr($nohp, 0, 1) === '0') {
        $nohp = '+62' . substr($nohp, 1);
    } elseif (substr($nohp, 0, 2) !== '62') {
        $nohp = '+62' . $nohp;
    } else {
        $nohp = '+' . $nohp;
    }

    return $nohp;
}

$query = "SELECT * FROM siswa WHERE email='$email'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    die("Data pengguna tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
                * {
            box-sizing: border-box;
        }
        body {
    background-color: #f9fbfd;
    font-family: 'Poppins';
    color: #333;
}

.container {
    background: #fff;
    max-width: 650px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 150px;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

h2.text-center {
    font-weight: 600;
    margin-bottom: 30px;
    text-align: center;
    color: #2c3e50;
}

label {
    font-weight: 500;
    margin-bottom: 8px;
    display: inline-block;
    color: #444;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    font-size: 15px;
    border-radius: 10px;
    border: 1px solid #ccd6dd;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #faaf1d;
    box-shadow: 0 0 8px rgba(250, 175, 29, 0.25);
    outline: none;
}

.btn-success, .btn-secondary {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s ease;
}

.btn-success {
    background-color: #faaf1d;
    color: white;
    border: none;
}

.btn-success:hover {
    background-color: #f89c0e;
}

.btn-secondary {
    background-color: #145375;
    color: white;
    border: none;
}

.btn-secondary:hover {
    background-color: #103f5b;
}

img.preview-img {
    width: 100px;
    margin-top: 15px;
    border-radius: 10px;
    border: 1px solid #ddd;
    display: block;
}

.mb-3 {
    margin-bottom: 20px;
}

.d-flex {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 30px;
    flex-wrap: wrap;
}

input[type="file"] {
    background-color: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.notification {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: none;
    z-index: 1000;
}

.notification-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
}

.btn-danger {
    background-color: #e74c3c;
    color: white;
    border: none;
}

.btn-danger:hover {
    background-color: #c0392b;
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
            <li><a href="home.php" >Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><a href="jadwal1.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php" class="active">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
    <h2 class="text-center">Edit Profil Siswa</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="sekolah" class="form-label">Sekolah</label>
            <input type="text" id="sekolah" name="sekolah" value="<?= htmlspecialchars($data['sekolah']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" id="kelas" name="kelas" value="<?= htmlspecialchars($data['kelas']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="ttl" class="form-label">Tanggal Lahir</label>
            <input type="date" id="ttl" name="ttl" value="<?= htmlspecialchars($data['ttl']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat Rumah</label>
            <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($data['alamat']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nohp" class="form-label">No. HP</label>
            <input type="text" id="nohp" name="nohp" value="<?= htmlspecialchars($data['nohp']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Foto Profil</label>
            <input type="file" id="gambar" name="gambar" class="form-control">
            <?php if (!empty($data['gambar'])): ?>
                <img src="<?= htmlspecialchars($data['gambar']); ?>" alt="Foto Profil" class="preview-img">
            <?php endif; ?>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="profile.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

<div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
</div>

<script>
    document.getElementById('nohp').addEventListener('input', function (e) {
        let value = e.target.value;
        value = value.replace(/[^0-9]/g, '');
        if (value.startsWith('0')) {
            value = '+62' + value.slice(1);
        } else if (value.startsWith('62') && !value.startsWith('+62')) {
            value = '+62' + value.slice(2);
        }
        e.target.value = value;
    });

    function confirmLogout() {
        document.getElementById('logout-notification').style.display = 'block';
    }

    function cancelLogout() {
        document.getElementById('logout-notification').style.display = 'none';
    }
</script>
<script src="js/menu.js" defer></script>
<script src="js/logout.js" defer></script>
</body>
</html>