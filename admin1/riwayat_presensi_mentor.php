<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
    echo "Pengajar tidak ditemukan.";
    exit;
}

$pengajar_id = intval($_GET['id']);

// Ambil data mentor
$mentorQuery = $conn->prepare("SELECT full_name FROM mentor WHERE pengajar_id = ?");
$mentorQuery->bind_param("i", $pengajar_id);
$mentorQuery->execute();
$mentorResult = $mentorQuery->get_result();
if ($mentorRow = $mentorResult->fetch_assoc()) {
    $full_name = $mentorRow['full_name'];
} else {
    echo "<script>alert('Mentor tidak ditemukan'); window.history.back();</script>";
    exit();
}

// Ambil riwayat presensi mentor
$stmt = $conn->prepare("SELECT tanggal, sesi, status, mapel, materi, kelas, jumlah_siswa, keterangan, note, gambar, waktu_presensi FROM presensi_pengajar WHERE pengajar_id = ? ORDER BY waktu_presensi DESC");
$stmt->bind_param("i", $pengajar_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * {

        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
            width: max-content;
            margin: 150px auto 20px;
            background-color: #145375;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
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
                margin-top: 90px;
                padding: 15px;
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

    .btn-detail {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 12px;
        background-color: #e6c200;
        color: #145375;
        text-decoration: none;
        border-radius: 5px;
    }

    .btn-detail:hover {
        background-color: #fff;

    }

    .btn-group-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
        margin-top: 10px;
    }


    /* Dropdown styles */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #0271ab;
        min-width: 180px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        padding: 0;
        margin: 5px;
        left: -35px;
        list-style: none;

        /* <- tambahkan border */
    }

    .dropdown-menu li a {
        color: #fff !important;
        /* pastikan warnanya terlihat */
        padding: 12px 16px;
        text-decoration: none;
        display: block;

        font-weight: bold;
        /* opsional biar lebih terlihat */
    }

    .dropdown-menu li a:hover {
        background-color: #e6c200;
        color: #145375;
    }


    .arrow {
        font-size: 12px;
        margin-left: 5px;
    }
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
    /* Notifikasi Pop-up */
    .notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f9f9f9;
        color: #145375;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        /* Default: disembunyikan */
        text-align: center;
        width: 300px;
    }

    .notification p {
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
    }

    /* Tombol di dalam notifikasi */
    .notification-buttons {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .notification-buttons .btn {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
        text-decoration: none;
        /* Hilangkan garis bawah */
    }

    /* Tombol Batal */
    .notification-buttons .btn-secondary {
        background-color: #145375;
        color: white;
    }

    .notification-buttons .btn-secondary:hover {
        background-color: #e6c200;
        color: #145375;
    }

    /* Tombol Keluar */
    .notification-buttons .btn-danger {
        background-color: #e74c3c;
        /* Warna merah */
        color: white;
        border: none;
    }

    .notification-buttons .btn-danger:hover {
        background-color: #c0392b;
        /* Warna merah lebih gelap saat hover */
        transform: scale(1.05);
        /* Efek zoom saat hover */
    }
    </style>
</head>
<script>
function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}

function toggleDropdown(event) {
    event.preventDefault(); // supaya gak reload atau pergi ke #
    const dropdown = event.currentTarget.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function toggleDropdown(event) {
    event.preventDefault();
    const link = event.currentTarget;
    const dropdown = link.nextElementSibling;
    const arrow = link.querySelector('#arrow');

    const isOpen = dropdown.style.display === 'block';
    dropdown.style.display = isOpen ? 'none' : 'block';
    arrow.innerHTML = isOpen ? '&#9660;' : '&#9650;'; // ▼ / ▲
}

// Tutup dropdown kalau klik di luar menu
document.addEventListener('click', function(event) {
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');
    dropdownMenus.forEach(menu => {
        if (!menu.parentElement.contains(event.target)) {
            menu.style.display = 'none';
        }
    });

    function confirmLogout() {
            if (confirm("Apakah kamu yakin ingin keluar?")) {
                window.location.href = "logout.php"; // ganti sesuai nama file logout-mu
            }
        }
});
</script>

<body>

    <nav class="navbar">
        <div class="logo">
            <a href="home.php">
                <img src="images/foto4.png" alt="Logo" class="logo-image">
            </a>
        </div>
        <h1 class="title">Dashboard Admin</h1>
        <ul class="nav-links">
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown(event)">Presensi <span id="arrow" class="arrow">&#9660;</span></a>
                <ul class="dropdown-menu">
                    <li><a href="home.php">Presensi Siswa</a></li>
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
                </ul>
            </li>

            <li><a href="pengajar.php" class="active">Pengajar</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    
    <div class="container">
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Sesi</th>
            <th>Status</th>
            <th>Mata Pelajaran</th>
            <th>Kelas</th>
            <th>Jumlah Siswa</th>
            <th>Materi</th>
            <th>Keterangan</th>
            <th>Catatan</th>
            <th>Gambar</th>
            <th>Waktu Presensi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['sesi']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['mapel']) ?></td>
            <td><?= htmlspecialchars($row['kelas']) ?></td>
            <td><?= htmlspecialchars($row['jumlah_siswa']) ?></td>
            <td><?= htmlspecialchars($row['materi']) ?></td>
            <td><?= htmlspecialchars($row['keterangan']) ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <button class="btn-view" onclick="showImageModal('<?= htmlspecialchars($row['gambar']) ?>')">Lihat</button>
                        <?php else: ?>
                            Tidak Ada Gambar
                        <?php endif; ?>
                    </td>
            <td><?= htmlspecialchars($row['waktu_presensi']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<div id="imageModal" class="modal">
    <span class="close" onclick="closeImageModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>
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
    </script>
</div>
<div id="logout-notification" class="notification">
    <p>Apakah Anda yakin ingin keluar?</p>
    <div class="notification-buttons">
        <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
        <a href="logout.php" class="btn btn-danger">Keluar</a>
    </div>
</div>
</body>
</html>