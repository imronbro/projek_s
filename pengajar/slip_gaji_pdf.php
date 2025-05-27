<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data pengajar
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

$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Poppins";
            padding: 20px;
            color: #333;
            background-color: #fff;
        }
        
        h1, h2 {
    text-align: center;
    color: #145375;
    margin: 0;
}

h1 {
    font-size: 28px;
    margin-bottom: 10px;
}

h2 {
    font-size: 20px;
    margin-bottom: 30px;
}
       .info {
    margin-top: 30px;
    margin-bottom: 30px;
    text-align: left;
    font-size: 14px;
    line-height: 1.6;
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
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
        .total {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .print-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #145375;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .print-btn:hover {
            background-color: #0f3a5a;
        }
        @media print {
    .no-print {
        display: none !important;
    }
    @media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}

}

    </style>
</head>
<body>

<div>
    
    <h1>Scover</h1>
    <h2>Slip Gaji Pengajar</h2>
</div>

    <div class="info">
        <p><strong>Nama Pengajar:</strong> <?= htmlspecialchars($pengajar_name) ?></p>
        <p><strong>Periode:</strong> <?= date('F Y', mktime(0, 0, 0, $selected_month, 1)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Keterangan</th>
                <th>Jumlah HR</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data_presensi) > 0): ?>
                <?php foreach ($data_presensi as $item): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($item['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($item['sesi']) ?></td>
                        <td><?= htmlspecialchars($item['kelas']) ?></td>
                        <td><?= htmlspecialchars($item['mapel']) ?></td>
                        <td><?= htmlspecialchars($item['keterangan']) ?></td>
                        <td>Rp <?= number_format($item['tarif'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Tidak ada data presensi untuk bulan ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total">
        Total Gaji: Rp <?= number_format($total_gaji, 0, ',', '.') ?>
    </div>

    <div class="footer">
        © <?= date('Y') ?> PT Edumedia Solusi Kreatif. All rights reserved.
    </div>

   <div style="text-align:center; margin-top:20px;">
    <button class="print-btn no-print" onclick="window.print()">Unduh/ Cetak PDF</button>
    <button class="print-btn no-print" onclick="history.back()">Kembali</button>
</div>


</body>
</html>
