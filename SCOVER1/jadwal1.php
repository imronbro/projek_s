<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
$user_email = $_SESSION['user_email'];

$query = "SELECT siswa_id, full_name FROM siswa WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $siswa_id = $row['siswa_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt->close();

$sql = "SELECT * FROM jadwal WHERE siswa_id = ? ORDER BY tanggal, sesi";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Siswa</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <nav class="navbar">
        <h1>Jadwal Siswa</h1>
        <a href="home.php">Kembali</a>
    </nav>
    <div class="content">
        <table border="1">
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Mata Pelajaran</th>
                <th>Pengajar</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['sesi']) ?></td>
                    <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
                    <td><?= htmlspecialchars($row['pengajar']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
