<?php
require '../dompdf/autoload.inc.php';
include '../koneksi.php';

use Dompdf\Dompdf;

session_start();

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
$siswa = $result->fetch_assoc();

if (!$siswa) {
    die("Akun siswa tidak ditemukan.");
}

$siswa_id = $siswa['siswa_id'];
$siswa_name = $siswa['full_name'];

$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$query = "SELECT n.nilai, n.nama_kuis, n.waktu, p.full_name AS pengajar_name 
          FROM nilai_siswa n 
          JOIN mentor p ON n.pengajar_id = p.pengajar_id 
          WHERE n.siswa_id = ? AND MONTH(n.waktu) = ? AND YEAR(n.waktu) = ?
          ORDER BY n.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $siswa_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

$html = '
<!DOCTYPE html>
<html lang="id">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            height: auto;
        }
        .header h1 {
            margin: 10px 0 5px;
            font-size: 24px;
            color: #145375;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            color: #e6c200;
            font-weight: bold;
        }
        h2 {
            text-align: center;
            background-color: #145375;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #145375;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #e6c200;
            color: #145375;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .info {
            margin: 20px 0;
            font-size: 16px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Scover</h1>
        <h1>Study and Discover</h1>
        <p>PT Edumedia Solusi Kreatif</p>
    </div>
    
    <h2>Daftar Nilai Siswa</h2>
    
    <div class="info">
        <strong>Nama:</strong> ' . htmlspecialchars($siswa_name) . '<br>
        <strong>Bulan:</strong> ' . date('F', mktime(0, 0, 0, $selected_month, 1)) . ' ' . $selected_year . '
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nama Kuis</th>
                <th>Nilai</th>
                <th>Pengajar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tanggal = date("d-m-Y H:i:s", strtotime($row['waktu']));
        $html .= '
            <tr>
                <td>' . htmlspecialchars($row['nama_kuis']) . '</td>
                <td>' . htmlspecialchars($row['nilai']) . '</td>
                <td>' . htmlspecialchars($row['pengajar_name']) . '</td>
                <td>' . $tanggal . '</td>
            </tr>';
    }
} else {
    $html .= '
            <tr>
                <td colspan="4">Tidak ada nilai untuk bulan dan tahun ini.</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        © ' . date('Y') . ' PT Edumedia Solusi Kreatif. All rights reserved.
    </div>
</body>
</html>';

$stmt->close();
$conn->close();

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('isRemoteEnabled', true); // Jika Anda ingin load font atau gambar dari URL
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('nilai_siswa_' . $selected_month . '_' . $selected_year . '.pdf', ['Attachment' => true]);
exit();
?>
