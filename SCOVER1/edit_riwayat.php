<?php
session_start();
include 'koneksi.php'; // File koneksi database

// Periksa apakah user sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID presensi dari URL
if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='riwayat_presensi.php';</script>";
    exit();
}

$presensi_id = $_GET['id'];

// Ambil data presensi berdasarkan ID
$query = "SELECT * FROM presensi_siswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $presensi_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $waktu_presensi = new DateTime($row['waktu_presensi']);
    $current_time = new DateTime();

    // Periksa apakah waktu akses masih dalam 1 jam
    if ($current_time->getTimestamp() - $waktu_presensi->getTimestamp() > 3600) {
        echo "<script>alert('Akses untuk edit atau hapus telah kadaluarsa!'); window.location.href='riwayat_presensi.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='riwayat_presensi.php';</script>";
    exit();
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $tanggal = $_POST['tanggal'];
        $sesi = $_POST['sesi'];
        $status = $_POST['status'];
        $komentar = $_POST['komentar'];

        $update_query = "UPDATE presensi_siswa SET tanggal = ?, sesi = ?, status = ?, komentar = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $tanggal, $sesi, $status, $komentar, $presensi_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Data berhasil diperbarui!'); window.location.href='riwayat_presensi.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data!');</script>";
        }
    }

    // Proses hapus data
    if (isset($_POST['delete'])) {
        $delete_query = "DELETE FROM presensi_siswa WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $presensi_id);

        if ($delete_stmt->execute()) {
            echo "<script>alert('Data berhasil dihapus!'); window.location.href='riwayat_presensi.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Riwayat Presensi</title>
    <link rel="stylesheet" href="css/edit_riwayat.css">
</head>
<body>
<div class="container">
    <h2>Edit atau Hapus Data Presensi</h2>
    <form method="POST">
        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($row['tanggal']); ?>" required>

        <label for="sesi">Sesi:</label>
        <input type="text" id="sesi" name="sesi" value="<?php echo htmlspecialchars($row['sesi']); ?>" required>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Hadir" <?php echo $row['status'] === 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
            <option value="Tidak Hadir" <?php echo $row['status'] === 'Tidak Hadir' ? 'selected' : ''; ?>>Tidak Hadir</option>
        </select>

        <label for="komentar">Komentar:</label>
        <textarea id="komentar" name="komentar"><?php echo htmlspecialchars($row['komentar']); ?></textarea>

        <div class="button-container">
            <button type="submit" name="update" class="btn update-btn">Perbarui</button>
            <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</button>
        </div>
    </form>
</div>
</body>
</html>