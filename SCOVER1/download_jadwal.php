<?php
session_start();
include 'koneksi.php';
require('fpdf/fpdf.php');

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

$siswa_id   = $siswa['siswa_id'];
$siswa_name = $siswa['full_name'];

// Ambil jadwal siswa
$tanggal_sekarang = date('Y-m-d');
$query = "SELECT j.tanggal, j.sesi, j.mata_pelajaran, m.full_name AS pengajar 
          FROM jadwal_siswa j
          LEFT JOIN mentor m ON j.pengajar_id = m.pengajar_id
          WHERE j.siswa_id = ? AND j.tanggal >= ?
          ORDER BY j.tanggal, j.sesi";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $siswa_id, $tanggal_sekarang);
$stmt->execute();
$result = $stmt->get_result();

// Buat PDF menggunakan FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header PDF
$pdf->Cell(0, 10, 'Jadwal Siswa', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Nama Siswa: ' . $siswa_name, 0, 1, 'L');
$pdf->Cell(0, 10, 'Tanggal: ' . date('d-m-Y'), 0, 1, 'L');
$pdf->Ln(10);

// Header Tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(30, 10, 'Sesi', 1, 0, 'C');
$pdf->Cell(70, 10, 'Mata Pelajaran', 1, 0, 'C');
$pdf->Cell(50, 10, 'Pengajar', 1, 1, 'C');

// Isi Tabel
$pdf->SetFont('Arial', '', 12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['tanggal'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['sesi'], 1, 0, 'C');
        $pdf->Cell(70, 10, $row['mata_pelajaran'], 1, 0, 'C');
        $pdf->Cell(50, 10, $row['pengajar'] ?? 'Tidak Ditemukan', 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada jadwal tersedia.', 1, 1, 'C');
}

// Output PDF
$pdf->Output('D', 'Jadwal_Siswa_' . date('d-m-Y') . '.pdf');
?>