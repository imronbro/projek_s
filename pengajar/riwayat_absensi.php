<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

try {
    $user_email = $_SESSION['user_email'];

    // Ambil data pengajar
    $query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $user_email);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $pengajar_id = $row['pengajar_id'];
        $full_name = $row['full_name'];
    } else {
        throw new Exception("Akun tidak ditemukan!");
    }
    $stmt->close();

    // Ambil data riwayat absensi dengan error handling
    $query = "SELECT a.id, s.full_name AS nama_siswa, a.kelas, a.mapel, a.sekolah, 
              a.status, a.alasan, a.sesi, a.tanggal, a.waktu_presensi 
              FROM absensi_siswa a
              JOIN siswa s ON a.siswa_id = s.siswa_id
              WHERE a.pengajar_id = ?
              ORDER BY a.tanggal DESC";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $pengajar_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;

        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            overflow-x: hidden;
            margin-top: 110px;
        }

        .container {
            margin-top: 80px;
            padding: 20px;
            max-width: 1300px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeIn 0.5s ease-out;
        }

        .desktop-mode-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #145375;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            display: none;
            z-index: 1000;
        }

        h2 {
            color: #145375;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #145375, #faaf1d);
            border-radius: 2px;
        }

        .table-scroll-container {
            position: relative;
            margin-top: 20px;
        }

        .table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            -webkit-overflow-scrolling: touch; /* Smooth scroll on iOS */
        }

        .scroll-indicator {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            color: #666;
            font-size: 12px;
            display: none;
            align-items: center;
            gap: 5px;
        }

        .scroll-indicator svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            min-width: 120px; /* Prevent narrow columns */
        }

        th {
            background-color: #145375;
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr {
            transition: transform 0.2s ease;
        }

        tr:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .btn-edit, .btn-delete {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            margin: 2px;
        }

        .btn-edit {
            background: #4CAF50;
            color: white;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .text-muted {
            color: #666;
            font-size: 0.8rem;
            display: block;
            margin-top: 5px;
        }

        /* Add these styles in your existing <style> tag */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-edit, .btn-delete {
            flex: 1;
            min-width: 70px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-edit {
            background: #145375;
            color: white;
        }

        .btn-edit:hover {
            background: #0d3c54;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .action-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .text-muted {
            text-align: center;
            width: 100%;
            margin-top: 5px;
            font-size: 0.75rem;
        }

        /* Responsive Styles */
        @media screen and (max-width: 1024px) {
            .container {
                padding: 15px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }

        @media screen and (max-width: 768px) {
            .container {
                margin-top: 70px;
                padding: 10px;
            }

            .desktop-mode-btn {
                display: block;
            }

            .force-desktop {
                min-width: 1024px;
            }

            .scroll-indicator {
                display: flex;
            }

            th, td {
                padding: 8px;
                font-size: 14px;
            }

            .btn-edit, .btn-delete {
                padding: 5px 10px;
                font-size: 12px;
            }

            .action-buttons {
        flex-direction: column;
    }
    
    .btn-edit, .btn-delete {
        width: 100%;
        padding: 6px 10px;
        font-size: 12px;
    }
        }

        @media screen and (max-width: 480px) {
            .container {
                margin-top: 60px;
                padding: 8px;
            }

            h2 {
                font-size: 1.2rem;
                margin-bottom: 15px;
            }

            th, td {
                padding: 8px;
                font-size: 0.85rem;
                min-width: 100px;
            }

            .btn-container {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .btn-edit, .btn-delete {
                width: 100%;
                text-align: center;
                padding: 8px;
                margin: 2px 0;
            }

            tr:hover {
                transform: none;
            }

            td {
        padding: 8px 4px;
    }
    
    .action-buttons {
        gap: 4px;
    }
    
    .btn-edit, .btn-delete {
        padding: 5px 8px;
        font-size: 11px;
        min-width: 60px;
    }
        }

        /* Custom Scrollbar */
        .table-container::-webkit-scrollbar {
            height: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #145375;
            border-radius: 3px;
        }

        /* Loading Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
        }
  .header-flex {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    position: relative;
    margin-bottom: 25px;
}

.header-flex h2 {
    margin: 0;
    text-align: center;
    flex: 1 1 100%;
    font-size: 1.8rem;
    font-weight: 600;
    color: #145375;
    position: relative;
    padding-bottom: 10px;
}

.btn-back {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    background-color: #e6c200;
    color: #145375;
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    transition: background-color 0.3s ease;
    z-index: 999; /* <=== tambahkan ini */
}

@media screen and (max-width: 600px) {
    .header-flex {
        flex-direction: column;
        align-items: center;
    }

    .btn-back {
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        margin-bottom: 10px;
    }

    .header-flex h2 {
        font-size: 1.4rem;
    }
}

#deleteOverlay {
    display: none;
}

#deleteConfirmation {
    display: none;
}

/* Replace existing notification styles */
.notification-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.notification-overlay.show {
    opacity: 1;
    visibility: visible;
}

.notification-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.notification-popup.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, -50%) scale(1);
}

.notification-icon i {
    font-size: 48px;
    color: #dc3545;
    margin-bottom: 20px;
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

.notification-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.notification-button {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.cancel-button {
    background: #ffffff;
    color: #145375;
    border: 2px solid #145375;
}

.cancel-button:hover {
    background: #145375;
    color: #ffffff;
}

.confirm-button {
    background: #dc3545;
    color: #ffffff;
}

.confirm-button:hover {
    background: #c82333;
}
    </style>
</head>
<body>
    <div class="container">
       <div class="header-flex">
    <a href="proses_presensi.php" class="btn-back">Kembali</a>
    <h2>Riwayat Absensi</h2>
</div>

        
<nav class="navbar">
    <div class="logo">
        <img src="images/foto4.png" alt="Logo">
    </div>
    <h1 class="title">Dashboard Mentor</h1>
    <ul class="nav-links">
        <li><a href="home_mentor.php">Jurnal</a></li>
        <li><a href="proses_presensi.php" class="active">Presensi Siswa</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>
  

        <div class="table-scroll-container">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th>Sekolah</th>
                            <th>Status</th>
                            <th>Alasan</th>
                            <th>Sesi</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if ($result && $result->num_rows > 0):
                            while($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($row['kelas']) ?></td>
                                <td><?= htmlspecialchars($row['mapel']) ?></td>
                                <td><?= htmlspecialchars($row['sekolah']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['alasan'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($row['sesi']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                                <td>
                                    <?php
                                    $waktu_presensi = strtotime($row['waktu_presensi']);
                                    $batas_edit = 30 * 60; // 30 menit dalam detik
                                    $waktu_sekarang = time();
                                    $dapat_diedit = ($waktu_sekarang - $waktu_presensi) <= $batas_edit;
                                    
                                    if ($dapat_diedit):
                                    ?>
                                        <div class="action-container">
                                            <div class="action-buttons">
                                                <a href="edit_absensi.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                                <a href="javascript:void(0)" 
                                                   class="btn-delete" 
                                                   onclick="showDeleteConfirmation(<?= $row['id'] ?>)">
                                                   <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </div>
                                            <small class="text-muted">
                                                Sisa waktu: <?= gmdate("i:s", $batas_edit - ($waktu_sekarang - $waktu_presensi)) ?>
                                            </small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak dapat diedit</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="10" style="text-align: center;">Tidak ada data presensi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="scroll-indicator">
                <svg viewBox="0 0 24 24">
                    <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/>
                </svg>
                Geser tabel ke kanan/kiri
                <svg viewBox="0 0 24 24">
                    <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                </svg>
            </div>
        </div>
    </div>
    <a href="#" class="desktop-mode-btn" onclick="toggleDesktopMode(event)">View Desktop</a>
    <div class="notification-overlay" id="deleteOverlay"></div>
<div class="notification-popup" id="deleteConfirmation">
    <div class="notification-icon">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="notification-title">Konfirmasi Hapus</div>
    <div class="notification-message">Apakah Anda yakin ingin menghapus data ini?</div>
    <div class="notification-buttons">
        <button class="notification-button cancel-button" id="cancelDelete">
            <i class="fas fa-times"></i> Batal
        </button>
        <button class="notification-button confirm-button" id="confirmDelete">
            <i class="fas fa-check"></i> Hapus
        </button>
    </div>
</div>
     <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
    <script src="js/menu.js" defer></script>
    <script>
        function toggleDesktopMode(e) {
            e.preventDefault();
            const container = document.querySelector('.container');
            container.classList.toggle('force-desktop');
            
            const btn = document.querySelector('.desktop-mode-btn');
            if (container.classList.contains('force-desktop')) {
                btn.textContent = 'View Mobile';
            } else {
                btn.textContent = 'View Desktop';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const tableWrapper = document.querySelector('.table-wrapper');
            const scrollIndicator = document.querySelector('.scroll-indicator');
            
            function checkScroll() {
                if (tableWrapper.scrollWidth > tableWrapper.clientWidth) {
                    scrollIndicator.style.display = 'flex';
                } else {
                    scrollIndicator.style.display = 'none';
                }
            }

            // Check on load and resize
            checkScroll();
            window.addEventListener('resize', checkScroll);
        });

let deleteId = null;

function showDeleteConfirmation(id) {
    deleteId = id;
    const overlay = document.getElementById('deleteOverlay');
    const popup = document.getElementById('deleteConfirmation');
    
    // Show both overlay and popup
    overlay.style.display = 'block';
    popup.style.display = 'block';
    
    // Force reflow
    void overlay.offsetWidth;
    void popup.offsetWidth;
    
    // Add show classes
    overlay.classList.add('show');
    popup.classList.add('show');
    
    // Setup event listeners
    document.getElementById('cancelDelete').onclick = hideDeleteConfirmation;
    document.getElementById('confirmDelete').onclick = confirmDelete;
    overlay.onclick = function(e) {
        if (e.target === overlay) {
            hideDeleteConfirmation();
        }
    };
}

function hideDeleteConfirmation() {
    const overlay = document.getElementById('deleteOverlay');
    const popup = document.getElementById('deleteConfirmation');
    
    // Remove show classes
    overlay.classList.remove('show');
    popup.classList.remove('show');
    
    // Hide after animation
    setTimeout(() => {
        overlay.style.display = 'none';
        popup.style.display = 'none';
    }, 300);
    
    deleteId = null;
}

function confirmDelete() {
    if (deleteId) {
        window.location.href = `hapus_absensi.php?id=${deleteId}`;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('deleteOverlay');
    const popup = document.getElementById('deleteConfirmation');
    
    overlay.style.display = 'none';
    popup.style.display = 'none';
});
    </script>
</body>
</html>