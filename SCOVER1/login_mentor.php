<?php
session_start();
include 'koneksi.php'; // Koneksi database

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM mentor WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_name'] = $user['full_name']; // Simpan nama lengkap di sesi
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['mentor_id'] = $user['pengajar_id']; // Simpan pengajar_id ke sesi
            header("Location: home_mentor.php");
            exit();
        } else {
            echo "<script>alert('Password salah!');</script>";
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
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <div class="icon"></div>
                <h1>SCOVER</h1>
            </div>
        </div>
        <div class="right-section">
            <div class="form-container">
                <h2>Masuk Mentor</h2>
                <p>Masuk jika Anda sudah memiliki akun.</p>
                <form method="POST" action="">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukan Email" required>

                    <label for="password">Kata Sandi</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Masukan Kata Sandi" required>
                    </div>
                    <div class="forgot">
                        <a href="forgot_password.php">Lupa Kata Sandi?</a>
                    </div>
                    <button type="submit" class="btn" name="login">Masuk</button>
                    <p>Belum punya akun? <a href="register_mentor.php">Buat Akun</a></p>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.querySelector('.icon').addEventListener('click', function() {
        window.location.href = 'index.php';
    });
</script>
</body>
</html>
