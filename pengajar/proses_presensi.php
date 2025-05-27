<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi.php';

// Cek login mentor
if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Ambil data mentor
$user_email = $_SESSION['user_email'];
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

// Ambil data siswa
$siswa = [];
$result = $conn->query("SELECT siswa_id, full_name FROM siswa ORDER BY full_name");
while ($row = $result->fetch_assoc()) {
    $siswa[] = $row;
}

// Proses form
$notif = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelas = $_POST['kelas'] ?? '';
    $mapel = $_POST['mapel'] ?? '';
    $sekolah = $_POST['sekolah'] ?? '';
    $status = $_POST['status'] ?? '';
    $alasan = $_POST['alasan'] ?? '';
    $sesi = $_POST['sesi'] ?? '';
    $siswa_ids = explode(',', $_POST['siswa_ids'] ?? '');

    if (empty($siswa_ids) || $kelas === '' || $mapel === '' || $status === '' || $sesi === '') {
        $notif = "Isi semua data wajib dan pilih minimal satu siswa!";
    } else {
        $success = true;
        foreach ($siswa_ids as $sid) {
            $sid = intval($sid);
            $stmt = $conn->prepare("INSERT INTO absensi_siswa (pengajar_id, siswa_id, kelas, mapel, sekolah, status, alasan, sesi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissssss", $pengajar_id, $sid, $kelas, $mapel, $sekolah, $status, $alasan, $sesi);
            if (!$stmt->execute()) $success = false;
            $stmt->close();
        }
        if ($success) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Presensi berhasil disimpan!'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Presensi gagal disimpan!'
            ];
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Siswa</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/navbar.css">
    <style>
            * {
        box-sizing: border-box;
    }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f4f4f4
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 120px auto 0 auto; /* Ubah dari 48px ke 120px agar turun */
            background: rgba(255,255,255,0.97);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(20, 83, 117, 0.18), 0 1.5px 4px rgba(250, 175, 29, 0.08);
            padding: 32px 28px 28px 28px;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #145375;
            font-weight: 700;
            margin-bottom: 24px;
            letter-spacing: 1px;
        }

        label {
            font-weight: 600;
            color: #145375;
            margin-top: 18px;
            display: block;
            letter-spacing: 0.5px;
        }

        select, input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border-radius: 8px;
            border: 1.7px solid #e6c200;
            font-size: 1.08rem;
            color: #145375;
            background: #f9f9f9;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 500;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(20, 83, 117, 0.04);
        }

        select:focus, input[type="text"]:focus, textarea:focus {
            border-color: #145375;
            box-shadow: 0 0 0 2px #faaf1d33;
        }

        .siswa-list {
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid #eee;
            margin-top: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #fafbfc;
        }

        .siswa-item {
            display: flex;
            align-items: center;
            padding: 6px 0;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.15s;
            border-radius: 5px;
        }

        .siswa-item:hover {
            background: #faaf1d22;
        }

        .siswa-item input[type="checkbox"] {
            accent-color: #faaf1d;
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }

        .siswa-nama {
            flex: 1;
            color: #145375;
        }

        .btn {
            margin-top: 22px;
            width: 100%;
            background: linear-gradient(90deg, #ffd700 60%, #faaf1d 100%);
            color: #145375;
            padding: 14px 0;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-family: "Poppins", Arial, sans-serif;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(250, 175, 29, 0.08);
            transition: background 0.3s, color 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.5px;
            outline: none;
            overflow: hidden;
        }

        .btn:hover, .btn:focus {
            background: linear-gradient(90deg, #145375 60%, #faaf1d 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.03);
        }

        .notif {
            text-align: center;
            margin: 10px 0 18px 0;
            color: #fff;
            background: #145375;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px #faaf1d22;
        }

        .siswa-card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .siswa-card {
            background: #faaf1d22;
            color: #145375;
            border-radius: 6px;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.98rem;
        }
        .siswa-card button {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            cursor: pointer;
            font-size: 1rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .siswa-card button:hover {
            background: #c0392b;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 97vw;
                padding: 16px 4vw 16px 4vw;
                border-radius: 12px;
            }
            h2 {
                font-size: 1.3rem;
                margin-bottom: 18px;
            }
            .btn {
                font-size: 0.98rem;
                padding: 10px 0;
            }
            select, input[type="text"], textarea {
                font-size: 0.98rem;
                padding: 10px;
            }
            .siswa-list {
                max-height: 120px;
                padding: 7px;
            }
        }

        @media (max-width: 480px) {
            .container {
                max-width: 99vw;
                padding: 8px 2vw 8px 2vw;
                border-radius: 8px;
            }
            h2 {
                font-size: 1.05rem;
                margin-bottom: 13px;
            }
            .btn {
                font-size: 0.9rem;
                padding: 8px 0;
            }
            select, input[type="text"], textarea {
                font-size: 0.9rem;
                padding: 8px;
            }
            .siswa-list {
                max-height: 90px;
                padding: 5px;
            }
        }

        @media (max-width: 600px) {
            .container {
                max-width: 100vw;
                margin: 80px 0 0 0;
                padding: 10px 2vw 18px 2vw;
                border-radius: 8px;
                box-shadow: none;
            }
            h2 {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }
            .btn {
                font-size: 0.95rem;
                padding: 10px 0;
            }
            select, input[type="text"], textarea {
                font-size: 0.95rem;
                padding: 8px;
            }
            .siswa-list {
                max-height: 90px;
                padding: 5px;
            }
            .siswa-card {
                font-size: 0.93rem;
                padding: 4px 8px;
            }
            .siswa-card button {
                width: 18px;
                height: 18px;
                font-size: 0.95rem;
            }
            .nav-links {
                flex-wrap: wrap;
                font-size: 0.95rem;
            }
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
    <nav class="navbar">
    <div class="logo">
        <img src="images/foto4.png" alt="Logo">
    </div>
    <h1 class="title">Dashboard Mentor</h1>
    <ul class="nav-links">
        <li><a href="home_mentor.php" >Jurnal</a></li>
        <li><a href="proses_presensi.php" class="active">Presensi Siswa</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </div>
</nav>
<div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
</div>
<div class="container">
    <h2>Presensi Siswa</h2>
    <?php if($notif): ?>
        <div class="notif"><?= htmlspecialchars($notif) ?></div>
    <?php endif; ?>
    <form method="post">
        <a href="riwayat_absensi.php" class="btn" style="background: #145375; color: #fff; margin-bottom:12px; display:block; text-align:center;">
            Lihat Riwayat Absensi
        </a>

        <label for="search-siswa">Cari Siswa:</label>
        <input type="text" id="search-siswa" placeholder="Ketik nama siswa..." onkeyup="cariSiswa()">

        <div id="siswa-list"></div>

        <div id="selected-siswa" style="margin-top:15px;"></div>
        <input type="hidden" id="selected-siswa-ids" name="siswa_ids" value="">
        
        <label for="sesi">Sesi:</label>
        <select id="sesi" name="sesi" required>
            <option value="Sesi 1">Sesi 1 (09.00-10.30)</option>
            <option value="Sesi 2">Sesi 2 (10.30-12.00)</option>
            <option value="Sesi 3">Sesi 3 (13.00-14.30)</option>
            <option value="Sesi 4">Sesi 4 (14.30-16.00)</option>
            <option value="Sesi 5">Sesi 5 (16.00-17.30)</option>
            <option value="Sesi 6">Sesi 6 (18.00-19.30)</option>
            <option value="Sesi 7">Sesi 7 (19.30-21.00)</option>
        </select>

        <label for="kelas">Kelas:</label>
        <input type="text" name="kelas" id="kelas" required>

        <label for="mapel">Mata Pelajaran:</label>
        <input type="text" name="mapel" id="mapel" required>

        <label for="sekolah">Sekolah (Opsional):</label>
        <input type="text" name="sekolah" id="sekolah">

        <label for="status">Status:</label>
        <select name="status" id="status" required onchange="document.getElementById('alasan').style.display=(this.value=='Izin'||this.value=='Sakit')?'block':'none';">
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
        </select>

        <textarea name="alasan" id="alasan" placeholder="Alasan (jika Izin/Sakit)" style="display:none;margin-top:10px;"></textarea>


        <button type="submit" class="btn">Simpan Presensi</button>
    </form>
</div>
    <script src="js/menu.js" defer></script>
<script>
const selectedSiswa = {};

function cariSiswa() {
    const keyword = document.getElementById('search-siswa').value;
    const siswaList = document.getElementById('siswa-list');
    if (keyword.length > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'cari_siswa.php?keyword=' + encodeURIComponent(keyword), true);
        xhr.onload = function() {
            if (this.status === 200) {
                siswaList.innerHTML = this.responseText;
                // Ceklist yang sudah dipilih tetap tercentang
                Object.keys(selectedSiswa).forEach(function(id) {
                    const cb = document.getElementById('cb-siswa-' + id);
                    if (cb) cb.checked = true;
                });
            }
        };
        xhr.send();
    } else {
        siswaList.innerHTML = '';
    }
}

function pilihSiswa(checkbox, siswaId, siswaName) {
    if (checkbox.checked) {
        selectedSiswa[siswaId] = siswaName;
    } else {
        delete selectedSiswa[siswaId];
    }
    updateSelectedSiswa();
}

function updateSelectedSiswa() {
    const selectedSiswaContainer = document.getElementById('selected-siswa');
    const selectedSiswaIds = document.getElementById('selected-siswa-ids');
    selectedSiswaContainer.innerHTML = '<h4>Siswa yang Dipilih:</h4>';

    const siswaNames = [];
    const siswaIds = [];

    for (const [id, name] of Object.entries(selectedSiswa)) {
        siswaNames.push(`
            <div class="siswa-card">
                <span>${name}</span>
                <button type="button" onclick="hapusSiswa('${id}')">&times;</button>
            </div>
        `);
        siswaIds.push(id);
    }

    selectedSiswaContainer.innerHTML += `<div class="siswa-card-container">${siswaNames.join('')}</div>`;
    selectedSiswaIds.value = siswaIds.join(',');
}

function hapusSiswa(siswaId) {
    delete selectedSiswa[siswaId];
    updateSelectedSiswa();
    cariSiswa();
}
</script>
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
                // Clear notification parameter before reloading
                window.location.href = 'proses_presensi.php';
            }
        }, 300);
    };

    <?php 
    if (isset($_SESSION['notification'])): 
        $notification = $_SESSION['notification'];
        unset($_SESSION['notification']); // Clear the notification after showing
    ?>
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

