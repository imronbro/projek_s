<?php
session_start();
include '../koneksi.php';

if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Field tambahan dikosongkan saja
    $ttl    = '0000-00-00';
    $alamat = '';
    $nohp   = '';
    $gambar = '';
    $mapel  = '';

    // Validasi password dan konfirmasi
    if ($password !== $confirm_password) {
        $notification = [
            'type' => 'error',
            'title' => 'Kata Sandi Tidak Cocok',
            'message' => 'Kata sandi dan konfirmasi kata sandi harus sama.'
        ];
    } elseif (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
        $notification = [
            'type' => 'error',
            'title' => 'Kata Sandi Tidak Valid',
            'message' => 'Kata sandi harus minimal 8 karakter dan mengandung setidaknya satu angka.'
        ];
    } else {
        // Cek email sudah terdaftar
        $cek = $conn->prepare("SELECT email FROM mentor WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $notification = [
                'type' => 'error',
                'title' => 'Email Sudah Terdaftar',
                'message' => 'Email ini sudah digunakan. Silakan gunakan email lain.'
            ];
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert ke database
            $stmt = $conn->prepare("INSERT INTO mentor (full_name, email, password, ttl, alamat, nohp, gambar, mapel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $full_name, $email, $hashed_password, $ttl, $alamat, $nohp, $gambar, $mapel);

            if ($stmt->execute()) {
                $notification = [
                    'type' => 'success',
                    'title' => 'Registrasi Berhasil!',
                    'message' => 'Akun Anda telah berhasil dibuat. Silakan login untuk melanjutkan.'
                ];
            } else {
                $notification = [
                    'type' => 'error',
                    'title' => 'Registrasi Gagal',
                    'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'
                ];
            }
            $stmt->close();
        }
        $cek->close();
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
</head>
<style>

    body {
        font-family: 'Poppins';
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

    /* Notification Styles */
    #notificationOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
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
        z-index: 10000; /* Increased z-index to be above overlay */
        text-align: center;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
        z-index: 9999; /* Increased z-index */
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .notification-overlay.show {
        opacity: 1;
        display: block;
        z-index: 9999;
    }

    .notification-popup.show {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
        display: block;
        z-index: 10000;
    }

    .notification-popup.success {
        border-left: 5px solid #4caf50;
    }

    .notification-popup.error {
        border-left: 5px solid #f44336;
    }

    #notificationTitle {
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    #notificationMessage {
        font-size: 14px;
        margin-bottom: 15px;
        text-align: center;
    }

    #notificationButton {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    #notificationButton:hover {
        background: #0056b3;
    }

    #notificationIcon {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 24px;
        color: #007bff;
    }

    .login-container {
        position: relative;
        z-index: 1;
    }
</style>

<body>
    <div class="login-container">
        <!-- Form Section -->
        <div class="login-box">
            <div class="logo-container">
                <a href="../index.php"> <!-- Tautkan logo ke halaman tampilan.php -->
                    <img src="images/foto4.png" alt="Logo Asrama Kita" class="logo">
                </a>
            </div>

            <h2>Pendaftaran Mentor</h2>


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
                    <a href="google_login.php" class="responsive-link">
                        <img src="images/signupgoogle.png" alt="Sign in with Google" class="responsive-img">
                    </a>
                </div>
        </div>

        <p class="login-link">Sudah punya akun? <a href="login_mentor.php">Masuk</a></p>
        </form>

    </div>
    </div>

    <!-- Notification Overlay -->
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
        // Replace the existing toggleVisibility function with this:
function toggleBothPasswords() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm-password');
    const togglePassword = document.getElementById('toggle-password');
    const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
    
    const type = password.type === 'password' ? 'text' : 'password';
    
    // Toggle both password fields
    password.type = type;
    confirmPassword.type = type;
    
    // Toggle both icons
    togglePassword.classList.toggle('fa-eye');
    togglePassword.classList.toggle('fa-eye-slash');
    toggleConfirmPassword.classList.toggle('fa-eye');
    toggleConfirmPassword.classList.toggle('fa-eye-slash');
}

// Add click handlers to both icons
document.getElementById('toggle-password').addEventListener('click', toggleBothPasswords);
document.getElementById('toggle-confirm-password').addEventListener('click', toggleBothPasswords);
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
                        window.location.href = 'login_mentor.php';
                    } else {
                        window.history.back();
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