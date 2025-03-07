<?php

session_start();

$host = "localhost";

$user = "root";   

$pass = "";  

$db   = "scover";


$conn = mysqli_connect($host, $user, $pass, $db);



if (!$conn) {

    die("Koneksi database gagal: " . mysqli_connect_error());

}



// Proses registrasi

if (isset($_POST['register'])) {

    // Ambil data dari form

    $full_name = $_POST['full_name'];

    $email = $_POST['email'];

    $password = $_POST['password'];

    $confirm_password = $_POST['confirm-password'];



    // Validasi password

    if ($password !== $confirm_password) {

        echo "Kata sandi dan konfirmasi kata sandi tidak cocok.";

        exit;

    }



    // Hash password

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);



    // Insert ke database

    $query = "INSERT INTO mentor (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";

    

    if (mysqli_query($conn, $query)) {

        // Jika registrasi berhasil, tampilkan notifikasi dan alihkan ke halaman login

        echo "<script>

                alert('Registrasi berhasil!');

                window.location.href = 'login_mentor.php'; // Mengarahkan ke halaman login

              </script>";

    } else {

        echo "Error: " . $query . "<br>" . mysqli_error($conn);

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
<div class="login-container">
        <!-- Form Section -->
        <div class="login-box">
            <div class="logo-container">
                <a href="index.php"> <!-- Tautkan logo ke halaman tampilan.php -->
                    <img src="images/foto4.png" alt="Logo Asrama Kita" class="logo">
                </a>
            </div>

            <h2>Pendaftaran Mentor</h2>


            <form method="POST" action="">
                <div class="input-field">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Masukkan full_name Lengkap" required>
                </div>
                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                </div>

                <div class="input-field">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required>
                </div>

                <div class="input-field">
                    <label for="confirm-password">Konfirmasi Kata Sandi</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Ulangi Kata Sandi" required>
                </div>

                <button type="submit" class="btn" name="register">Daftar</button>
            </form>

            <p class="login-link">Sudah punya akun? <a href="login_mentor.php">Masuk</a></p>
        </div>
    </div>
    
</body>
</html>
