<?php
session_start();
include '../koneksi.php';


$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
if ($email === null) {
    die("Anda belum login. Harap login terlebih dahulu.");
}
if (isset($_SESSION['edit_success'])) {
    $showSuccess = true;
    unset($_SESSION['edit_success']);
}
$showSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $mapel = mysqli_real_escape_string($conn, $_POST['mapel']);
    $ttl = mysqli_real_escape_string($conn, $_POST['ttl']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);
    
    $uploadOk = 1;
    $gambar = '';
    if (!empty($_FILES["gambar"]["name"])) {
        $target_dir = "../uploads/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file benar-benar gambar
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check === false) {
            echo "File bukan gambar.";
            $uploadOk = 0;
        }

        // Cek ukuran file
        if ($_FILES["gambar"]["size"] > 5000000) {
            echo "Ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        // Cek ekstensi file
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
            $uploadOk = 0;
        }

        // Cek MIME type menggunakan finfo
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES["gambar"]["tmp_name"]);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            echo "File bukan tipe gambar yang valid.";
            $uploadOk = 0;
        }

        // Proses upload jika lolos semua validasi
        if ($uploadOk && move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $target_file;
        } else if ($uploadOk) {
            echo "Terjadi kesalahan saat mengupload gambar.";
            $uploadOk = 0;
        }
    }
    
    $query = "UPDATE mentor SET full_name='$full_name', mapel='$mapel', ttl='$ttl', alamat='$alamat', nohp='$nohp'";
    if ($gambar) {
        $query .= ", gambar='$gambar'";
    }
    $query .= " WHERE email='$email'";
    
    if (mysqli_query($conn, $query)) {
        // Remove the immediate redirect
        $notification = [
            'type' => 'success',
            'title' => 'Sukses!',
            'message' => 'Profil berhasil diperbarui.'
        ];
        // Don't redirect here
        // header("Location: profile_mentor.php");
        // exit();
    } else {
        $notification = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Terjadi kesalahan: ' . mysqli_error($conn)
        ];
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

$query = "SELECT * FROM mentor WHERE email='$email'";
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
    <title>Dashboard Mentor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    }

    .container {
    max-width: 800px;
    margin: 30px auto;
    background-color: #145375;
    color: white;
    padding: 40px;
    border-radius: 20px; /* Membuat sudut lebih lembut */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15); /* Efek bayangan lebih halus */
    transition: all 0.3s ease-in-out; /* Transisi halus saat efek berubah */
    border: 2px solid #1a5d7e; /* Border yang lebih halus dengan warna yang lebih terang */
}

    h2 {
        text-align: center;
        color: #faaf1d;
        margin-bottom: 30px;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }

    input[type="text"],
    input[type="date"],
    input[type="file"],
    textarea {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
        font-size: 16px;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    textarea:focus {
        outline: none;
        border-color: #faaf1d;
        box-shadow: 0 0 5px rgba(250, 175, 29, 0.5);
    }

    .btn-primary,
    .btn-secondary {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        margin: 10px 5px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #faaf1d;
        color: #003049;
        border: 1px solid #faaf1d;
    }

    .btn-primary:hover {
        background-color: #fff;
        color: #145375;
    }

    .btn-secondary {
        background-color: #faaf1d;
        color: #003049;
        border: 1px solid #faaf1d;
    }

    .btn-secondary:hover {
        background-color: #fff;
        color: #145375;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        margin: 10px 5px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }
    }
    .notification-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.notification-popup {
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
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    text-align: center;
}

.notification-icon {
    font-size: 52px;
    margin-bottom: 20px;
}

.notification-popup.success .notification-icon {
    color: #28a745;
}

.notification-popup.error .notification-icon {
    color: #dc3545;
}

.notification-button {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #145375;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.notification-overlay.show,
.notification-popup.show {
    opacity: 1;
    visibility: visible;
}

.notification-popup.show {
    transform: translate(-50%, -50%) scale(1);
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
            <li><a href="home_mentor.php">Jurnal</a></li>
            <li><a href="proses_presensi.php">Presensi Siswa</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php" class="active">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profil</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($data['full_name']); ?>"
                    class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mapel" class="form-label">Mata Pelajaran</label>
                <input type="text" id="mapel" name="mapel" value="<?= htmlspecialchars($data['mapel']); ?>"
                    class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="ttl" class="form-label">Tanggal Lahir</label>
                <input type="date" id="ttl" name="ttl" value="<?= htmlspecialchars($data['ttl']); ?>"
                    class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Rumah</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($data['alamat']); ?>"
                    class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nohp" class="form-label">No. HP</label>
                <input type="text" id="nohp" name="nohp" value="<?= htmlspecialchars($data['nohp']); ?>"
                    class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Upload Foto Profil</label>
                <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">

                <?php if (!empty($data['gambar'])): ?>
                <img src="<?= htmlspecialchars($data['gambar']); ?>" alt="Profil" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="profile_mentor.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <!-- Notification Overlay & Popup -->
<div class="notification-overlay" id="notificationOverlay"></div>
<div class="notification-popup" id="notificationPopup">
    <div class="notification-icon">
        <i class="fas" id="notificationIcon"></i>
    </div>
    <div class="notification-title" id="notificationTitle"></div>
    <div class="notification-message" id="notificationMessage"></div>
    <button class="notification-button" id="notificationButton">OK</button>
</div>

    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
    
    <script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
    <script>
    document.getElementById('nohp').addEventListener('input', function(e) {
        let value = e.target.value;

        // Hapus karakter selain angka dan tanda +
        value = value.replace(/[^0-9]/g, '');

        // Jika dimulai dengan 0, ubah menjadi +62
        if (value.startsWith('0')) {
            value = '+62' + value.slice(1);
        }

        // Jika dimulai dengan 62 tanpa tanda +, tambahkan +
        if (value.startsWith('62') && !value.startsWith('+62')) {
            value = '+62' + value.slice(2);
        }

        // Tetapkan nilai yang telah diformat kembali ke input
        e.target.value = value;
    });

     document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('gambar');
    const file = fileInput.files[0];
    if (file) {
        const maxSizeMB = 5;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            alert(`Ukuran file terlalu besar. Maksimum ${maxSizeMB}MB.`);
            e.preventDefault(); // hentikan submit form
            fileInput.value = "";
        }
    }
});
    </script>
    <?php if ($showSuccess): ?>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('notification-overlay');
        const popup = document.getElementById('notification-popup');
        const btnClose = document.getElementById('notification-close');

        overlay.classList.add('show');
        popup.classList.add('show');

        function closeNotification() {
            popup.classList.remove('show');
            overlay.classList.remove('show');
        }

        btnClose.addEventListener('click', closeNotification);
        overlay.addEventListener('click', closeNotification);
    });
</script>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('notificationOverlay');
    const popup = document.getElementById('notificationPopup');
    const title = document.getElementById('notificationTitle');
    const message = document.getElementById('notificationMessage');
    const button = document.getElementById('notificationButton');
    const icon = document.getElementById('notificationIcon');

    function showNotification(type, titleText, messageText) {
        // Reset any existing styles
        overlay.style.removeProperty('display');
        popup.style.removeProperty('display');
        
        popup.className = `notification-popup ${type}`;
        icon.className = `fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`;
        title.textContent = titleText;
        message.textContent = messageText;

        // Force reflow
        void overlay.offsetWidth;
        
        // Show notification
        overlay.classList.add('show');
        popup.classList.add('show');
    }

    button.onclick = function() {
        overlay.classList.remove('show');
        popup.classList.remove('show');

        setTimeout(() => {
            if (popup.classList.contains('success')) {
                window.location.href = 'profile_mentor.php';
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