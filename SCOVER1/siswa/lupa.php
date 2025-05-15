<?php
// filepath: c:\xampp\htdocs\projek_s-1\SCOVER1\siswa\lupa.php
session_start();
include '../koneksi.php'; // Koneksi ke database
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi PHPMailer

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Periksa apakah email ada di database
    $query = "SELECT * FROM siswa WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Buat token unik untuk reset password
        $token = bin2hex(random_bytes(50));
        $reset_link = "http://localhost/projek_s-1/SCOVER1/siswa/reset_password.php?token=$token";

        // Simpan token ke database
        $update_query = "UPDATE siswa SET reset_token = '$token', reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = '$email'";
        mysqli_query($conn, $update_query);

        // Kirim email reset password menggunakan PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Ganti dengan email Anda
            $mail->Password = 'your-app-password'; // Ganti dengan App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = 2; // Ubah ke 3 untuk lebih detail
            $mail->Debugoutput = 'html';

            // Pengaturan email
            $mail->setFrom('your-email@gmail.com', 'Asrama Kita');
            $mail->addAddress($email); // Email penerima
            $mail->Subject = 'Reset Kata Sandi Anda';
            $mail->Body = "Klik tautan berikut untuk mereset kata sandi Anda: $reset_link\n\nTautan ini berlaku selama 1 jam.";

            // Kirim email
            $mail->send();
            echo "<script>alert('Tautan reset kata sandi telah dikirim ke email Anda.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Gagal mengirim email. Kesalahan: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/lupa.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Lupa Kata Sandi</h2>
            <p>Masukkan email Anda untuk menerima tautan reset kata sandi.</p>
            <form method="POST" action="">
                <div class="input-field">
                    <label for="email">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope icon-left"></i>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                    </div>
                </div>
                <button type="submit" class="btn" name="submit">Kirim Tautan Reset</button>
            </form>
            <p class="login-link">Kembali ke <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>