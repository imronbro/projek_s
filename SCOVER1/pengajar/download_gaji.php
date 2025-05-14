<?php
require '../dompdf/autoload.inc.php';
include '../koneksi.php';

use Dompdf\Dompdf;

session_start();

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
    die("Akun pengajar tidak ditemukan.");
}

$pengajar_id = $pengajar['pengajar_id'];
$pengajar_name = $pengajar['full_name'];

// Ambil bulan dan tahun dari parameter GET
$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m', strtotime('last month'));
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Daftar honor per kelas
$honor = [
    'AL-IZZAH' => 200000,
    'AL-UMM' => 80000,
    'AR-ROHMAH' => 150000,
    'MENGAJAR LUAR KOTA' => 100000,
    'MENGAJAR POWER HOUR' => 40000,
    'MENGAJAR TETAP' => 80000,
    'ONLINE CLASS' => 80000,
    'OLIMPIADE' => 120000,
    'TELKOM' => 80000,
    'THURSINA' => 100000,
    'SOSIALISAS' => 80000,
];

// Ambil data presensi
$query = "SELECT tanggal, kelas, mapel 
          FROM presensi_pengajar 
          WHERE pengajar_id = ? 
          AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? 
          AND status = 'Hadir'";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $pengajar_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

// Bangun HTML
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #145375;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
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
        .info {
            margin: 20px 0;
            font-size: 16px;
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
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
        <p>PT Edumedia Solusi Kreatif</p>
    </div>

    <h2>Slip Gaji Pengajar</h2>

    <div class="info">
        <strong>Nama:</strong> ' . htmlspecialchars($pengajar_name) . '<br>
        <strong>Bulan:</strong> ' . date('F', mktime(0, 0, 0, $selected_month, 1)) . ' ' . $selected_year . '
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Honor</th>
            </tr>
        </thead>
        <tbody>';

$total = 0;
$no = 1;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kelas = strtoupper(trim($row['kelas']));
        $honor_kelas = $honor[$kelas] ?? 0;
        $total += $honor_kelas;

        $html .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . date("d-m-Y", strtotime($row['tanggal'])) . '</td>
                <td>' . htmlspecialchars($kelas) . '</td>
                <td>' . htmlspecialchars($row['mapel']) . '</td>
                <td>Rp ' . number_format($honor_kelas, 0, ',', '.') . '</td>
            </tr>';
    }

    $html .= '
        <tr>
            <td colspan="4" style="text-align: right;"><strong>Total Gaji</strong></td>
            <td><strong>Rp ' . number_format($total, 0, ',', '.') . '</strong></td>
        </tr>';
} else {
    $html .= '
        <tr>
            <td colspan="5">Tidak ada data presensi untuk bulan ini.</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh sistem pada ' . date('d-m-Y H:i') . '<br>
        © ' . date('Y') . ' PT Edumedia Solusi Kreatif.
    </div>
</body>
</html>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("slip_gaji_" . str_replace(' ', '_', $pengajar_name) . ".pdf", ['Attachment' => true]);

$stmt->close();
$conn->close();
?>
