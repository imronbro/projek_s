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
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            
            if (isset($_POST['remember'])) {
                setcookie("email_mentor", $email, time() + (10 * 365 * 24 * 60 * 60), "/");
                setcookie("password_mentor", $password, time() + (10 * 365 * 24 * 60 * 60), "/");
            } else {
                setcookie("email_mentor", "", time() - 3600, "/");
                setcookie("password_mentor", "", time() - 3600, "/");
            }
            
            $notification = [
                'type' => 'success',
                'title' => 'Login Berhasil!',
                'message' => 'Selamat datang kembali, ' . $user['full_name']
            ];
            $_SESSION['redirect'] = 'home_mentor.php';
        } else {
            $notification = [
                'type' => 'error',
                'title' => 'Login Gagal',
                'message' => 'Password yang Anda masukkan salah.'
            ];
        }
    } else {
        $notification = [
            'type' => 'error',
            'title' => 'Login Gagal',
            'message' => 'Email tidak ditemukan.'
        ];
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
        /* default: 10px 20px */
    

        border-radius: 5px;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s ease;

        width: 100%;
        /* Tombol memenuhi lebar form */
        box-sizing: border-box;
    }


    .btn-google:hover {
        background-color: #c23321;
    }

    .btn-google i {
        margin-right: 8px;
        font-size: 16px;
    }

    .notification-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .notification-popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        width: 320px;
        padding: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        text-align: center;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notification-overlay.show {
        opacity: 1;
        display: block;
    }

    .notification-popup.show {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
        display: block;
    }

    .notification-icon {
        font-size: 52px;
        margin-bottom: 20px;
        display: inline-block;
        transform: scale(0);
        transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .notification-popup.show .notification-icon {
        transform: scale(1);
    }

    .notification-popup.success {
        border-top: 5px solid #28a745;
    }

    .notification-popup.error {
        border-top: 5px solid #dc3545;
    }

    .notification-popup.success .notification-icon {
        color: #28a745;
    }

    .notification-popup.error .notification-icon {
        color: #dc3545;
    }

    .notification-title {
        font-size: 20px;
        font-weight: 600;
        color: #145375;
        margin-bottom: 12px;
    }

    .notification-message {
        font-size: 15px;
        color: #666;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .notification-button {
        background: #145375;
        color: white;
        border: none;
        padding: 12px 35px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .notification-button:hover {
        background: #0e3e5a;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(20, 83, 117, 0.3);
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
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; margin-top: 10px;">
                            <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px; margin: 0;"
                                <?php if (isset($_COOKIE['email_mentor'])) echo 'checked'; ?>>
                            <label for="remember" style="margin: 0; font-weight: normal; white-space: nowrap;">Ingatkan saya nanti</label>
                        </div>
                        <br>
                        <div class="forgot">
                            <a href="lupa.php">Lupa Kata Sandi?</a>
                        </div>
                        <br>
                        <button type="submit" class="btn" name="login">Masuk</button>
                        <div class="input-field">
                            <p style="text-align: center;">Atau masuk dengan:</p>
                            <a href="google_login.php" class="responsive-link">
                                <img src="images/continue2.png" alt="Sign in with Google">
                            </a>
                        </div>
                        
                        <p class="responsive-text">Belum punya akun? <a href="register_mentor.php">Buat Akun</a></p>
                </form>
            </div>
        </div>
    </div>
    <div class="notification-overlay" id="notificationOverlay"></div>
<div class="notification-popup" id="notificationPopup">
    <div class="notification-icon">
        <i class="fas fa-check-circle" id="notificationIcon"></i>
    </div>
    <div class="notification-title" id="notificationTitle"></div>
    <div class="notification-message" id="notificationMessage"></div>
    <button class="notification-button" id="notificationButton">OK</button>
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
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('notificationOverlay');
    const popup = document.getElementById('notificationPopup');
    const title = document.getElementById('notificationTitle');
    const message = document.getElementById('notificationMessage');
    const button = document.getElementById('notificationButton');
    const icon = document.getElementById('notificationIcon');

    function showNotification(type, titleText, messageText) {
        overlay.style.display = 'block';
        popup.style.display = 'block';
        
        title.innerText = titleText;
        message.innerText = messageText;
        
        popup.className = `notification-popup ${type}`;
        icon.className = type === 'success' ? 
            'fas fa-check-circle' : 
            'fas fa-exclamation-circle';
        
        setTimeout(() => {
            overlay.classList.add('show');
            popup.classList.add('show');
        }, 10);
    }

    function hideNotification(callback) {
        overlay.classList.remove('show');
        popup.classList.remove('show');
        
        setTimeout(() => {
            overlay.style.display = 'none';
            popup.style.display = 'none';
            if (callback) callback();
        }, 300);
    }

    button.onclick = function() {
        hideNotification(() => {
            if (popup.classList.contains('success')) {
                window.location.href = '<?php echo isset($_SESSION["redirect"]) ? $_SESSION["redirect"] : "home_mentor.php"; ?>';
            }
        });
    };

    <?php if (isset($notification)): ?>
    showNotification(
        '<?php echo $notification['type']; ?>', 
        '<?php echo $notification['title']; ?>', 
        '<?php echo $notification['message']; ?>'
    );
    <?php endif; ?>
});
</script>

</body>

</html>