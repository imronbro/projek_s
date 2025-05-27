<?php
session_start();
include '../koneksi.php'; // Koneksi database

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM admin WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            header("Location: home.php");
            // Cek Remember Me
            if (isset($_POST['remember'])) {
               // Simpan cookie selama 10 tahun
               setcookie("email_admin", $email, time() + (10 * 365 * 24 * 60 * 60), "/");
               setcookie("password_admin", $password, time() + (10 * 365 * 24 * 60 * 60), "/");
           } else {
               // Hapus cookie jika tidak dicentang
               setcookie("email_admin", "", time() - 3600, "/");
               setcookie("password_admin", "", time() - 3600, "/");
           }
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
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    
</head>
<style>
    body {
        font-family: 'poppins';
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f4f4f4;
        color: #fff;
    }

    .input-field {
        margin-bottom: 10px;
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
        top: 34%;
        transform: translateY(-50%);
        color: #145375;
        font-size: 16px;
    }

    .icon-right {
        position: absolute;
        right: 12px;
        top: 34%;
        transform: translateY(-50%);
        color: #145375;
        font-size: 16px;
        cursor: pointer;
    }
</style>

<body>
    <style> 
    body{
        font-family: 'Poppins';
    }
</style>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <div class="icon"></div>
                <h1>SCOVER</h1>
            </div>
        </div>
        <div class="right-section">
            <div class="form-container">
                <h2>Masuk</h2>
                <p>Masuk jika Anda sudah memiliki akun.</p>
                <form method="POST" action="">
                    <div class="input-field">
                        <label for="email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope icon-left"></i>
                            <input type="email" id="email" name="email" placeholder="Masukan Email" required
                            value="<?php if (isset($_COOKIE['email_admin'])) echo $_COOKIE['email_admin']; ?>">
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="password">Kata Sandi</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock icon-left"></i>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" placeholder="Masukan Kata Sandi"required
                                value="<?php if (isset($_COOKIE['password_admin'])) echo $_COOKIE['password_admin']; ?>">
                            </div>
                            <i class="fas fa-eye icon-right" id="toggle-password"></i> <!-- Ikon Mata -->
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; margin-top: 10px;">
                        <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px; margin: 0;"
                            <?php if (isset($_COOKIE['email_admin'])) echo 'checked'; ?>>
                        <label for="remember" style="margin: 0; font-weight: normal; white-space: nowrap;">Ingatkan saya nanti</label>
                    </div>
                    <br>
                    <button type="submit" class="btn" name="login">Masuk</button>
                    <p>Belum punya akun? <a href="register.php">Buat Akun</a></p>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.querySelector('.icon').addEventListener('click', function() {
        window.location.href = '../index.php';
    });
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