<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID absensi tidak ditemukan!'); window.location.href='riwayat_absensi.php';</script>";
    exit();
}

$id = $_GET['id'];

// Hapus data absensi berdasarkan ID
$query = "DELETE FROM absensi_siswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Data absensi berhasil dihapus!'); window.location.href='riwayat_absensi.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data absensi!'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>