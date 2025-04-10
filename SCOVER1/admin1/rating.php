<?php
$conn = new mysqli("localhost", "root", "", "scover");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil keyword pencarian jika ada
$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : "";

// Query gabung 3 tabel: rating_pengajar + mentor + siswa
$sql = "
    SELECT 
        rp.*, 
        m.full_name AS nama_pengajar, 
        s.full_name AS nama_siswa
    FROM rating_pengajar rp
    JOIN mentor m ON rp.pengajar_id = m.pengajar_id
    JOIN siswa s ON rp.siswa_id = s.siswa_id
    WHERE m.full_name LIKE '%$keyword%'
    ORDER BY rp.created_at DESC
";


$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top:100px;
        }
        .content{
            padding: 100px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h3 {
            text-align: center;
        }
        table {
            width: 100%;
            max-width: 960px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 10px;
            border: 1px solid #faaf1d;
        }
        th {
            background-color: #faaf1d;
            color: #003049;
        }
    </style>
</head>

<body>
<nav class="navbar">
    <div class="logo">
        <img src="images/foto4.png" alt="Logo">
    </div>
    <h1 class="title">Dashboard Admin</h1>
    <ul class="nav-links">
        <li><a href="home.php">Beranda</a></li>
        <li><a href="pengajar.php">Pengajar</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="rating_pengajar.php" class="active">Rating Pengajar</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="nilai.php">Nilai</a></li>
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

<h2>Riwayat Rating Pengajar</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nama Siswa</th>
        <th>ID Pengajar</th>
        <th>Rating</th>
        <th>Komentar</th>
        <th>Tanggal Dibuat</th>
    </tr>
    <?php
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_pengajar']) . "</td>";
            echo "<td>" . htmlspecialchars($row['pengajar_id']) . "</td>";
            echo "<td>" . str_repeat("⭐", $row['rating']) . "</td>";
            echo "<td>" . htmlspecialchars($row['komentar']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Belum ada rating yang diberikan.</td></tr>";
    }
    ?>
</table>

<script src="js/logout.js" defer></script>
<script src="js/home.js" defer></script>
<script src="js/menu.js" defer></script>

<?php
mysqli_close($conn);
?>
</body>
</html>
