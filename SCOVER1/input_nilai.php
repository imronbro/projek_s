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

// Pastikan pengajar sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data pengajar berdasarkan email
$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$pengajar = $result->fetch_assoc();

if (!$pengajar) {
    die("Akun pengajar tidak ditemukan. Pastikan Anda login dengan akun pengajar yang benar.");
}

$pengajar_id   = $pengajar['pengajar_id'];
$pengajar_name = $pengajar['full_name'];

// Ambil data riwayat penilaian berdasarkan pengajar_id, dan join dengan tabel siswa untuk mendapatkan nama siswa
$query = "SELECT n.nilai, n.nama_kuis, n.waktu, s.full_name AS siswa_name
          FROM nilai_siswa n
          JOIN siswa s ON n.siswa_id = s.siswa_id
          WHERE n.pengajar_id = ?
          ORDER BY n.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penilaian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #003049;
            color: #fabe49;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #0271ab;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fabe49;
            color: #003049;
        }
        th, td {
            padding: 12px;
            border: 1px solid #003049;
            text-align: center;
        }
        th {
            background-color: #faaf1d;
        }
        tr:nth-child(even) {
            background-color: #e0e0e0;
        }
        .back-button {
            display: inline-block;
            background-color: #faaf1d;
            color: #003049;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #fabe49;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Riwayat Penilaian</h2>
    <p>Selamat datang, <?php echo htmlspecialchars($pengajar_name); ?></p>
    <table>
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th>Nama Kuis</th>
                <th>Nilai</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $tanggal = date("d-m-Y H:i:s", strtotime($row['waktu']));
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['siswa_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_kuis']); ?></td>
                        <td><?php echo htmlspecialchars($row['nilai']); ?></td>
                        <td><?php echo $tanggal; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Belum ada riwayat penilaian yang tersedia.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div style="text-align: center;">
        <a href="home_mentor.php" class="back-button">Kembali</a>
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
mysqli_close($conn);
?>
