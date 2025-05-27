<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Get attendance ID from URL
$id = $_GET['id'] ?? 0;
$notif = '';

try {
    // Get existing attendance data
    $query = "SELECT a.*, s.full_name AS nama_siswa 
              FROM absensi_siswa a
              JOIN siswa s ON a.siswa_id = s.siswa_id
              WHERE a.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception("Data tidak ditemukan!");
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $kelas = $_POST['kelas'] ?? '';
        $mapel = $_POST['mapel'] ?? '';
        $sekolah = $_POST['sekolah'] ?? '';
        $status = $_POST['status'] ?? '';
        $alasan = $_POST['alasan'] ?? '';
        $sesi = $_POST['sesi'] ?? '';

        if (empty($kelas) || empty($mapel) || empty($status) || empty($sesi)) {
            throw new Exception("Isi semua data wajib!");
        }

        $query = "UPDATE absensi_siswa 
                 SET kelas = ?, mapel = ?, sekolah = ?, 
                     status = ?, alasan = ?, sesi = ? 
                 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $kelas, $mapel, $sekolah, $status, $alasan, $sesi, $id);
        
        if ($stmt->execute()) {
            ?>
            <div class="notification-overlay">
                <div class="notification-popup">
                    <div class="notification-icon">
                        <i class="fas fa-check-circle success"></i>
                    </div>
                    <div class="notification-title">Berhasil</div>
                    <div class="notification-message">Data presensi berhasil diperbarui!</div>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = 'riwayat_absensi.php';
                }, 2000);
            </script>
            <?php
            exit();
        } else {
            ?>
            <div class="notification-overlay">
                <div class="notification-popup">
                    <div class="notification-icon">
                        <i class="fas fa-times-circle error"></i>
                    </div>
                    <div class="notification-title">Error</div>
                    <div class="notification-message">Gagal mengupdate data presensi!</div>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    window.history.back();
                }, 2000);
            </script>
            <?php
            throw new Exception("Gagal mengupdate data!");
        }
    }

} catch (Exception $e) {
    $notif = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Presensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .container {
            max-width: 600px;
            margin: 100px auto 0;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #145375;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
        }
        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
        }
        .btn-primary {
            background: #145375;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .notif {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
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
        @media (max-width: 768px) {
            .container {
                margin: 60px 10px 0;
            }
        }
    </style>
</head>
<body>
 
    <div class="container">
        <h2>Edit Presensi</h2>
        
        <?php if ($notif): ?>
            <div class="notif"><?= htmlspecialchars($notif) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Nama Siswa</label>
                <input type="text" value="<?= htmlspecialchars($data['nama_siswa']) ?>" disabled>
            </div>

                        <div class="form-group">
                <label for="sesi">Sesi *</label>
                <select name="sesi" id="sesi" required>
                    <option value="Sesi 1" <?= $data['sesi'] == 'Sesi 1' ? 'selected' : '' ?>>Sesi 1 (09.00-10.30)</option>
                    <option value="Sesi 2" <?= $data['sesi'] == 'Sesi 2' ? 'selected' : '' ?>>Sesi 2 (10.30-12.00)</option>
                    <option value="Sesi 3" <?= $data['sesi'] == 'Sesi 3' ? 'selected' : '' ?>>Sesi 3 (13.00-14.30)</option>
                    <option value="Sesi 4" <?= $data['sesi'] == 'Sesi 4' ? 'selected' : '' ?>>Sesi 4 (14.30-16.00)</option>
                    <option value="Sesi 5" <?= $data['sesi'] == 'Sesi 5' ? 'selected' : '' ?>>Sesi 5 (16.00-17.30)</option>
                    <option value="Sesi 6" <?= $data['sesi'] == 'Sesi 6' ? 'selected' : '' ?>>Sesi 6 (18.00-19.30)</option>
                    <option value="Sesi 7" <?= $data['sesi'] == 'Sesi 7' ? 'selected' : '' ?>>Sesi 7 (19.30-21.00)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="kelas">Kelas *</label>
                <input type="text" name="kelas" id="kelas" required 
                       value="<?= htmlspecialchars($data['kelas']) ?>">
            </div>

            <div class="form-group">
                <label for="mapel">Mata Pelajaran *</label>
                <input type="text" name="mapel" id="mapel" required 
                       value="<?= htmlspecialchars($data['mapel']) ?>">
            </div>

            <div class="form-group">
                <label for="sekolah">Sekolah</label>
                <input type="text" name="sekolah" id="sekolah" 
                       value="<?= htmlspecialchars($data['sekolah']) ?>">
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select name="status" id="status" required onchange="toggleAlasan()">
                    <option value="Hadir" <?= $data['status'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="Izin" <?= $data['status'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="Sakit" <?= $data['status'] == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                </select>
            </div>

            <div class="form-group" id="alasan-container" style="display: <?= ($data['status'] == 'Izin' || $data['status'] == 'Sakit') ? 'block' : 'none' ?>;">
                <label for="alasan">Alasan</label>
                <textarea name="alasan" id="alasan" rows="3"><?= htmlspecialchars($data['alasan']) ?></textarea>
            </div>


            <div class="btn-container">
                <a href="riwayat_absensi.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        function toggleAlasan() {
            const status = document.getElementById('status').value;
            const alasanContainer = document.getElementById('alasan-container');
            alasanContainer.style.display = (status === 'Izin' || status === 'Sakit') ? 'block' : 'none';
        }

        // Notification functions
        let deleteId = null;

        function showNotification(type, title, message) {
            const overlay = document.createElement('div');
            overlay.className = 'notification-overlay';
            
            const popup = document.createElement('div');
            popup.className = 'notification-popup';
            
            const icon = type === 'success' ? 'check-circle' : 'times-circle';
            const iconClass = type === 'success' ? 'success' : 'error';
            
            popup.innerHTML = `
                <div class="notification-icon">
                    <i class="fas fa-${icon} ${iconClass}"></i>
                </div>
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            `;
            
            overlay.appendChild(popup);
            document.body.appendChild(overlay);
            
            // Force reflow
            void overlay.offsetWidth;
            
            // Show animation
            requestAnimationFrame(() => {
                overlay.classList.add('show');
                popup.classList.add('show');
            });
            
            return overlay;
        }

        function hideNotification(overlay) {
            overlay.classList.remove('show');
            
            setTimeout(() => {
                overlay.remove();
            }, 300); // Match transition duration
        }

        // Handle form submission
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const overlay = showNotification(
                        'success',
                        'Berhasil',
                        'Data presensi berhasil diperbarui!'
                    );
                    
                    setTimeout(() => {
                        hideNotification(overlay);
                        window.location.href = 'riwayat_absensi.php';
                    }, 2000);
                } else {
                    throw new Error('Gagal mengupdate data');
                }
            } catch (error) {
                const overlay = showNotification(
                    'error',
                    'Error',
                    'Gagal mengupdate data presensi!'
                );
                
                setTimeout(() => {
                    hideNotification(overlay);
                }, 2000);
            }
        });

        // Initialize alasan visibility
        document.addEventListener('DOMContentLoaded', function() {
            toggleAlasan();
        });
    </script>
</body>
</html>