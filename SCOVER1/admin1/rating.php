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

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}

$query = "
    SELECT rp.id, s.full_name AS nama_siswa, rp.pengajar_id, rp.rating, rp.komentar, rp.created_at
    FROM rating_pengajar rp
    INNER JOIN siswa s ON rp.siswa_id = s.siswa_id
    ORDER BY rp.created_at DESC
";
$result = mysqli_query($conn, $query);



?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Rating Pengajar</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/logout.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #003049;
            color: #fabe49;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #0271ab;
            color: #fabe49;
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
