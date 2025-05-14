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
$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Daftar tarif berdasarkan keterangan
$tarif_mapel = [
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
    'SOSIALISASI' => 80000
];

// Ambil data presensi
$query = "SELECT tanggal, sesi, mapel, kelas, keterangan 
          FROM presensi_pengajar 
          WHERE pengajar_id = ? 
            AND MONTH(tanggal) = ? 
            AND YEAR(tanggal) = ? 
            AND status = 'Hadir'";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $pengajar_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

$data_presensi = [];
$total_gaji = 0;

while ($row = $result->fetch_assoc()) {
    $keterangan = strtoupper(trim($row['keterangan']));
    $tarif = $tarif_mapel[$keterangan] ?? 0;
    $total_gaji += $tarif;

    $data_presensi[] = [
        'tanggal' => $row['tanggal'],
        'sesi' => $row['sesi'],
        'kelas' => strtoupper(trim($row['kelas'])),
        'mapel' => strtoupper(trim($row['mapel'])),
        'keterangan' => $keterangan,
        'tarif' => $tarif
    ];
}

// Buat HTML untuk PDF
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
            color: #333;
        }
        h1, h2 {
            text-align: center;
            color: #145375;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #145375;
            padding: 8px;
            font-size: 12px;
            text-align: center;
        }
        th {
            background-color: #e6c200;
        }
        .info {
            margin-bottom: 20px;
        }
        .total {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Scover</h1>
    <h2>Slip Gaji Pengajar</h2>

    <div class="info">
        <strong>Nama Pengajar:</strong> ' . htmlspecialchars($pengajar_name) . '<br>
        <strong>Periode:</strong> ' . date('F Y', mktime(0, 0, 0, $selected_month, 1)) . '
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Keterangan</th>
                <th>Tarif</th>
            </tr>
        </thead>
        <tbody>';

if (count($data_presensi) > 0) {
    foreach ($data_presensi as $item) {
        $html .= '
            <tr>
                <td>' . date('d-m-Y', strtotime($item['tanggal'])) . '</td>
                <td>' . htmlspecialchars($item['sesi']) . '</td>
                <td>' . htmlspecialchars($item['kelas']) . '</td>
                <td>' . htmlspecialchars($item['mapel']) . '</td>
                <td>' . htmlspecialchars($item['keterangan']) . '</td>
                <td>Rp ' . number_format($item['tarif'], 0, ',', '.') . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="6">Tidak ada data presensi untuk bulan ini.</td></tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total">
        Total Gaji: Rp ' . number_format($total_gaji, 0, ',', '.') . '
    </div>

    <div style="margin-top: 40px; text-align: center; font-size: 12px;">
        © ' . date('Y') . ' PT Edumedia Solusi Kreatif. All rights reserved.
    </div>
</body>
</html>';

// Export PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("slip_gaji_{$selected_month}_{$selected_year}.pdf", ['Attachment' => true]);

$stmt->close();
$conn->close();
?>
