<?php
session_start();
include '../koneksi.php';

if (isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validasi password dan konfirmasi
    if ($password !== $confirm_password) {
        echo "<script>alert('Kata sandi dan konfirmasi kata sandi tidak cocok.'); window.history.back();</script>";
        exit;
    }

    // Validasi panjang dan angka
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
        echo "<script>alert('Kata sandi harus minimal 8 karakter dan mengandung setidaknya satu angka.'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert ke database
    $query = "INSERT INTO siswa (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Registrasi berhasil!');
                window.location.href = 'login.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }

        .input-field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
        }



        .input-with-icon input {
            width: 100%;
            padding: 10px 10px 10px 38px;
            /* padding kiri untuk ruang ikon */
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .input-with-icon .icon-left {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #145375;
            font-size: 16px;
        }

        .icon-right {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #145375;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-google {
            display: inline-block;
            background-color: #db4437;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-google:hover {
            background-color: #c23321;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Form Section -->
        <div class="login-box">
            <div class="logo-container">
                <a href="../index.php"> <!-- Tautkan logo ke halaman tampilan.php -->
                    <img src="images/foto4.png" alt="Logo Asrama Kita" class="logo">
                </a>
            </div>

            <h2>Pendaftaran Siswa</h2>


            <form method="POST" action="">
                <div class="input-field">
                    <label for="full_name">Nama Lengkap</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user icon-left"></i>
                        <input type="text" id="full_name" name="full_name" placeholder="Masukkan Nama Lengkap" required>

                    </div>
                </div>

                <div class="input-field">
                    <label for="email">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope icon-left"></i>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" required />
                    </div>
                </div>
                <div class="input-field">
                    <label for="password">Kata Sandi</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock icon-left"></i> <!-- Ikon Kunci -->
                        <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required />
                        <i class="fas fa-eye icon-right" id="toggle-password"></i> <!-- Ikon Mata -->
                    </div>
                </div>


                <div class="input-field">
                    <label for="confirm-password">Konfirmasi Kata Sandi</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock icon-left"></i>
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Ulangi Kata Sandi" required>
                        <i class="fas fa-eye icon-right" id="toggle-confirm-password"></i>
                    </div>
                </div>
                <br>
                <button type="submit" class="btn" name="register">Daftar</button>
                <div class="input-field">
                    <p style="text-align: center;">Atau daftar dengan:</p>
                    <a href="google_login.php" style="display: inline-block; margin-top: 2px;">
                        <img src="images/signupgoogle.png" alt="Sign in with Google" style="width: 90%; max-width: 200px; margin-left: 150px;">
                    </a>
                </div>

        </div>


        <p class="login-link">Sudah punya akun? <a href="login.php">Masuk</a></p>
        </form>
    </div>
    </div>
    <script>
        function toggleVisibility(toggleId, inputId) {
            const toggleIcon = document.getElementById(toggleId);
            const inputField = document.getElementById(inputId);

            toggleIcon.addEventListener('click', function() {
                const type = inputField.type === 'password' ? 'text' : 'password';
                inputField.type = type;
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        toggleVisibility('toggle-password', 'password');
        toggleVisibility('toggle-confirm-password', 'confirm-password');
    </script>



</body>

</html>