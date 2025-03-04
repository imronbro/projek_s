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

// Periksa apakah user sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil siswa_id dan full_name berdasarkan email
$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();
$siswa_id = $siswa['siswa_id'];
$full_name = $siswa['full_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pengajar_id = $_POST["pengajar_id"];
    $rating = $_POST["rating"];
    $komentar = $_POST["komentar"];

    $sql = "INSERT INTO rating_pengajar (pengajar_id, siswa_id, rating, komentar) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $pengajar_id, $siswa_id, $rating, $komentar);

    if (mysqli_stmt_execute($stmt)) {
        echo "Rating berhasil dikirim!";
    } else {
        echo "Terjadi kesalahan: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating Pengajar</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/logout.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #003049;
            color: #fabe49;
            text-align: center;
        }
        form {
            background-color: #0271ab;
            padding: 20px;
            width: 50%;
            margin: auto;
            border-radius: 10px;
        }
        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        button {
            background-color: #faaf1d;
            color: #003049;
            font-size: 16px;
            border: none;
            cursor: pointer;
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
        <li><a href="home.php">Presensi</a></li>
        <li><a href="pengajar.php">Pengajar</a></li>
        <li><a href="rating.php" class="active">Rating</a></li>
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

<h2>Rating Pengajar</h2>

<form action="" method="POST" onsubmit="return confirm('Yakin ingin mengirim rating?');">
    <input type="hidden" name="siswa_id" value="<?php echo $siswa_id; ?>">
    
    <label for="pengajar">Nama Pengajar:</label>
    <select name="pengajar_id" required>
        <?php
        $query = "SELECT pengajar_id, full_name FROM mentor";
        $result = mysqli_query($conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['pengajar_id'] . "'>" . $row['full_name'] . "</option>";
            }
        }
        ?>
    </select>
    
    <label for="rating">Rating:</label>
    <select name="rating" required>
        <option value="1">⭐</option>
        <option value="2">⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="5">⭐⭐⭐⭐⭐</option>
    </select>
    
    <label for="komentar">Komentar:</label>
    <textarea name="komentar" placeholder="Tambahkan komentar (opsional)"></textarea>
    
    <button type="submit">Kirim Rating</button>
</form>

<script src="js/logout.js" defer></script>
<script src="js/home.js" defer></script>
<script src="js/menu.js" defer></script>

<?php
mysqli_close($conn);
?>
</body>
</html>
