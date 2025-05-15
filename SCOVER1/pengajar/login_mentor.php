<?php
session_start();
include '../koneksi.php';

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
            // Cek Remember Me
            if (isset($_POST['remember'])) {
                // Simpan cookie selama 10 tahun
                setcookie("email_mentor", $email, time() + (10 * 365 * 24 * 60 * 60), "/");
                setcookie("password_mentor", $password, time() + (10 * 365 * 24 * 60 * 60), "/");
            } else {
                // Hapus cookie jika tidak dicentang
                setcookie("email_mentor", "", time() - 3600, "/");
                setcookie("password_mentor", "", time() - 3600, "/");
            }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<style>
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
        margin-top: 10px;
        width: 100%; /* Tombol memenuhi lebar form */
        box-sizing: border-box;
    }

    .btn-google:hover {
        background-color: #c23321;
    }

    .btn-google i {
        margin-right: 8px;
        font-size: 16px;
    }
</style>

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
                    <div class="input-field">
                        <label for="email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope icon-left"></i>
                            <input type="email" id="email" name="email" placeholder="Masukan Email" required
                                value="<?php if (isset($_COOKIE['email_mentor'])) echo $_COOKIE['email_mentor']; ?>">
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="password">Kata Sandi</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock icon-left"></i>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" placeholder="Masukan Kata Sandi" required
                                value="<?php if (isset($_COOKIE['password_mentor'])) echo $_COOKIE['password_mentor']; ?>">
                            </div>
                            <i class="fas fa-eye icon-right" id="toggle-password"></i> <!-- Ikon Mata -->
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; margin-top: 10px;">
                        <input type="checkbox" name="remember" id="remember"
                            style="width: 16px; height: 16px; margin: 0;"
                            <?php if (isset($_COOKIE['email_mentor'])) echo 'checked'; ?>>

                        <label for="remember" style="margin: 0; font-weight: normal; white-space: nowrap;">Ingatkan saya nanti</label>
                    </div>
                    <br>
                    <div class="forgot">
                        <a href="forgot_password.php">Lupa Kata Sandi?</a>
                    </div>
                    <br>
                    <button type="submit" class="btn" name="login">Masuk</button>
                    <div class="input-field">
                            <p style="text-align: center;">Atau masuk dengan:</p>
                            <a href="google_login.php" class="btn-google" style="margin-top: 4px;">
                                <i class="fab fa-google"></i> Masuk dengan Google
                            </a>
                    </div>
                    <br>
                    <p>Belum punya akun? <a href="register_mentor.php">Buat Akun</a></p>
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