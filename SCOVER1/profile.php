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
    $query = "SELECT full_name, email, sekolah, alamat, gambar, kelas, ttl, nohp 
              FROM siswa 
              WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
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
/* Global Styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #2c3e50;
    color: #ecf0f1;
    margin: 0;
    padding: 0;
}

/* Container and Card Styling */
.container {
    margin: 50px auto;
    max-width: 800px;
    background-color: #34495e;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: left;
}

.card {
    background-color: #3b4a5a;
    padding: 20px;
    border-radius: 8px;
    color: #ecf0f1;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card p {
    margin: 10px 0;
    font-size: 1.1rem;
}

.card p strong {
    color: #3498db;
}

/* Buttons Styling */
.btn-primary {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    border: none;
    text-align: center;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    color: #fff;
}

.actions {
    text-align: center;
    margin-top: 20px;
}

/* Back Button */
.back {
    background-color: #6c757d;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.back:hover {
    background-color: #545b62;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        margin: 20px;
        padding: 15px;
    }

    .card p {
        font-size: 1rem;
    }

    .btn-primary {
        width: 100%;
        padding: 12px;
    }

    .actions {
        margin-top: 15px;
    }
}

</style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Profil Pengguna</h2>
        <div class="card p-3 shadow mb-4">
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['full_name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
            <p><strong>Sekolah:</strong> <?= htmlspecialchars($data['sekolah'] ?? '-'); ?></p>
            <p><strong>Kelas:</strong> <?= htmlspecialchars($data['kelas'] ?? '-'); ?></p>
            <p><strong>TTL:</strong> <?= htmlspecialchars($data['ttl'] ?? '-'); ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat'] ?? '-'); ?></p>
            <p><strong>No HP:</strong> <?= htmlspecialchars($data['nohp'] ?? '-'); ?></p>
            <p><strong>Upload Profile:</strong> <?= htmlspecialchars($data['jurusan'] ?? '-'); ?></p>
        </div>
        <a href="home.php" class="btn btn-primary">Kembali</a>
    </div>
    <div class="actions">
        <a href="edit_profile.php" class="btn btn-primary">Edit Profil</a>
            </div>
</body>
</body>
</html>
