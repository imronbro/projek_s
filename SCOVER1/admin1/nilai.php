<?php
$conn = new mysqli("localhost", "root", "", "scover");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil keyword pencarian
$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : "";

// Query gabung tabel nilai_siswa + mentor + siswa
$sql = "
    SELECT 
        n.*, 
        m.full_name AS nama_pengajar, 
        s.full_name AS nama_siswa
    FROM nilai_siswa n
    JOIN mentor m ON n.pengajar_id = m.pengajar_id
    JOIN siswa s ON n.siswa_id = s.siswa_id
    WHERE s.full_name LIKE '%$keyword%'
    ORDER BY n.waktu DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nilai Siswa</title>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h3 {
            text-align: center;
            color: #145375;
            margin: 30px 0;
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }

        .filter-bar form {
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #e6c200;
            color: #145375;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        button:hover {
            background-color: #145375;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        th, td {
            padding: 10px 12px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background-color: #145375;
            color: white;
            text-transform: uppercase;
            font-size: 12px;
        }

        tr:hover {
            background-color: #f1f9ff;
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
            <li><a href="home.php">Presensi</a></li>
            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php" class="active">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
    </nav>

    <div class="container">
        <h3>Data Nilai Siswa</h3>

        <div class="filter-bar">
            <form method="GET">
                <input type="text" name="keyword" placeholder="Cari Nama Siswa..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Nama Pengajar</th>
                    <th>Nama Kuis</th>
                    <th>Nilai</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td><?= htmlspecialchars($row['nama_pengajar']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kuis']) ?></td>
                            <td><?= htmlspecialchars($row['nilai']) ?></td>
                            <td><?= htmlspecialchars($row['waktu']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Tidak ada data ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $conn->close(); ?>
