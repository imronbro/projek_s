<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$user_email = $_SESSION['user_email'];

$id = intval($_GET['id']);

// Ambil data jadwal
$query = "SELECT * FROM jadwal_siswa WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan!";
    exit;
}

$jadwal = $result->fetch_assoc();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $sesi = $_POST['sesi'];
    $mata_pelajaran = $_POST['mata_pelajaran'];
    $pengajar = $_POST['pengajar'];

    $update = "UPDATE jadwal SET 
        tanggal = '$tanggal',
        sesi = '$sesi',
        mata_pelajaran = '$mata_pelajaran',
        pengajar_name = '$pengajar'
        WHERE id = $id";

    if ($conn->query($update)) {
        echo "<script>alert('Jadwal berhasil diperbarui'); window.location='jadwal.php';</script>";
    } else {
        echo "Gagal memperbarui: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Jadwal</title>
    <style>
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label, input, select {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        input, select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: darkblue;
            color: #fff;
            border: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Edit Jadwal</h2>
    <form method="post">

        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($jadwal['tanggal']) ?>" required>

        <label>Sesi</label>
        <select name="sesi" required>
            <option <?= $jadwal['sesi'] === 'Pagi' ? 'selected' : '' ?>>Pagi</option>
            <option <?= $jadwal['sesi'] === 'Siang' ? 'selected' : '' ?>>Siang</option>
            <option <?= $jadwal['sesi'] === 'Sore' ? 'selected' : '' ?>>Sore</option>
        </select>

        <label>Mata Pelajaran</label>
        <input type="text" name="mata_pelajaran" value="<?= htmlspecialchars($jadwal['mata_pelajaran']) ?>" required>

        <label>Pengajar</label>
        <input type="text" name="pengajar" value="<?= htmlspecialchars($jadwal['full_name'] ??' ')?>" required>

        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>
