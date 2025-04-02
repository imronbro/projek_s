<?php
session_start();
include 'koneksi.php';

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
    $rating = isset($_POST["rating"]) ? $_POST["rating"] : null;
    $komentar = $_POST["komentar"];

    // Cek apakah rating sudah dipilih
    if (!$rating) {
        echo "<script>alert('Harap pilih rating sebelum mengirim.'); window.history.back();</script>";
        exit();
    }

    // Cek apakah user sudah memberikan rating dalam 90 menit terakhir
    $query = "SELECT created_at FROM rating_pengajar WHERE siswa_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_rating = $result->fetch_assoc();

    if ($last_rating) {
        $last_rating_time = strtotime($last_rating['created_at']);
        $current_time = time();
        $time_diff = $current_time - $last_rating_time;

        if ($time_diff < 5400) { // 5400 detik = 90 menit
            echo "<script>alert('Anda hanya bisa memberikan rating setiap 90 menit sekali.'); window.history.back();</script>";
            exit();
        }
    }

    // Simpan rating ke database
    $sql = "INSERT INTO rating_pengajar (pengajar_id, siswa_id, rating, komentar, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $pengajar_id, $siswa_id, $rating, $komentar);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Rating berhasil dikirim!'); window.location.href = 'rating.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($conn) . "'); window.history.back();</script>";
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
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/rating.css">
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

    <div class="rating">
    <input type="radio" name="rating" id="star5" value="5">
    <label for="star5">⭐</label>

    <input type="radio" name="rating" id="star4" value="4">
    <label for="star4">⭐</label>

    <input type="radio" name="rating" id="star3" value="3">
    <label for="star3">⭐</label>

    <input type="radio" name="rating" id="star2" value="2">
    <label for="star2">⭐</label>

    <input type="radio" name="rating" id="star1" value="1">
    <label for="star1">⭐</label>
</div>
    
    <label for="komentar">Komentar:</label>
    <textarea name="komentar" placeholder="Tambahkan komentar (opsional)"></textarea>
    
    <button type="submit">Kirim Rating</button>
</form>

<script src="js/logout.js" defer></script>
<script src="js/home.js" defer></script>
<script src="js/menu.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const stars = document.querySelectorAll(".rating input");
        const labels = document.querySelectorAll(".rating label");

        stars.forEach((star) => {
            star.addEventListener("change", function () {
                updateStars(this.value);
            });
        });

        function updateStars(value) {
            labels.forEach((label, index) => {
                if (index >= 5 - value) {
                    label.style.color = "gold";
                } else {
                    label.style.color = "#ccc"; 
                }
            });
        }
    });
</script>


<?php
mysqli_close($conn);
?>
</body>
</html>
