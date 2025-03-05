<?php
session_start();

// Informasi koneksi database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scover";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Mendapatkan email pengajar yang sedang login
$pengajar_email = $_SESSION['user_email'];

// Mendapatkan pengajar_id berdasarkan email
$query_pengajar = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt_pengajar = $conn->prepare($query_pengajar);
$stmt_pengajar->bind_param("s", $pengajar_email);
$stmt_pengajar->execute();
$result_pengajar = $stmt_pengajar->get_result();

if ($result_pengajar->num_rows > 0) {
    $pengajar = $result_pengajar->fetch_assoc();
    $pengajar_id = $pengajar['pengajar_id'];
    $pengajar_name = $pengajar['full_name'];

    // Mengambil data rating untuk pengajar yang sedang login
    $query_rating = "
        SELECT rp.id, s.full_name AS nama_siswa, rp.rating, rp.komentar, rp.created_at
        FROM rating_pengajar rp
        INNER JOIN siswa s ON rp.siswa_id = s.siswa_id
        WHERE rp.pengajar_id = ?
        ORDER BY rp.created_at DESC
    ";
    $stmt_rating = $conn->prepare($query_rating);
    $stmt_rating->bind_param("i", $pengajar_id);
    $stmt_rating->execute();
    $result_rating = $stmt_rating->get_result();
} else {
    echo "Pengajar tidak ditemukan.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Rating untuk <?php echo htmlspecialchars($pengajar_name); ?></title>
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
        .tombol-kembali {
            padding: 10px 20px;
            background-color: #faaf1d;
            color: #003049;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s, color 0.3s;
            margin-top: 20px;
        }
        .tombol-kembali:hover {
            background-color: #003049;
            color: #faaf1d;
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Presensi</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="jurnal.php">Jurnal</a></li>
            <li><a href="profile_mentor.php" class="active">Profil</a></li>
            <li><a href="kontak.php">Kontak</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

<h2>Riwayat Rating untuk <?php echo htmlspecialchars($pengajar_name); ?></h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nama Siswa</th>
        <th>Rating</th>
        <th>Komentar</th>
        <th>Tanggal Dibuat</th>
    </tr>
    <?php
    if ($result_rating->num_rows > 0) {
        while ($row = $result_rating->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_siswa']) . "</td>";
            echo "<td>" . str_repeat("⭐", $row['rating']) . "</td>";
            echo "<td>" . htmlspecialchars($row['komentar']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Belum ada rating yang diberikan.</td></tr>";
    }
    ?>
</table>
<button class="tombol-kembali" onclick="history.back()">Kembali</button>

<script src="js/logout.js" defer></script>
<script src="js/home.js" defer></script>
<script src="js/menu.js" defer></script>

<?php
$conn->close();
?>
</body>
</html>
