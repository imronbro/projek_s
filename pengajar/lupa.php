<?php
include '../koneksi.php';

// Proses ubah password
if (isset($_POST['submit'])) {
    // Tangkap data dari form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validasi input
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $message = "Semua kolom harus diisi!";
    } elseif ($new_password != $confirm_password) {
        $message = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Cek apakah email ada di database
        $query = "SELECT * FROM mentor WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Jika email ditemukan, update password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query = "UPDATE mentor SET password = '$hashed_password' WHERE email = '$email'";
            if (mysqli_query($conn, $update_query)) {
                $message = "Password berhasil diubah. <a href='login_mentor.php'>Login</a>";
            } else {
                $message = "Gagal mengubah password. Coba lagi!";
            }
        } else {
            $message = "Email tidak ditemukan di database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    </style>
</head>
<style>/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(-45deg, #333, #676767, #000000, #003366);
    background-size: 400% 400%;
    animation: gradientAnimation 15s ease infinite;
    overflow: hidden;
}

/* Gradient Background Animation */
@keyframes gradientAnimation {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.container {
    width: 350px;
    padding: 20px 25px;
    background-color: rgba(244, 246, 249, 0.95); /* Transparan */
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    text-align: center;
    position: relative;
    animation: fadeIn 1.5s ease-in-out;
}

/* Fade In Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

h2 {
    color: #003366;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 15px;
}

p {
    font-size: 14px;
    margin-bottom: 15px;
    color: #555;
}

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #003366;
    box-shadow: 0 0 5px #003366;
}

button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(to right, #000000, #333333);
    color: #ffffff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    letter-spacing: 1px;
    transition: background 0.9s ease, transform 0.1s ease;
}

button:hover {
    background: linear-gradient(to right, #737373, #000000);
    transform: scale(1.05);
}

a {
    color: #003366;
    text-decoration: none;
    font-size: 13px;
    transition: color 0.3s ease;
}

a:hover {
    color: #0055aa;
}

/* Pesan Notifikasi */
.message {
    padding: 10px;
    background-color: #ffdddd;
    color: #a33;
    border: 1px solid #f44336;
    border-radius: 5px;
    margin-bottom: 15px;
}
</style>
<body>
    <div class="container">
        <h2>Lupa Password</h2>
        <p>Masukkan email Anda dan buat password baru.</p>

        <!-- Menampilkan pesan -->
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="email" name="email" placeholder="Masukkan Email Anda" required>
            <input type="password" name="new_password" placeholder="Password Baru" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password Baru" required>
            <button type="submit" name="submit">Ubah Password</button>
        </form>
        <p>Kembali? <a href="login_mentor.php">Login</a></p>
    </div>
</body>
</html>