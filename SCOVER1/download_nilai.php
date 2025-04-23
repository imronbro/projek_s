<?php
require 'dompdf/autoload.inc.php'; // Pastikan path ini sesuai dengan lokasi Dompdf Anda
include 'koneksi.php';

use Dompdf\Dompdf;

session_start();

// Pastikan siswa sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data siswa berdasarkan email
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

// Ambil bulan dan tahun dari parameter GET
$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query untuk mengambil data nilai berdasarkan filter
$query = "SELECT n.nilai, n.nama_kuis, n.waktu, p.full_name AS pengajar_name 
          FROM nilai_siswa n 
          JOIN mentor p ON n.pengajar_id = p.pengajar_id 
          WHERE n.siswa_id = ? AND MONTH(n.waktu) = ? AND YEAR(n.waktu) = ?
          ORDER BY n.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $siswa_id, $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

// Format data nilai menjadi HTML
$html = '<h2>Daftar Nilai Siswa</h2>';
$html .= '<p>Nama: ' . htmlspecialchars($siswa_name) . '</p>';
$html .= '<p>Bulan: ' . date('F', mktime(0, 0, 0, $selected_month, 1)) . ' ' . $selected_year . '</p>';
$html .= '<table border="1" style="width: 100%; border-collapse: collapse; text-align: center;">';
$html .= '<thead>
            <tr>
                <th>Nama Kuis</th>
                <th>Nilai</th>
                <th>Pengajar</th>
                <th>Tanggal</th>
            </tr>
          </thead>';
$html .= '<tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tanggal = date("d-m-Y H:i:s", strtotime($row['waktu']));
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['nama_kuis']) . '</td>
                    <td>' . htmlspecialchars($row['nilai']) . '</td>
                    <td>' . htmlspecialchars($row['pengajar_name']) . '</td>
                    <td>' . $tanggal . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="4">Tidak ada nilai untuk bulan dan tahun ini.</td></tr>';
}

$html .= '</tbody></table>';

// Periksa format unduhan
if (isset($_GET['format']) && $_GET['format'] === 'pdf') {
    // Unduh sebagai PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('nilai_siswa.pdf', ['Attachment' => true]);
} else {
    // Unduh sebagai HTML
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="nilai_siswa.html"');
    echo $html;
}

$stmt->close();
$conn->close();
?>