<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID pengajar tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM mentor WHERE pengajar_id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data pengajar tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['full_name'];
    $email = $_POST['email'];
    $mapel = $_POST['mapel'];
    $nohp = $_POST['nohp'];
    $ttl = $_POST['ttl'];
    $alamat = $_POST['alamat'];

    // Upload gambar baru jika ada
    if ($_FILES['gambar']['name']) {
        $gambar = '../uploads/' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../" . $gambar);
    } else {
        $gambar = $data['gambar'];
    }

    $update = mysqli_query($conn, "UPDATE mentor SET 
        full_name='$nama', 
        email='$email', 
        mapel='$mapel', 
        nohp='$nohp', 
        ttl='$ttl', 
        alamat='$alamat',
        gambar='$gambar'
        WHERE pengajar_id=$id");

    if ($update) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='detail_pengajar.php?id=$id';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pengajar</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins';
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            background: white;
            margin: 120px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #083d6e;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input, textarea {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            background-color: #083d6e;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0c4b85;
        }

        .back-btn {
            margin-top: 15px;
            text-align: center;
        }

        .back-btn a {
            text-decoration: none;
            color: #083d6e;
            font-weight: bold;
        }
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

<body>
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

            <li><a href="pengajar.php" class="active">Pengajar</a></li>
            <li><a href="siswa.php">Siswa</a></li>
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
    <div class="container">
        <h2>Edit Data Pengajar</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Nama Lengkap</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($data['full_name']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>

            <label>Mata Pelajaran</label>
            <input type="text" name="mapel" value="<?= htmlspecialchars($data['mapel']); ?>" required>

            <label>No HP</label>
            <input type="text" name="nohp" value="<?= htmlspecialchars($data['nohp']); ?>" required>

            <label>Tanggal Lahir</label>
            <input type="text" name="ttl" value="<?= htmlspecialchars($data['ttl']); ?>" required>

            <label>Alamat</label>
            <textarea name="alamat" rows="3"><?= htmlspecialchars($data['alamat']); ?></textarea>

            <label>Foto (upload jika ingin mengganti)</label>
            <input type="file" name="gambar" accept="image/*">

            <button type="submit">Simpan Perubahan</button>
        </form>
        <div class="back-btn">
            <a href="detail_pengajar.php?id=<?= $id ?>">← Kembali</a>
        </div>
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
