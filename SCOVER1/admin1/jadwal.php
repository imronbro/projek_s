<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$user_email = $_SESSION['user_email'];

// Proses tambah jadwal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['siswa_id'])) {
    $siswa_id = $_POST['siswa_id'];
    $tanggal = $_POST['tanggal'];
    $sesi = $_POST['sesi'];
    $mata_pelajaran = $_POST['mata_pelajaran'];
    $pengajar_id = $_POST['pengajar_id'];

    $query = "INSERT INTO jadwal_siswa (siswa_id, tanggal, sesi, mata_pelajaran, pengajar_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi", $siswa_id, $tanggal, $sesi, $mata_pelajaran, $pengajar_id);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal!');</script>";
    }
    $stmt->close();
}

// Ambil daftar siswa
$siswa_result = $conn->query("SELECT siswa_id, full_name FROM siswa");

// Ambil daftar pengajar
$pengajar_result = $conn->query("SELECT pengajar_id, full_name FROM mentor");

// Proses pencarian siswa
$search = isset($_GET['search']) ? $_GET['search'] : '';
$jadwal_query = "SELECT j.id, s.full_name AS siswa_name, j.tanggal, j.sesi, j.mata_pelajaran, m.full_name AS pengajar_name 
                 FROM jadwal_siswa j 
                 JOIN siswa s ON j.siswa_id = s.siswa_id 
                 JOIN mentor m ON j.pengajar_id = m.pengajar_id";
if (!empty($search)) {
    $jadwal_query .= " WHERE s.full_name LIKE ?";
}
$jadwal_query .= " ORDER BY j.tanggal, j.sesi";

$stmt = $conn->prepare($jadwal_query);
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
}
$stmt->execute();
$jadwal_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>

    <style>
        body {
            background-color: #003049;
            color: #fabe49;
        }

        .card {
            background-color: #145375;
            color: white;
            border: 2px solid white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #faaf1d;
            display: block;
            margin: 0 auto;
        }

        .btn-whatsapp {
            background-color: #faaf1d;
            color: #003049;
            border: none;
        }

        .btn-whatsapp:hover {
            background-color: #fabe49;
        }
    </style>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/logout.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
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
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    
    <h2>Atur Jadwal Siswa</h2>

    <!-- Form Tambah Jadwal -->
    <form action="" method="post">
        <label for="siswa_id">Siswa:</label>
        <select name="siswa_id" required>
            <?php while ($row = $siswa_result->fetch_assoc()) { ?>
                <option value="<?= $row['siswa_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
            <?php } ?>
        </select>

        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" required>

        <label for="sesi">Sesi:</label>
        <select name="sesi" required>
            <option value="Sesi 1">Sesi 1 (09.00-10.30)</option>
            <option value="Sesi 2">Sesi 2 (10.30-12.00)</option>
            <option value="Sesi 3">Sesi 3 (13.00-14.30)</option>
            <option value="Sesi 4">Sesi 4 (14.30-16.00)</option>
            <option value="Sesi 5">Sesi 5 (16.00-17.30)</option>
            <option value="Sesi 6">Sesi 6 (18.00-19.30)</option>
            <option value="Sesi 7">Sesi 7 (19.30-21.00)</option>
        </select>

        <label for="mata_pelajaran">Mata Pelajaran:</label>
        <input type="text" name="mata_pelajaran" required>

        <label for="pengajar_id">Pengajar:</label>
        <select name="pengajar_id" required>
            <?php while ($row = $pengajar_result->fetch_assoc()) { ?>
                <option value="<?= $row['pengajar_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
            <?php } ?>
        </select>

        <button type="submit">Tambah Jadwal</button>
    </form>

    <!-- Form Pencarian -->
    <form method="get" action="">
        <input type="text" name="search" placeholder="Cari siswa..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
    </form>

    <!-- Tabel Jadwal -->
    <h3>Jadwal yang Telah Diatur</h3>
    <table border="1">
        <tr>
            <th>Siswa</th>
            <th>Tanggal</th>
            <th>Sesi</th>
            <th>Mata Pelajaran</th>
            <th>Pengajar</th>
            <th>Aksi</th>
        </tr>
        <?php if ($jadwal_result->num_rows > 0) {
            while ($row = $jadwal_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['siswa_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sesi']) . "</td>";
                echo "<td>" . htmlspecialchars($row['mata_pelajaran']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pengajar_name']) . "</td>";
                echo "<td><a href='edit_jadwal.php?id=" . $row['id'] . "'>Edit</a> | ";
                echo "<a href='hapus_jadwal.php?id=" . $row['id'] . "' onclick='return confirm(\"Hapus jadwal ini?\")'>Hapus</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Tidak ada data jadwal.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
