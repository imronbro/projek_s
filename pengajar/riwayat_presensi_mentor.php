<?php
session_start();
include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta'); 

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Ambil data mentor
$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login_mentor.php';</script>";
    exit();
}
$stmt->close();

// Ambil riwayat presensi mentor
$sql = "SELECT id, tanggal, sesi, status, mapel, materi, kelas, jumlah_siswa, keterangan, note, gambar, waktu_presensi 
        FROM presensi_pengajar 
        WHERE pengajar_id = ? 
        ORDER BY waktu_presensi DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Presensi Mentor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="css/home.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
    <style>
              * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins';
            background-color: #fff;
            color: #fabe49;
            margin: 0;
            padding: 0;
        }

        .container {
            width: max-content;
            margin: 60px auto 20px;
            background-color: #145375;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
      .btn-kembali {
    display: inline-block;
    background-color: #e6c200;
    color: #145375;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
    border-radius: 5px;
    text-decoration: none;
    margin-left: 2px;
    margin-bottom: 10px;
    margin-top: 150px; /* Jarak dari atas navbar */
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;
}

.btn-kembali:hover {
    background-color: #d1ad00;
}



        /* Gaya untuk modal */
    .modal {
    display: none; /* hanya ini, jangan ada display:flex di sini */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.7);
    align-items: center;
    justify-content: center;
}


    /* Gambar di dalam modal */
    .modal-content {
        max-width: 300px;
        width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        animation: zoomIn 0.3s ease;
    }

    /* Tombol close */
    .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #fff;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
    }

        .table-wrapper {
            overflow-x: auto;
            max-width: 100%;
            margin-bottom: 15px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            color: #145375;
            border-radius: 10px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #145375;
            text-align: center;
            white-space: nowrap;
        }

        th {
            background-color: #e6c200;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #e0e0e0;
        }
        .btn-view {
            background-color: #145375;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-action {
            background-color: #145375;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .back-button {
            display: inline-block;
            background-color: #e6c200;
            color: #145375;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }

        .scroll-hint {
            display: none;
            text-align: center;
            margin: 10px 0;
            font-size: 0.9rem;
        }

        @media screen and (max-width: 768px) {
            .container {
                width: 120%;
                margin-top: 40px;
                
                padding: 15px;
            }
            .btn-kembali {
        margin: 100px auto 10px 15px;
        padding: 8px 16px;
        font-size: 13px;
    }

            h2 {
                font-size: 1.5rem;
            }

            .scroll-hint {
                display: block;
            }

            th, td {
                padding: 8px;
                font-size: 0.9rem;
            }

            .back-button {
                width: 100%;
                padding: 12px;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                width: 120%;
                margin-top: 70px;
                padding: 10px;
                border-radius: 0;
            }

            h2 {
                font-size: 1.3rem;
            }

            th, td {
                padding: 6px;
                font-size: 0.85rem;
            }
        }
        .btn-edit {
    padding: 8px 16px;
    background-color: #145375; /* warna kuning */
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-edit:hover {
    background-color: #e6c200; /* warna kuning lebih gelap saat hover */
}

.btn-delete {
    padding: 8px 16px;
    background-color: #dc3545; /* warna merah */
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-delete:hover {
    background-color: #a71d2a; /* merah lebih gelap saat hover */
}

.notification-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 25px;
}

.notification-button {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cancel-button {
    background: #ffffff;
    color: #145375;
    border: 2px solid #145375;
}

.cancel-button:hover {
    background: #145375;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(20, 83, 117, 0.2);
}

.confirm-button {
    background: #dc3545;
    color: #ffffff;
    border: 2px solid #dc3545;
}

.confirm-button:hover {
    background: #c82333;
    border-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.notification-button:active {
    transform: translateY(0);
    box-shadow: none;
}

/* Add animation for buttons */
@keyframes buttonPop {
    0% {
        transform: scale(0.95);
    }
    100% {
        transform: scale(1);
    }
}

.notification-button {
    animation: buttonPop 0.3s ease-out;
}

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
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Mentor</h1>
    <ul class="nav-links">
        <li><a href="home_mentor.php"class="active">Jurnal</a></li>
        <li><a href="proses_presensi.php">Presensi Siswa</a></li>
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
 


<a href="home_mentor.php" class="btn-kembali">Kembali</a>
<div class="container">
    <h2>Riwayat Presensi Mentor</h2>
    <div class="scroll-hint">
        ← Geser untuk melihat selengkapnya →
    </div>
    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Status</th>
                <th>Gambar</th>
                <th>Waktu Presensi</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Jumlah Siswa</th>
                <th>Materi</th>
                <th>Keterangan</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $waktu_presensi = strtotime($row['waktu_presensi']);
                $batas_edit = 30 * 60; // 30 menit dalam detik
                $waktu_sekarang = time();
                $dapat_diedit = ($waktu_sekarang - $waktu_presensi) <= $batas_edit;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['sesi']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <button class="btn-view" onclick="showImageModal('<?= htmlspecialchars($row['gambar']) ?>')">Lihat</button>
                        <?php else: ?>
                            Tidak Ada Gambar
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($row['waktu_presensi']))) ?></td>
                    <td><?= htmlspecialchars($row['mapel']) ?></td>
                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['materi']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td><?= htmlspecialchars($row['note']) ?></td>
                    <td>
                        <?php if ($dapat_diedit): ?>
                            <div class="action-container">
                                <button class="btn-action" onclick="showActionMenu(this)">Aksi</button>
                                <div class="action-menu" style="display: none;">
                                    <a href="edit_presensi.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="javascript:void(0)" 
                                       class="btn-delete" 
                                       data-id="<?= $row['id'] ?>">Hapus</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <span>Tidak Dapat Diedit</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>
   

<div id="imageModal" class="modal">
    <span class="close" onclick="closeImageModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<!-- Delete Confirmation Modal -->
<div class="notification-overlay" id="deleteOverlay" style="display: none;"></div>
<div class="notification-popup" id="deleteConfirmation" style="display: none;">
    <div class="notification-icon">
        <i class="fas fa-exclamation-circle" id="deleteIcon"></i>
    </div>
    <div class="notification-title">Konfirmasi Hapus</div>
    <div class="notification-message">Apakah Anda yakin ingin menghapus presensi ini?</div>
    <div class="notification-buttons">
        <button class="notification-button cancel-button" id="cancelDelete">Batal</button>
        <button class="notification-button confirm-button" id="confirmDelete">Hapus</button>
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

    function showImageModal(src) {
        const modal = document.getElementById("imageModal");
        const modalImage = document.getElementById("modalImage");
        modalImage.src = src;
        modal.style.display = "flex";
    }

    function closeImageModal() {
        document.getElementById("imageModal").style.display = "none";
    }

function openModal(imageUrl) {
        const modal = document.getElementById("imageModal");
        const img = document.getElementById("modalImage");
        img.src = imageUrl;
        modal.style.display = "flex";
    }

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    // Tutup modal jika klik di luar gambar
    window.addEventListener('click', function(e) {
        const modal = document.getElementById("imageModal");
        if (e.target === modal) {
            closeModal();
        }
    });
    function toggleActionMenu(button) {
        const actionMenu = button.nextElementSibling;
        const isVisible = actionMenu.style.display === "block";
        actionMenu.style.display = isVisible ? "none" : "block";
    }

    function showActionMenu(button) {
        const actionMenu = button.nextElementSibling; // Ambil elemen menu aksi
        actionMenu.style.display = "block"; // Tampilkan menu aksi
        button.style.display = "none"; // Sembunyikan tombol aksi
    }
        function openModal(imageUrl) {
        const modal = document.getElementById("imageModal");
        const img = document.getElementById("modalImage");
        img.src = imageUrl;
        modal.style.display = "flex";
    }

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to all delete buttons
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            showDeleteConfirmation(id);
        });
    });
});

function showDeleteConfirmation(id) {
    const overlay = document.getElementById('deleteOverlay');
    const popup = document.getElementById('deleteConfirmation');
    
    // Reset display style
    overlay.style.removeProperty('display');
    popup.style.removeProperty('display');
    
    // Force reflow
    void overlay.offsetWidth;
    
    // Show overlay and popup
    overlay.classList.add('show');
    popup.classList.add('show');

    // Setup event handlers
    document.getElementById('cancelDelete').onclick = hideDeleteConfirmation;
    document.getElementById('confirmDelete').onclick = function() {
        window.location.href = `hapus_presensi.php?id=${id}`;
    };
    overlay.onclick = hideDeleteConfirmation;
}

function hideDeleteConfirmation() {
    const overlay = document.getElementById('deleteOverlay');
    const popup = document.getElementById('deleteConfirmation');
    
    overlay.classList.remove('show');
    popup.classList.remove('show');
    
    // Hide after animation completes
    setTimeout(() => {
        overlay.style.display = 'none';
        popup.style.display = 'none';
    }, 300);
}

// Close on overlay click
document.getElementById('deleteOverlay').onclick = hideDeleteConfirmation;
</script>

<div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
</div>
</body>
</html>