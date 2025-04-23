<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus presensi berdasarkan ID
    $sql = "DELETE FROM presensi_pengajar WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Presensi berhasil dihapus!'); window.location.href='riwayat_presensi_mentor.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus presensi.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID presensi tidak ditemukan.'); window.history.back();</script>";
}

$conn->close();
?>