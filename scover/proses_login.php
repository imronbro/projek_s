<?php
session_start();
$conn = new mysqli("localhost", "root", "", "scover_db");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tangkap data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Cek apakah nama dan email cocok dengan database
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? AND email = ?");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $name;
        header("Location: beranda.html"); // Redirect ke halaman beranda
        exit();
    } else {
        echo "<script>alert('Nama atau Email salah!'); window.location.href='index.html';</script>";
    }
}
?>
