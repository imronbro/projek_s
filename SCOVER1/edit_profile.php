<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scover";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

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
    // Pastikan hanya angka
    $nohp = preg_replace('/[^0-9]/', '', $nohp);

    // Jika diawali dengan 0, ubah menjadi +62
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/logout.css">
    <link rel="stylesheet" href="css/home.css">
    <style>
        body { background-color: #003049; color: white; }
        .container { background-color: #0271ab; padding: 20px; border-radius: 10px; }
        h2 { color: #fabe49; }
        .btn-success { background-color: #faaf1d; border: none; }
        .btn-secondary { background-color: #145375; }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai_siswa.php">Nilai</a></li>
            <li><a href="profile.php" class="active">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profil</h2>
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
                <label for="gambar" class="form-label">Upload Foto Profil</label>
                <input type="file" id="gambar" name="gambar" class="form-control">
                <?php if (!empty($data['gambar'])): ?>
                    <img src="<?= htmlspecialchars($data['gambar']); ?>" alt="Profil" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="profile.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <script>
        document.getElementById('nohp').addEventListener('input', function (e) {
            let value = e.target.value;
            // Hanya angka dan tanda plus yang diperbolehkan
            value = value.replace(/[^0-9+]/g, '');
            // Jika dimulai dengan 0, ubah menjadi +62
            if (value.startsWith('0')) {
                value = '+62' + value.slice(1);
            }
            // Jika dimulai dengan 62 tanpa tanda plus, tambahkan tanda plus
            if (value.startsWith('62')) {
                value = '+62' + value.slice(2);
            }
            e.target.value = value;
        });
    </script>
    <script src="js/menu.js" defer></script>
    <script src="js/logout.js" defer></script>
</body>
</html>
