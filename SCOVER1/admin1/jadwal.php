<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$user_email = $_SESSION['user_email'];

// Proses tambah jadwal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siswa_id = $_POST['siswa_id'];
    $tanggal = $_POST['tanggal'];
    $sesi = $_POST['sesi'];
    $mata_pelajaran = $_POST['mata_pelajaran'];
    $pengajar = $_POST['pengajar'];

    $query = "INSERT INTO jadwal_siswa (siswa_id, tanggal, sesi, mata_pelajaran, pengajar) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $siswa_id, $tanggal, $sesi, $mata_pelajaran, $pengajar);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal!');</script>";
    }
    $stmt->close();
}

// Ambil daftar siswa
$siswa_result = $conn->query("SELECT siswa_id, full_name FROM siswa");

// Ambil semua jadwal
$jadwal_result = $conn->query("SELECT j.id, s.full_name, j.tanggal, j.sesi, j.mata_pelajaran, j.pengajar 
                               FROM jadwal_siswa j 
                               JOIN siswa s ON j.siswa_id = s.siswa_id 
                               ORDER BY j.tanggal, j.sesi");

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Atur Jadwal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="admin1/jadwal.css">
</head>
<body>
    <h2>Atur Jadwal Siswa</h2>

    <!-- Form Tambah Jadwal -->
    <form action="" method="post">
        <label for="siswa_id">Siswa:</label>
        <select name="siswa_id" required>
            <?php while ($row = $siswa_result->fetch_assoc()) { ?>
                <option value="<?= $row['siswa_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
            <?php } ?>
        </select>

        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" required>

        <label for="sesi">Sesi:</label>
        <select name="sesi" required>
            <option value="Sesi 1">Sesi 1 (09.00-10.30)</option>
            <option value="Sesi 2">Sesi 2 (10.30-12.00)</option>
            <option value="Sesi 3">Sesi 3 (13.00-14.30)</option>
            <option value="Sesi 4">Sesi 4 (14.30-16.00)</option>
            <option value="Sesi 5">Sesi 5 (16.00-17.30)</option>
            <option value="Sesi 6">Sesi 6 (18.00-19.30)</option>
            <option value="Sesi 7">Sesi 7 (19.30-21.00)</option>
        </select>

        <label for="mata_pelajaran">Mata Pelajaran:</label>
        <input type="text" name="mata_pelajaran" required>

        <label for="pengajar">Pengajar:</label>
        <input type="text" name="pengajar" required>

        <button type="submit">Tambah Jadwal</button>
    </form>

    <!-- Tabel Jadwal -->
    <h3>Jadwal yang Telah Diatur</h3>
    <table border="1">
        <tr>
            <th>Siswa</th>
            <th>Tanggal</th>
            <th>Sesi</th>
            <th>Mata Pelajaran</th>
            <th>Pengajar</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $jadwal_result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['sesi']) ?></td>
            <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
            <td><?= htmlspecialchars($row['pengajar']) ?></td>
            <td>
                <a href="edit_jadwal.php?id=<?= $row['id'] ?>">Edit</a> |
                <a href="hapus_jadwal.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
