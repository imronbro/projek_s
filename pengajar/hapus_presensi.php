<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Add notification variable
$notification = null;

if (!isset($_GET['id'])) {
    $notification = [
        'type' => 'error',
        'title' => 'Error!',
        'message' => 'ID presensi tidak ditemukan!'
    ];
} else {
    $id = $_GET['id'];
    $query = "DELETE FROM presensi_pengajar WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $notification = [
            'type' => 'success',
            'title' => 'Berhasil!',
            'message' => 'Data absensi berhasil dihapus!'
        ];
    } else {
        $notification = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Gagal menghapus data absensi!'
        ];
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Presensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .notification-popup {
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
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            text-align: center;
        }

        .notification-icon {
            font-size: 52px;
            margin-bottom: 20px;
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
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #145375;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notification-button:hover {
            background: #0e3e5a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(20, 83, 117, 0.3);
        }

        .notification-overlay.show,
        .notification-popup.show {
            opacity: 1;
            visibility: visible;
        }

        .notification-popup.show {
            transform: translate(-50%, -50%) scale(1);
        }
    </style>
</head>
<body>
    <div class="notification-overlay" id="notificationOverlay"></div>
    <div class="notification-popup" id="notificationPopup">
        <div class="notification-icon">
            <i class="fas" id="notificationIcon"></i>
        </div>
        <div class="notification-title" id="notificationTitle"></div>
        <div class="notification-message" id="notificationMessage"></div>
        <button class="notification-button" id="notificationButton">OK</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('notificationOverlay');
        const popup = document.getElementById('notificationPopup');
        const title = document.getElementById('notificationTitle');
        const message = document.getElementById('notificationMessage');
        const button = document.getElementById('notificationButton');
        const icon = document.getElementById('notificationIcon');

        function showNotification(type, titleText, messageText) {
            popup.className = `notification-popup ${type}`;
            icon.className = `fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`;
            title.textContent = titleText;
            message.textContent = messageText;

            overlay.style.display = 'block';
            popup.style.display = 'block';

            setTimeout(() => {
                overlay.classList.add('show');
                popup.classList.add('show');
            }, 10);
        }

        button.onclick = function() {
            overlay.classList.remove('show');
            popup.classList.remove('show');

            setTimeout(() => {
                overlay.style.display = 'none';
                popup.style.display = 'none';
                if (popup.classList.contains('success')) {
                    window.location.href = 'riwayat_presensi_mentor.php';
                } else {
                    window.history.back();
                }
            }, 300);
        };

        <?php if ($notification): ?>
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