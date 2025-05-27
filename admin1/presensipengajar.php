<?php
session_start();
include '../koneksi.php';
// Koneksi database
if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}

// Ambil data pengajar untuk dropdown
$pengajar_list = [];
$sql_pengajar = "SELECT pengajar_id, full_name FROM mentor ORDER BY full_name";
$result_pengajar = $conn->query($sql_pengajar);
while ($row = $result_pengajar->fetch_assoc()) {
    $pengajar_list[] = $row;
}

// Ambil filter dari GET
$pengajar_id = isset($_GET['pengajar_id']) ? $_GET['pengajar_id'] : '';
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$full_name = '';
$presensi = [];

// Jika pengajar dipilih, ambil nama lengkap
if ($pengajar_id) {
    $stmt = $conn->prepare("SELECT full_name FROM mentor WHERE pengajar_id = ?");
    $stmt->bind_param("i", $pengajar_id);
    $stmt->execute();
    $result_nama = $stmt->get_result();
    if ($row = $result_nama->fetch_assoc()) {
        $full_name = $row['full_name'];
    }
    $stmt->close();
}

// Siapkan query presensi
if ($pengajar_id && $tanggal_filter) {
    // Filter: pengajar + tanggal
    $query = "SELECT id, tanggal, sesi, status, mapel, materi, kelas, jumlah_siswa, keterangan, note, gambar, waktu_presensi
              FROM presensi_pengajar 
              WHERE pengajar_id = ? AND tanggal = ?
              ORDER BY waktu_presensi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $pengajar_id, $tanggal_filter);

} elseif ($pengajar_id) {
    // Filter: pengajar saja (tampilkan hari ini)
    $today = date('Y-m-d');
    $query = "SELECT id, tanggal, sesi, status, mapel, materi, kelas, jumlah_siswa, keterangan, note, gambar, waktu_presensi
              FROM presensi_pengajar 
              WHERE pengajar_id = ? AND tanggal = ?
              ORDER BY waktu_presensi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $pengajar_id, $today);

} elseif ($tanggal_filter) {
    // Filter: tanggal saja (tanpa pengajar)
    $query = "SELECT p.id, p.tanggal, p.sesi, p.status, p.mapel, p.materi, p.kelas, p.jumlah_siswa, p.keterangan, p.note, p.gambar, p.waktu_presensi, m.full_name
              FROM presensi_pengajar p
              JOIN mentor m ON p.pengajar_id = m.pengajar_id
              WHERE p.tanggal = ?
              ORDER BY p.waktu_presensi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tanggal_filter);

} else {
    // Tidak ada filter: tampilkan semua presensi hari ini
    $today = date('Y-m-d');
    $query = "SELECT p.id, p.tanggal, p.sesi, p.status, p.mapel, p.materi, p.kelas, p.jumlah_siswa, p.keterangan, p.note, p.gambar, p.waktu_presensi, m.full_name
              FROM presensi_pengajar p
              JOIN mentor m ON p.pengajar_id = m.pengajar_id
              WHERE p.tanggal = ?
              ORDER BY p.waktu_presensi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $today);
}

// Eksekusi dan ambil data
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $presensi[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * {

        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #f4f4f4;
        color: #145375;
        margin: 0;
        padding: 0;
        padding-top: 100px;
        overflow-x: hidden;

    }

    .container {
        margin-top: 35px;
        padding: 20px;
        width: 90%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 1s ease-in-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        text-align: center;
        color: #145375;
        margin-bottom: 20px;
        font-size: 2.5em;
    }

    p {
        text-align: center;
        font-size: 1.2em;
        margin-bottom: 30px;
    }

    .filter-form {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        justify-content: center;
    }

    .filter-form label {
        font-weight: bold;
        color: #003049;
    }

    .filter-form select#pengajar_id {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1em;
        width: 200px;
    }


    .filter-form input[type="date"] {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1em;
        width: 200px;
    }

    .filter-form button {
        padding: 10px 20px;
        background-color: #faaf1d;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 1em;
    }

    .filter-form button:hover {
        background-color: #145375;
        transform: scale(1.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fabe49;
        color: #003049;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    th,
    td {
        padding: 15px;
        border: 1px solid #003049;
        text-align: center;
        font-size: 1em;
    }

    th {
        background-color: #faaf1d;
        color: #003049;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    tr:nth-child(odd) {
        background-color: #ffffff;
    }


    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-form input,
        .filter-form button {
            width: 100%;
        }

        h2 {
            font-size: 2em;
        }

        th,
        td {
            font-size: 0.9em;
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.8em;
        }

        th,
        td {
            font-size: 0.8em;
            padding: 8px;
        }

        .filter-form input,
        .filter-form button {
            font-size: 0.9em;
            padding: 8px;
        }
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

    .btn-view {
        padding: 10px 20px;
        background-color: #faaf1d;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 1em;
    }
    .btn-view:hover {
        background-color: #145375;
        transform: scale(1.05);
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

<div>
    <nav class="navbar">

        <div class="logo">
            <a href="home.php">
                <img src="images/foto4.png" alt="Logo" class="logo-image">
            </a>
        </div>
        <h1 class="title">Dashboard Admin</h1>
        <ul class="nav-links">
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown(event)" class="active">Presensi <span id="arrow"
                        class="arrow">&#9660;</span></a>
                <ul class="dropdown-menu">
                    <li><a href="home.php">Presensi Siswa</a></li>
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
                </ul>
            </li>

            <li><a href="pengajar.php">Pengajar</a></li>
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
        <h2>Riwayat Presensi<?= $full_name ? ' - ' . htmlspecialchars($full_name) : ' Mentor' ?></h2>

        <form method="GET" class="filter-form">
            <label for="pengajar_id">Pilih Pengajar:</label>
            <select name="pengajar_id" id="pengajar_id">
                <option value="">-- Semua Pengajar --</option>
                <?php foreach ($pengajar_list as $pengajar): ?>
                <option value="<?= $pengajar['pengajar_id'] ?>"
                    <?= ($pengajar['pengajar_id'] == $pengajar_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pengajar['full_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>">
            <button type="submit">Tampilkan</button>
        </form>

        <?php if (empty($presensi)) : ?>
        <p style="text-align: center;">Belum ada data presensi.</p>
        <?php else : ?>
        <table>
            <thead>
                <tr>
                    <?php if (!$pengajar_id): ?>
                    <th>Nama</th>
                    <?php endif; ?>
                    <th>Tanggal</th>
                    <th>Sesi</th>
                    <th>Status</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presensi as $p): ?>
                <?php
                        $waktu = date('H:i', strtotime($p['waktu_presensi']));
                        $sesi = 'Tidak Diketahui';
                        if ($waktu >= '09:00' && $waktu < '10:30') {
                            $sesi = 'Sesi 1 (09.00-10.30)';
                        } elseif ($waktu >= '10:30' && $waktu < '12:00') {
                            $sesi = 'Sesi 2 (10.30-12.00)';
                        } elseif ($waktu >= '13:00' && $waktu < '14:30') {
                            $sesi = 'Sesi 3 (13.00-14.30)';
                        } elseif ($waktu >= '14:30' && $waktu < '16:00') {
                            $sesi = 'Sesi 4 (14.30-16.00)';
                        } elseif ($waktu >= '16:00' && $waktu < '17:30') {
                            $sesi = 'Sesi 5 (16.00-17.30)';
                        } elseif ($waktu >= '18:00' && $waktu < '19:30') {
                            $sesi = 'Sesi 6 (18.00-19.30)';
                        } elseif ($waktu >= '19:30' && $waktu < '21:00') {
                            $sesi = 'Sesi 7 (19.30-21.00)';
                        }
                    ?>
                <tr>
                    <?php if (!$pengajar_id): ?>
                    <td><?= htmlspecialchars($p['full_name']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($p['tanggal']) ?></td>
                    <td><?= $sesi ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td>
                        <?php if (!empty($p['gambar'])): ?>
                        <button class="btn-view"
                            onclick="showImageModal('<?= htmlspecialchars($p['gambar']) ?>')">Lihat</button>
                        <?php else: ?>
                        Tidak Ada Gambar
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Modal untuk melihat gambar -->
    <div id="imageModal" style="display: none;" class="modal">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>
    <style>
    /* Gaya untuk modal */
    .modal {
        display: none;
        /* Default disembunyikan */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        /* Transparansi latar belakang */
        display: flex;
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

    /* Animasi masuk */
    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    </style>

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
    </script>

    <script>
    function confirmLogout() {
        if (confirm("Apakah kamu yakin ingin keluar?")) {
            window.location.href = "logout.php"; // ganti sesuai nama file logout-mu
        }
    }

    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    function toggleDropdown(event) {
        event.preventDefault();
        const dropdown = event.target.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        const arrow = dropdown.querySelector('.arrow');

        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        arrow.innerHTML = menu.style.display === 'block' ? '&#9650;' : '&#9660;'; // ▲ dan ▼
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

    // Tutup dropdown jika klik di luar
    window.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.dropdown');
        if (!dropdown.contains(e.target)) {
            dropdown.querySelector('.dropdown-menu').style.display = 'none';
            dropdown.querySelector('.arrow').innerHTML = '&#9660;';
        }
    });
    </script>
    </body>

</html>