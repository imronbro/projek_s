<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scover";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data user berdasarkan email dari session
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT nama, email, alamat, gambar, ttl, mapel, nohp FROM mentor WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login_mentor.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #003049;
            color: #fabe49;
        }
        .card {
            background-color: #145375;
            color:rgb(255, 255, 255);
            border: 2px solid rgb(255, 255, 255);
        }

        .card p {
            text-align:justify;
            padding-left:500px;
        }


        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 20px;
            border: 3px solid #faaf1d;
        }
        .btn-primary {
            background-color: #0271ab;
            border-color: #0271ab;
        }
        .btn-secondary {
            background-color: #faaf1d;
            border-color:rgb(88, 79, 59);
            color: #003049;
        }
        .btn-primary:hover {
            background-color: #145375;
            border-color: #145375;
        }
        .btn-secondary:hover {
            background-color: #fabe49;
            border-color: #fabe49;
        }
        h2 {
            color: #faaf1d;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Profil Pengguna Pengajar</h2>
        <div class="card p-3 shadow mb-4 text-center">
            <?php 
            $imagePath = "uploads/" . basename(htmlspecialchars($data['gambar']));
            if (!empty($data['gambar']) && file_exists($imagePath)) {
            ?>
                <img src="<?= $imagePath; ?>" alt="Foto Profil" class="profile-img">
            <?php 
            } else { 
            ?>
                <img src="uploads1/default.png" alt="Foto Profil Default" class="profile-img">
            <?php 
            } 
            ?>
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['nama']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
            <p><strong>Mata Pelajaran:</strong> <?= htmlspecialchars($data['mapel'] ?? '-'); ?></p>
            <p><strong>TTL:</strong> <?= htmlspecialchars($data['ttl'] ?? '-'); ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat'] ?? '-'); ?></p>
            <p><strong>No HP:</strong> <?= htmlspecialchars($data['nohp'] ?? '-'); ?></p>
        </div>
        <a href="home_mentor.php" class="btn btn-primary">Kembali</a>
        <a href="edit_profile_mentor.php" class="btn btn-secondary">Edit Profil</a>
    </div>
</body>
</html>
