<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

if (!isset($_GET['id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            .notification-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(5px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .notification-popup {
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                text-align: center;
                max-width: 400px;
                width: 90%;
                font-family: 'Poppins', sans-serif;
            }

            .notification-icon i {
                font-size: 48px;
                margin-bottom: 20px;
            }

            .notification-icon i.success {
                color: #28a745;
            }

            .notification-icon i.error {
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
            }
        </style>
    </head>
    <body>
    <div class="notification-overlay">
        <div class="notification-popup">
            <div class="notification-icon">
                <i class="fas fa-exclamation-circle error"></i>
            </div>
            <div class="notification-title">Error</div>
            <div class="notification-message">ID absensi tidak ditemukan!</div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'riwayat_absensi.php';
        }, 2000);
    </script>
    </body>
    </html>
    <?php
    exit();
}

$id = $_GET['id'];

// Hapus data absensi berdasarkan ID
$query = "DELETE FROM absensi_siswa WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            .notification-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(5px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .notification-popup {
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                text-align: center;
                max-width: 400px;
                width: 90%;
                font-family: 'Poppins', sans-serif;
            }

            .notification-icon i {
                font-size: 48px;
                margin-bottom: 20px;
            }

            .notification-icon i.success {
                color: #28a745;
            }

            .notification-icon i.error {
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
            }
        </style>
    </head>
    <body>
    <div class="notification-overlay">
        <div class="notification-popup">
            <div class="notification-icon">
                <i class="fas fa-check-circle success"></i>
            </div>
            <div class="notification-title">Berhasil</div>
            <div class="notification-message">Data absensi berhasil dihapus!</div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'riwayat_absensi.php';
        }, 2000);
    </script>
    </body>
    </html>
    <?php
} else {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
            .notification-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(5px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .notification-popup {
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                text-align: center;
                max-width: 400px;
                width: 90%;
                font-family: 'Poppins', sans-serif;
            }

            .notification-icon i {
                font-size: 48px;
                margin-bottom: 20px;
            }

            .notification-icon i.success {
                color: #28a745;
            }

            .notification-icon i.error {
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
            }
        </style>
    </head>
    <body>
    <div class="notification-overlay">
        <div class="notification-popup">
            <div class="notification-icon">
                <i class="fas fa-times-circle error"></i>
            </div>
            <div class="notification-title">Error</div>
            <div class="notification-message">Gagal menghapus data absensi!</div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            window.history.back();
        }, 2000);
    </script>
    </body>
    </html>
    <?php
}

$stmt->close();
$conn->close();
?>